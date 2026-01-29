<?php
declare(strict_types=1);

// config/routes.php

//

$router->protected('GET', '/usuarios', 'Controllers\\UsuarioController@index');

$router->post('/login', 'Controllers\\AuthController@login');
$router->get('/logout', 'Controllers\\AuthController@logout');
$router->post('/logout', 'Controllers\\AuthController@logout');

// //aqui se redirigen las peticiones hechas con el protocolo que sea (get, post ...) a la direccion (primer parametro)  y ejecuta la funcion (segundo parametro). La funcion es carpeta\\directorio@funcion.
// $router->get('/profesores', 'Controllers\\ProfesorController@index'); //seleccionar todos los profesores
// $router->get('/profesores/{id}', 'Controllers\\ProfesorController@show'); // ver info de un profesor por id

// $router->post('/profesores', 'Controllers\\ProfesorController@store'); // insertar nuevo profesor
// $router->put('/profesores/{id}', 'Controllers\\ProfesorController@update'); // actualizar profesor por id
// $router->delete('/profesores/{id}', 'Controllers\\ProfesorController@destroy'); // eliminar profesor por id
// $router->patch('/profesores/{id}/email','Controllers\\ProfesorController@updateEmail'); // actualizar email de profesor


// // INCIDENCIAS
// $router->get('/incidencias', 'Controllers\\IncidenciaController@index'); //seleccionar todos los Incidenciaes
// $router->post('/incidencias', 'Controllers\\IncidenciaController@store'); // insertar nuevo Incidencia
// $router->put('/incidencias/{id}', 'Controllers\\IncidenciaController@update'); // actualizar Incidencia por id
// $router->delete('/incidencias/{id}', 'Controllers\\IncidenciaController@destroy'); // eliminar Incidencia por id

// USUARIOS
$router->protected( 'GET', '/user',               'Controllers\\UsuarioController@index'); // Se reciben los datos de los usuarios para listarlos
$router->protected( 'GET', '/user/{id}',          'Controllers\\UsuarioController@show'); // Se reciben los datos del usuario con el id que se mande
$router->protected( 'GET', '/user/{id}/nombre',   'Controllers\\UsuarioController@showName'); // Se recibe el nombre del usuario del que se pase el id
$router->protected( 'GET', '/user/{id}/correo',   'Controllers\\UsuarioController@showEmail'); // Se recibe el correo del usuario del que se pase el id
$router->protected( 'GET', '/user/{id}/rol',      'Controllers\\UsuarioController@showRol'); // Se recibe el rol del usuario del que se pase el id
$router->protected( 'GET', '/user/{id}/token',   'Controllers\\UsuarioController@showToken'); // Se recibe el token  y su fecha de expiración del usuario del que se pase el id
$router->protected( 'POST', '/user',              'Controllers\\UsuarioController@store'); // Se envían los datos del usuario desde un formulario para añadirlo a la DDBB
$router->protected( 'PUT', '/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el id
$router->protected( 'PATCH', '/user/{id}/active',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id
$router->protected( 'PATCH', '/user/{id}/token',       'Controllers\\UsuarioController@setToken'); // Se guarda un token y su fecha de expiración del usuario del que se pase el id
//$router->protected( 'PATCH', '/user/{id}',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive del usuario del que se pase el id

// EDIFICIOS
$router->protected( 'GET', '/edificios', 'Controllers\\EdificioController@index'); //seleccionar todos los edificios
$router->protected( 'GET', '/edificios/{id}', 'Controllers\\EdificioController@show'); // ver info de un edificio por id
$router->post('/edificios', 'Controllers\\EdificioController@store'); // insertar nuevo edificio
$router->put('/edificios/{id}', 'Controllers\\EdificioController@update'); // actualizar edificio por id 
$router->delete('/edificios/{id}', 'Controllers\\EdificioController@destroy'); // eliminar edificio por id
 

// RESERVAS
$router->get('/mis-reservas', 'Controllers\\ReservaController@misReservas');

// Caracteristicas

$router->get('/caracteristicas', 'Controllers\\CaracteristicaController@index');
$router->get('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@show');
$router->post('/caracteristicas', 'Controllers\\CaracteristicaController@store');
$router->put('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@update');
$router->delete('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@destroy');

//Reservas de portatiles
$router->get('/reservas-portatiles', 'Controllers\\ReservaPortatilController@index');
$router->get('/reservas-portatiles/{id}', 'Controllers\\ReservaPortatilController@show');
$router->post('/reservas-portatiles', 'Controllers\\ReservaPortatilController@store');

