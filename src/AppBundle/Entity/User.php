<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Proszę podać swoje imię", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=255,
     *     minMessage="Imię jest zbyt krótkie.",
     *     maxMessage="Imię jest zbyt długie.",
     *     groups={"Registration", "Profile"}
     * )
     */
    public $imie;
    
     /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Proszę podać swoje nazwisko", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=255,
     *     minMessage="Nazwisko jest zbyt krótkie.",
     *     maxMessage="Nazwisko jest zbyt długie.",
     *     groups={"Registration", "Profile"}
     * )
     */
    public $nazwisko;
    
    /**
     * Override $email so that we can apply custom validation.
     * 
     * @Assert\NotBlank(groups={"AppRegistration"})
     * 
     * @Assert\Email(groups={"Registration"})
     */
    protected $email;
    
    /**
     *
     * @var int
     * 
     * @ORM\OneToMany(targetEntity="UserPerson", mappedBy="userId")
     */
    private $personId;
    
      /**
     * @ORM\Column(type="boolean")
     *
     *
     */
    public $gender;
    
     /**   @ORM\Column(type="date", length=255)
     * @Assert\Date()
     */
     public $born;
    
    
    
    
    public function __construct() {
        parent::__construct();
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
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
     /**
     * Get nazwisko
     *
     * @return string
     */
    public function getNazwisko()
    {
        return $this->nazwisko;
    }
    
     /**
     * Get imie
     *
     * @return string
     */
    public function getImie()
    {
        return $this->imie;
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
     * Set gender
     *
     * @return boolean
     */
    public function setGender()
    {
        return $this->gender;
    }
    
    /**
     * Get born
     *
     * @return date
     */
    public function getBorn()
    {
        return $this->born;
    }
    
    
    public function displayFullName()
    {
        return $this->imie." ".$this->nazwisko;
    }

    
    
    
    /**
     * Sets the email.
     *
     * @param string $email
     * @return User
     */
   
   
    
    
    
}
