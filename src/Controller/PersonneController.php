<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Services\PdfService;
use App\Services\Helpers;
use App\Services\MailerService;
use App\Services\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route("/personne"), isGranted("ROLE_USER")]
class PersonneController extends AbstractController
{
    public function __construct(LoggerInterface $logger, Helpers $helpers)
    {

    }

    /**
     * @Route("/", name="personne_list")
     */
    public function index(ManagerRegistry $managerRegistry): Response
    {
        $personneRepository = $managerRegistry->getRepository(Personne::class);
        $personnes = $personneRepository->findAll();

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    /**
     * @Route("/All/{page?1}/{nb?12}", name="personne_list_All"),
     * @IsGranted("ROLE_USER")
     */
    public function indexAll(ManagerRegistry $managerRegistry, $page, $nb): Response
    {
        $personneRepository = $managerRegistry->getRepository(Personne::class);
        $nbPersonne = $personneRepository->count([]);
        $nbPage = ceil($nbPersonne / $nb);
        $personnes = $personneRepository->findBy([], [], $nb, ($page - 1) * $nb);
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes, 'isPaginated' => true, 'nbPage' => $nbPage, 'page' => $page, 'nb' => $nb
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="personne_details")
     */
    //Param Convertor
    public function details(Personne $personne = null): Response
    {
        if (!$personne) {
            $this->addFlash('danger', 'Personne non trouvée');
            return $this->redirectToRoute('personne_list');
        }

        return $this->render('personne/details.html.twig', [
            'personne' => $personne,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/add', name: 'app_personne_add')]
    public function addPersonne(ManagerRegistry $managerRegistry, Request $request, UploaderService $uploaderService, MailerService $mailerService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Vous n\'avez pas accès à cette page');
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        //The form is going to handle the request
        $form->handleRequest($request);
        //Form submitted --> Add in database + redirect to list + message
        if ($form->isSubmitted() && $form->isValid()) {
            $personne = $form->getData();
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($personne);
            $image = $form->get('image')->getData();

            if ($image) {
                $directory = $this->getParameter('personne_directory');
                $personne->setImage($uploaderService->uploadFile($image, $directory));
            }
            $entityManager->flush();
            $mailMessage = $personne->getFirstname() . ' ' . $personne->getLastname() . ' a été ajouté';

            $this->addFlash('success', 'Personne ajoutée');
            $personne->setCreatedBy($this->getUser());
            $mailerService->sendEmail(text: $mailMessage);

            return $this->redirectToRoute('personne_list_All');
        }
        return $this->render('personne/add-person.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/edit/{id?}', name: 'app_personne_edit')]
    public function editPersonne(ManagerRegistry $managerRegistry, Request $request, SluggerInterface $slugger, MailerService $mailerService, Personne $personne = null): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Vous n\'avez pas accès à cette page');
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        //The form is going to handle the request
        $form->handleRequest($request);
        //Form submitted --> Add in database + redirect to list + message
        if ($form->isSubmitted() && $form->isValid()) {
            $personne = $form->getData();

            $image = $form->get('image')->getData();

            if ($image) {
                $originalImageName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalImageName);
                $newImageName = $safeFilename . '-' . uniqid('', true) . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('personne_directory'),
                        $newImageName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $personne->setImage($newImageName);
            }

            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();
            $entityManager->flush();

            $mailMessage = $personne->getFirstname() . ' ' . $personne->getLastname() . ' a été modifié';
            $this->addFlash('success', 'Personne modifiée');
            $mailerService->sendEmail(text: $mailMessage);
            return $this->redirectToRoute('personne_list_All');
        }

        return $this->render('personne/add-person.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_personne_delete'), IsGranted(['ROLE_ADMIN', 'ROLE_USER'])]
    public function deletePersonne(ManagerRegistry $managerRegistry, Personne $personne = null): RedirectResponse
    {
        if (!$personne) {
            $this->addFlash('danger', 'Personne non trouvée');
        } else {
            $manager = $managerRegistry->getManager();
            $manager->remove($personne);
            $manager->flush();
            $this->addFlash('success', 'Personne supprimée');
        }
        return $this->redirectToRoute('personne_list_All');
    }

    #[Route('/update/{id<\d+>}/{name}/{firstname}/{age}', name: 'app_personne_update')]
    public function updatePersonne(ManagerRegistry $managerRegistry, $name, $firstname, $age, Personne $personne = null): RedirectResponse
    {
        if (!$personne) {
            $this->addFlash('danger', 'Personne non trouvée');
        } else {
            $personne->setLastname($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);

            $manager = $managerRegistry->getManager();
            $manager->persist($personne);
            $manager->flush();

            $this->addFlash('success', 'Personne a éditée');
        }
        return $this->redirectToRoute('personne_list_All');
    }

    #[Route('/All/age/{ageMin<\d+>}/{ageMax<\d+>}', name: 'app_personne_age')]
    public function PersonByAgeInterval(ManagerRegistry $managerRegistry, $ageMin, $ageMax): Response
    {
        $personneRepository = $managerRegistry->getRepository(Personne::class);
        $personnes = $personneRepository->findPersonByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    #[Route('/All/stats/{ageMin<\d+>}/{ageMax<\d+>}', name: 'app_personne_stats')]
    public function statsPersonByAgeInterval(ManagerRegistry $managerRegistry, $ageMin, $ageMax): Response
    {
        $personneRepository = $managerRegistry->getRepository(Personne::class);
        $stats = $personneRepository->statsPersonByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin' => $ageMin,
            'ageMax' => $ageMax
        ]);
    }

    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePdfPersonne(PdfService $pdf, Personne $personne = null): void
    {
        $html = $this->render('personne/details.html.twig', ['personne' => $personne]);
        $pdf->generatePdf($html);
    }
}
