<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\ManageTodo;
use \App\Auth;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller {
  /**
  * Show the index page with current todos
  *
  * @return void
  */
  public function indexAction() {
    // just to test mail, remove later
    // \App\Mail::send('gavrilenko.georgi@gmail.com', 'Test', 'This is a test', '<h1>This is a test</h1>');
    View::renderTemplate('Home/index.html');
    /*
    $todo = new ManageTodo();
    if (empty($_GET['sort'])) {
      // default sort
      $sort = 'user_name';
      $limit = 3;
      $start = 0;
      $order = 'asc';
    } else {
      $sort = $_GET['sort'];
      $start = $_GET['start'];
      $limit = $_GET['limit'];
      $order = $_GET['order'];
    }
    // data with current page, sorted
    $data = $todo->getPage($start, $limit, $sort, $order);
    // total pages count
    $totalTodos = $todo->getTodosCount();
    $totalPagesCount = ceil($totalTodos/$limit);
    $pagination = [];
    $lastPage = $totalPagesCount;

    while ($totalPagesCount > 0) {
      $totalPagesCount--;
      if ($totalPagesCount > 0) {
        $pagination[] = intval($totalPagesCount*$limit);
      } else {
        $pagination[] = 0;
      }
    }

    $pagination = array_reverse($pagination);
    if ($start <= 3) {
      // slice first three
      $pagination = array_slice($pagination, 0, $limit, true);
    } else if ($start < $limit * 3) {
      // three results on the page
      // get three pages in the middle
      $replacement = [];
      $startIndex = $start/$limit;
      $pagination = $this->array_splice_assoc($pagination, $startIndex, $limit);
    } else {
      // get last three
      $pagination = array_slice($pagination, -3, $limit, true);
    }
    View::renderTemplate('Home/index.html', [
      'data' => $data,
      'pagination' => $pagination,
      'limit' => $limit,
      'current' => $start,
      'sort' => $sort
    ]); */
  }
  public function array_splice_assoc($input, $offset, $limit = 0) {
    $keys = array_keys($input);
    $values = array_values($input);
    $keys = array_splice($keys, $offset, $limit);
    $values = array_splice($values, $offset, $limit);
    $result = array_combine($keys, $values);
    return $result;
  }
}
