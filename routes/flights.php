<?php
$router->get('/flights', ['FlightController', 'getAllFlights']);
$router->post('/flights/booking', ['FlightController', 'bookFlight']);
