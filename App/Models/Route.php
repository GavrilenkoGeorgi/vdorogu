<?php

namespace App\Models;

use PDO;
use DateTime;
use \App\Auth;
use \App\Mailer;
use \Core\View;
use Exception;

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
  public $errors = array();
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
    $this->errors = []; // ?

    // Origin
    if (empty($this->routeOrigin) || !is_string($this->routeOrigin)) {
      $this->errors[] = 'Місто відправлення обов\'язкове.';
    } else if (strlen($this->routeOrigin) > 245) {
      $this->errors[] = 'Перевірте правильність місця відправлення.';
    }

    // $val = trim(htmlspecialchars($val));
    // Destination
    if (empty($this->routeDestination) || !is_string($this->routeDestination)) {
      $this->errors[] = 'Місце призначення обов\'язкове.';
    } else if (strlen($this->routeDestination) > 245) {
      $this->errors[] = 'Перевірте правильність місця призначення.';
    }

    // Pax capacity
    if (empty($this->paxCapacity) || $this->paxCapacity > 5) {
      $this->errors[] = 'Кількість пасажирів повинна бути від 1 до 5.';
    }

    // Create departure date
    if (isset($this->createDepartureDate)) {
      $date = DateTime::createFromFormat("Y-m-d", $this->createDepartureDate);
      $now = new DateTime();
    }
    $validDate = $date !== false && !array_sum($date::getLastErrors());
    if (!$validDate) {
      $this->errors[] = 'Перевірте дату відправлення.';
    } else if ($date < $now){
      $this->errors[] = 'Ви не можете планувати поїздку в минулому, перевірте дату відправлення.';
    }
  }
  /**
  * Validate search route values, adding validation error messages
  * to the errors array property
  *
  * @return void
  */
  public function validateSearchRoute() {
    $this->errors = []; // ?

    // Origin
    if (empty($this->routeOrigin) || !is_string($this->routeOrigin)) {
      $this->errors[] = 'Місто відправлення обов\'язкове.';
    } else if (strlen($this->routeOrigin) > 245) {
      $this->errors[] = 'Перевірте правильність місця відправлення.';
    }

    // Destination
    if (!empty($this->routeOrigin) && strlen($this->routeDestination) > 245) {
      $this->errors[] = 'Перевірте правильність місця призначення.';
    }

    // Search departure date
    if (!empty($this->searchDepartureDate)) {
      $date = DateTime::createFromFormat("Y-m-d", $this->searchDepartureDate);
      $validDate = $date !== false && !array_sum($date::getLastErrors());
      if (!$validDate) {
        $this->errors[] = 'Перевірте дату відправлення.';
      }
    }
  }
  /**
   * Create route as a driver
   * 
   * @return boolean True if created, false otherwise
   */
  public function create() {
    $this->validate();
    $this->user = Auth::getUser();

    if (empty($this->errors)) {
      $created_at = date("Y-m-d H:i:s");

      $sql = 'INSERT INTO routes (origin, destination, created_at, departure, driver_id, pax_capacity)
              VALUES (:origin, :destination, :created_at, :departure, :driver_id, :pax_capacity)';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':origin', $this->routeOrigin, PDO::PARAM_STR);
      $stmt->bindValue(':destination', $this->routeDestination, PDO::PARAM_STR);
      $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
      $stmt->bindValue(':departure', $this->createDepartureDate, PDO::PARAM_STR);
      $stmt->bindValue(':driver_id', $this->user->id, PDO::PARAM_INT);
      $stmt->bindValue(':pax_capacity', $this->paxCapacity, PDO::PARAM_INT);

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

    $sql = 'SELECT routes.id,
                driver_id,
                created_at,
                origin,
                destination,
                departure,
                pax_capacity,
                name,
                last_name,
           (SELECT COUNT(route_id) FROM vdorogu_db.pax_list WHERE route_id = routes.id) AS occupied
            FROM vdorogu_db.routes
            INNER JOIN vdorogu_db.users
            ON routes.driver_id = users.id';
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
    
    $stmt->execute();
    return $stmt->fetchAll();
  }
  /**
   * Get passengers list for the route with given id
   * 
   * @param integer $routeId Route id
  */
  public static function getRoutePassengers($id) {
    $sql = 'SELECT name,
                  last_name
            FROM pax_list
            INNER JOIN users ON pax_list.passenger_id = users.id
            WHERE pax_list.route_id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

    $stmt->execute();
    return $stmt->fetchAll();
  }

  /**
   * Get all driver user routes by user id
   * 
   * @param integer $id Id of the user whose routes are needed
   * 
   * @return mixed User routes object
   */
  public static function driverUserRoutes($id) {
    $sql = 'SELECT routes.id,
                  driver_id,
                  created_at,
                  origin,
                  destination,
                  departure,
                  pax_capacity,
                  name,
                  last_name, (SELECT COUNT(route_id) FROM vdorogu_db.pax_list WHERE route_id = routes.id) AS occupied
              FROM vdorogu_db.routes
              INNER JOIN vdorogu_db.users
              ON routes.driver_id = users.id WHERE driver_id = :id';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

    $stmt->execute();
    return $stmt->fetchAll();
  }
  /**
   * Get all passenger user routes by user id
   * 
   * @param integer $id Id of the user whose routes are needed
   * 
   * @return mixed User routes object
   */
  public static function passengerUserRoutes($id) {
    $sql = 'SELECT routes.id,
                  departure,
                  origin, destination,
                  pax_capacity,
                  name as driver_name,
                  last_name as driver_last_name, (SELECT COUNT(route_id) FROM vdorogu_db.pax_list WHERE route_id = routes.id) AS occupied
            FROM pax_list
            JOIN routes ON routes.id = pax_list.route_id
            JOIN users as drivers ON drivers.id = routes.driver_id
            WHERE passenger_id = :id';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

    $stmt->execute();
    return $stmt->fetchAll();
  }
  /**
   * Get routes in which user acts as a passenger
   * 
   * @return array Array with routes ids
   */
  public static function getUserRoutesIds($id) {
    $sql = 'SELECT route_id
            FROM pax_list
            WHERE passenger_id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
   * Find route by origin and destination
   * 
   * @return boolean True if found, false otherwise
   */
  public function find() {
    $this->validateSearchRoute();
    if (empty($this->errors)) {
      $sql = 'SELECT routes.id,
                      driver_id,
                      created_at,
                      origin,
                      destination,
                      departure,
                      pax_capacity,
                      name,
                      last_name, (SELECT COUNT(route_id) FROM vdorogu_db.pax_list WHERE route_id = routes.id) AS occupied
        FROM vdorogu_db.routes
        INNER JOIN vdorogu_db.users
        ON routes.driver_id = users.id
        WHERE origin = :origin';
        if (!empty($this->routeDestination)) {
          $sql .= ' AND destination = :destination';
        }
        if (!empty($this->searchDepartureDate)) {
          $sql .= ' AND departure = :departure';
        }
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':origin', $this->routeOrigin, PDO::PARAM_STR);
      if (!empty($this->routeDestination)) {
        $stmt->bindValue(':destination', $this->routeDestination, PDO::PARAM_STR);
      }
      if (!empty($this->searchDepartureDate)) {
        $stmt->bindValue(':departure', $this->searchDepartureDate, PDO::PARAM_STR);
      }
      $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

      $stmt->execute();
      return $stmt->fetchAll();
    } return false;
  }
  /**
   * Get route by id
   * 
   * @param integer $id Route id
   * 
   * @return mixed Route object or false if not found
   */
  public static function getById($id) {
    $sql = 'SELECT routes.id,
        driver_id,
        created_at,
        origin,
        destination,
        departure,
        pax_capacity,
        name,
        last_name
    FROM vdorogu_db.routes
    INNER JOIN vdorogu_db.users
    ON routes.driver_id = users.id
    WHERE routes.id = :id';
    $db = static::getDb();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

    $stmt->execute();
    return $stmt->fetch();
  }
  /**
   * Check if the dates are not overlapping
   * Same date for now will suffice
   * 
   * @param integer $routeId, $passId Route and passenger id
   * 
   * @return mixed Routes array if there
   */
  public static function passengerRoutesThisDate($routeId, $passengerId) {
    // get current route date and check if there are
    // any records with the same date for current user
    // in user routes
    $date = Route::getDate($routeId);

    if ($date) {
      $sql = 'SELECT passenger_id, route_id, departure, routes.id
              FROM pax_list
              INNER JOIN routes
              ON route_id = routes.id
              WHERE departure = :departure
              AND passenger_id = :passenger_id';
      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':departure', $date['departure'], PDO::PARAM_STR);
      $stmt->bindValue(':passenger_id', $passengerId, PDO::PARAM_INT);

      $stmt->execute();
      return $stmt->fetchAll();
    }
  }
  /**
   * Get route date
   * 
   * @param integer $routeId Route id to query
   * 
   * @return string Route date with given id
   */
  public static function getDate($routeId) {
    $sql = 'SELECT departure
            FROM routes
            WHERE id = :id';
    $db= static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $routeId, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetch();
  }
  /**
   * Check if it's not the driver acting as a passenger
   * 
   * @param integer $route_id, $passId Route and passenger id
   * 
   * @return boolean True if ok, false orherwise
   */
  public static function userIsDriver($routeId, $passId) {
    $sql = 'SELECT driver_id
            FROM routes
            WHERE id = :route_id';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':route_id', $routeId, PDO::PARAM_INT);
    $stmt->execute();
    $driverId = $stmt->fetch();

    if (strcmp($driverId['driver_id'], $passId)) {
      return true;
    } else {
      return false;
    }
  }
  /**
   * Check if seats are availale
   * 
   * @param integer $route_id, $passenger_id Route and passenger id
   * 
   * @return mixed Integer i.e. how many seats are available, false otherwise
   */
  public static function getEmptySeats($routeId, $passId) {

    $sql = 'SELECT pax_capacity
            FROM routes
            WHERE id = :routeId';
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':routeId', $routeId, PDO::PARAM_INT);

    // current route capacity
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $routePaxCapacity = $stmt->fetch();
    $routePaxCapacity = intval($routePaxCapacity['pax_capacity']);

    // count current pax amount for this route if any
    $sql = 'SELECT COUNT(route_id)
            FROM pax_list
            WHERE route_id = :routeId';

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':routeId', $routeId, PDO::PARAM_INT);

    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $currentPaxAmount = $stmt->fetch();
    $currentPaxAmount = intval($currentPaxAmount['COUNT(route_id)']);

    // get vacant seats
    if (($routePaxCapacity - $currentPaxAmount) == 0) {
      return 0; // no seats left
    } else {
      // how many seats left
      return $routePaxCapacity - $currentPaxAmount;
    }
  }
  /** 
   * Add passenger to the route
   * 
   * @param integer $routeId, $passengerId Route and passenger ids
   * 
   * @return boolean True if added, false otherwise
  */
  public static function addPax($routeId, $passengerId) {
    $sql = 'INSERT INTO pax_list (route_id, passenger_id)
            VALUES (:route_id, :passenger_id)';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':route_id', $routeId, PDO::PARAM_INT);
    $stmt->bindValue(':passenger_id', $passengerId, PDO::PARAM_INT);

    return $stmt->execute();
  }
  /**
   * Remove passenger from the route
   * 
   * @param integer $routeId, $passengerId Route and passenger ids
   * 
   * @return boolean True if removed, false otherwise
   */
  public static function removePassenger($routeId, $passengerId) {
    $sql = 'DELETE FROM pax_list
            WHERE route_id = :route_id
            AND passenger_id = :passenger_id';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':route_id', $routeId, PDO::PARAM_INT);
    $stmt->bindValue(':passenger_id', $passengerId, PDO::PARAM_INT);

    return $stmt->execute();
  }
  /**
   * Send route instructions in an email to the user
   * 
   * @return void
   */
  public static function sendPassengerNotification($email, $data) {
    // $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;
    $url = 'Hi!';
    // $email = '???';

    // var_dump($this);
    var_dump($data);
    var_dump($email);
    // echo 'sending email';
    $text = View::getTemplate('Routes/notify_pass.txt', $data = (array) $data);
    $html = View::getTemplate('Routes/notify_pass.html', $data = (array) $data);
    Mailer::send($email, 'Пасажира додано', $text, $html);
  }
}
