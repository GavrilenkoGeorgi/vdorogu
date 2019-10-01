<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
// use \App\Models\Route;

/**
 * Profile controller
 */
class Profile extends Authenticated {
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
   * Show complete profile
   * 
   * @return void
   */
  public function showAction() {
    // $driverUserRoutes = Route::driverUserRoutes($this->user->id);
    // $passengerUserRoutes = Route::passengerUserRoutes($this->user->id);
    View::renderTemplate('Profile/show.html', [
      'user' => $this->user,
      // 'routes' => $driverUserRoutes,
      // 'passenger_routes' => $passengerUserRoutes
    ]);
  }
  /**
   * Show the form for editing the profile
   * 
   * @return void
   */
  public function editAction() {
    View::renderTemplate('Profile/edit.html', [
      'user' => $this->user
    ]);
  }
  /**
   * Update the profile
   * 
   * @return void
   */
  public function updateAction() {
    if ($this->user->updateProfile($_POST)) {
      Flash::addMessage('Changes saved.');
      $this->redirect('/profile/show');
    } else {
      View::renderTemplate('Profile/edit.html', [
        'user' => $this->user
      ]);
    }
  }
  /**
   * Delete user profile
   * 
   * @return void
   */
  public function deleteAction() {
    if ($this->user->deleteProfile($this->user->id)) {
      Flash::addMessage('Profile deleted.');
      $this->redirect('/');
    } else {
      Flash::addMessage('Something went wrong while deleting your profile, try again later.');
      $this->redirect('/profile/show');
    }
  }
}
