<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable {

    /**
     * @ORM\OneToMany(targetEntity="Part", mappedBy="addeduser")
     */
    protected $parts;
    
    /**
     * @ORM\OneToMany(targetEntity="LocationNote", mappedBy="addeduser")
     */
    protected $partsnotes;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=64)
     */
    private $name_first;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=64)
     */
    private $name_last;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="datetime")
     */
    private $added;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private $addedBy;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $deleted;

    /**
     * @ORM\Column(type="integer")
     */
    private $deletedBy;

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        if($this->getDeleted()=== 1){
            return false;
        } else{
            return true;
        }
    }

    public function __construct() {
    }

    public function getUsername() {
        return $this->username;
    }

    public function getSalt() {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
    
    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        return array('ROLE_USER', 'ROLE_ADMIN');
    }

    public function isAdmin() {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    public function isUser() {
        return in_array('ROLE_USER', $this->getRoles());
    }

    public function eraseCredentials() {
        
    }

    /** @see \Serializable::serialize() */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
                // see section on salt below
                // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized) {
        list (
                $this->id,
                $this->username,
                $this->password,
                // see section on salt below
                // $this->salt
                ) = unserialize($serialized);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }
    
    /**
     * @param string $email
     * @return User
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeleted() {
        return $this->deleted;
    }
    
    /**
     * @param string $email
     * @return User
     */
    public function setDeletedBy($deletedBy) {
        $this->deletedBy = $deletedBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeletedBy() {
        return $this->deletedBy;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Set name_first
     *
     * @param string $nameFirst
     * @return User
     */
    public function setNameFirst($nameFirst) {
        $this->name_first = $nameFirst;

        return $this;
    }

    /**
     * Get name_first
     *
     * @return string
     */
    public function getNameFirst() {
        return $this->name_first;
    }

    /**
     * Set name_last
     *
     * @param string $nameLast
     * @return User
     */
    public function setNameLast($nameLast) {
        $this->name_last = $nameLast;

        return $this;
    }

    /**
     * Get name_last
     *
     * @return string
     */
    public function getNameLast() {
        return $this->name_last;
    }

    /**
     * Set added
     *
     * @param \DateTime $added
     * @return User
     */
    public function setAdded($added) {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added
     *
     * @return \DateTime
     */
    public function getAdded() {
        return $this->added;
    }

    /**
     * Set addedBy
     *
     * @param integer $addedBy
     * @return User
     */
    public function setAddedBy($addedBy) {
        $this->addedBy = $addedBy;

        return $this;
    }

    /**
     * Get addedBy
     *
     * @return integer
     */
    public function getAddedBy() {
        return $this->addedBy;
    }

    static function getTotalNumber($em, $searchTerm) {
        $qs = 'SELECT COUNT(u.id) as number FROM AppBundle:User u';
        if ($searchTerm) {
            $qs .=' WHERE u.username LIKE :search  AND u.deleted IS NULL';
        } else{
            $qs .=' WHERE u.deleted IS NULL';
        }
        $query = $em->createQuery($qs);
        if ($searchTerm) {
            $query->setParameter(
                    ':search', '%' . $searchTerm . '%'
            );
        }
        return $query->getResult()[0]['number'];
    }

    static function search($em, $searchTerm, $limit, $offset) {
        $qs = 'SELECT u.id,
                u.username,
                u.name_first,
                u.name_last
            FROM AppBundle:User u';
        if ($searchTerm) {
            $qs .=' WHERE u.username LIKE :search AND u.deleted IS NULL';
        } else{
            $qs .=' WHERE u.deleted IS NULL';
        }
        $qs .=' ORDER BY u.username ASC';
        $query = $em->createQuery($qs);
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);
        if ($searchTerm) {
            $query->setParameter(
                    ':search', '%' . $searchTerm . '%'
            );
        }
        return array(
            'total' => self::getTotalNumber($em, $searchTerm),
            'rows' => $query->getResult()
        );
    }


    /**
     * Add parts
     *
     * @param \AppBundle\Entity\Part $parts
     * @return User
     */
    public function addPart(\AppBundle\Entity\Part $parts)
    {
        $this->parts[] = $parts;

        return $this;
    }

    /**
     * Remove parts
     *
     * @param \AppBundle\Entity\Part $parts
     */
    public function removePart(\AppBundle\Entity\Part $parts)
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

    /**
     * Add partsnotes
     *
     * @param \AppBundle\Entity\LocationNote $partsnotes
     * @return User
     */
    public function addPartsnote(\AppBundle\Entity\LocationNote $partsnotes)
    {
        $this->partsnotes[] = $partsnotes;

        return $this;
    }

    /**
     * Remove partsnotes
     *
     * @param \AppBundle\Entity\LocationNote $partsnotes
     */
    public function removePartsnote(\AppBundle\Entity\LocationNote $partsnotes)
    {
        $this->partsnotes->removeElement($partsnotes);
    }

    /**
     * Get partsnotes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartsnotes()
    {
        return $this->partsnotes;
    }
}
