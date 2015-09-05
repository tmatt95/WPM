<?php

  // src/AppBundle/Entity/Part.php
  namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="location")
 */
class Location
{
  /**
       * @ORM\Column(type="integer")
       * @ORM\Id
       * @ORM\GeneratedValue(strategy="AUTO")
       */
  protected $id;

  /**
     * @ORM\Column(type="string", length=300)
     */
  protected $name;

  /**
     * @ORM\Column(type="string")
     */
  protected $description;

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
  public function setName($name)
  {
    $this->name = $name;
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
}
