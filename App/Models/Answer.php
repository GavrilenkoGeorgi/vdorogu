<?php

namespace App\Models;

use PDO;
// use \Core\View;
use \App\Auth;
// use \App\Flash;
// use \App\Models\User;


/**
 * Answer model
 */
class Answer extends \Core\Model {
  /**
   * Error messages
   * 
   * @var array
   */
  public $errors = [];
  /**
   * Class constructor
   * @param array $data Initial property values
   * 
   * @return void
   */
  public function __construct($data = []) {
    foreach ($data as $key => $value) {
      $this->$key = $value;
    };
  }
  /**
  * Validate current property values, adding validation error messages
  * to the errors array property
  *
  * @return void
  */
  public function validate() {
    if ($this->answer =='') {
      $this->errors[] = 'Answer is required';
    }
  }
  /**
   * Add new question
   * 
   * @return boolean True if user was saved, false if not
   */
  public function add() {
    $this->user = Auth::getUser();
    $this->validate();
    // Add answer data
    if (empty($this->errors)) {
      $created_at = date("Y-m-d H:i:s");

      $sql = 'INSERT INTO answers (author_id, created_at, body)
              VALUES (:author_id, :created_at, :body)';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':author_id', $this->user->id, PDO::PARAM_STR);
      $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
      $stmt->bindValue(':body', $this->answer, PDO::PARAM_STR);

      $stmt->execute();
      $answerId = $db->lastInsertId();
      return $this->saveQandAIntermData($answerId, $this->questionId);
    }
    return false;
  }
  /**
   * Add data to intermediary tabel
   * 
   * @param integer $answer_id, $question_id Answer and question id
   * 
   * @return boolean True if saved, false otherwise
   */
  public static function saveQandAIntermData($answerId, $questionId) {
      // add intermediary table data
      $db = static::getDB();
      $sql = $db->prepare("INSERT INTO qna (`answer_id`, `question_id`) VALUES (:answerId, :questionId)");
      
      $sql->bindParam(':answerId', $answerId, PDO::PARAM_INT);
      $sql->bindParam(':questionId', $questionId, PDO::PARAM_INT);
      return $sql->execute();
  }
}
