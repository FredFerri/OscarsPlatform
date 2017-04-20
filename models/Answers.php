<?php

namespace Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 */
class Answers extends Model
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
     * @ManyToOne(targetEntity="Questions", inversedBy="answers", cascade={"persist", "remove"})
     * @JoinColumn(name="id_question", referencedColumnName="id")
     */
    public $id_question;


    public function __construct($parameters = null) {
        if (isset($parameters)) {
            foreach ($parameters as $key => $value) {
                $query = 'set' . ucfirst($key);
                $this->$query($value);
            }
        }
    }

    public function addAnswer()
    {
        $em = getEntityManager();
        $em->persist($this);
        $em->flush();

        return true;
    }

    public function editAnswer()
    {
        $em = getEntityManager();
        $em->merge($this);
        $em->flush();

        return true;
    }

    public function removeAnswer()
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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($new_content)
    {
        $this->content = $new_content;
    }

    public function getIdQuestion()
    {
        $id_question = $this->id_question;
        return $id_question;
    }

    public function setIdQuestion(Questions $id_question)
    {
        $this->id_question = $id_question;
    }

    public function getUsers() {
        $db = loadDB();
        $sql = "SELECT id_user FROM User_Answers WHERE id_answer=".$this->id;
        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
        $i = 0;
        $results = [];
        while ($row = mysqli_fetch_array($result)) {
            $results[$i] = $row;
            $i++;
        }
        if ($results) {
            return $results;
        }
    }

}

?>