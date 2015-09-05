<?php

  // src/AppBundle/Entity/Part.php
  namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="location_note")
 */
class LocationNote
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return LocationNote
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
}
