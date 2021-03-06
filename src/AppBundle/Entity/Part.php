<?php

/**
 * Part
 * The main entity in the application. These are the items which the system
 * is designed to track. Everything else is in some way linked to these.
 *
 * PHP version 5.6
 *
 * @category WPM
 * @package  Part
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  GIT: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Part
 * The main entity in the application. These are the items which the system
 * is designed to track. Everything else is in some way linked to these.
 *
 * PHP version 5.6
 *
 * @category               WPM
 * @package                Part
 * @author                 Matthew Turner <tmatt95@gmail.com>
 * @license                http://opensource.org/licenses/GPL-3.0 GPL3
 * @version                Release: <1.0.0>
 * @link                   https://github.com/tmatt95/WPM/
 * @ORM\Entity
 * @ORM\Table(name="part")
 */
class Part
{
    
    /**
     * @ORM\ManyToOne(targetEntity="PartType", inversedBy="parts")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    protected $parttype;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="parts")
     * @ORM\JoinColumn(name="added_by", referencedColumnName="id")
     */
    protected $addeduser;

    /**
     * @ORM\OneToMany(targetEntity="PartChange", mappedBy="partInfo")
     */
    protected $changes;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="parts")
     * @ORM\JoinColumn(name="location", referencedColumnName="id")
     */
    protected $locationinfo;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    protected $type;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    protected $location;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    protected $qty;

    /**
     * @ORM\Column(type="integer")
     */
    protected $added_by;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
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

    public function setName($value)
    {
        $this->name = $value;
    }

    // Type
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
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
        return $this->addedBy;
    }

    public function setAddedBy($addedBy)
    {
        $this->addedBy = $addedBy;
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

    /**
     * Set parttype.
     *
     * @param \AppBundle\Entity\PartType $parttype
     *
     * @return Part
     */
    public function setParttype(\AppBundle\Entity\PartType $parttype = null)
    {
        $this->parttype = $parttype;

        return $this;
    }

    /**
     * Get parttype.
     *
     * @return \AppBundle\Entity\PartType
     */
    public function getParttype()
    {
        return $this->parttype;
    }

    /**
     * Set addeduser.
     *
     * @param \AppBundle\Entity\User $addeduser
     *
     * @return Part
     */
    public function setAddeduser(\AppBundle\Entity\User $addeduser = null)
    {
        $this->addeduser = $addeduser;

        return $this;
    }

    /**
     * Get addeduser.
     *
     * @return \AppBundle\Entity\User
     */
    public function getAddeduser()
    {
        return $this->addeduser;
    }

    /**
     * Set locationinfo.
     *
     * @param \AppBundle\Entity\Location $locationinfo
     *
     * @return Part
     */
    public function setLocationinfo(\AppBundle\Entity\Location $locationinfo = null)
    {
        $this->locationinfo = $locationinfo;

        return $this;
    }

    /**
     * Get locationinfo.
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocationinfo()
    {
        return $this->locationinfo;
    }

    public static function getTotalNumber($em, $searchTerm, $limit, $offset, $locationid = null, $partType = null, $hasqty = null)
    {
        // generates where array and param array
        $where = array();
        $params = array();
        if($searchTerm){
            $where[] = '(p.name LIKE :search '
                . 'OR l.name LIKE :search '
                . 'OR pt.name LIKE :search '
                . 'OR CONCAT(u.name_first , \' \',u.name_last) LIKE :search)';
            $params[':search'] = '%'.$searchTerm.'%';
        }
        if($locationid){
            $where[] = '(p.location = :locationid)';
            $params[':locationid'] = $locationid;
        }
        if($partType){
            $where[] = '(p.type = :type)';
            $params[':type'] = $partType;
        }
        if($hasqty){
            if($hasqty === 'yes'){
                $where[] = '(p.qty > 0)';
            }
            if($hasqty === 'no'){
                $where[] = '(p.qty = 0)';
            }
        }
        $qs = 'SELECT COUNT(p.id) as number
            FROM AppBundle:Part p
            JOIN p.locationinfo l
            JOIN p.parttype pt
            JOIN p.addeduser u';
        if(count($where) > 0){
            $qs .= ' WHERE '. implode(' AND ', $where);
        }
        $qs .= ' ORDER BY p.name ASC';
        $query = $em->createQuery($qs);
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);
        foreach ($params as $id=>$value){
             $query->setParameter(
                $id,
                $value
            );
        }
        return $query->getResult()[0]['number'];
    }

    public static function moveAllOutLocation($em, $locationid, $locationidnew = null)
    {
        $qs = 'UPDATE AppBundle:Part p SET p.location = :locationidnew WHERE p.location = :locationid';
        $query = $em->createQuery($qs);
        $query->setParameter(
            ':locationid',
            $locationid
        );
        $query->setParameter(
            ':locationidnew',
            $locationidnew
        );

        return $query->getResult();
    }

    public static function moveAllOutPartType($em, $parttype, $parttypenew = null)
    {
        $qs = 'UPDATE AppBundle:Part p SET p.type = :parttypenew WHERE p.type = :parttype';
        $query = $em->createQuery($qs);
        $query->setParameter(
            ':parttype',
            $parttype
        );
        $query->setParameter(
            ':parttypenew',
            $parttypenew
        );

        return $query->getResult();
    }

    public static function search($em, $searchTerm, $limit, $offset, $locationid = null, $partType = null, $hasqty = null)
    {
        // generates where array and param array
        $where = array();
        $params = array();
        if($searchTerm){
            $where[] = '(p.name LIKE :search '
                . 'OR l.name LIKE :search '
                . 'OR pt.name LIKE :search '
                . 'OR CONCAT(u.name_first , \' \',u.name_last) LIKE :search)';
            $params[':search'] = '%'.$searchTerm.'%';
        }
        if($locationid){
            $where[] = '(p.location = :locationid)';
            $params[':locationid'] = $locationid;
        }
        if($partType){
            $where[] = '(p.type = :type)';
            $params[':type'] = $partType;
        }
        if($hasqty){
            if($hasqty === 'yes'){
                $where[] = '(p.qty > 0)';
            }
            if($hasqty === 'no'){
                $where[] = '(p.qty = 0)';
            }
        }
        
        $qs = 'SELECT p.id,
            p.name,
            p.qty,
            p.added,
            p.added_by,
            p.location,
            l.name AS location_name,
            pt.name AS part_type,
            p.type,
            p.added_by,
            CONCAT(u.name_first , \' \',u.name_last) AS added_by_name
            FROM AppBundle:Part p
            JOIN p.locationinfo l
            JOIN p.parttype pt
            JOIN p.addeduser u';
        
        if(count($where) > 0){
            $qs .= ' WHERE '. implode(' AND ', $where);
        }

        $qs .= ' ORDER BY p.name ASC';
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
            'total' => self::getTotalNumber($em, $searchTerm, $limit, $offset, $locationid, $partType, $hasqty),
            'rows' => $query->getResult(),
        );
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->changes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add changes.
     *
     * @param \AppBundle\Entity\PartChange $changes
     *
     * @return Part
     */
    public function addChange(\AppBundle\Entity\PartChange $changes)
    {
        $this->changes[] = $changes;

        return $this;
    }

    /**
     * Remove changes.
     *
     * @param \AppBundle\Entity\PartChange $changes
     */
    public function removeChange(\AppBundle\Entity\PartChange $changes)
    {
        $this->changes->removeElement($changes);
    }

    /**
     * Get changes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChanges()
    {
        return $this->changes;
    }
}
