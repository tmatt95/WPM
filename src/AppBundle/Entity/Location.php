<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="location")
 */
class Location {
    
    /**
     * @ORM\OneToMany(targetEntity="Part", mappedBy="locationinfo")
     */
    protected $parts;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    protected $idDelete;

    /**
     * @ORM\Column(type="string", length=300)
     * @Assert\NotBlank()
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
    
    static function search($em,$searchTerm,$limit,$offset) {
        $qs = 'SELECT l.id,
                l.name,
                CONCAT(SUBSTRING(l.description,1,50),\'...\') as description
            FROM AppBundle:Location l';
        if($searchTerm){
            $qs .=' WHERE l.name LIKE :search';
        }
        $qs .=' ORDER BY l.name ASC';
        $query = $em->createQuery($qs);
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);
        if($searchTerm){
            $query->setParameter(
                ':search',
                '%'.$searchTerm.'%'
            );
        }
        return array(
            'total'=>self::getTotalNumber($em, $searchTerm),
            'rows'=>$query->getResult()
        );
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add parts
     *
     * @param \AppBundle\Entity\Location $parts
     * @return Location
     */
    public function addPart(\AppBundle\Entity\Location $parts)
    {
        $this->parts[] = $parts;

        return $this;
    }

    /**
     * Remove parts
     *
     * @param \AppBundle\Entity\Location $parts
     */
    public function removePart(\AppBundle\Entity\Location $parts)
    {
        $this->parts->removeElement($parts);
    }

    /**
     * Get parts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParts()
    {
        return $this->parts;
    }
}
