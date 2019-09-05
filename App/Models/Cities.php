<?php

namespace App\Models;

use PDO;

/**
 * Cities controller
 */
class Cities extends \Core\Model {
  /**
   * Get cities list for the form
   * 
   * @return mixed
   */
  public function getAllCities() {
    $sql = 'SELECT city, county, province FROM cities';
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
  }
}