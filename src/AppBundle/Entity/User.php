<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable {

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
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name_first;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name_last;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added;

    /**
     * @ORM\Column(type="integer")
     */
    private $addedBy;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct() {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
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
            $qs .=' WHERE u.username LIKE :search';
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
            $qs .=' WHERE u.username LIKE :search';
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

}
