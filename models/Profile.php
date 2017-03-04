<?php

namespace Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 */
class Profile extends Model
{
    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(name="name", type="string", length=50)
     */
    public $name;

    /**
     * @OneToMany(targetEntity="User", mappedBy="profile", cascade={"persist"})
     * @var User $users
     **/
    public $users;

    public function __construct($name = null)
    {
        if (isset($id)) {
            $profile = $this->getEntityByName('Profile', $name);
            return $profile;
        }
    }

    public function getProfiles()
    {
        $listProfiles = $this->getAll('Profile');
        return $listProfiles;
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

}

?>