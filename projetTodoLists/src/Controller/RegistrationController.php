<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function index(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            // Set their role
            $user->setRoles(['ROLE_USER']);

            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/modifier/", name="app.modifier_profil")
     */
    public function modifierProfil(Request $request)
    {
        $user = $this->getUser() ;

       
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $new_user = new User();
            $new_user =$user;
            // Encode the new users password
            $new_user->setPassword($this->passwordEncoder->encodePassword($user, $form['password']->getData()));
            $new_user->setEmail($form['email']->getData());



            // Save
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->persist($new_user);
            $em->flush();

            return $this->render('home/home.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->render('profil/index.html.twig', [
            'form' => $form->createView()
        ]);
        
    }


    /**
     * @Route("/admin/user/", name="app.user_admin")
     */
    public function adminUser(UserRepository $rep)
    {
        return $this->render('admin/user/menuUser.html.twig', [
            'controller_name' => 'RegistrationController',
            'users' =>  $rep->findAll()
        ]);
    }
     /**
     * @Route("/admin/user/modifier/{id}", name="app.modifier_profil_admin")
     */
    public function modifierUserProfil(Request $request,UserRepository $rp,$id)
    {
        $user = $rp->findOneBy(['id' => $id]);

       
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $new_user = new User();
            $new_user =$user;
            // Encode the new users password
            $new_user->setPassword($this->passwordEncoder->encodePassword($user, $form['password']->getData()));
            $new_user->setEmail($form['email']->getData());



            // Save
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->persist($new_user);
            $em->flush();

            return $this->render('admin/user/menuUser.html.twig', [
                'form' => $form->createView(),
                'users' => $rp->findAll()
            ]);
        }
        return $this->render('profil/index.html.twig', [
            'form' => $form->createView()
        ]);
        
    }
}
