<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Question;

/**
 * User questions
 * 
 * PHP version 7.0
 */
class Questions extends Authenticated {
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
   * Show add question form
   * 
   * @return void
   */
  public function indexAction() {
    View::renderTemplate('Questions/add.html');
  }
  /**
   * Add question
   * 
   * @return void
   */
  public function addAction() {
    $question = new Question($_POST);
    if ($question->add()) {
      Flash::addMessage('Питання додано.', Flash::SUCCESS);
      $this->redirect('/allquestions');
    } else {
      View::renderTemplate('Questions/add.html', [
        'question' => $question
      ]);
    }
  }
  /**
   * Delete question
   * 
   * @return void
   */
  public function deleteAction() {
    $id = $_GET['id'];
    if (Question::delete($id)) {
      Flash::addMessage('Питання видалено.', Flash::SUCCESS);
    } else {
      Flash::addMessage('Питання не видалено.', Flash::WARNING);
    }
    $this->redirect('/allquestions');
  }
}
