<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('auth', ['namespace' => 'App\Controllers'], function ($routes) {
	$routes->post('register', 'AuthController::register');
	$routes->post('login', 'AuthController::login');
	// $routes->get('logout', 'AuthController::logout');
	$routes->get('me', 'AuthController::me');
});

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
	// Tickets
	$routes->post('tickets/start', 'TicketController::start');
	$routes->post('tickets/(:num)/end', 'TicketController::end/$1');
	$routes->get('tickets/(:num)', 'TicketController::show/$1');

	// Payments
	$routes->post('tickets/(:num)/pay', 'PaymentController::pay/$1');
	$routes->post('payments/webhook', 'PaymentController::webhook');
	$routes->get('payments/(:num)/receipt', 'ReceiptController::show/$1');

	// Users
	$routes->get('users/(:num)/tickets', 'UserController::tickets/$1');

	// Parking spots
	$routes->get('parking-spots', 'ParkingSpotController::index');
	$routes->put('parking-spots/(:num)', 'ParkingSpotController::update/$1');
});
