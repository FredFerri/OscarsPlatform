<?php

namespace Models;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 */
class Questions extends Model
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
     * @ManyToOne(targetEntity="Poll", inversedBy="questions", cascade={"persist", "remove"})
     * @JoinColumn(name="id_poll", referencedColumnName="id")
     */
    public $id_poll;

    /**
     * @ManyToOne(targetEntity="QuestionsTypes"), inversedBy="questions", cascade={"persist"})
     * @JoinColumn(name="id_type", referencedColumnName="id")
     */
    public $id_type;

    /**
     * @OneToMany(targetEntity="Answers", mappedBy="id_question", cascade={"remove"})
     * @var Answers $answers
     */
    public $answers;


    public function __construct($parameters = null) {
        if (isset($parameters)) {
            foreach ($parameters as $key => $value) {
                $query = 'set' . ucfirst($key);
                $this->$query($value);
            }
        }
    }

    public function addQuestion()
    {
        $em = getEntityManager();
        $em->persist($this);
        $em->flush();

        return true;
    }

    public function editQuestion()
    {
        $em = getEntityManager();
        $em->merge($this);
        $em->flush();

        return true;
    }

    public function removeQuestion()
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

    public function getIdPoll()
    {
        $id_poll = $this->id_poll;
        return $id_poll;
    }

    public function setIdPoll(Poll $id_poll)
    {
        $this->id_poll = $id_poll;
    }

    public function getIdType()
    {
        $id_type = $this->id_type->id;
        return $id_type;
    }

    public function setIdType(QuestionsTypes $id_type)
    {
        $this->id_type = $id_type;
    }

}

?>