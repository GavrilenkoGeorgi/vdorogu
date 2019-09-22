<?php

namespace App\Models;

use PDO;

/**
 * Cities model
 */
class Cities extends \Core\Model {
  /**
   * Get cities list for the form
   * 
   * @return mixed
   */
  public function getAllCities() {
    $sql = 'SELECT city, provinces.province, counties.county
            FROM cities
            JOIN provinces ON cities.province = provinces.id
            JOIN counties ON provinces.county = counties.id
            ORDER BY city
            LIMIT 5';
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
  }
  /**
   * Search suggestions
   * 
   * @param string Two or more chars in the name of the city
   * 
   * @return array List of cities that have been found
   */
  public static function searchCity($search_param) {
    $sql = 'SELECT city, provinces.province, counties.county
      FROM cities
      JOIN provinces ON cities.province = provinces.id
      JOIN counties ON provinces.county = counties.id
      WHERE city LIKE :search_param';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue('search_param', $search_param.'%', PDO::PARAM_STR);
    $stmt->execute();
    $result = array();

    while($location = $stmt->fetch(PDO::FETCH_OBJ)) {
      array_push($result, $location->city .' '. $location->province .' '. $location->county);
    }
    return $result;
  }
}
