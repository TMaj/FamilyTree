<?php

namespace AppBundle\Entity;
use AppBundle\Entity\Person;

use Doctrine\ORM\Mapping as ORM;

/**
 * Choice
 *
 * @ORM\Table(name="choice")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChoiceRepository")
 */
class Choice
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
     * @var Person
     *
     * @ORM\Column(name="choice", type="integer")
     */
    private $choice;
    
    /**
     * @var string
     *
     * @ORM\Column(name="option", type="string")
     */
    private $option;



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
     * Set choice
     *
     * @param integer $choice
     *
     * @return Choice
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }

    /**
     * Get choice
     *
     * @return Person
     */
    public function getChoice()
    {
        return $this->choice;
    
        
    }
    
      /**
     * Set option
     *
     * @param string $option
     *
     * @return Choice
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }
    
}

