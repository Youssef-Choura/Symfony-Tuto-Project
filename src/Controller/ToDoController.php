<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo')]
class ToDoController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/",name="app_to_do")
     */
    public function index(Request $request): Response

    {
        $session = $request->getSession();
        if (!$session->has('todos')){
            $todos = array(
                'achat' => 'acheter clé USB',
                'cours' => 'finaliser mon cours',
                'correction' => 'corriger mes examens'
            );
            $session->set('todos',$todos);
            $this->addFlash('info',"la liste des todos viens d'être initialisée");
        }
        return $this->render('to_do/index.html.twig'
        );
    }

    #[route(
        '/add/{key?test}/{element?test}',
        name: 'todo_add',
//        defaults: [ 'key' => 'NewOne' ,'element' => 'salem' ]
    )]
    public function addToDo(Request $request, $key, $element) : Response
    {
        $session = $request->getSession();

        if ($session->has('todos')){
            $todos = $session->get('todos');
            if (isset($todos[$key])){
                //erreur
                $this->addFlash('danger',"La todo d'id $key existe deja");
            }else {
                //ajouter et afficher message success
                $todos[$key] = $element;
                $this->addFlash('success',"La todo d'id $key est ajouté");
                $session->set('todos',$todos);
            }
        } else {
            $this->addFlash('danger',"La liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_to_do');
    }

    #[route('/delete/{key}', name: 'todo_delete')]
    public function deleteToDO(Request $request, $key) : Response
    {

        $session = $request->getSession();
        if ($session->has('todos')){
            $todos = $session->get('todos');
            if (!isset($todos[$key])){
                //erreur
                $this->addFlash('danger',"La todo d'id $key n'existe dans la liste");
            }else {
                unset($todos[$key]);
                $session->set('todos',$todos);
                $this->addFlash('success',"La todo d'id $key a été mise à jour avec succé");
            }
        } else {
            $this->addFlash('danger',"La liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_to_do');
    }

    #[route('/update/{key}/{element}', name: 'todo_update')]
    public function updateToDO(Request $request, $key, $element) : Response
    {
        $session = $request->getSession();

        if ($session->has('todos')){
        $todos = $session->get('todos');
        if (!isset($todos[$key])){
            //erreur
            $this->addFlash('danger',"La todo d'id $key n'existe dans la liste");
        }else {
            //ajouter et afficher message success
            $todos[$key] = $element;
            $session->set('todos',$todos);
            $this->addFlash('success',"La todo d'id $key a été mise à jour avec success");

        }
        } else {
            $this->addFlash('danger', "La liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_to_do');
    }

    #[route('/reset', name: 'todo_reset')]
    public function resetToDO(Request $request) : Response
    {
        $session = $request->getSession();
        $session->remove('$todos');
        return $this->redirectToRoute('app_to_do');
    }

}
