<?php
$router->post('/admin/login', ['AdminController', 'login']);
// editing database
$router->put('/admin/edit', ['AdminController', 'editAdmin']);
$router->post('/admin/edit/users', ['AdminController', 'editUser']);
//adding to database
$router->post('/admin/add/plane', ['AdminController', 'addPlane']);
$router->post('/admin/add/flight', ['AdminController', 'addFlight']);
//viewing database
$router->get('/admin/view/planes', ['AdminController', 'viewPlanes']);
$router->get('/admin/view/users', ['AdminController', 'viewUsers']);
$router->get('/admin/view/flights', ['AdminController', 'viewFlights']);
//deleting content
$router->delete('/admin/delete/user',['AdminController','deleteUser']);
$router->delete('/admin/delete/plane',['AdminController','deletePlane']);
$router->delete('/admin/delete/flight',['AdminController','deleteflight']);