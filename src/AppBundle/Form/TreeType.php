<?php
// src/AppBundle/Form/TreeType.php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Form\Extension\Core\Type\TextType as TextType;
use \Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class TreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options)
    {
        
        
        $builder->add('firstname',TextType::class,array('label' => 'Imię'))
                ->add('lastname',TextType::class,array('label' => 'Nazwisko'))
                ->add('familyname',TextType::class,array('label' => 'Nazwisko rodowe','required'=>false))
                ->add('gender',ChoiceType::class, 
                    
                array('label' => 'Płeć',
                      'choices' => array(
                                         'Mężczyzna' => true,
                                         'Kobieta' => false,),
                
                    ))
                ->add('born',BirthdayType::class,array('widget' => 'choice', 'label' => 'Data urodzenia'))                          
                ->add('died',BirthdayType::class,array('widget' => 'choice', 
                                                         'label' => 'Data śmierci',
                                                         
                                                         'required'    => false,                                                         
                                                         'empty_data'  =>  array('----')))
                
                 ->add('description', TextareaType::class, array( 'attr' => array('style' => 'height: 25'), 'required'=>false, 'label'=>'Opis'))                                                                                                                                                     
                                                  
                ;
    }
            
     
    

  
}