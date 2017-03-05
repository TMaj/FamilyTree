<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 */
class Person
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;
    
     /**
     * @var string
     *
     * @ORM\Column(name="familyname", type="string", length=255, nullable=true)
     */
    private $familyname;
    

    /**
     * @var boolean
     *
     * @ORM\Column(name="gender", type="boolean")
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="born", type="date")
     */
    private $born;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="died", type="date", nullable=true)
     */
    private $died;

    /**
     *
     * @var int
     * 
     * @ORM\OneToMany(targetEntity="UserPerson", mappedBy="personId")
     */
    private $userId;
    
    
    /**
     * 
     * @var 
     * @ORM\Column(name="children", type="integer")
     */
    private $children;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="children")
     *
     */
    private $parent;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;
    
   
 
    
    
    
    
     public function __construct() {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
  
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Person
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Person
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
    
    
        /**
     * Set familyname
     *
     * @param string $familyname
     *
     * @return Person
     */
    public function setFamilyname($familyname)
    {
        $this->familyname = $familyname;

        return $this;
    }

    /**
     * Get familyname
     *
     * @return string
     */
    public function getFamilyname()
    {
        return $this->familyname;
    }
    
    

    /**
     * Set gender
     *
     * @param boolean $gender
     *
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return boolean
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set born
     *
     * @param \DateTime $born
     *
     * @return Person
     */
    public function setBorn($born)
    {
        $this->born = $born;

        return $this;
    }

    /**
     * Get born
     *
     * @return \DateTime
     */
    public function getBorn()
    {
        return $this->born;
    }

    /**
     * Set died
     *
     * @param \DateTime $died
     *
     * @return Person
     */
    public function setDied($died)
    {
        $this->died = $died;

        return $this;
    }

    /**
     * Get died
     *
     * @return \DateTime
     */
    public function getDied()
    {
        return $this->died;
    }
    
     /**
     * Get parent
     *
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Set parent
     *
     * @param int $parent
     *
     * @return Person
     * 
     */
    public function setParent($parent)
    {
       $this->parent=$parent;
    }
    
     /**
     * Get children
     *
     * @return array
     */
    public function getChildren()
    {
        return (array)$this->children;
    }
    
    /**
     * Set children
     *
     * @param array $children
     *
     * @return array
     * 
     */
    public function setChildren($children)
    {
       $this->children=$children;
    }
    
    
     /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return string
     * 
     */
    public function setDescription($description)
    {
       $this->description=$description;
    }
    
}

