<?php
$router->post('/admin/login', ['AdminController', 'login']);
$router->post('/admin/edit', ['AdminController', 'editAdmin']);
$router->post('/admin/add/plane', ['AdminController', 'addPlane']);
$router->post('/admin/add/flight', ['AdminController', 'addFlight']);
$router->post('/admin/edit/users', ['AdminController', 'editUser']);
$router->get('/admin/view/planes', ['AdminController', 'viewPlanes']);
$router->get('/admin/view/users', ['AdminController', 'viewUsers']);
$router->get('/admin/view/flights', ['AdminController', 'viewFlights']);
