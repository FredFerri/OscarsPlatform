<?php

namespace Models;

class Model {

    public function hydrate($parameters)
    {
        foreach ($parameters as $key => $value) {
            $query = 'set' . ucfirst($key);
            $this->$query($value);
        }
        return $this;
    }

    public function getAll($class)
    {
        $em = getEntityManager();
        $class = 'Models\\'.$class;
        $entity = $em->getRepository($class)->findAll();
        return $entity;
    }

    public function setEntity($request, $user) {
        foreach ($request as $key => $value) {
            $query = 'set' . ucfirst($key);
            $user->$query($value);
        }
        return $user;
    }

    public function getEntityById($class, $id) {
        $class = 'Models\\'.$class;
        $em = getEntityManager();
        $user = $em->find($class, $id);
        return $user;
    }

    public function removeEntity($class, $id) {
        $class = 'Models\\'.$class;
        $em = getEntityManager();
        $user = $em->find($class, $id);
        $em->remove($user);
        $em->flush();
        return true;
    }

    public function getEntityByName($class, $name) {
        $class = 'Models\\'.$class;
        $em = getEntityManager();
        if ($entity = $em->getRepository($class)->findOneBy(array('name' => $name))) {
            return $entity;
        }
        else {
            return false;
        }
    }

    public function getQuestionsByPoll($poll_id) {
        $class = 'Models\Questions';
        $em = getEntityManager();
        if ($entity = $em->getRepository($class)->findBy(array('id_poll' => $poll_id))) {
            return $entity;
        }
        else {
            return false;
        }
    }

    public function getAnswersByQuestion($question_id) {
        $class = 'Models\Answers';
        $em = getEntityManager();
        if ($entity = $em->getRepository($class)->findBy(array('id_question' => $question_id))) {
            return $entity;
        }
        else {
            return false;
        }
    }


}












