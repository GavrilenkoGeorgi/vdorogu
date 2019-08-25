<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\ManageTodo;

/**
 * Todos controller
 *
 * PHP version 7.0
 */
class Todos extends \Core\Controller {
  /**
  * Show the add todo page
  *
  * @return void
  */
  public function newAction() {
    View::renderTemplate('Todos/create.html');
  }
  /**
  * Create new todo
  *
  * @return void
  */
  public function createAction() {
    $todo = new ManageTodo($_POST);
    if ($todo->save()) {
      header('Location: http://' . $_SERVER['HTTP_HOST'] . '/Todos/success', true, 303);
      exit;
    } else {
      View::renderTemplate('Todos/create.html', [
        'todo' => $todo
      ]);
    }
  }
  /**
  * Show todo to edit
  *
  * @return void
  */
  public function editAction() {
    $todo = new ManageTodo($_POST);
    $data = $todo->getSome();
    View::renderTemplate('Todos/edit.html', [
      'data' => $data
    ]);
  }
  /**
  * Update existing todo
  *
  * @return void
  */
  public function updateAction() {
    $todo = new ManageTodo($_POST);
    if ($todo->update()) {
      header('Location: http://' . $_SERVER['HTTP_HOST'] . '/Todos/success', true, 303);
      exit;
    } else {
      View::renderTemplate('Todos/create.html', [
        'todo' => $todo
      ]);
    }
  }
  /**
  * Set todo completed state
  *
  * @return void
  */
  public function setCompletedAction() {
    // id here
    $todo = new ManageTodo($_POST);
    if ($todo->setCompleted()) {
      header('Location: http://' . $_SERVER['HTTP_HOST'] . '/Todos/success', true, 303);
      exit;
    } else {
      View::renderTemplate('Todos/create.html', [
        'todo' => $todo
      ]);
    }
  }
  /**
  * Show the add todo success page
  *
  * @return void
  */
  public function successAction() {
    View::renderTemplate('Todos/success.html');
  }
}
