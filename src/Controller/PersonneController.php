<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/personne")]
class PersonneController extends AbstractController
{
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
     * @Route("/All/{page?1}/{nb?12}", name="personne_list_All")
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

    #[Route('/add/{name}/{firstname}/{age}', name: 'app_personne_add')]
    public function addPersonne(ManagerRegistry $managerRegistry, $name, $firstname, $age): Response
    {

        $personne = new Personne();
        $personne->setName($name);
        $personne->setFirstname($firstname);
        $personne->setAge($age);

        $manager = $managerRegistry->getManager();
        $manager->persist($personne);
        $manager->flush();

        $this->addFlash('success', 'Personne a éte ajoutée');
        return $this->render('personne/details.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_personne_delete')]
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
    public function updatePersonne(Personne $personne = null, ManagerRegistry $managerRegistry, $name, $firstname, $age): RedirectResponse
    {
        if (!$personne) {
            $this->addFlash('danger', 'Personne non trouvée');
        } else {
            $personne->setName($name);
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
}
