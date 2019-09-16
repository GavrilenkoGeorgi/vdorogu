<?php
/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sessions
 */
session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('rules', ['controller' => 'Rules', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('allquestions', ['controller' => 'AllQuestions', 'action' => 'index']);
$router->add('questions', ['controller' => 'Questions', 'action' => 'index']);
$router->add('answers', ['controller' => 'Answers', 'action' => 'index']);
$router->add('routes', ['controller' => 'Routes', 'action' => 'index']);
// route to items index page
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
// regexp to match token
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);

/**
 * Load environment variables
 * can't get this thing working
 * cause of shitty free hosting
 */

/*
if (file_exists(__DIR__ . '/.env')) {
  $dotenv = Dotenv\Dotenv::create(__DIR__);
  $dotenv->load();
} else {
  echo 'gtfo';
}
*/
