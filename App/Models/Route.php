<?php

namespace App\Models;

use PDO;
use \App\Auth;

/**
 * Route model
 *
 * PHP version 7.0
 */
class Route extends \Core\Model {
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
   * Create route
   * 
   * @return boolean True if created, false otherwise
   */
  public function create() {
    // Validate!
    $this->user = Auth::getUser();

    if (empty($this->errors)) {
      $created_at = date("Y-m-d H:i:s");

      $sql = 'INSERT INTO routes (origin, destination, created_at, departure, driver_id, pax_amount)
              VALUES (:origin, :destination, :created_at, :departure, :driver_id, :pax_amount)';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':origin', $this->routeOrigin, PDO::PARAM_STR);
      $stmt->bindValue(':destination', $this->routeDestination, PDO::PARAM_STR);
      $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
      $stmt->bindValue(':departure', $this->departureDate, PDO::PARAM_STR);
      $stmt->bindValue(':driver_id', $this->user->id, PDO::PARAM_INT);
      $stmt->bindValue(':pax_amount', $this->paxAmount, PDO::PARAM_INT);

      return $stmt->execute();
    }
    return false;
  }
  /**
   * Get all current routes
   * 
   * @return mixed All available routes
   */
  public static function getAll() {
    $sql = 'SELECT * FROM routes';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
    $stmt->execute();
    return $stmt->fetchAll();
  }
  /**
   * Get all user routes by user id
   * 
   * @param integer $id Id of the user whose routes are needed
   * 
   * @return mixed User routes object
   */
  public static function getUserRoutes($id) {
    $sql = 'SELECT * FROM routes WHERE driver_id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
    $stmt->execute();
    return $stmt->fetchAll();
  }
  /**
   * Delete user created route
   * 
   * @param integer $id Id of the route to delete
   * 
   * @return boolean True if deleted, false otherwise
   */
  public static function delete($id) {
    $sql = 'DELETE FROM routes
            WHERE id = :id';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
  }
  /**
   * Find route
   * 
   * @return boolean True if found, false otherwise
   */
  public function find() {
    // Validate!
    $sql = 'SELECT * FROM mvclogin.routes WHERE origin = :origin AND destination = :destination';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':origin', $this->routeOrigin, PDO::PARAM_STR);
    $stmt->bindValue(':destination', $this->routeDestination, PDO::PARAM_STR);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
    $stmt->execute();
    return $stmt->fetchAll();
  }
  /**
   * Delete selected route
   */
  /**
   * Add data to intermediary table
   * 
   * @param integer $route_id, $passenger_id Route and passenger id
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
