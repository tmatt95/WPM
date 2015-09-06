<?php

// src/AppBundle/Entity/Part.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="location")
 */
class Location {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    protected $idDelete;

    /**
     * @ORM\Column(type="string", length=300)
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    // Id
    public function getId() {
        return $this->id;
    }

    public function setId($value) {
        $this->name = $value;
    }

    // Name
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getIdDelete() {
        return $this->idDelete;
    }

    public function setIdDelete($value) {
        $this->idDelete = $value;
    }

    // Description
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    static function getNoLocations($em) {
        $query = $em->createQuery('SELECT COUNT(l.id) as number FROM AppBundle:Location l');
        return $query->getResult()[0]['number'];
    }

}
