<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(Request $request): Response
    {

        // session->start() in PHP
        $session = $request->getSession();
        if ($session->has('Visits')){
            $Visits = $session->get('Visits') + 1;
        }else{
            $Visits = 1;
        }
        $session->set('Visits',$Visits);

        return $this->render('session/index.html.twig',['Visits' => $Visits]);
    }
}
