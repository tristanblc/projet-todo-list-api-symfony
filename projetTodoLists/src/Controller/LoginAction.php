<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginAction extends AbstractController{


    public function __invoke(Request $request)
    {
        $user = $this->getUser();
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return new JsonResponse(["error" => "invalid login request"],Response::HTTP_BAD_REQUEST);
        }
        $user = $this->getUser();
      
       
        return $user;
    }


}