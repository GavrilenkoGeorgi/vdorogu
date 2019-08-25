<?php
namespace App\Controllers;
use \App\Models\User;

/**
 * Account controller
 */
class Account extends \Core\Controller {
  /**
   * Validate if email is available (AJAX) for a new signup
   * 
   * @return void
   */
  public function validateEmailAction() {
    $is_valid = ! User::emailExists($_GET['email']);

    header('Content-type: application/json');
    echo json_encode($is_valid);
  }
}