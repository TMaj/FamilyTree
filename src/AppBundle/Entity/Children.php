<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Children
 *
 * @ORM\Table(name="children")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChildrenRepository")
 */
class Children
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
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="id",cascade={"remove"})
     */
    private $parentId;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="id",cascade={"remove"})
     */
    private $childId;


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
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return Children
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set childId
     *
     * @param integer $childId
     *
     * @return Children
     */
    public function setChildId($childId)
    {
        $this->childId = $childId;

        return $this;
    }

    /**
     * Get childId
     *
     * @return int
     */
    public function getChildId()
    {
        return $this->childId;
    }
}

