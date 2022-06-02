<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwigInheritanceController extends AbstractController
{
    #[Route('/twig/', name: 'app_twig')]
    public function index(): Response
    {
        return $this->render('twig_inheritance/index.html.twig', [
            'controller_name' => 'TwigInheritanceController',
        ]);
    }
    #[Route('/twig/inheritance', name: 'app_twig_inheritance')]
    public function inheritance(): Response
    {
        return $this->render('heritage.html.twig');
    }
}
