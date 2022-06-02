<?php

namespace App\Controller;

use Exception;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{


    #[Route('/template', name: 'template')]
    public function template(): Response
    {
        return $this->render('template.html.twig');
    }


    /**
     * @return Response
     * @param $var
     * @Route("/order/{var}", name="order_route_test")
     */

    public function testOrderRoute ($var): Response
    {
        return new Response($var);
    }


    #[Route('/first', name: 'app_first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig',
            ['name' => 'choura',
                'firstname' => 'youssef'
            ]);
    }


    //#[Route('/sayHello/{name}/{firstname}', name: 'say_hello')]
    public function sayHello(Request $request,$name,$firstname): Response
    {
        //dd($request);
        return $this->render('first/hello.html.twig',
            ['name'=>$name , 'firstname' => $firstname]);
    }

    #[Route('multi/{entier1<\d+>}/{entier2<\d+>}', name:'multi_app')]
//        requirements: ['entier1'=> '\d+', 'entier2'=> '\d+'])] regex replaced with <\d+>
    public function multiplication($entier1,$entier2):Response
    {
        $result = $entier1 * $entier2;
        return new Response("<h1>$result</h1>");
    }
}