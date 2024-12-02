<?php
require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';
require_once __DIR__ . '/../src/Controllers/FlightController.php';
require_once __DIR__ . '/../src/Controllers/BookingController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php'; 

$router = new Router();

// Include route definitions
require_once __DIR__ . '/../routes/user.php';
require_once __DIR__ . '/../routes/flights.php';
require_once __DIR__ . '/../routes/admin.php';
require_once __DIR__ . '/../routes/tickets.php';

// Dispatch the request
$router->dispatch();
