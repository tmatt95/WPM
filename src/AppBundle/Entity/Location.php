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
    
    static function getList($em){
        $qs = 'SELECT l.id, l.name FROM AppBundle:Location l ORDER BY l.name ASC';
        $query = $em->createQuery($qs);
        $pt = $query->getResult();
        $list = [];
        foreach ($pt as $p) {
            $list[$p['id']] = $p['name'];
        }
        return $list;
    }

    static function getTotalNumber($em,$searchTerm) {
        $qs = 'SELECT COUNT(l.id) as number FROM AppBundle:Location l';
        if($searchTerm){
            $qs .=' WHERE l.name LIKE :search';
        }
        $query = $em->createQuery($qs);
        if($searchTerm){
            $query->setParameter(
                ':search',
                '%'.$searchTerm.'%'
            );
        }
        return $query->getResult()[0]['number'];
    }

}
