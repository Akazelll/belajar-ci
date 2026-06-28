<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->group('produk', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'ProductController::index');
    $routes->post('', 'ProductController::create');
    $routes->post('edit/(:any)', 'ProductController::edit/$1');
    $routes->get('delete/(:any)', 'ProductController::delete/$1');
    $routes->get('download', 'ProductController::download');
});
$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TransactionController::index');
    $routes->post('', 'TransactionController::cart_add');
    $routes->post('edit', 'TransactionController::cart_edit');
    $routes->get('delete/(:any)', 'TransactionController::cart_delete/$1');
    $routes->get('clear', 'TransactionController::cart_clear');
});

// $routes->get('/transaction', 'TransactionController::index');

$routes->get('checkout', 'TransactionController::checkout', ['filter' => 'auth']);
$routes->post('buy', 'TransactionController::buy', ['filter' => 'auth']);

$routes->get('ajax/destinations', 'TransactionController::destinations', ['filter' => 'auth']);
$routes->get('ajax/costs', 'TransactionController::costs', ['filter' => 'auth']);

$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('/product', 'ProductController::index', ['filter' => 'auth']);
$routes->get('/transaction', 'TransactionController::index', ['filter' => 'auth']);

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');