<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\NvUserType;
use App\Form\TodoListType;
use App\Repository\UserRepository;
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
            'todos' => $user->getTodoLists()
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
            $todo->setIsAdmin(true);
            $todo->addUser($this->getUser());
            $this->getUser()->addTodoList($todo);
            $em->persist($todo);
         
            $em->flush();
            
            return $this->render('todo_list/index.html.twig', [
                'form' => $form->createView(),
                'todos' => $this->getUser()->getTodoLists()
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
        foreach($todo[0]->getUsers() as $user){
            $todo[0]->removeUser($user);
        }

        $em->remove($todo[0]);
        $em->flush();

        return $this->render('todo_list/index.html.twig', [
            'controller_name' => 'TodoListController',
            'todos' => $this->getUser()->getTodoLists()
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
            $todo->setIsAdmin(true);
            $this->getUser()->addTodoList($todo);
            $em->persist($todo);
          
            $em->flush();
            
            return $this->render('todo_list/index.html.twig', [
                'form' => $form->createView(),
                'todos' => $this->getUser()->getTodoLists()
            ]);
        }

        return $this->render('todo_list/todo.html.twig', [
            'form' => $form->createView()         
        ]);
    }


    /**
     * @Route("/todo/ajoutUtilisateur/{id}", name="app.todo_utilisateur")
     */
    public function ajoutUtilisateur(Request $request,EntityManagerInterface $em,TodoListRepository $repoTodo,UserRepository $repoUser,$id): Response
    {
        $todo = $repoTodo->findByIdField($id);
        $form = $this->createForm(NvUserType::class);
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {
            if($repoUser->findByEmail($form['email']->getData())){
            $other_user = $repoUser->findByEmail($form['email']->getData())[0];
            $new_todo = new TodoList();
            $new_todo = $todo[0];
            $new_todo->setId(intval($repoTodo->findMaxId()[0]) + 1);   
            $new_todo->setIsDone(false);         
            $new_todo->setIsAdmin(true);
           
          
            $other_user->addTodoList($new_todo);
            $em->persist($new_todo);
        
            $em->flush();
            
           
         
           

            return $this->render('todo_list/index.html.twig', [
                'form' => $form->createView(),
                'todos' =>  $this->getUser()->getTodoLists()
            ]);

            }
            else{
                return $this->render('todo_list/index.html.twig', [
                    'form' => $form->createView(),
                    'todos' =>  $this->getUser()->getTodoLists()
                ]);
            }
           
            
           
        }

        return $this->render('todo_list/nv.html.twig', [
            'form' => $form->createView()         
        ]);
    }


     /**
     * @Route("/admin/todo/", name="app.todo_admin")
     */
    public function adminTodo(UserRepository $repo): Response
    {
        $todos = [];
        foreach($repo->findAll() as $user){
            foreach($user->getTodoLists() as $todo){
                array_push($todos,$todo);
            }
           
        }
 
    
        return $this->render('admin/todo/menuAdminTodo.html.twig', [
            'controller_name' => 'TodoListController',
            'todos' =>  $todos
        ]);
    }


       /**
     * @Route("/admin/todo/delete/{id}", name="app.todo_delete_admin")
     */
    public function deleteadminTodo(EntityManagerInterface $em,TodoListRepository $repoTodo,$id,UserRepository $repo): Response
    {
        
        $todo = $repoTodo->findByIdField($id);
        foreach($todo[0]->getUsers() as $user){
            $todo[0]->removeUser($user);
        }

        $em->remove($todo[0]);
        $em->flush();

        $todose = [];
        foreach($repo->findAll() as $user){
            foreach($user->getTodoLists() as $todo){
                array_push($todose,$todo);
            }
           
        }
 

        return $this->render('admin/todo/menuAdminTodo.html.twig', [
            'controller_name' => 'TodoListController',
            'todos' =>  $todose
        ]);
    }


    
     /**
     * @Route("admin/todo/modify/{id}", name="app.todo_modify_admin")
     */
    public function modifyAdminTodo(Request $request,EntityManagerInterface $em,TodoListRepository $repoTodo,$id,UserRepository $repoUser): Response
    {
        $todo = $repoTodo->findByIdField($id);

        $form = $this->createForm(TodoListType::class, $todo[0]);

        $form->handleRequest($request); 
 

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $todo  = $form->getData();             
            $todo->setIsDone(false);
            $todo->setUser($this->getUser());
            $todo->setIsAdmin(true);
            $this->getUser()->addTodoList($todo);
            $em->persist($todo);
          
            $em->flush();
            
        $todose = [];
        foreach($repoUser->findAll() as $user){
            foreach($user->getTodoLists() as $todo){
                array_push($todose,$todo);
            }
           
        }
            
            return $this->render('admin/todo/menuAdminTodo.html.twig', [
                'form' => $form->createView(),
                'todos' => $todose
            ]);
        }

        return $this->render('todo_list/todo.html.twig', [
            'form' => $form->createView(),       
        ]);
    }







}
