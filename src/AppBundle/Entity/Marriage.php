<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marriage
 *
 * @ORM\Table(name="marriage")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarriageRepository")
 */
class Marriage
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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="id",cascade={"remove"} )
     */
    private $person1Id;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="id",cascade={"remove"})
     */
    private $person2Id;


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
     * Set person1Id
     *
     * @param integer $person1Id
     *
     * @return Marriage
     */
    public function setPerson1Id($person1Id)
    {
        $this->person1Id = $person1Id;

        return $this;
    }

    /**
     * Get person1Id
     *
     * @return int
     */
    public function getPerson1Id()
    {
        return $this->person1Id;
    }

    /**
     * Set person2Id
     *
     * @param integer $person2Id
     *
     * @return Marriage
     */
    public function setPerson2Id($person2Id)
    {
        $this->person2Id = $person2Id;

        return $this;
    }

    /**
     * Get person2Id
     *
     * @return int
     */
    public function getPerson2Id()
    {
        return $this->person2Id;
    }
}

