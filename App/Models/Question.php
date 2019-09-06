<?php

namespace App\Models;

use PDO;
// use \Core\View;
use \App\Auth;
// use \App\Flash;
// use \App\Models\User;


/**
 * Question model
 */
class Question extends \Core\Model {
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
    if ($this->subject == '') {
      $this->errors[] = 'Subject is required';
    }
    if ($this->body == '') {
      $this->errors[] = 'Question is required';
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
    if (empty($this->errors)) {
      $created_at = date("Y-m-d H:i:s");

      $sql = 'INSERT INTO questions (author_id, created_at, subject, body)
              VALUES (:author_id, :created_at, :subject, :body)';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':author_id', $this->user->id, PDO::PARAM_STR);
      $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
      $stmt->bindValue(':subject', $this->subject, PDO::PARAM_STR);
      $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);

      return $stmt->execute();
    }
    return false;
  }
  /**
   * Get all questions
   *
   * @return mixed All current questions object, null otherwise
   */
  public static function getAll() {
    $sql = 'SELECT
              questions.id, created_at, subject,
              body, answered,
              name, last_name
            FROM questions
            INNER JOIN users
            ON questions.author_id = users.id;';
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
  }
  /**
   * Show single question
   *
   * @return mixed Question object if found, null otherwise
   */
  public static function getById($id) {
    $sql = 'SELECT * FROM questions
            WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
    $stmt->execute();

    return $stmt->fetch();
  }
  /**
   * Set question as answered
   *
   * @return boolean True of set, false otherwise
   */
  public static function setAnswered($id) {
    // $sql = 'UPDATE questions SET answered = :answered'
    $db = static::getDB();
    $sql = "UPDATE questions SET answered = :answered WHERE id = :id";
    // $stmt = $db->prepare("UPDATE questions SET answered = :answered WHERE id = :id");
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':answered', true, PDO::PARAM_BOOL);
    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
  }
}
