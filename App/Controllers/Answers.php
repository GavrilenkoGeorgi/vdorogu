<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Question;
use \App\Models\Answer;

/**
 * User answers controller
 * 
 * PHP version 7.0
 */
class Answers extends Authenticated {
  /**
   * Before filter - called before each action method
   * 
   * @return void
   */
  protected function before() {
    parent::before();
    $this->user = Auth::getUser();
  }
  /**
   * Show single question to answer
   * 
   * @return void
   */
  public function indexAction() {
    // get the question to display
    $question = Question::getById($_GET['id']);
    View::renderTemplate('Answers/show.html', [
      'question' => $question
    ]);
  }
  /**
   * Add answer
   * 
   * @return void
   */
  public function addAction() {
    $answer = new Answer($_POST);
    if ($answer->add()) {
      // set question as answered
      Question::setAnswered($_POST['questionId']);
      Flash::addMessage('Відповідь додано.');
      $this->redirect('/allquestions');
    } else {
      // with the help of hidden input field
      $question = Question::getById($_POST['questionId']);
      View::renderTemplate('answers/show.html', [
        'answer' => $answer,
        'question' => $question
      ]);
    }
  }
  /**
   * Delete answer
   * 
   * @return void
   */
  public function deleteAction() {
    $id = $_GET['id'];
    if (Answer::delete($id)) {
      Flash::addMessage('Відповідь видалено.', Flash::SUCCESS);
    } else {
      Flash::addMessage('Відповідь не видалено.', Flash::WARNING);
    }
    $this->redirect('/allquestions');
  }
}
