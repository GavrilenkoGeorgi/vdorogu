<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller {

  /**
  * Show the login page
  *
  * @return void
  */
  public function newAction()
  {
    View::renderTemplate('Login/new.html');
  }

  /**
  * Log in a user
  *
  * @return void
  */
  public function createAction() {

    $remember_me = isset($_POST['remember_me']);

    $errors = [];
    // get recaptcha score
    $score = User::getRecaptchaScore($_POST['recaptcha_response']);
    if ($score < 0.3) {
      $errors[] = 'Recaptcha score is too low ' . $score;
    }
    $user = User::authenticate($_POST['email'], $_POST['password']);
    // if user exists and score is high enough
    if ($user && $score >= 0.3) {
      Auth::Login($user, $remember_me);
      Flash::addMessage('Login successful');
      $this->redirect(Auth::getReturnToPage());
    } else {
      // either user misspelled credentials or/and
      // score is too low, add another error
      // and render the login page
      Flash::addMessage('Please, check your credentials', Flash::WARNING);
      $errors[] = 'Please, check your credentials';
      View::renderTemplate('Login/new.html', [
        'email' => $_POST['email'],
        'errors' => $errors, // remove this
        'remember_me' => $remember_me
      ]);
    }
  }
  /**
  * Log out a user
  *
  * @return void
  */
  public function destroyAction() {
    Auth::logout();
    $this->redirect('/login/show-logout-message');
  }
  /**
  * Show the login success page
  *
  * @return void
  */
  public function successAction() {
    View::renderTemplate('Login/success.html');
  }
  /**
   * Show a 'logged out' flash message and redirect to the homepage.
   * Necessary to use the flash messages as they use the session and
   * at the end of the logout method (destroyAction) the session is
   * destroyed so a new action needs to be called in order to use the session.
   * 
   * @return void
   */
  public function showLogoutMessageAction() {
    Flash::addMessage('Logout successful');
    $this->redirect('/');
  }
}
