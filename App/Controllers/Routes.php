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
   * Create route
   * 
   * @return void
   */
  public function createAction() {
    $route = new Route($_POST);
    if ($route->create()) {
      Flash::addMessage('Route added.');
      $this->redirect('/routes');
    } else {
      Flash::addMessage('Route was not added.');
      $this->redirect('/');
    }
  }
  /**
   * Find route
   * 
   * @return void
   */
  public function findAction() {
    $routes = new Route($_POST);
    $list = $routes->find();
    if ($list) {
      Flash::addMessage('Routes found: ');
      View::renderTemplate('Routes/index.html', [
        'routes' => $list
      ]);
    } else {
      Flash::addMessage('Not a single route found.');
      $this->redirect('/');
    }
  }
  /**
   * Delete route
   * 
   * @return void
   */
  public function deleteAction() {
    // $route = new Route($_POST);
    // echo 'Deleting';
    // echo $_GET['id'];
    // var_dump($_GET);
    if (Route::delete($_GET['id'])) {
      Flash::addMessage('Route deleted.');
      $this->redirect('/profile/show');
    } else {
      Flash::addMessage('Something went wrong while deleting your rooute, try again later.');
      $this->redirect('/profile/show');
    }
  }
}