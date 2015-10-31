<?php

// src/AppBundle/Entity/Part.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="part_change")
 */
class PartChange {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $no_added;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $no_taken;

    /**
     * @ORM\Column(type="integer")
     */
    protected $no_total;

    /**
     * @ORM\Column(type="integer")
     */
    protected $location_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $part_id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $added;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $comment;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $added_by;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set no_added
     *
     * @param integer $noAdded
     * @return PartChange
     */
    public function setNoAdded($noAdded)
    {
        $this->no_added = $noAdded;

        return $this;
    }

    /**
     * Get no_added
     *
     * @return integer 
     */
    public function getNoAdded()
    {
        return $this->no_added;
    }

    /**
     * Set no_taken
     *
     * @param integer $noTaken
     * @return PartChange
     */
    public function setNoTaken($noTaken)
    {
        $this->no_taken = $noTaken;

        return $this;
    }

    /**
     * Get no_taken
     *
     * @return integer 
     */
    public function getNoTaken()
    {
        return $this->no_taken;
    }

    /**
     * Set no_total
     *
     * @param integer $noTotal
     * @return PartChange
     */
    public function setNoTotal($noTotal)
    {
        $this->no_total = $noTotal;

        return $this;
    }

    /**
     * Get no_total
     *
     * @return integer 
     */
    public function getNoTotal()
    {
        return $this->no_total;
    }

    /**
     * Set location_id
     *
     * @param integer $locationId
     * @return PartChange
     */
    public function setLocationId($locationId)
    {
        $this->location_id = $locationId;

        return $this;
    }

    /**
     * Get location_id
     *
     * @return integer 
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Set part_id
     *
     * @param integer $partId
     * @return PartChange
     */
    public function setPartId($partId)
    {
        $this->part_id = $partId;

        return $this;
    }

    /**
     * Get part_id
     *
     * @return integer 
     */
    public function getPartId()
    {
        return $this->part_id;
    }

    /**
     * Set added
     *
     * @param \DateTime $added
     * @return PartChange
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added
     *
     * @return \DateTime 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return PartChange
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set added_by
     *
     * @param integer $addedBy
     * @return PartChange
     */
    public function setAddedBy($addedBy)
    {
        $this->added_by = $addedBy;

        return $this;
    }

    /**
     * Get added_by
     *
     * @return integer 
     */
    public function getAddedBy()
    {
        return $this->added_by;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return PartChange
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}
