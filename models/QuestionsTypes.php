<?php

namespace Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 */
class QuestionsTypes extends Model
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
     * @OneToMany(targetEntity="Questions", mappedBy="id_type", cascade={"persist"})
     * @var Questions $questions
     */
    public $questions;


    public function __construct($parameters = null) {
        if (isset($parameters)) {
            foreach ($parameters as $key => $value) {
                $query = 'set' . ucfirst($key);
                $this->$query($value);
            }
        }
    }

    public function addQuestionType()
    {
        $em = getEntityManager();
        $em->persist($this);
        $em->flush();

        return true;
    }

    public function editQuestionType()
    {
        $em = getEntityManager();
        $em->merge($this);
        $em->flush();

        return true;
    }

    public function removeQuestionType()
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

    public function setContent($new_name)
    {
        $this->name = $new_name;
    }

}

?>