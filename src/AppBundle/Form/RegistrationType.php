<?php
// src/AppBundle/Form/RegistrationType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Form\Extension\Core\Type\TextType as TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('imie',TextType::class,array('label' => 'Imię'))
                ->add('nazwisko')
                ->add('gender',ChoiceType::class, 
                    
                array('label' => 'Płeć',
                      'choices' => array(
                                         'Mężczyzna' => true,
                                         'Kobieta' => false,),
                
                    ))
                ->add('born',BirthdayType::class,array('widget' => 'choice', 'label' => 'Data urodzenia'
                    ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getImie()
    {
        return $this->getBlockPrefix();
    }
    
    public function getNazwisko()
    {
        return $this->getBlockPrefix();
    }
    
}