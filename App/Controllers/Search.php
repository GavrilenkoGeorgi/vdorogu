<?php
namespace App\Controllers;
use \App\Models\Cities;
use \Core\View;

/**
 * Search suggestions controller
 */
class Search extends \Core\Controller {
  /**
   * Search cities
   * 
   * @return void
   */
  public function cityAction() {
    $list = Cities::searchCity($_GET['query']);
    header('Content-Type: application/json');
    echo json_encode(array_values($list));
  }
}