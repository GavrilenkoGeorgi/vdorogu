<?php

namespace App\Models;

use PDO;
use \App\Auth;

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
      $this->errors[] = 'Потрібна відповідь.';
    }
  }
  /**
   * Get all answers
   * 
   * @return array Array of answers
   */
  public static function getAll() {
    $sql = 'SELECT answers.id,
                  answers.author_id,
                  answers.for_id,
                  answers.subject,
                  answers.body,
                  users.name,
                  users.last_name
      FROM qna
      JOIN answers ON answers.id = qna.answer_id
      JOIN users ON users.id = answers.author_id;';
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
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

      $sql = 'INSERT INTO answers (author_id, created_at, body, for_id)
              VALUES (:author_id, :created_at, :body, :for_id)';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':author_id', $this->user->id, PDO::PARAM_STR);
      $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
      $stmt->bindValue(':body', $this->answer, PDO::PARAM_STR);
      $stmt->bindValue(':for_id', $this->questionId, PDO::PARAM_INT);

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
  /**
   * Delete answer
   * 
   * @return boolean True if deleted, false otherwise
   */
  public static function delete($id) {
    $db = static::getDB(); //?
    $sql = $db->prepare("DELETE FROM answers
                         WHERE id = :id");
    $sql->bindParam(':id', $id, PDO::PARAM_INT);
    return $sql->execute();
  }
}
