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
   * @return boolean True if added, false otherwise
   */
  public function addAction() {
    $question = new Question($_POST);
    if ($question->add()) {
      Flash::addMessage('Question added.');
      $this->redirect('/allquestions');
    } else {
      View::renderTemplate('Questions/add.html', [
        'question' => $question
      ]);
    }
  }
}
