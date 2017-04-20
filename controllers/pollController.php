<?php



namespace Oscars\Controllers;

use controller;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Models\Answers;
use Models\Media;
use Models\Model;
use Models\Poll;
use Models\Profile;
use Models\Questions;
use Models\QuestionsTypes;
use \Models\User;
use Models\User_Answer;
use Models\User_Poll;

include($_SERVER["DOCUMENT_ROOT"] . "/models/User.php");
include($_SERVER["DOCUMENT_ROOT"] . "/database.php");
include($_SERVER["DOCUMENT_ROOT"] . "/controllers/controller.php");
include($_SERVER["DOCUMENT_ROOT"] . "/entityManager.php");

class pollController extends controller
{
    /* Charge la page listant tous les sondages */
    function index() {

        session_start();
        if (!array_key_exists('mdp', $_SESSION)) {
            header('Location:/');
        }

        $class = new Model();
        $listPolls = $class->getAll('Poll');

        $twig = $this->loadTwig();
        $template = $twig->load('polls.html.twig');
        echo $template->render(array('listPolls' => $listPolls, 'session' => $_SESSION));

    }

    /* Charge la page de création de nouveau sondage */
    function addPollPage() {

        session_start();
        if (!array_key_exists('mdp', $_SESSION)) {
            header('Location:/');
        }

        $twig = $this->loadTwig();
        $template = $twig->load('addPoll.html.twig');
        echo $template->render(array());

    }

    function submitPoll() {
        session_start();

        $user_id = $_SESSION['id'];
        $poll_id = array_shift($_POST);
        $answers = [];
        foreach ($_POST as $elmnt) {
            array_push($answers, $elmnt);
        }

        $user = new User();
        $user->setId($user_id);

        $answers = array_filter($answers, 'is_numeric');

        if ($user->addAnswers($answers) && $user->addPoll($poll_id)) {
            header('Location:/success');
        }

    }

    /* Sondage complété */
    function success() {
        $twig = $this->loadTwig();
        $template = $twig->load('success.html.twig');
        echo $template->render(array());
    }

    function pollResults() {
        session_start();
        if ($_SESSION['profile'] != 1) {
            header('Location:/error');
            return false;
        }
        $user_id = $_SESSION['id'];
        $url_id = $_SERVER['REQUEST_URI'];
        $poll_id = preg_replace('/[^0-9]+/', '', $url_id);

        $poll = new Poll();
        $poll = $poll->getEntityById('Poll', $poll_id);
        $poll->setId($poll_id);
        $pollVotes = $poll->nbrVotes();

        $questions = new Questions();
        $questions = $questions->getQuestionsByPoll($poll_id);

        $types = new QuestionsTypes();
        $types = $types->getAll('QuestionsTypes');

        $listAnswers = [];
        $answers = new Answers();
        for ($i=0; $i<count($questions); $i++) {
            $listAnswers[$i] = $answers->getAnswersByQuestion($questions[$i]->id);
        }

        $answersResults = [];
        for ($i=0; $i<count($listAnswers); $i++) {
            for ($y=0; $y<count($listAnswers[$i]); $y++) {
                $answersResults[$i][$y]['content'] = $listAnswers[$i][$y]->content;
                $answers->setId($listAnswers[$i][$y]->id);
                $nbrVotes = count($answers->getUsers());
                $answersResults[$i][$y]['votes'] = $nbrVotes;
                $progressbar = ($nbrVotes / $pollVotes[0]) * 100;
                $progressbar = floor($progressbar);
                $answersResults[$i][$y]['progressbar'] = $progressbar;
            }
        }

        $twig = $this->loadTwig();
        $template = $twig->load('results.html.twig');
        echo $template->render(array('listQuestions' => $questions, 'poll' => $poll, 'listTypes' => $types, 'listAnswers' => $answersResults));
    }

