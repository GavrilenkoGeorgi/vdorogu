<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

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
    View::renderTemplate('Profile/show.html', [
      'user' => $this->user
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
    // $user = $this->user; //? What's this?
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
   * @return boolean True if deleted, false otherwise
   */
  public function deleteAction() {
    echo 'deleting user profile with id: ';
    echo $this->user->id;
    if ($this->user->deleteProfile($this->user->id)) {
      Flash::addMessage('Profile deleted.');
      $this->redirect('/');
    } else {
      Flash::addMessage('Something went wrong while deleting your profile, try again later.');
      $this->redirect('/profile/show');
    }
  }
}
