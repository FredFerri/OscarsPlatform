<?php


namespace Models;

use Doctrine\ORM\Mapping as ORM;


/**
 * @Entity
 */
class Media extends Model
{
    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(name="title", type="string", length=100)
     */
    public $title;

    /**
     * @Column(name="description", type="string", length=255, nullable=true)
     */
    public $description;

    /**
     * @Column(name="date", type="date")
     */
    public $date;

    /**
     * @Column(name="place", type="string", length=30, nullable=true)
     */
    public $place;

    /**
     * @Column(name="URL", type="text", nullable=true)
     */
    public $URL;

    /**
     * @Column(name="URI", type="string", length=255, nullable=true)
     */
    public $URI;

    /**
     * @Column(name="type", type="string", length=50)
     */
    public $type;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="mediasPublished", cascade={"persist"})
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user_id;

    public function __construct($parameters = null) {
        if (isset($parameters)) {
            foreach ($parameters as $key => $value) {
                $query = 'set' . ucfirst($key);
                $this->$query($value);
            }
        }
    }

    public function addMedia()
    {
        $em = getEntityManager();
        $em->persist($this);
        $em->flush();

        return true;
    }

    public function editMedia()
    {
        $em = getEntityManager();
        $em->merge($this);
        $em->flush();

        return true;
    }

    public function removeMedia()
    {
        $em = getEntityManager();
        $em->remove($this);
        $em->flush();

        return true;
    }

    public function limitedRequestMedia($offset)
    {
        $db = loadDB();
        $request = "SELECT * FROM Media ORDER BY id DESC LIMIT 12 OFFSET ".$offset;
        $result = mysqli_query($db, $request);
        $results = [];
        $i=0;
        while ($row = mysqli_fetch_array($result)) {
            $results[$i] = $row;
            $i++;
        }
        return $results;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($new_id)
    {
        $this->id = $new_id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($new_title)
    {
        $this->title = $new_title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($new_description)
    {
        $this->description = $new_description;
    }

    public function getDate()
    {
        $date = new \DateTime($this->date);
        $result = $date->format('d-m-Y');
        return $result;
    }

    public function setDate($new_date)
    {
        $this->date = new \DateTime($new_date);
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($new_place)
    {
        $this->place = $new_place;
    }

    public function getURL()
    {
        return $this->URL;
    }

    public function setURL($new_URL)
    {
        $this->URL = $new_URL;
    }

    public function getURI()
    {
        return $this->URI;
    }

    public function setURI($new_URI)
    {
        $this->URI = $new_URI;
    }

    public function getUserId()
    {
        return $this->user_id['id'];
    }

    public  function setUserId(User $user_id)
    {
        $this->user_id = $user_id;
    }
}

?>