    /* Charge la page de vote à un sondage */
    function fillPoll() {
        session_start();
        $user_id = $_SESSION['id'];
        $url_id = $_SERVER['REQUEST_URI'];
        $poll_id= preg_replace('/[^0-9]+/', '', $url_id);

        $poll = new Poll();
        $poll = $poll->getEntityById('Poll', $poll_id);

        $questions = new Questions();
        $questions = $questions->getQuestionsByPoll($poll_id);

        $types = new QuestionsTypes();
        $types = $types->getAll('QuestionsTypes');

        $user = new User();
        $user->setId($user_id);
        if ($user->getPollById($poll_id)) {
            $alreadyChecked = true;
        }
        else {
            $alreadyChecked = false;
        }

        $listAnswers = [];
        $answers = new Answers();
        for ($i=0; $i<count($questions); $i++) {
            $answer = $answers->getAnswersByQuestion($questions[$i]->id);
            array_push($listAnswers, $answer);
        }

        $twig = $this->loadTwig();
        $template = $twig->load('fillPoll.html.twig');
        echo $template->render(array('listQuestions' => $questions, 'poll' => $poll, 'listTypes' => $types, 'listAnswers' => $listAnswers, 'alreadyChecked' => $alreadyChecked));
    }

    /* Suppression d'un sondage */
    function deletePoll($id) {
        $id = str_replace("'", "", $id);
        $id = substr($id,1);
        $model = new Poll();
        $poll = $model->getEntityById('Poll',$id);
        if (is_object($poll)) {
            $poll->removeEntity('Poll', $id);
            header('Location:/polls');
        }
        else {
            echo "Sondage introuvable";
        }
    }

    /* Insertion en base de données d'un nouveau sondage */
    function addPoll() {
        $db = loadDB();
        $request['name'] = mysqli_real_escape_string($db, $_POST['pollname']);
        $request['description'] = mysqli_real_escape_string($db, $_POST['polldesc']);
        $questions = explode(";,", $_POST['questions']);
        $questions = str_replace(";", "", $questions);

        $questions_type = explode(",", mysqli_real_escape_string($db, $_POST['questions_type']));
        $questions_type = array_filter($questions_type);
        $questions_type = array_slice($questions_type, 0);
        $answers = explode(",,,,,", $_POST['answers']);
        for ($i=0; $i<count($answers); $i++) {
            $answers[$i] = explode(";,", $answers[$i]);
            for ($y=0; $y<count($answers[$i]); $y++) {
                $answers[$i][$y] = str_replace(";", "", $answers[$i][$y]);
            }
        }
        $answers[0][0] = str_replace(",,,,", "", $answers[0][0]);

        $poll = new Poll($request);
        if ($poll->addPoll()) {
            $poll_id = $poll->getId();
        }
        else {
            echo "Erreur d'enregistrement du sondage";
            return false;
        }

        for ($i=0; $i<count($questions); $i++) {
            $objQuestion = new Questions();
            $objQuestion->setContent(trim($questions[$i], ","));
            $type = new QuestionsTypes();
            $objQuestion->addQuestion();
            $poll = new Poll();
            $objQuestion->setIdPoll($poll->getEntityById('Poll', $poll_id));
            $objQuestion->setIdType($type->getEntityById('QuestionsTypes', $questions_type[$i]));
            $result = $objQuestion->editQuestion();
            $question_id = $objQuestion->getId();
            $question_id_type = $objQuestion->getIdType();
            if ($question_id_type != 3) {
                for ($y=0; $y<=count($answers[$i])-1; $y++) {
                    $objAnswer = new Answers();
                    $objAnswer->setContent($answers[$i][$y]);
                    $objAnswer->addAnswer();
                    $objQuestion = new Questions();
                    $objAnswer->setIdQuestion($objQuestion->getEntityById('Questions', $question_id));
                    $result = $objAnswer->editAnswer();
                }
            }
        }

        echo 'OK';

    }

}