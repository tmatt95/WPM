<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="location")
 * @UniqueEntity(
 *     fields={"name"},
 *     errorPath="name",
 *     message="There is already a location with this name")
 */
class Location
{
    /**
     * @ORM\OneToMany(targetEntity="Part", mappedBy="locationinfo")
     */
    protected $parts;

    /**
     * @ORM\OneToMany(targetEntity="PartChange", mappedBy="addedlocation")
     */
    protected $partLocChanges;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    protected $idDelete;

    /**
     * @ORM\Column(type="string", length=300, unique=true)
     * @Assert\NotBlank()
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

    public function setId($value)
    {
        $this->name = $value;
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

    public function getIdDelete()
    {
        return $this->idDelete;
    }

    public function setIdDelete($value)
    {
        $this->idDelete = $value;
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

    public static function getList($em)
    {
        $qs = 'SELECT l.id, l.name FROM AppBundle:Location l ORDER BY l.name ASC';
        $query = $em->createQuery($qs);
        $pt = $query->getResult();
        $list = [];
        foreach ($pt as $p) {
            $list[$p['id']] = $p['name'];
        }

        return $list;
    }

    public static function getTotalNumber($em, $searchTerm, $hasqty=null)
    {
        // generates where array and param array
        $where = array();
        $having = array();
        $params = array();
        if($searchTerm){
            $where[] = 'l.name LIKE :search';
            $params[':search'] = '%'.$searchTerm.'%';
        }
        if($hasqty){
            if($hasqty === 'yes'){
                $having[] = '(COUNT(p.id) > 0)';
            }
            if($hasqty === 'no'){
                $having[] = '(COUNT(p.id) = 0)';
            }
        }
        
        $qs = 'SELECT l.id
            FROM AppBundle:Location l
            LEFT JOIN l.parts p';
        if(count($where) > 0){
            $qs .= ' WHERE '. implode(' AND ', $where);
        }
        $qs .= ' GROUP BY l.id';
        if(count($having) > 0){
            $qs .= ' HAVING '. implode(' AND ', $having);
        }
        $query = $em->createQuery($qs);
        foreach ($params as $id=>$value){
             $query->setParameter(
                $id,
                $value
            );
        }
        return count($query->getResult());
    }

    public static function search($em, $searchTerm, $limit, $offset, $hasqty = null)
    {
        // generates where array and param array
        $where = array();
        $having = array();
        $params = array();
        if($searchTerm){
            $where[] = 'l.name LIKE :search';
            $params[':search'] = '%'.$searchTerm.'%';
        }
        if($hasqty){
            if($hasqty === 'yes'){
                $having[] = '(COUNT(p.id) > 0)';
            }
            if($hasqty === 'no'){
                $having[] = '(COUNT(p.id) = 0)';
            }
        }
        
        $qs = 'SELECT l.id,
                l.name,
                CONCAT(SUBSTRING(l.description,1,50),\'...\') as description,
                COUNT(p.id) as no_parts
            FROM AppBundle:Location l
            LEFT JOIN l.parts p';
        if(count($where) > 0){
            $qs .= ' WHERE '. implode(' AND ', $where);
        }
        $qs .= ' GROUP BY l.id';
        if(count($having) > 0){
            $qs .= ' HAVING '. implode(' AND ', $having);
        }
        $qs .= ' ORDER BY l.name ASC';
        $query = $em->createQuery($qs);
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);
        foreach ($params as $id=>$value){
             $query->setParameter(
                $id,
                $value
            );
        }
        return array(
            'total' => self::getTotalNumber($em, $searchTerm, $hasqty),
            'rows' => $query->getResult(),
        );
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add parts.
     *
     * @param \AppBundle\Entity\Location $parts
     *
     * @return Location
     */
    public function addPart(\AppBundle\Entity\Location $parts)
    {
        $this->parts[] = $parts;

        return $this;
    }

    /**
     * Remove parts.
     *
     * @param \AppBundle\Entity\Location $parts
     */
    public function removePart(\AppBundle\Entity\Location $parts)
    {
        $this->parts->removeElement($parts);
    }

    /**
     * Get parts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Add partLocChanges.
     *
     * @param \AppBundle\Entity\PartChange $partLocChanges
     *
     * @return Location
     */
    public function addPartLocChange(\AppBundle\Entity\PartChange $partLocChanges)
    {
        $this->partLocChanges[] = $partLocChanges;

        return $this;
    }

    /**
     * Remove partLocChanges.
     *
     * @param \AppBundle\Entity\PartChange $partLocChanges
     */
    public function removePartLocChange(\AppBundle\Entity\PartChange $partLocChanges)
    {
        $this->partLocChanges->removeElement($partLocChanges);
    }

    /**
     * Get partLocChanges.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPartLocChanges()
    {
        return $this->partLocChanges;
    }
}
