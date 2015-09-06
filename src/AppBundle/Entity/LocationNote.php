<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="location_note")
 */
class LocationNote {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $notes;

    /**
     * @ORM\Column(type="integer")
     */
    protected $location_id;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $added_by;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $added;

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
     * Set notes
     *
     * @param string $notes
     * @return LocationNote
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set location_id
     *
     * @param integer $locationId
     * @return LocationNote
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
     * Set added_by
     *
     * @param integer $addedBy
     * @return LocationNote
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
     * Set added
     *
     * @param \DateTime $added
     * @return LocationNote
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
}
