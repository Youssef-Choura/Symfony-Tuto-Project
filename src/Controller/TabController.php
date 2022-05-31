<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TabController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/tab/{nb?5}', name: 'app_tab')]
    public function index($nb): Response
    {
        $notes = [];
        for ($i=0 ; $i<$nb ;$i++){
            $notes[] = random_int(0,20);
        }
        return $this->render('tab/index.html.twig', [
            'notes' => $notes,
        ]);
    }
}
