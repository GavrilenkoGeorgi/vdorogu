<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Cities;
use \App\Auth;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller {
  /**
   * Before filter - called before each action method
   * 
   * @return void
   */
  protected function before() {
    $this->user = Auth::getUser();
  }
  /**
  * Show the index page with the cities list
  *
  * @return void
  */
  public function indexAction() {
    // get cities for search form
    $cities = new Cities();
    $citiesList = $cities->getAllCities();
    View::renderTemplate('Home/index.html', [
      'cities' => $citiesList
    ]);
  }
}
