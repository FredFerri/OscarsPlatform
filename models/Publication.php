<?php

namespace Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 */
class Publication extends Model
{
    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(name="content", type="text")
     */
    public $content;

    /**
     * @Column(name="date", type="datetime")
     */
    public $date;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="posts_published", cascade={"persist"})
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $author;

    public function __construct($parameters = null) {
        if (isset($parameters)) {
            foreach ($parameters as $key => $value) {
                $query = 'set' . ucfirst($key);
                $this->$query($value);
            }
        }
    }

    public function addPublication()
    {
        $em = getEntityManager();
        $em->persist($this);
        $em->flush();

        return true;
    }

    public function editPublication()
    {
        $em = getEntityManager();
        $em->merge($this);
        $em->flush();

        return true;
    }

    public function removePublication()
    {
        $em = getEntityManager();
        $em->remove($this);
        $em->flush();

        return true;
    }

    public function limitedRequestPublication($offset)
    {
        $db = loadDB();
        $request = "SELECT * FROM Publication ORDER BY date DESC LIMIT 10 OFFSET ".$offset;
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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($new_content)
    {
        $this->content = $new_content;
    }

    public function getDate()
    {
        $date = $this->date;
        return $date->format('d-m-Y H:i');
    }

    public function setDate($new_date)
    {
        $this->date = $new_date;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $new_author) {
        $this->author = $new_author;
    }

    public function getAuthorId() {
        return $this->author->id;
    }

}

?>