<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Route;

/**
 * User travel routes controller
 */
class Routes extends Authenticated {
  /**
   * Before filter - called before each action method
   * 
   * @return void
   */
  protected function before() {
    parent::before();
    $this->user = Auth::getUser();
  }
  /**
   * Index action
   * 
   * @return void
   */
  public function indexAction() {
    $allRoutes = Route::getAll();
    View::renderTemplate('Routes/index.html', [
      'routes' => $allRoutes
    ]);
  }
  /**
  * Show user routes
  *
  * @return void
  */
  public function showAction() {
    $driverUserRoutes = Route::driverUserRoutes($this->user->id);
    $passengerUserRoutes = Route::passengerUserRoutes($this->user->id);
    View::renderTemplate('Routes/show.html', [
      'routes' => $driverUserRoutes,
      'passenger_routes' => $passengerUserRoutes
    ]);
  }
  /**
   * Create route new route as a driver
   * 
   * @return void
   */
  public function createAction() {
    $route = new Route($_POST);
    if ($route->create()) {
      Flash::addMessage('Route added.');
      $this->redirect('/routes');
    } else {
      $errors = '';
      foreach ($route->errors as $value) {
        $errors .= $value . ' ';
      }
      Flash::addMessage($errors, 'danger');
      View::renderTemplate('Home/index.html', [
        'createRouteData' => $route
      ]);
    }
  }
  /**
   * Find route by origin and destination
   * Form action in the main page
   * 
   * @return void
   */
  public function findAction() {
    $route = new Route($_POST);
    $list = $route->find();
    if (!empty($list)) {
      Flash::addMessage('Routes found: ');
      View::renderTemplate('Routes/index.html', [
        'routes' => $list
      ]);
    } else if (!empty($route->errors)) {
      $errors = '';
      foreach ($route->errors as $value) {
        $errors .= $value . ' ';
      }
      Flash::addMessage($errors, 'danger');
      View::renderTemplate('Home/index.html', [
        'searchRouteData' => $route
      ]);
    } else {
      Flash::addMessage('Not a single route found.');
      View::renderTemplate('Home/index.html', [
        'searchRouteData' => $route
      ]);
    }
  }
  /**
   * Delete route
   * 
   * @return void
   */
  public function deleteAction() {
    if (Route::delete($_GET['id'])) {
      Flash::addMessage('Route deleted.');
      $this->redirect('/profile/show');
    } else {
      Flash::addMessage('Something went wrong while deleting your route, try again later.');
      $this->redirect('/profile/show');
    }
  }
  /**
   * View trip details and confirm
   * adding user to passengers list
   *
   * @return void
   */
  public function rideAction() {
    $route = Route::getById($_GET['routeId']);
    $passengersList = Route::getRoutePassengers($_GET['routeId']);
    View::renderTemplate('Routes/ride.html', [
      'route' => $route,
      'passengers' => $passengersList
    ]);
  }
  /**
   * View route details
   *
   * @return void
   */
  public function detailsAction() {
    $route = Route::getById($_GET['routeId']);
    $passengersList = Route::getRoutePassengers($_GET['routeId']);
    View::renderTemplate('Routes/details.html', [
      'route' => $route,
      'passengers' => $passengersList
    ]);
  }
  /**
   * Remove passenger from route
   * 
   * @return void
   */
  public function removePassengerAction() {
    $routeId = $_GET['routeId'];
    $passengerId = $this->user->id;
    if(Route::removePassenger($routeId, $passengerId)) {
      Flash::addMessage('You canceled this trip.');
      $this->redirect('/profile/show');
    } else {
      Flash::addMessage('Something went wrong, try again later.');
      $this->redirect('/profile/show');
    }
  }
  /**
   * Add passenger to the route
   * 
   * @return void
   */
  public function addPaxAction() {
    $routeId = $_GET['routeId'];
    $passengerId = $this->user->id;
    // user can't have two trips in one day
    // more date checking needed
    $routesThisDay = Route::passengerRoutesThisDate($routeId, $passengerId);
    // check if route have enough places left
    $emptySeats = Route::getEmptySeats($routeId, $passengerId);
    // check if user is not the driver of this route
    $userIsDriver = Route::userIsDriver($routeId, $passengerId);

    if (!$userIsDriver) {
      Flash::addMessage('Ви водій.');
    } else if ($emptySeats == 0) {
      Flash::addMessage('Місць не залишилося.');
    } else if (!empty($routesThisDay)) {
      // if it is the same date
      Flash::addMessage('Вибачте, але зараз ви не можете здійснити дві поїздки за один день.');
    } else {
      Route::addPax($routeId, $passengerId);
      // Email stuff
      $route = Route::getById($_GET['routeId']);
      Route::sendPassengerNotification($this->user->email, $route);
      // Confirmation
      Flash::addMessage('Пасажира додано.');
    }
    $this->redirect('/routes');
  }
}
