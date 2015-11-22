<?php

// src/AppBundle/Entity/PartType.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="part_type")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PartTypeRepository")
 * @UniqueEntity(
 *     fields={"name"},
 *     errorPath="name",
 *     message="There is already a part type with this name")
 */
class PartType {

    /**
     * @ORM\OneToMany(targetEntity="Part", mappedBy="parttype")
     */
    protected $parts;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200, unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return PartType
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return PartType
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    public static function getTotalNumber($em, $searchTerm, $hasqty=null) {
        // generates where array and param array
        $where = array();
        $having = array();
        $params = array();
        if($searchTerm){
            $where[] = 'pt.name LIKE :search';
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
        
        $qs = 'SELECT pt.id FROM AppBundle:PartType pt
            LEFT JOIN pt.parts p';
        if(count($where) > 0){
            $qs .= ' WHERE '. implode(' AND ', $where);
        }
        $qs .= ' GROUP BY pt.id';
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

    public static function search($em, $searchTerm, $limit, $offset, $hasqty= null) {
        // generates where array and param array
        $where = array();
        $having = array();
        $params = array();
        if($searchTerm){
            $where[] = 'pt.name LIKE :search';
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
        
        $qs = 'SELECT pt.id,
                pt.name,
                CONCAT(SUBSTRING(pt.description,1,50),\'...\') as description,
                COUNT(p.id) as no_parts
            FROM AppBundle:PartType pt
            LEFT JOIN pt.parts p';
        if(count($where) > 0){
            $qs .= ' WHERE '. implode(' AND ', $where);
        }
        $qs .= ' GROUP BY pt.id';
        if(count($having) > 0){
            $qs .= ' HAVING '. implode(' AND ', $having);
        }
        $qs .= ' ORDER BY pt.name ASC';
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

    public static function getStats($em) {
        $qs = 'SELECT pt.name,
        SUM(p.qty) as qty
        FROM AppBundle:PartType pt
        JOIN pt.parts p';
        $qs .= '  GROUP BY pt.name ORDER BY pt.name DESC';
        $query = $em->createQuery($qs);

        $output = array();
        $color = 0;
        $chartColors = array(
            '#4D4D4D',
            '#5DA5DA',
            '#FAA43A',
            '#60BD68',
            '#F17CB0',
            '#B2912F',
            '#B276B2',
            '#DECF3F',
            '#F15854',
        );
        foreach ($query->getResult() as $index => $stat) {
            $output[$index] = ['color' => $chartColors[$color],
            'highlight' => '#CCCCCC',
            'label' => $stat['name'],
            'value' => $stat['qty'],];

            // Loops the color back round if needed
            $color ++;
            if ($color === count($chartColors)) {
                $color = 0;
            }
        }

        return $output;
    }

    public static function getList($em) {
        $qs = 'SELECT p.id, p.name FROM AppBundle:PartType p ORDER BY p.name ASC';
        $query = $em->createQuery($qs);
        $pt = $query->getResult();
        $list = [];
        foreach ($pt as $p) {
            $list[$p['id']] = $p['name'];
        }

        return $list;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->parts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add parts.
     *
     * @param \AppBundle\Entity\Part $parts
     *
     * @return PartType
     */
    public function addPart(\AppBundle\Entity\Part $parts) {
        $this->parts[] = $parts;

        return $this;
    }

    /**
     * Remove parts.
     *
     * @param \AppBundle\Entity\Part $parts
     */
    public function removePart(\AppBundle\Entity\Part $parts) {
        $this->parts->removeElement($parts);
    }

    /**
     * Get parts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParts() {
        return $this->parts;
    }

}
