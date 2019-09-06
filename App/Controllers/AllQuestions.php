<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Question;
/**
 * All questions to show for unregistered users
 * 
 * PHP version 7.0
 */

class AllQuestions extends \Core\Controller {
  /**
   * Rules index view
   * 
   * @return void
   */
  public function indexAction() {
    $allQuestions = Question::getAll();
    View::renderTemplate('Questions/index.html', [
      'allQuestions' => $allQuestions
    ]);
  }
}
