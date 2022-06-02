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
    #[Route('/tab/{nb<\d+>?5}', name: 'tab')]
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
    #[Route('/tab/users', name: 'users')]
    public function users(): Response
    {
        $users = [
            ['firstname' => 'youssef', 'name' => 'choura', 'age' => 39],
            ['firstname' => 'taha', 'name' => 'choura', 'age' => 3],
            ['firstname' => 'ahmed', 'name' => 'choura', 'age' => 59]
        ];
        return $this->render('tab/users.html.twig',[
            'users' => $users
        ]);
    }
}
