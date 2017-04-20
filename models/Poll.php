<?php

namespace Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @Entity
 */
class Poll extends Model
{
    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(name="name", type="text")
     */
    public $name;

    /**
     * @Column(name="description", type="text")
     */
    public $description;

    /**
     * @OneToMany(targetEntity="Questions", mappedBy="id_poll", cascade={"persist", "remove"})
     * @var Questions $questions
     */
    public $questions;

    /**
     * @var ArrayCollection User $users
     *
     * Inverse Side
     *
     * @ORMManyToMany(targetEntity="User", mappedBy="polls", cascade={"persist", "merge"})
     */
    public $users;


    public function __construct($parameters = null) {
        if (isset($parameters)) {
            foreach ($parameters as $key => $value) {
                $query = 'set' . ucfirst($key);
                $this->$query($value);
            }
        }
    }

    public function addPoll()
    {
        $em = getEntityManager();
        $em->persist($this);
        $em->flush();

        return true;
    }

    public function editPoll()
    {
        $em = getEntityManager();
        $em->merge($this);
        $em->flush();

        return true;
    }

    public function removePoll()
    {
        $em = getEntityManager();
        $em->remove($this);
        $em->flush();

        return true;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($new_description)
    {
        $this->description = $new_description;
    }

    public function addUser(User $user) {
        if (!$this->users->contains($user)) {
            if (!$user->getPolls()->contains($this)) {
                $user->addPoll($this);  // Lie le Client au produit.
            }
            $this->users->add($user);
        }
    }


    public function nbrVotes() {
        $db = loadDB();
        $sql = "SELECT COUNT(id_user) FROM User_Poll WHERE id_poll=".$this->id;
        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
        $result = mysqli_fetch_row($result);
        return $result;
    }



}

?>