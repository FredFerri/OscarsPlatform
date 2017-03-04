<?php

namespace Models;

use Doctrine\ORM\Mapping as ORM;

include($_SERVER["DOCUMENT_ROOT"] . "/models/Model.php");

/**
 * @Entity
 */
class User extends Model
{
    public  $directory = '/medias/images/';

    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(name="name", type="string", length=50, nullable=true)
     */
    public $name;

    /**
     * @Column(name="password", type="string", length=50)
     */
    public $password;

    /**
     * @Column(name="email", type="string", length=60)
     */
    public $email;

    /**
     * @Column(name="last_classement", type="smallint", nullable=true)
     */
    public $last_classement;

    /**
     * @Column(name="statut", type="string", length=255, nullable=true)
     */
    public $statut;

    /**
     * @Column(name="picture", type="string", length=255, nullable=true)
     */
    public $picture;

    /**
     * @ManyToOne(targetEntity="Profile", inversedBy="users", cascade={"persist"})
     * @JoinColumn(name="profile", referencedColumnName="id")
     */
    public $profile;

    /**
     * @OneToMany(targetEntity="Media", mappedBy="user_id", cascade={"all"})
     * @var Media $medias_published
     */
    public $medias_published;

    /**
     * @OneToMany(targetEntity="Publication", mappedBy="author", cascade={"all"})
     * @var Publication $posts
     */
    public $posts;

    public function __construct($parameters = null) {
        if (isset($parameters)) {
            foreach ($parameters as $key => $value) {
                $query = 'set' . ucfirst($key);
                $this->$query($value);
            }
        }
    }

    public function addUser()
    {
        $em = getEntityManager();
        $em->persist($this);
        $em->flush();

        return true;
    }

    public function editUser()
    {
        $em = getEntityManager();
        $em->merge($this);
        $em->flush();

        return true;
    }

    public function removeUser()
    {
        $em = getEntityManager();
        $em->remove($this);
        $em->flush();
    }

    public function getUsers() {
        $listUsers = $this->getAll('User');
        return $listUsers;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_name) {
        $this->name = $new_name;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($new_password) {
        $this->password = $new_password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($new_email) {
        $this->email = $new_email;
    }

    public function getLast_classement() {
        return $this->last_classement;
    }

    public function setLast_classement($new_lastclassement) {
        $this->last_classement = $new_lastclassement;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function setStatut($new_statut) {
        $this->statut = $new_statut;
    }

    public function getPicture() {
        return $this->picture;
    }

    public function setPicture($new_picture) {
        if (isset($new_picture)) {
            $this->picture =  $new_picture;
        }
    }

    public function getProfileId() {
        return $this->profile->id;
    }

    public function setProfile(Profile $new_profile) {
        $this->profile = $new_profile;
    }


}










