<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="partsnotes")
     * @ORM\JoinColumn(name="added_by", referencedColumnName="id")
    */
    protected $addeduser;

    /**
     * @Assert\NotBlank()
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
        $this->addeduser = $addedBy;

        return $this;
    }

    /**
     * Get added_by
     *
     * @return integer 
     */
    public function getAddedBy()
    {
        return $this->addeduser;
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
    
    static function getTotalNumber($em,$searchTerm) {
        $qs = 'SELECT COUNT(ln.id) as number FROM AppBundle:LocationNote ln';
        if($searchTerm){
            $qs .=' WHERE ln.notes LIKE :search';
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
    
    static function search($em,$locationId,$searchTerm,$limit,$offset) {
        $qs = 'SELECT ln.id,
                ln.notes,
                ln.added,
                ln.added_by,
                u.name_first,
                u.name_last
        FROM AppBundle:LocationNote ln
        JOIN ln.addeduser u
        WHERE ln.location_id = :locationid';
        if($searchTerm){
            $qs .=' AND ln.notes LIKE :search';
        }
        $qs .=' ORDER BY ln.added DESC';
        $query = $em->createQuery($qs);
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);
        if($searchTerm){
            $query->setParameter(
                ':search',
                '%'.$searchTerm.'%'
            );
        }
        $query->setParameter(
            ':locationid',
            $locationId
        );
        return array(
            'total'=>1,
            'rows'=>$query->getResult()
        );
    }

    /**
     * Set addeduser
     *
     * @param \AppBundle\Entity\User $addeduser
     * @return LocationNote
     */
    public function setAddeduser(\AppBundle\Entity\User $addeduser = null)
    {
        $this->addeduser = $addeduser;

        return $this;
    }

    /**
     * Get addeduser
     *
     * @return \AppBundle\Entity\User 
     */
    public function getAddeduser()
    {
        return $this->addeduser;
    }
}
