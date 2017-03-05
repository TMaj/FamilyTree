<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle;
use AppBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
 * Description of CreatePerson
 *
 * @author Zbigniew
 */
class CreatePerson extends Controller{
    
    public function createFirstPerson(){
      
        $usr= $this->getUser();
    //  $usr=   $this->get('security.token_storage')->getToken()->getUser();                  
              $person = new Person();
              $person->setFirstname( $usr->getImie());
              $person->setLastname($usr->getNazwisko());
              $person->setGender($usr->getGender());
              $person->setBorn($usr->getBorn());
              $em = $this->getDoctrine()->getManager();
              $em->persist($person);
              $em->flush(); 
        
        
        
    }
}
