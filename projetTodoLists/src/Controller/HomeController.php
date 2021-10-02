<?php

namespace App\Controller;

use App\Repository\ImagesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ImagesRepository $repo): Response
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'lesImages' =>$repo->findAll()
        ]);
    }
}
