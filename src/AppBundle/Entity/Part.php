<?php

  // src/AppBundle/Entity/Part.php
  namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="part")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PartTypeRepository")
 */
class Part
{

  /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="parts")
     * @ORM\JoinColumn(name="added_by", referencedColumnName="id")
  */
  protected $addeduser;

  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=200)
   */
  protected $name;

  /**
   * @ORM\Column(type="integer")
   */
  protected $type;

  /**
   * @ORM\Column(type="integer")
   */
  protected $location;

  /**
  * @ORM\Column(type="text")
  */
  protected $description;

  /**
   * @ORM\Column(type="integer")
   */
  protected $qty;

  /**
   * @ORM\Column(type="integer")
   */
  protected $added_by;

  /**
   * @ORM\Column(type="datetime")
   */
  protected $added;

  /**
   * @ORM\Column(type="datetime")
   */
  protected $deleted;

  /**
   * @ORM\Column(type="integer")
   */
  protected $deleted_by;

  // Id
  public function getId()
  {
    return $this->id;
  }

  // Name
  public function getName()
  {
    return $this->name;
  }
  public function setName( $value)
  {
    $this->name = $value;
  }

  // Type
  public function getType()
  {
    return $this->type;
  }
  public function setType( $type)
  {
    $this->type = $type;
  }

  // Location
  public function getLocation()
  {
    return $this->location;
  }
  public function setLocation($location)
  {
    $this->location = $location;
  }

  // Description
  public function getDescription()
  {
    return $this->description;
  }
  public function setDescription($description)
  {
    $this->description = $description;
  }

  // qty
  public function getQty()
  {
    return $this->qty;
  }
  public function setQty($qty)
  {
    $this->qty = $qty;
  }

  // Added
  public function getAdded()
  {
    return $this->added;
  }
  public function setAdded(\DateTime $added)
  {
    $this->added = $added;
  }

  // Added By
  public function getAddedBy()
  {
    return $this->added_by;
  }
  public function setAddedBy($addedBy)
  {
    $this->added_by = $addedBy;
  }

  // Deleted
  public function getDeleted()
  {
    return $this->deleted;
  }
  public function setDeleted(\DateTime $deleted)
  {
    $this->deleted = $deleted;
  }

  // Deleted By
  public function getDeletedBy()
  {
    return $this->deleted_by;
  }
  public function setDeletedBy($addedBy)
  {
    $this->deleted_by = $addedBy;
  }
}
