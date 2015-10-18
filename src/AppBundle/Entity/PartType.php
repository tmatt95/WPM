<?php

  // src/AppBundle/Entity/PartType.php
  namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="part_type")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PartTypeRepository")
 */
class PartType
{
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
   * @ORM\Column(type="text")
   */
  protected $description;

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
     * @return PartType
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

    /**
     * Set description
     *
     * @param string $description
     * @return PartType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    static function getTotalNumber($em,$searchTerm) {
        $qs = 'SELECT COUNT(p.id) as number FROM AppBundle:PartType p';
        if($searchTerm){
            $qs .=' WHERE p.name LIKE :search';
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
        $qs = 'SELECT p.id,
                p.name,
                CONCAT(SUBSTRING(p.description,1,50),\'...\') as description
            FROM AppBundle:PartType p';
        if($searchTerm){
            $qs .=' WHERE p.name LIKE :search';
        }
        $qs .=' ORDER BY p.name ASC';
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
    
    static function getList($em){
        $qs = 'SELECT p.id, p.name FROM AppBundle:PartType p ORDER BY p.name ASC';
        $query = $em->createQuery($qs);
        $pt = $query->getResult();
        $list = [];
        foreach ($pt as $p) {
            $list[$p['id']] = $p['name'];
        }
        return $list;
    }
}
