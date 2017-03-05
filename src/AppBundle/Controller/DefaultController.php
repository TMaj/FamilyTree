<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Entity\UserPerson;
use AppBundle\Entity\Children;
use AppBundle\Entity\Marriage;
use AppBundle\Entity\Siblings;
use AppBundle\Entity\Choice;
use AppBundle\Form\TreeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Twig_Extension_StringLoader;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        
    $authChecker = $this->container->get('security.authorization_checker');
    $router = $this->container->get('router');
    
    

    if ($authChecker->isGranted('ROLE_USER')) {
        return new RedirectResponse($router->generate('fos_user_profile_show'), 307);
    } 
          
            

        
        
            return $this->render('default/index.html.twig', array( ));
       
    }
    
     /**
     * @Route("/faq", name="faq")
     */
    public function faqAction(Request $request)
    {
        
            return $this->render('default/faq.html.twig', array( ));
       
    }
    
    /**
     * @Route("/home", name="user_home")
     */
    public function homepageAction(Request $request)
    { 
        
       
       
            return $this->render('default/user_home.html.twig', array( ));
       
    }
    
    public $labelarr;
    /**
     * @var int
     */
    public $id;
    
    public $chosenperson;
    
     /**
     * @Route("/tree", name="tree")
     */
    public function treeAction(Request $request)
    {     
        $usr=$this->get('security.token_storage')->getToken()->getUser();           
        //patrzy czy w bazie jest jakikolwiek rekord person dla usera jak nie to wrzuca
        $em = $this->getDoctrine()->getManager(); // ...or getEntityManager() prior to Symfony 2.1
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT person_id_id FROM user_person WHERE user_id_id = :id");
        $statement->bindValue('id', $usr->getId());
        $statement->execute();
       
       
       if ($statement->fetch()==NULL){
             $this->createFirstPerson();
       }
        
   
        // $options=array();
         
         $tablica=$this->personQuery();
         
         $entitytablica=$this->getEntityArrFromId($tablica);
         
        
         $this->labelarr=$labeltablica = $this->getPersonNameTable($tablica);
         
        
       
       $person= new Person();
      
       $form = $this-> createForm(TreeType::class,$person);
       $form->add('parent', ChoiceType::class,array('choices'=>$entitytablica,
                                                    'choice_label'=> function ($value){
        
            //return $this->labelarr[2];
            return $value->getFirstName()." ".$value->getLastName();      },
                                                     'label'=>"Rodzic",
                                                    'required'    => false,
                                                    'placeholder' => 'Choose your parent',
                                                    'empty_data'  => null
            
//            ->add('children', ChoiceType::class,array('choices'=>$entitytablica,
//                                                    'choice_label'=> function ($value){
//        
//    
//            return $value->getFirstName()." ".$value->getLastName();      },
//                                                    'label'=>"Dzieci",
//                                                    'required'    => false,
//                                                    'placeholder' => 'Choose your children',
//                                                    'empty_data'  => null,
//                                                    'multiple'=> true,
//                                                    'expanded'=>false
                ))  ;
      
       // $data = $form->getData();
      //  $childrenarray=array();
      //  $childrenarray=$data->getChildren();
      //  $data->setChildren('1');
      //  $form->setData($data);
      //      
        $form->handleRequest($request);

      
         
    if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
        //$person = $form->getData();

        // ... perform some action, such as saving the task to the database
        // for example, if Task is a Doctrine entity, save it!
       
       // $children=new Children();
      //  $children->setParentId($person);
        
        
      
      
        
        $userperson= new UserPerson();
              
              $userperson->setUserId($usr);
              $userperson->setPersonId($person);
        
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($person);
        $em->persist($userperson);
        $em->flush();

        return $this->redirectToRoute('tree');
    }
       
       
       
            return $this->render('default/tree.html.twig', array( 'form'=>$form->createView()));
       
    }
    
    public function displayInfo($info){
        
         
        $this->render('default/display_info.html.twig', array('info'=>$info,));
         
        sleep(2);
        
     return   $this->redirectToRoute('treechoice');
        
    }
    /**
     * @Route("/test/",name="test")
     * 
     */
    public function testAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $user = $em->find('AppBundle:Person', 74);
        $em->remove($user);
        $em->flush();
      return  $this->render('default/display_info.html.twig', array('info'=>'Informacja'));
    }
    
    /**
     * @Route("/editperson/{parameter}", name="editperson")
     * 
     */
    public function editpersonAction(Request $request, $parameter){
       
        $informacja="";
        $person1=new Person();
        $person1->setFirstname(" ");
        $person1->setLastname(" ");
        $osoba = $request->getUri();
      //  var_dump($osoba);
        $spot=strrpos($osoba,'?');
        $osoba=substr($osoba,$spot+3); 
        $spot2=strrpos($osoba,'&1');
        $wybor=substr($osoba,0,$spot2);
        $spot3=strrpos($osoba,'1=');
        $numer= intval(substr($osoba,$spot3+2));
    
        
        //var_dump($wybor);
       
       
        if($wybor=="malzonek"){
           $title="Uwaga: nie można dodać więcej jednego małżonka dla tej osoby. Proszę usunąć, lub edytować:";
           $idarray=$this->marriageQuery($numer);
           $marriagearray=$this->getEntityArrFromId($idarray);
           $person1=$marriagearray[0];
           
           $form = $this-> createForm(TreeType::class,$person1,array('allow_extra_fields' => true)) 
                ->add('remove', SubmitType::class, array('label' => 'Usuń'))
                ->add('edit', SubmitType::class, array('label' => 'Edytuj'));
              
        $form->handleRequest($request);  
           
           
       }else{
           $title="Uwaga: nie można dodać więcej niż dwoje rodziców dla tej osoby. Proszę wybrać, którą osobę usunąć:";
           $idarray=$this->parentQuery($numer);
           $parentarray=$this->getEntityArrFromId($idarray);
           
           
        $form = $this-> createForm(TreeType::class,$person1)
                  ->remove('firstname')->remove('lastname')->remove('born')->remove('gender')->remove('died')->remove('description')
                  ->add('button1', SubmitType::class, array('label' => $parentarray[0]->getFirstname()))                                   
                  ->add('button2', SubmitType::class, array('label' => $parentarray[1]->getFirstname()));
                 
                //  ->add('remove', SubmitType::class, array('label' => 'Usuń', ))
                 // ->add('edit', SubmitType::class, array('label' => 'Edytuj', )); 
        $form->handleRequest($request);  
        
       }
       if ((($form->isSubmitted()))){
        
       if ($wybor=="malzonek"){
           if($form->get('remove')->isClicked()){
               
            $person1 = $this->getDoctrine()->getRepository('AppBundle:Person')->find($idarray[0]);
            
            $this->removePerson($person1);
                  
            
            $this->addFlash('notice','Pomyślnie usunięto małżonka!');
            $parameter=array("malzonek"." ".$numer);
            return $this->redirectToRoute('addperson',$parameter);
        
           } 
           if ($form->get('edit')->isClicked()){
                 $em = $this->getDoctrine()->getManager();    
                 $em->persist($person1);
                 $em->flush();
                 $this->addFlash('notice','Pomyślnie edytowano małżonka!');
                 return $this->redirectToRoute('treechoice');
                 
           }
        
           
           
       }
       
       else{
           
        
           
       if($form->get('button1')->isClicked()){
            $person1 = $this->getDoctrine()->getRepository('AppBundle:Person')->find($idarray[0]);
            $this->removePerson($person1);
            
              $this->addFlash('notice','Pomyślnie usunięto rodzica!');
            $parameter=array("rodzic"." ".$numer);
            return $this->redirectToRoute('addperson',$parameter);
        }  
       if($form->get('button2')->isClicked()){
            $person1 = $this->getDoctrine()->getRepository('AppBundle:Person')->find($idarray[1]); 
            $this->removePerson($person1); 
            
              $this->addFlash('notice','Pomyślnie usunięto rodzica!');
            $parameter=array("rodzic"." ".$numer);
            return $this->redirectToRoute('addperson',$parameter);
        }                                       
              $form = $this-> createForm(TreeType::class,$person1)
                  
                  ->add('button1', SubmitType::class, array('label' => $parentarray[0]->getFirstname()))                                   
                  ->add('button2', SubmitType::class, array('label' => $parentarray[1]->getFirstname()))
                  ->add('remove', SubmitType::class, array('label' => 'Usuń'))
                  ->add('edit', SubmitType::class, array('label' => 'Edytuj'));           
              
        
        
        
       
          
           
        if($form->get('remove')->isClicked()){
        
        //   if($person1->getFirstname()==" "){ 
        //       $this->addFlash('notice','Prosze wybrac osobę!'); 
         //  }else{
                
            $em=$this->getDoctrine()->getEntityManager();           
            $this->removePerson($person1);  
             
            //var_dump($person1->getFirstname());
            $this->addFlash('notice','Pomyślnie usunięto rodzica!');
            $parameter=array("rodzic"." ".$numer);
            return $this->redirectToRoute('addperson',$parameter);
      // } 
       }
       
        if($form->get('edit')->isClicked()){
         
             if($person1->getFirstname()==" "){ 
             $this->addFlash('notice','Prosze wybrac osobę!'); 
             
             }else{
                 $em = $this->getDoctrine()->getManager();    
                 $em->persist($person1);
                 $em->flush();
                 $this->addFlash('notice','Pomyślnie edytowano rodzica!');
             return $this->redirectToRoute('treechoice');
             
             }
        }
          
        
        

       }
          
          
       
       }    
       
       if ($form->isValid()){
           
          
       
       
           
       }
       
       if($wybor="malzonek"){
                      return $this->render('default/edit_marriage.html.twig', array('form' => $form->createView(),'info'=>$informacja,'title'=>$title,));

       }else{
                      return $this->render('default/edit_parents.html.twig', array('form' => $form->createView(),'info'=>$informacja,'title'=>$title,));

       }
                                             
    }
    
    /**
     * @Route("/addperson/{parameter}", name="addperson")
     * 
     */
    public function addpersonAction(Request $request, $parameter){
       
        $person1=new Person();
        $form=NULL;
         unset($person1);
         unset($form);
         
         $informacja="";
         
         $osoba = $request->getUri();
         $spot=strrpos($osoba,'=');
         $osoba=substr($osoba,$spot);
        
        $spot2=strrpos($osoba,'20');
        $numer= intval(substr($osoba,$spot2+2));
        
        
        $znacznik=strpos($osoba,'%');
        $wybor=substr($osoba,1,$znacznik-1);
        
         $elegant_wybor="";
        $person = $this->getDoctrine()->getRepository('AppBundle:Person')->find($numer);
        if ($wybor== "malzonek"){
        $elegant_wybor="małżonek";
        }else if ($wybor== "rodzenstwo"){
        $elegant_wybor="rodzeństwo";
        }else if ($wybor== "rodzic"){
        $elegant_wybor="rodzic";
        }else if ($wybor== "dziecko"){
        $elegant_wybor="dziecko";
        }else if ($wybor== "edit"){
        $elegant_wybor="edycja";
        }
        
        $title=$person->getFirstname()." ".$person->getLastname().": dodawanie w  kategorii: ".$elegant_wybor;
        
       if($wybor == "remove" ){
             $this->removePerson($person);
             return $this->redirectToRoute('treechoice');
         } 
        
        if($wybor == "edit" ){
         $person1=$person;
         $form = $this-> createForm(TreeType::class,$person1)
                  ->add('button', SubmitType::class, array('label' => 'Edytuj'));
    }else{
         $person1=new Person();
         $form = $this-> createForm(TreeType::class,$person1)
                  ->add('button', SubmitType::class, array('label' => 'Dodaj'));
    }
        if($wybor == "malzonek" ){
        if (($this->checkIfHasSpouse($person))){
                      $errorcode=2;
                      $informacja="Nie można dodać więcej niż jednego małżonka";
                      $parameter=array('malzonek',$person->getId());
                      return $this->redirectToRoute('editperson', $parameter);
                  }
         }
        if($wybor == "rodzic" )   {   
        if (($this->checkIfHasTwoParents($person))){
                       $errorcode=1;
                       $informacja="Nie mozna dodac wiecej niz 2 rodzicow";
                       $parameter=array('rodzic',$person->getId());
                       return $this->redirectToRoute('editperson', $parameter);
        }     }  
       
       //  var_dump($wybor);
       $form->handleRequest($request);                                  
       if ((($form->isSubmitted()) && ($form->isValid()))) {
       
        
           
         if($wybor == "edit" ){
             $this->updatePerson($person, $person1);
             return $this->redirectToRoute('treechoice');
         }            
         if($wybor == "rodzic" ){
           
//                $em = $this->getDoctrine()->getManager();
//                $qb=$em->createQueryBuilder();
//                $qb     ->select('m')
//                        ->from('AppBundle:Children', 'm')
//                        ->where('m.childId = :id')                       
//                        ->setParameter('id',$numer);
//                
//                $total = $qb->getQuery()->getScalarResult();
//                    var_dump($total);
//                     var_dump(count($total));
//                 if ((count($total)<=1)){
//                     $children=new Children();
//                    $children->setChildId($person);
//                    $children->setParentId($person1);
//                     
//                  $usr=$this->get('security.token_storage')->getToken()->getUser();         
//                 
//                
//                  
//                  $em->persist($children);
//                  
//                  $em->persist($person1);
//                  $em->flush();    
             if (!($this->addParent($person, $person1))){
             $informacja="Dodano osobę";
                 }else{
                       $errorcode=1;
                       $informacja="Nie mozna dodac wiecej niz 2 rodzicow";
                       $parameter=array('rodzic',$person->getId());
                       return $this->redirectToRoute('editperson', $parameter);
                 }
       
       }
         if($wybor == "dziecko" ){
//           
//                 $children=new Children();
//                 $children->setChildId($person1);
//                 $children->setParentId($person);
//               
//                  $em = $this->getDoctrine()->getManager();
//                  $em->persist($children);
//                  $em->persist($person1);
//                  $em->flush();    
             $this->addChild($person, $person1);
                  $informacja="Dodano osobe";
       }
         if($wybor == "rodzenstwo" ){
           
//                 $siblings=new Siblings();
//                 $siblings->setPerson1Id($person1);
//                 $siblings->setPerson2Id($person);
//                 
//                 
//                  $em = $this->getDoctrine()->getManager();
//                  $em->persist($siblings);
//                  $em->persist($person1);
//                  $em->flush();  
             $this->addSibling($person, $person1);
                  
                  $informacja="Pomyślnie dodano osobę";
       }
         if($wybor == "malzonek" ){
           
//                $em = $this->getDoctrine()->getManager();
//                $qb=$em->createQueryBuilder();
//                $qb     ->select('m')
//                        ->from('AppBundle:Marriage', 'm')
//                        ->where('m.person1Id = :id')
//                        ->orWhere('m.person2Id = :id')
//                        ->setParameter('id',$numer);
//                        
//                // $query=$qb->getQuery();
//                 $total = $qb->getQuery()->getScalarResult();
//                    var_dump($total);
//                     var_dump(count($total));
//                 if ((count($total)<=1)){
//                                      
//                 $marriage=new Marriage();
//                 $marriage->setPerson1Id($person1);
//                 $marriage->setPerson2Id($person);
//                 
//                  
//                  
//                  $em->persist($marriage);
//                  $em->persist($person1);
//                  $em->flush();    
//                  $informacja="Dodano osobe";
             if (!($this->addSpouse($person, $person1))){
                  $informacja="Dodano osobę";
                  } else{
                      $errorcode=2;
                      $informacja="Nie można dodać więcej niż jednego małżonka";
                      $parameter=array('malzonek',$person->getId());
                      return $this->redirectToRoute('editperson', $parameter);
                  }
       }     
       
//                  $usr=$this->get('security.token_storage')->getToken()->getUser();   
//                  $userperson= new UserPerson();
//                  $userperson->setUserId($usr);  
//                  $userperson->setPersonId($person1);  
//                  $em->persist($userperson);
//                  $em-flush();
                  
           unset($person1);
           unset($form);
         
        
           
         $person1=new Person();      
         $form = $this-> createForm(TreeType::class,$person1)
                  ->add('button', SubmitType::class, array('label' => 'Dodaj'));
         
           $zmienna=$this->printChildren($person);
           
         
           return $this->render('default/add_person.html.twig', array('form' => $form->createView(),'info'=>$informacja,'title'=>$title,'zmienna'=>$zmienna));
       }                                 
                                             
         $informacja="";  
         
        
        $zmienna=$this->printChildren($person);
       $this->getChildren($person);
       
       
          
       
        return $this->render('default/add_person.html.twig', array('form' => $form->createView(),'info'=>$informacja,'title'=>$title,'zmienna'=>$zmienna));
    }
    
     /**
     * @Route("/treechoice_{parameter}", name="treechoice",  defaults={"parameter": "0"},)
     */
    public function treechoiceAction(Request $request,$parameter){
        
        
         $usr=$this->get('security.token_storage')->getToken()->getUser();           
        //patrzy czy w bazie jest jakikolwiek rekord person dla usera jak nie to wrzuca
        $em = $this->getDoctrine()->getManager(); // ...or getEntityManager() prior to Symfony 2.1
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT person_id_id FROM user_person WHERE user_id_id = :id");
        $statement->bindValue('id', $usr->getId());
        $statement->execute();
       
       
       if ($statement->fetch()==NULL){
             $this->createFirstPerson();
       }
       
        $this->chosenperson=new Person();
        $choice= new Choice();
        $tablica=$this->personQuery();         
        $entitytablica=$this->getEntityArrFromId($tablica);
       

   $form = $this->createFormBuilder($choice)
            ->add('choice', ChoiceType::class,array(
                                            
                                            'choices'=>$entitytablica,
                                            'choice_label'=> function ($value){
                                             return $value->getFirstName()." ".$value->getLastName();      },
                                            'label'=>false,))
            ->add('rodzic', SubmitType::class, array('label' => 'Dodaj rodzica'))
            ->add('dziecko', SubmitType::class, array('label' => 'Dodaj dziecko'))
            ->add('malzonek', SubmitType::class, array('label' => 'Dodaj małżonka'))
            ->add('rodzenstwo', SubmitType::class, array('label' => 'Dodaj rodzeństwo'))
            ->add('edytuj', SubmitType::class, array('label' => 'Edytuj'))
            ->add('usun', SubmitType::class, array('label' => 'Usuń'))
            ->getForm();
  
       $form->handleRequest($request);                                  
       if ((($form->isSubmitted()) && ($form->isValid()))) {
      
           
       
       if ($choice->getChoice()==NULL){
             return $this->redirectToRoute('homepage');
       }
       $osoba=new Person();
       $osoba=$choice->getChoice();
       $this->id=22;
      //  $this->chosenperson=$entitytablica;
           
       if($form->get('rodzic')->isClicked()){
              $nextAction ='rodzic';
        }  
       if($form->get('dziecko')->isClicked()){
              $nextAction ='dziecko';
        }  
         if($form->get('malzonek')->isClicked()){
              $nextAction ='malzonek';
        }  
         if($form->get('rodzenstwo')->isClicked()){
              $nextAction ='rodzenstwo';
        } 
        if($form->get('edytuj')->isClicked()){
              $nextAction ='edit';
        }
        if($form->get('usun')->isClicked()){
              $nextAction ='remove';
        }
        
        
        
        $parameter=array($nextAction." ".$osoba->getId());
       // $parameter[0]=$nextAction;
      //  $parameter[1]=$choice->getChoice();
        
        $request->attributes->add(array('osoba'=>$osoba->getId()));
            
          // return $this->redirectToRoute('test', array('name'=>'Kamilek'));
          return $this->redirectToRoute('addperson', $parameter);
        
      
       }                                 
                                             
        $title="Wybierz akcję";                             
        $zmienna="<a> Tekst </br> testowy </a>";

          $roots=array('person'=>$this->getRoots(),'trees'=>array());
          $drzewa=array();
          $tablica=array('person'=>array(), 'trees'=>array());
          foreach($roots['person'] as $chunk){
              array_push($roots['trees'],$this->printChildren($chunk));
             
          } 
          foreach($roots['person'] as $chunk){
               array_push($tablica['person'],$chunk->getFirstname()." ".$chunk->getLastname());
          }
          foreach($roots['trees'] as $chunk){
               array_push($tablica['trees'],$chunk);
          }
          
        
          $zmienna=$tablica['trees'][$parameter];
          
        return $this->render('default/tree_choice.html.twig', array('form' => $form->createView(),'info'=>'','title'=>$title,'zmienna'=>$zmienna,
            'tablica'=>$tablica ));
    }
    
     /**
     * @Route("/showperson/{parameter}", name="showperson")
     */      
    public function showPersonAction($parameter){
        
         $title="";   
        $zmienna="<a> Tekst </br> testowy </a>";
       
        $person = $this->getDoctrine()->getRepository('AppBundle:Person')->find($parameter);
        $siblingsarray=$this->getSiblingsArray($person);
        $parentsarray=$this->getParentsArray($person);
        $childrenarray=$this->getChildrenArray($person);
        
       // $test=$this->getSiblingsArray2($person);
//        if ($siblingsarray != NULL){
//        foreach($siblingsarray['name'] as $chunk){
//            var_dump($chunk);
//        }
//        foreach($siblingsarray['id'] as $chunk){
//              var_dump($chunk);
//        }
//        }
        
      // var_dump($parentsarray);
       // var_dump(count($childrenarray));
        //$this->removeAllPersons();
        
       
        
        
        if($person->getDied()!=NULL){
            $died=$person->getDied()->format('Y-m-d');
        }else{
            $died=NULL;
        }
        
        
          return $this->render('default/display_info.html.twig', array('ID'=>$parameter,'imie'=>$person->getFirstname(), 'nazwisko'=>$person->getLastname(),'born'=>$person->getBorn()->format('Y-m-d'),
              'died'=>$died, 'description'=>$person-> getDescription(), 'siblings_names'=>$siblingsarray['name'], 'siblings_ids'=>$siblingsarray['id'], 'parents'=>$parentsarray,
    'children'=>$childrenarray, 'familyname'=>$person->getFamilyname()));
    }
    
     /**
     * @Route("/other", name="other")
     */
    public function otherAction(){
        
        
        
        return $this->render('default/other.html.twig', array());
    }
    
     /**
     * @Route("/remove", name="remove")
     */
    public function removeAction(){
        
        $this->removeAllPersons();
        
        return $this->redirectToRoute('treechoice');
    }
    
     /**
     * @Route("/removeuser", name="removeuser")
     */
    public function removeUserAction(){
         $userManager = $this->get('fos_user.user_manager');
         $user=   $this->get('security.token_storage')->getToken()->getUser();

        $this->removeAllPersons();
        
        
        $em = $this->getDoctrine()->getManager();
               
                $q1=$em->createQuery('DELETE AppBundle:UserPerson u WHERE u.userId = ?1');
                $q1->setParameter(1,$user->getId());  
                $q1->getResult(); 
        
        
        $userManager->deleteUser($user);
        
        return $this->redirectToRoute('homepage');
    }
    
    
  
     public function createFirstPerson(){
        
      $usr=   $this->get('security.token_storage')->getToken()->getUser();                  
              $person = new Person();
              $userperson= new UserPerson();
              
              $userperson->setUserId($usr);
              $userperson->setPersonId($person);
              
              $person->setFirstname( $usr->getImie());
              $person->setLastname($usr->getNazwisko());
              $person->setGender($usr->getGender());
              $person->setBorn($usr->getBorn());
              $em = $this->getDoctrine()->getManager();
              $em->persist($person);
              $em->persist($userperson);
              $em->flush(); 
        
        
        
    }
    
     public function personQuery(){
       
        $usr=$this->get('security.token_storage')->getToken()->getUser();     
        $usrid= $usr->getId();
         $em = $this->getDoctrine()->getManager();    

      //  $em = $this->getDoctrine()->getManager(); // ...or getEntityManager() prior to Symfony 2.1
        $connection = $em->getConnection();
        $statement = $connection->prepare('SELECT person.id FROM user_person ' .
       'INNER JOIN person  ON user_person.person_id_id = person.id WHERE user_person.user_id_id = :id ORDER BY person.firstname ASC');
        $statement->bindValue('id', $usr->getId());
        $statement->execute();
        $arr= $statement->fetchAll();
        $target=array();
   foreach( $arr as $chunk){
       
       array_push($target, intval( $chunk['id']));
       
   }
  // var_dump($target);
   return $target;
   
    //$qb = $em->createQueryBuilder();
    //$qb->select('u')
      //          ->from('AppBundle:UserPerson', 'u')
        //        ->where('u.id = :identifier')
          //      ->setParameter('identifier',  $usrid);
    
   //  $query = $qb->getQuery();
    
//     $query = $em->createQuery("SELECT person.id FROM person , user_person "
//           . "WHERE user_person.person_id_id = $usrid "
//           . "AND user_person.person_id_id=person.id");
//    $tests = $query->getArrayResult();
//    $array= $query->getArrayResult();
//   
//        
//        $rsm = new ResultSetMapping;
//$rsm->addEntityResult('UserPerson', 'up');
//$rsm->addFieldResult('up', 'person_id_id', 'personId');
////$rsm->addFieldResult('p', 'name', 'name');
//$rsm->addJoinedEntityResult('Person' , 'p', 'up', 'id');
//$rsm->addFieldResult('p', 'person_id', 'id');
////$rsm->addFieldResult('a', 'street', 'street');
////$rsm->addFieldResult('a', 'city', 'city');
//
//$sql = 'SELECT u.id, u.name, a.id AS address_id, a.street, a.city FROM user_person up ' .
//       'INNER JOIN person p ON up.person_id_id = p.id WHERE up.user_id_id = ?';
//$query = $this->_em->createNativeQuery($sql, $rsm);
//$query->setParameter(1, '$usrid');
//
//$users = $query->getArrayResult();
//return $users;
   
}
   
     public function getPersonNameTable($idarr){
         
         $labeltarget=array(); 
          
         $em = $this->getDoctrine()->getManager();    
     
        $conn1 = $em->getConnection();
       
      foreach( $idarr as $chunk){
       
       $statement = $conn1->prepare('SELECT person.firstname, person.lastname FROM user_person ' .
       'INNER JOIN person  ON user_person.person_id_id = person.id WHERE person.id = :id');
    
     $statement->bindValue('id', $chunk);
     $statement->execute();
     $name= $statement->fetch();
     
        
       array_push($labeltarget,$name['firstname']." ".$name['lastname']);
       
   }
 
   return $labeltarget;
         
         
         
     }
     
     public function getEntityArrFromId($tablica){
         
         $targetarray=array();
         
         foreach($tablica as $chunk){
         $person = $this->getDoctrine()
        ->getRepository('AppBundle:Person')
        ->find($chunk);
          array_push($targetarray,$person);
         }
         return $targetarray;
     }
     
     public function parentQuery($childId){
       
       
        $em = $this->getDoctrine()->getManager();    
        $connection = $em->getConnection();
        $statement = $connection->prepare('SELECT parent_id_id FROM children '.
       'WHERE child_id_id = :id');
        $statement->bindValue('id', $childId);
        $statement->execute();
        $arr= $statement->fetchAll();
        $target=array();
   foreach( $arr as $chunk){
       
       array_push($target, intval( $chunk["parent_id_id"]));
       
   }
    return $target;
}
 
     public function marriageQuery($childId){
       
       
        $em = $this->getDoctrine()->getManager();    
        $connection = $em->getConnection();
        $statement = $connection->prepare('SELECT person1id_id FROM marriage '.
       'WHERE person2id_id = :id');
        $statement->bindValue('id', $childId);
        $statement->execute();
        $arr= $statement->fetchAll();
        $target=array();
   foreach( $arr as $chunk){
       
       array_push($target, intval( $chunk["person1id_id"]));
       
   }
   $statement = $connection->prepare('SELECT person2id_id FROM marriage '.
       'WHERE person1id_id = :id');
        $statement->bindValue('id', $childId);
        $statement->execute();
        $arr= $statement->fetchAll();
        
   foreach( $arr as $chunk){
       
       array_push($target, intval( $chunk["person2id_id"]));
       
   }
   
   
    return $target;
}
 
     public function addParent($child, $parent){
         
                $em = $this->getDoctrine()->getManager();
                //LICZENIE ILOSCI RODZICOW
                $qb=$em->createQueryBuilder();
                $qb     ->select('m')
                        ->from('AppBundle:Children', 'm')
                        ->where('m.childId = :id')                       
                        ->setParameter('id',$child->getId());                
                $total = $qb->getQuery()->getScalarResult();
               
                  
                 
                    if ((count($total)<=1)){
                        
                        if(count($total)==1){
                            
                            $query=$em->createQuery('SELECT m FROM AppBundle:Children m WHERE m.childId = ?1');
                           
                            $query->setParameter(1,$child->getId());              
                            $existingparent = $query -> getResult();
                           // $dane = $existingparent->getParentId();
                           // var_dump($dane);
                            foreach($existingparent as $chunk ){
                  
                            $mrg=new Marriage();

                                $mrg->setPerson1Id($chunk->getParentId());
                                $mrg->setPerson2Id($parent);
                                
                            $em->persist($mrg);
                    
                                 }
                            
                        }
                        
                        
                        
                        $q = $em->createQuery('select u from AppBundle:Siblings u WHERE u.person2Id = ?1 OR u.person2Id = ?1 ');
                        $q->setParameter(1,$child->getId());
                        $siblings=$q->getResult();
               // var_dump($siblings[0]->getPerson1Id()->getId());
                foreach($siblings as $chunk ){
                   $chld=new Children();
                    if($chunk->getPerson1Id()->getId()==$child->getId()){
                       
                        $chld->setChildId($chunk->getPerson2Id());
                        $chld->setParentId($parent);
                    }else{
                        
                         $chld->setChildId($chunk->getPerson1Id());
                         $chld->setParentId($parent);
                    }
                    $em->persist($chld);
                    
                }
                        
                        
                        
                    $children=new Children();
                    $children->setChildId($child);
                    $children->setParentId($parent);
                     
                    $user=$this->get('security.token_storage')->getToken()->getUser();         
                 
                    $userperson=new UserPerson();
                    $userperson->setPersonId($parent);
                    $userperson->setUserId($user);
                
                  $em->persist($userperson);
                  $em->persist($children);                  
                  $em->persist($parent);
                  $em->flush();    
                  
                  return 0;
                 }else{
                     return 1;
                 }
         
     }
     
     public function addSpouse($oldSpouse,$newSpouse){
         
          $em = $this->getDoctrine()->getManager();
                //LICZENIE ILOSCI MALZONKOW
                $qb=$em->createQueryBuilder();
                $qb     ->select('m')
                        ->from('AppBundle:Marriage', 'm')
                        ->where('m.person1Id = :id') 
                        ->orWhere('m.person2Id = :id')
                        ->setParameter('id',$oldSpouse->getId());                
                $total = $qb->getQuery()->getScalarResult();
                
                if ((count($total)<=0)){
                        
                        
                        $q = $em->createQuery('select u from AppBundle:Children u WHERE u.parentId = ?1');
                        $q->setParameter(1,$oldSpouse->getId());
                        $children=$q->getResult();
               // var_dump($children[0]->getPerson1Id()->getId());
                
                foreach($children as $chunk ){
                  
                    $chld=new Children();
                
                        $chld->setChildId($chunk->getChildId());
                        $chld->setParentId($newSpouse);
                    
                    $em->persist($chld);
                    
                }
                
                
                
                
                    $marriage=new Marriage();
                    $marriage->setPerson1Id($oldSpouse);
                    $marriage->setPerson2Id($newSpouse);
                     
                    $user=$this->get('security.token_storage')->getToken()->getUser();         
                 
                    $userperson=new UserPerson();
                    $userperson->setPersonId($newSpouse);
                    $userperson->setUserId($user);
                
                  $em->persist($userperson);       
                  $em->persist($marriage);
                  $em->persist($newSpouse);
                  $em->flush();    
                  
                  return 0;
                 }else{
                     
                  return 1;
                     
                 } 
         
     }
     
     public function checkIfHasTwoParents($person){
         
          $em = $this->getDoctrine()->getManager();
                //LICZENIE ILOSCI RODZICOW
                $qb=$em->createQueryBuilder();
                $qb     ->select('m')
                        ->from('AppBundle:Children', 'm')
                        ->where('m.childId = :id')                       
                        ->setParameter('id',$person->getId());                
                $total = $qb->getQuery()->getScalarResult();
                
                if ((count($total)<=1)){
                    return 0;
                }else{
                    return 1;
                }
     }
     
     public function checkIfHasSpouse($person){
         
          $em = $this->getDoctrine()->getManager();
                //LICZENIE ILOSCI MALZONKOW
                $qb=$em->createQueryBuilder();
                $qb     ->select('m')
                        ->from('AppBundle:Marriage', 'm')
                        ->where('m.person1Id = :id') 
                        ->orWhere('m.person2Id = :id')
                        ->setParameter('id',$person->getId());                
                $total = $qb->getQuery()->getScalarResult();
                
                if ((count($total)<=0)){                    
                    return 0;
                }
                else {
                    return 1;
                }
         
     }
     
     public function addSibling($oldSibling, $newSibling){
          $em = $this->getDoctrine()->getManager();
                        $q = $em->createQuery('select u from AppBundle:Children u WHERE u.childId = ?1');
                        $q->setParameter(1,$oldSibling->getId());
                        $children=$q->getResult();
               
                
                foreach($children as $chunk ){
                  
                    $chld=new Children();
                
                        $chld->setChildId($newSibling);
                        $chld->setParentId($chunk->getParentId());
                        
                        
                        
                    
                    $em->persist($chld);
                    
                }
                
                
                
                
                    $siblings=new Siblings();
                    $siblings->setPerson1Id($oldSibling);
                    $siblings->setPerson2Id($newSibling);
                     
                    $user=$this->get('security.token_storage')->getToken()->getUser();         
                 
                    $userperson=new UserPerson();
                    $userperson->setPersonId($newSibling);
                    $userperson->setUserId($user);
                
                  $em->persist($userperson);       
                  $em->persist($siblings);
                  $em->persist($newSibling);
                  $em->flush();    
                  
                  return 0;
         
         
     }
     
     public function addChild($parent, $child){
         
          $em = $this->getDoctrine()->getManager();
          
          $q = $em->createQuery('select u from AppBundle:Marriage u WHERE u.person1Id = ?1 OR u.person2Id = ?1 ');
                        $q->setParameter(1,$parent->getId());
                        $children=$q->getResult();
               
                
                foreach($children as $chunk ){
                  
                    $chld=new Children();
                
                    if($chunk->getPerson1Id()->getId()==$parent->getId()){
                       
                        $chld->setChildId($child);
                        $chld->setParentId($chunk->getPerson2Id());
                    }else{
                        
                         $chld->setChildId($child);
                         $chld->setParentId($chunk->getPerson1Id());
                    }
                    
                    $em->persist($chld);
                    
                }
           
           $query= $em->createQuery('select u from AppBundle:Children u WHERE u.parentId = ?1'); 
           $query->setParameter(1,$parent->getId());
           $variable=$query->getResult();
           
            foreach($variable as $chunk ){
            
                $sibling=new Siblings();
                $sibling->setPerson1Id($child);
                $sibling->setPerson2Id($chunk->getChildId());
                $em->persist($sibling);
                
            }
                
                    $children=new Children();
                    $children->setParentId($parent);
                    $children->setChildId($child);
                     
                    $user=$this->get('security.token_storage')->getToken()->getUser();         
                 
                    $userperson=new UserPerson();
                    $userperson->setPersonId($child);
                    $userperson->setUserId($user);
                
                  $em->persist($userperson);       
                  $em->persist($children);
                  $em->persist($child);
                  $em->flush();    
                  
                  return 0;
         
         
     }
     
     public function removePerson($person){
         
         $em = $this->getDoctrine()->getManager();
               
                $q1=$em->createQuery('DELETE AppBundle:Children u WHERE u.parentId = ?1 OR u.childId = ?1');
                $q1->setParameter(1,$person->getId());  
                $q1->getResult(); 
                
                $q2=$em->createQuery('DELETE AppBundle:Marriage u WHERE u.person1Id = ?1 OR u.person2Id = ?1');
                $q2->setParameter(1,$person->getId());  
                $q2->getResult(); 
                
                $q3=$em->createQuery('DELETE AppBundle:Siblings u WHERE u.person1Id = ?1 OR u.person2Id = ?1');
                $q3->setParameter(1,$person->getId());  
                $q3->getResult(); 
                
                $q4=$em->createQuery('DELETE AppBundle:UserPerson u WHERE u.personId = ?1');
                $q4->setParameter(1,$person->getId());  
                $q4->getResult(); 
                
                $q5=$em->createQuery('DELETE AppBundle:Person u WHERE u.id = ?1');
                $q5->setParameter(1,$person->getId());  
                $q5->getResult();
         
     }
     
     public function updatePerson($person,$person1){
         
          $em = $this->getDoctrine()->getEntityManager();
          $qb = $em->createQueryBuilder();
          $qb       ->update('AppBundle:Person', 'u')
                    ->set('u.firstname','?2')
                    ->set('u.lastname', '?3')
                    ->set('u.gender','?4')
                    ->set('u.born','?5')
                    ->set('u.died','?6')
                    ->set('u.description','?7')
                    ->set('u.familyname','?8')
                    ->where('u.id = ?1')
                    ->setParameter(1, $person->getId())
                    ->setParameter(2, $person->getFirstname())
                    ->setParameter(3, $person->getLastname())
                    ->setParameter(4, $person->getGender())
                    ->setParameter(5, $person->getBorn())
                    ->setParameter(6, $person->getDied())
                    ->setParameter(7, $person->getDescription())
                    ->setParameter(8, $person->getFamilyname())
                    ->getQuery()
                    ->execute();
     }
     
     public function createTreeHtml(){}
     
     public function printChildren($parent){
        
         
        
         
         $string=""; 
         $string.=$string."<ul>";
         $string.="<li>";
        
         $spouse=$this->getSpouse($parent);
         //$path='{{ path(\'homepage\') }}';
        
         if($spouse!=NULL){ $path="%lol%/".$spouse->getId(); $string.=' <a href="'.$path.' ">'.$spouse->getFirstname()."</a> ";}
         $path="%lol%/".$parent->getId();
         $string.=' <a href="'. $path.'">'.$parent->getFirstname()." ".$parent->getLastname()."</a>";
        
         
        
         
         $childrenarray=$this->getChildren($parent);
         if ($childrenarray !=NULL){
             $string.="<ul>";
             foreach($childrenarray as $chunk){
               $string.="<li>";
            
               $string.=$this->printChildren($chunk);
               $string.="</li>";
                 
             }
             $string.="</ul>";
         }
         
         
         
         $string.="</li>";
         $string.="</ul>";
       
         
         return $string;
         
     }
         
     public function getSpouse($person){
         
         $em = $this->getDoctrine()->getManager();
          
          $q = $em->createQuery('select u from AppBundle:Marriage u WHERE u.person1Id = ?1 OR u.person2Id = ?1 ');
                        $q->setParameter(1,$person->getId());
                        $spouse=$q->getResult();
                        $total=$q->getScalarResult();
                        
                       
          foreach ($spouse as $chunk){
              if($chunk->getPerson2Id()->getFirstname()!=$person->getFirstname()){
                  return $chunk->getPerson2Id();
                  
              }
              else{
                   return $chunk->getPerson1Id();
              }
              
              
              
             
          } 
     }
     
     public function getChildren($person){
         
         $em = $this->getDoctrine()->getManager();
         $query = $em->createQuery('SELECT u FROM AppBundle:Children u WHERE u.parentId = ?1');
         $query->setParameter(1,$person->getId());
         //($person->getId());
         $children=$query->getResult();
         
         $childrenarray=array();
         foreach($children as $chunk){
         array_push($childrenarray,$chunk->getChildId());
         }
         
         return $childrenarray;

     }
         
     public function getRoots(){
         
          $em = $this->getDoctrine()->getManager();
          $usr=   $this->get('security.token_storage')->getToken()->getUser();
          
          $q = $em->createQuery('select u from AppBundle:UserPerson u WHERE u.userId = ?1');
                        $q->setParameter(1,$usr->getId());
          $userpersonarray=$q->getResult();
          $personarray=array();
          foreach($userpersonarray as $chunk){
             
              array_push($personarray, $chunk->getPersonId());
          }
          $rootsarray=array();
          
          $qb = $em->createQueryBuilder();
          $qb->select('count(c)');
          $qb->from('AppBundle:Children','c');
          $qb->where('c.childId = ?1');
          $qb->setParameter(1, $chunk->getId());

$count = $qb->getQuery()->getSingleScalarResult();
          
          foreach($personarray as $chunk){
                $qb = $em->createQueryBuilder();
                    $qb->select('count(c)');
                    $qb->from('AppBundle:Children','c');
                    $qb->where('c.childId = ?1');
                    $qb->setParameter(1, $chunk->getId());
                    $count = $qb->getQuery()->getSingleScalarResult();
//                    var_dump($chunk->getFirstname()); 
//                    var_dump($count);    
                        if( intval($count) == 0 ){ array_push($rootsarray,$chunk);    };
          }
         return $rootsarray;
          //var_dump($rootsarray[0]->getId());
         
     }
    
     public function getSiblingsArray( $person ){
        
        
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery('select u from AppBundle:Children u WHERE u.childId = ?1');
        $q->setParameter(1,$person->getId());
        $parents=$q->getResult();
        $targetarray=array();
        
       $parent=NULL;
        
         foreach($parents as $chunk){
                  $parent=$chunk->getParentId();               
          }
       
          if($parent!=NULL){
          
           $q = $em->createQuery('select u from AppBundle:Children u WHERE u.parentId = ?1');
           $q->setParameter(1,$parent->getId());
           $siblings=$q->getResult();
           $targetarray=array();
           
            foreach($siblings as $chunk){
                  array_push($targetarray,$chunk->getChildId()); 
                 
          }
           
         $key=  array_search ( $person , $targetarray  );
         unset($targetarray[$key]);  
       
         // var_dump(count($targetarray));
          
          $finalarray=array('name'=>array(),'id'=>array());
          
          foreach($targetarray as $chunk){
              array_push($finalarray['name'],$chunk->getFirstname());
              array_push($finalarray['id'],$chunk->getId());
          }
          
          return $finalarray;
          }
          return NULL;
         
     }
     
     public function getParentsArray($person){
         
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery('select u from AppBundle:Children u WHERE u.childId = ?1');
        $q->setParameter(1,$person->getId());
        $parents=$q->getResult();
        $parentsarray=array();
        
        if ($parents!=NULL){
            
            foreach($parents as $chunk){
                array_push($parentsarray,$chunk->getParentId());
            }
           
            $finalarray=array('names'=>array(), 'id'=>array()) ;       
            
             foreach($parentsarray as $chunk){
                 array_push($finalarray['names'],$chunk->getFirstname());
                 array_push($finalarray['id'],$chunk->getId());
             }
            
             return $finalarray;
             
        }else{
            return NULL;
        }
        
        
     }
     
     public function getChildrenArray($person){
         
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery('select u from AppBundle:Children u WHERE u.parentId = ?1');
        $q->setParameter(1,$person->getId());
        $children=$q->getResult();
        $childrenarray=array();
        
        if ($children!=NULL){
            
            foreach($children as $chunk){
                array_push($childrenarray,$chunk->getChildId());
            }
           
            $finalarray=array('names'=>array(), 'id'=>array()) ;       
            
              // var_dump(count($childrenarray));
            
             foreach($childrenarray as $chunk){
                 array_push($finalarray['names'],$chunk->getFirstname());
                 array_push($finalarray['id'],$chunk->getId());
             }
             
             return $finalarray;
             
        }else{
            return NULL;
        }
     
        
     }
     
     public function getSiblingsArray2($person){
         
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery('select u from AppBundle:Siblings u WHERE u.person1Id = ?1 OR u.person2Id =?1 ');
        $q->setParameter(1,$person->getId());
        $siblings=$q->getResult();
        $siblingsarray=array();
        $unwantedarray=array();
        
       
        
        foreach($siblings as $chunk){
            
            if($chunk->getPerson1Id()==$person){
                array_push($siblingsarray,$chunk->getPerson2Id());
                array_push($unwantedarray,$chunk->getPerson2Id());
                array_push($siblingsarray,$this->getSiblingsArrayHelper($chunk->getPerson2Id(), $unwantedarray));
                
            }else{
                array_push($siblingsarray,$chunk->getPerson1Id());
                array_push($unwantedarray,$chunk->getPerson1Id());
                array_push($siblingsarray,$this->getSiblingsArrayHelper($chunk->getPerson1Id(), $unwantedarray));
            }
            
        }
         
     }
     
     public function getSiblingsArrayHelper($person,$unwantedarray){
    
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery('select u from AppBundle:Siblings u WHERE u.person1Id = ?1 OR u.person2Id =?1 ');
        $q->setParameter(1,$person->getId());
        $siblings=$q->getResult();
        $siblingsarray=array();
        
        foreach($siblings as $chunk){
            
            if($chunk->getPerson1Id()==$person){
                
                if(!(array_search($chunk->getPerson2Id(), $unwantedarray))){
                array_push($siblingsarray,$chunk->getPerson2Id());
                array_push($unwantedarray,$chunk->getPerson2Id());
                array_push($siblingsarray,$this->getSiblingsArrayHelper($chunk->getPerson2Id(), $unwantedarray));
                }
            }else{
                if(!(array_search($chunk->getPerson1Id(), $unwantedarray))){
                array_push($siblingsarray,$chunk->getPerson1Id());
                array_push($unwantedarray,$chunk->getPerson1Id());
                array_push($siblingsarray,$this->getSiblingsArrayHelper($chunk->getPerson1Id(), $unwantedarray));
                }
            }
            
        }
        
        return $siblingsarray;
        
    }
    
    public function removeAllPersons(){
        
        $usr=   $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery('select u from AppBundle:UserPerson u WHERE u.userId = ?1');
        $q->setParameter(1,$usr->getId());
        $persons=$q->getResult();
        
        
        foreach($persons as $chunk){
            $this->removePerson($chunk->getPersonId());
        }
        
    }

     
}