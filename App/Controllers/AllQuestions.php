<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Question;
use \App\Models\Answer;
/**
 * Show all questions to unregistered users
 * 
 * PHP version 7.0
 */

class AllQuestions extends \Core\Controller {
  /**
   * Questions index view
   * 
   * @return void
   */
  public function indexAction() {
    $unanswered = Question::getAllUnanswered();
    $questions = Question::getAll();
    $answers = Answer::getAll();
    $postsToShow = [];

    foreach ($questions as $question) {
      $post = [$question];
      // O(a*b)
      foreach ($answers as $answer) {
        if ($answer['for_id'] == $question['question_id']) {
          array_push($post, $answer);
        }
      }
      array_push($postsToShow, $post);
    }
    View::renderTemplate('Questions/index.html', [
      'posts' => $postsToShow,
      'questions' => $unanswered
    ]);
  }
}
