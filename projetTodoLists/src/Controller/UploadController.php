<?php 

namespace App\Controller;

use Exception;
use App\Entity\Images;
use App\Entity\Gallerie;
use App\Form\FileUploadType;

use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use App\Repository\ImagesRepository;
use App\Repository\GallerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadController extends AbstractController
{
 /**
   * @Route("/image", name="app_image")
   */
  public function gererimage(ImagesRepository $repo): Response
  {

      $user = $this->getUser();
      return $this->render('back/menuImage.html.twig', [
          'controller_name' => 'UploadController',
          'lesImages' => $repo->findAll()
      ]);
  }
  
  // ...
  /**
   * @Route("/image/televerser", name="app_televerser")
   */
  public function excelCommunesAction(Request $request,FileUploader $file_uploader,EntityManagerInterface $em,ImagesRepository $repo)
  {
    $form = $this->createForm(FileUploadType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) 
    {
      $file = $form['upload_file']->getData();
      if ($file) 
      {
        $file_name = $file_uploader->upload($file);
        if (null !== $file_name) // for example
        {
          $directory = $file_uploader->getTargetDirectory();
          $full_path = $directory.'/'.$file_name;
          $file_path_image = "/uploads/".$file_name;
          $file_info = new Images();
          $file_info->setLibelle($form->get('libelle')->getData());
          $file_info->setDatetime(new \Datetime('now'));
          $file_info->setSrc($file_path_image );
          $em->persist($file_info);
          $em->flush();

          $user = $this->getUser();
          return $this->render('back/menuImage.html.twig', [
           'controller_name' => 'UploadController',
           'lesImages' => $repo->findAll()
       ]);
          
        }
        else
        {
          // Oups, an error occured !!!
          throw new Exception("Type de fichier non accepté");
        }
      }
    }
    return $this->render('back/televerser.html.twig', [
      'form' => $form->createView(),
    ]);
  }
 // ...
    /**
   * @Route("/image/modifier/{id}", name="app_modifier")
   */
   public function modifierImage($id,Request $request,FileUploader $file_uploader,ImagesRepository $repo,EntityManagerInterface $em){
    $image = $repo->findOneBy(['id' => $id]);
    $form = $this->createForm(FileUploadType::class);
    $form->get('libelle')->setData($image->getLibelle());


    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) 
    {
      $file = $form['upload_file']->getData();
      if ($file) 
      {
        $file_name = $file_uploader->upload($file);
        if (null !== $file_name) // for example
        {
          $filesystem = new Filesystem();
          $path=$this->getParameter("public_directory").$image->getSrc();
          $filesystem->remove($path);
          $directory = $file_uploader->getTargetDirectory();
          $full_path = $directory.'/'.$file_name;
          $file_path_image = "/uploads/".$file_name;
          $file_info = $image;
          $file_info->setLibelle($form->get('libelle')->getData());
          $file_info->setDatetime(new \Datetime('now'));
          $file_info->setSrc($file_path_image );
          $em->persist($file_info);
          $em->flush();
          $user = $this->getUser();
          return $this->render('back/menuImage.html.twig', [
           'controller_name' => 'UploadController',
           'lesImages' => $repo->findAll()
       ]);
        }
        else
        {
          // Oups, an error occured !!!
          throw new Exception("Type de fichier non accepté");
        }
      }
    }
    return $this->render('back/televerser.html.twig', [
      'form' => $form->createView(),
    ]);

 }
    // ...
    /**
   * @Route("/image/supprimer/{id}", name="app_supprimer")
   */
    public function deleteImage($id,ImagesRepository $repo,EntityManagerInterface $em){
       $image = $repo->findOneBy(['id' => $id]);
       $filesystem = new Filesystem();
       $path=$this->getParameter("public_directory").$image->getsrc();
    
       $filesystem->remove($path);
    
       $em->remove($image);
       $em->flush();
       $user = $this->getUser();
       return $this->render('back/menuImage.html.twig', [
           'controller_name' => 'UploadController',
           'lesImages' => $repo->findAll()
       ]);

    }

     // ...
    /**
   * @Route("/afficher", name="app_afficher")
   */
  public function afficherTodoList(ImagesRepository $repo){
  
    $user = $this->getUser();
    return $this->render('back/afficher.html.twig', [
        'controller_name' => 'UploadController',
        'todos' =>  $repo->findAll()
     
    ]);

 }
  // ...
}
