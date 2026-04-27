<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/product', 'ProductController::index');

$routes->get('/transaction', 'TransactionController::index');

$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('/product', 'ProductController::index', ['filter' => 'auth']);
$routes->get('/transaction', 'TransactionController::index', ['filter' => 'auth']);

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');