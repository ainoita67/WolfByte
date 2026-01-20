<?php
declare(strict_types=1);

// config/routes.php

// LOGIN / LOGOUT
$router->post('/login', 'Controllers\\AuthSessionController@login');
$router->get('/logout', 'Controllers\\AuthSessionController@logout');
$router->post('/logout', 'Controllers\\AuthSessionController@logout');


// USUARIOS
$router->get('/user',               'Controllers\\UsuarioController@index'); // Se reciben los datos de los usuarios para listarlos
$router->get('/user/{id}',          'Controllers\\UsuarioController@show'); // Se reciben los datos del usuario con el id que se mande
$router->get('/user/{id}/nombre',   'Controllers\\UsuarioController@showName'); // Se recibe el nombre del usuario del que se pase el id
$router->get('/user/{id}/correo',   'Controllers\\UsuarioController@showEmail'); // Se recibe el correo del usuario del que se pase el id
$router->get('/user/{id}/rol',      'Controllers\\UsuarioController@showRol'); // Se recibe el rol del usuario del que se pase el id
$router->get('/user/{$id}/token',   'Controllers\\UsuarioController@showToken'); // Se recibe el token  y su fecha de expiración del usuario del que se pase el id
$router->post('/user',              'Controllers\\UsuarioController@store'); // Se envían los datos del usuario desde un formulario para añadirlo a la DDBB
$router->put('/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el id
$router->patch('/user/{id}/active',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id
$router->patch('/user/{id}/token',       'Controllers\\UsuarioController@setToken'); // Se guarda un token y su fecha de expiración del usuario del que se pase el id
//$router->dpatch('/user/{id}',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive del usuario del que se pase el id


// EDIFICIOS
$router->get('/edificios', 'Controllers\\EdificioController@index');
$router->get('/edificios/{id}', 'Controllers\\EdificioController@show');
$router->post('/edificios', 'Controllers\\EdificioController@store');
$router->put('/edificios/{id}', 'Controllers\\EdificioController@update');
$router->delete('/edificios/{id}', 'Controllers\\EdificioController@destroy');


// AULAS
