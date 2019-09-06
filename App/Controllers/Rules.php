<?php

namespace App\Controllers;

use \Core\View;

/**
 * Rules
 * 
 * PHP version 7.0
 */

class Rules extends \Core\Controller {
  /**
   * Rules index view
   * 
   * @return void
   */
  public function indexAction() {
    View::renderTemplate('Rules/index.html');
  }
}
