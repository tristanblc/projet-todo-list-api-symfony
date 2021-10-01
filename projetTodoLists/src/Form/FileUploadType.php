<?php
// src/Form/FileUploadType.php
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FileUploadType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder
        ->add('libelle', TextType::class)
        ->add('upload_file', FileType::class, [
          'label' => false,
          'mapped' => false, // Tell that there is no Entity to link
          'required' => true,
          'constraints' => [
            new File([ 
              'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
                "image/png",
                "image/jpg",
                "image/jpeg",
                "image/gif"
              ],
              'mimeTypesMessage' => "Mauvais type de fichier.",
            ])
          ],
        ])
        ->add('send', SubmitType::class); // We could have added it in the view, as stated in the framework recommendations
  }
}