<?php
$router->post('/register', ['UserController', 'register']);
$router->post('/login', ['UserController', 'login']);
$router->get('/user/me', ['UserController', 'getUserDetails']);
$router->put('/user/edit', ['UserController', 'editUser']);
$router->get('/user/me/tickets',['BookingController','getUserBookings']);
$router->delete('/user/delete', ['UserController', 'deleteUser']);
$router->post('/user/me/update-balance',['UserController','topUpBalance']);