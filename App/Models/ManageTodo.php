<?php

namespace App\Models;

use PDO;

/**
 * Todo manager
 *
 * PHP version 7.0
 */
class ManageTodo extends \Core\Model {
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
  * Select todo by id
  *
  * @return object Single todo with given id
  */
  public static function getSome() {
    $id = $_GET['id'];
    $db = static::getDB();
    $sql=$db->prepare('SELECT * FROM todos WHERE id = :id');
    $sql->bindValue(':id', $id, PDO::PARAM_INT);
    $sql->execute();
    return $sql->fetch(PDO::FETCH_ASSOC);
  }
  /**
  * Get result for single page to display
  *
  * @return array Todos to display
  */
  public static function getPage($start, $limit, $sort, $order) {
    $db = static::getDB();
    $sql=$db->prepare('SELECT * FROM todos ORDER BY '. $sort .' '. $order . ' LIMIT :start, :limit');
    $sql->bindValue(':start', $start, PDO::PARAM_INT);
    $sql->bindValue(':limit', $limit, PDO::PARAM_INT);
    $sql->execute();
    $data=$sql->fetchAll();
    return $data;
  }
  /**
  * Get total number of records in db
  *
  * @return integer Total number of records
  */
  public static function getTodosCount() {
    $db = static::getDB();
    $count=$db->prepare("SELECT COUNT(id) FROM todos ORDER BY id");
    $count->execute();
    $count=$count->fetchColumn();
    return $count;
  }
  /**
  * Save the todo with the current property values
  *
  * @return boolean True if user was saved, false if not
  */
  public function save() {
    $this->validate();

    if (empty($this->errors)) {

      $sql = 'INSERT INTO todos (user_name, user_email, todo_text)
              VALUES (:user_name, :user_email, :todo_text)';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':user_name', $this->user_name, PDO::PARAM_STR);
      $stmt->bindValue(':user_email', $this->user_email, PDO::PARAM_STR);
      $stmt->bindValue(':todo_text', $this->todo_text, PDO::PARAM_STR);

      return $stmt->execute();
    }
    return false;
  }
  /**
  * Update todo
  *
  * @return boolean
  */
  public function update() {
    $this->validate();

    if (empty($this->errors)) {
      $sql = 'UPDATE todos SET user_name = :user_name, user_email = :user_email, todo_text = :todo_text WHERE id = :id';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':user_name', $this->user_name, PDO::PARAM_STR);
      $stmt->bindValue(':user_email', $this->user_email, PDO::PARAM_STR);
      $stmt->bindValue(':todo_text', $this->todo_text, PDO::PARAM_STR);
      $stmt->bindValue(':id', $_GET['id'], PDO::PARAM_STR);

      return $stmt->execute();
    }
    return false;
  }
  /**
  * Toggle completed state
  *
  * @return boolean
  */
  public function setCompleted() {
    if (empty($this->errors)) {
      $currentState = $this->checkCompletedState($_GET['id']);
      // toggle it
      if ($currentState == 1) {
        // boolean
        $currentState = 0;
      }
      else {
        $currentState = 1;
      }
      $sql = 'UPDATE todos SET completed = :completed WHERE id = :id';
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':completed', $currentState, PDO::PARAM_STR);
      $stmt->bindValue(':id', $_GET['id'], PDO::PARAM_STR);

      return $stmt->execute();
    }
    return false;
  }
  /**
  * Check completed state
  *
  * @param string $id id of todo in question
  * @return integer 1 or 0 completed or not
  */
  protected function checkCompletedState($id) {
    $sql = 'SELECT * FROM todos WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);

    $stmt->execute();
    $state = $stmt->fetch();
    return $state['completed'];
  }
  /**
  * Validate current property values, adding validation error messages
  * to the errors array property
  *
  * @return void
  */
  public function validate() {
    // Name
    $name = $this->prepareData($this->user_name);
    if ($name == '') {
      $this->errors[] = 'Name is required';
    }
    if (strlen($name) > 50) {
      $this->errors[] = 'Name is too long';
    }

    // email
    if (filter_var($this->user_email, FILTER_VALIDATE_EMAIL) === false) {
      $this->errors[] = 'Invalid email';
    }

    // todo text
    $text = $this->prepareData($this->todo_text);
    if ($text == '') {
      $this->errors[] = 'Todo is required';
    }
    if (strlen($text) > 500) {
      $this->errors[] = 'Todo text is too large';
    }
  }
  /**
   * Prepare data for validation
   * 
   * @return string
   */
  public function prepareData($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
}
