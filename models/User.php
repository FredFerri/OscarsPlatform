<?php

namespace Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

include($_SERVER["DOCUMENT_ROOT"] . "/models/Model.php");

/**
 * @Entity
 */
class User extends Model
{
    public $directory = '/medias/images/';

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

    /**
     * @var ArrayCollection Polls $polls
     * Owning Side
     *
     * @ORMManyToMany(targetEntity="Polls", inversedBy="users", cascade={"persist", "merge"})
     * @ORMJoinTable(name="User_Poll",
     *   joinColumns={@ORMJoinColumn(name="id_user", referencedColumnName="id")},
     *   inverseJoinColumns={@ORMJoinColumn(name="id_poll", referencedColumnName="id")}
     * )
     */
    public $polls;

    public function __construct($parameters = null)
    {
        $this->polls = new ArrayCollection();

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

    public function getUsers()
    {
        $listUsers = $this->getAll('User');
        return $listUsers;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($new_id)
    {
        $this->id = $new_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($new_name)
    {
        $this->name = $new_name;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($new_password)
    {
        $this->password = $new_password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($new_email)
    {
        $this->email = $new_email;
    }

    public function getLast_classement()
    {
        return $this->last_classement;
    }

    public function setLast_classement($new_lastclassement)
    {
        $this->last_classement = $new_lastclassement;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($new_statut)
    {
        $this->statut = $new_statut;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($new_picture)
    {
        if (isset($new_picture)) {
            $this->picture = $new_picture;
        }
    }

    public function getProfileId()
    {
        return $this->profile->id;
    }

    public function setProfile(Profile $new_profile)
    {
        $this->profile = $new_profile;
    }


    public function addPoll($id_poll)
    {
        $id_user = $this->id;
        $db = loadDB();
        $sql = "INSERT INTO User_Poll (id_user, id_poll) values(" . $id_user . ", " . $id_poll . ")";
        if ($result = mysqli_query($db, $sql)) {
            return true;
        } else {
            return false;
        }

    }

    public function getPolls()
    {
        $id_user = $this->id;
        $db = loadDB();
        $sql = "SELECT id_poll FROM User_Poll WHERE id_user=" . $id_user;
        if ($result = mysqli_query($db, $sql)) {
            $results = [];
            $i = 0;
            while ($row = mysqli_fetch_array($result)) {
                $results[$i] = $row;
                $i++;
            }
            return $results;
        } else {
            return false;
        }
    }

    public function getPollById($id_poll)
    {
        $db = loadDB();
        $sql = "SELECT id_poll FROM User_Poll WHERE id_user=" . $this->id . " AND id_poll=" . $id_poll;
        if ($result = mysqli_query($db, $sql) or die(mysqli_error($db))) {
            $results = [];
            $i = 0;
            while ($row = mysqli_fetch_array($result)) {
                $results[$i] = $row;
                $i++;
            }
            if (count($results) != 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function addAnswers($answers)
    {
        $db = loadDB();
        foreach ($answers as $answer) {
            $sql = "INSERT INTO User_Answers (id_user, id_answer) values(" . $this->id . ", " . $answer . ")";
            mysqli_query($db, $sql) or die(mysqli_error($db));
        }
        return true;

    }
}










