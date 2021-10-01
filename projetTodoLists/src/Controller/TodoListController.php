<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TodoListController extends AbstractController
{
    /**
     * @Route("/todo/list", name="app.todo_list")
     */
    public function index(TodoListRepository $repoTodo): Response
    {
        $user = $this->getUser();
        return $this->render('todo_list/index.html.twig', [
            'controller_name' => 'TodoListController',
            'todos' => $repoTodo->findTodoByUserField($user)
        ]);
    }

     /**
     * @Route("/todo/create", name="app.todo_create")
     */
    public function createTodo(Request $request,EntityManagerInterface $em,TodoListRepository $repoTodo): Response
    {
        $todo = new TodoList();

        $form = $this->createForm(TodoListType::class, $todo);

        $form->handleRequest($request); 
 

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $todo  = $form->getData();             
            $todo->setIsDone(false);
            $todo->setUser($this->getUser());
            $em->persist($todo);
            $em->flush();
            
            return $this->render('todo_list/index.html.twig', [
                'form' => $form->createView(),
                'todos' => $repoTodo->findTodoByUserField($this->getUser())
            ]);
        }

        return $this->render('todo_list/todo.html.twig', [
            'form' => $form->createView()         
        ]);
    }

       /**
     * @Route("/todo/delete/{id}", name="app.todo_delete")
     */
    public function deleteTodo(EntityManagerInterface $em,TodoListRepository $repoTodo,$id): Response
    {
        $todo = $repoTodo->findByIdField($id);

        $em->remove($todo[0]);
        $em->flush();

        return $this->render('todo_list/index.html.twig', [
            'controller_name' => 'TodoListController',
            'todos' => $repoTodo->findTodoByUserField($this->getUser())
        ]);
    }

     /**
     * @Route("/todo/modify/{id}", name="app.todo_modify")
     */
    public function modifyTodo(Request $request,EntityManagerInterface $em,TodoListRepository $repoTodo,$id): Response
    {
        $todo = $repoTodo->findByIdField($id);

        $form = $this->createForm(TodoListType::class, $todo[0]);

        $form->handleRequest($request); 
 

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $todo  = $form->getData();             
            $todo->setIsDone(false);
            $todo->setUser($this->getUser());
            $em->persist($todo);
            $em->flush();
            
            return $this->render('todo_list/index.html.twig', [
                'form' => $form->createView(),
                'todos' => $repoTodo->findTodoByUserField($this->getUser())
            ]);
        }

        return $this->render('todo_list/todo.html.twig', [
            'form' => $form->createView()         
        ]);
    }



}
