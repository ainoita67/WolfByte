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
$router->get('/user',               'Controllers\\UsuarioController@index'); // Se reciben los datos de los usuarios activos para listarlos
$router->get('/user/inactivos',     'Controllers\\UsuarioController@indexin'); // Se reciben los datos de los usuarios inactivos para listarlos
$router->get('/user/{id}',          'Controllers\\UsuarioController@show'); // Se reciben los datos del usuario con el id que se mande
$router->get('/user/{id}/nombre',   'Controllers\\UsuarioController@showName'); // Se recibe el nombre del usuario del que se pase el id
$router->get('/user/{id}/correo',  'Controllers\\UsuarioController@showEmail'); // Se recibe el correo del usuario del que se pase el id
$router->get('/user/{id}/rol',      'Controllers\\UsuarioController@showRol'); // Se recibe el rol del usuario del que se pase el id
$router->post('/user',              'Controllers\\UsuarioController@store'); // Se envían los datos del usuario desde un formulario para añadirlo a la DDBB
$router->put('/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el id
$router->patch('/user/{id}/active',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id
$router->patch('/user/{id}/token',       'Controllers\\UsuarioController@setToken'); // Se guarda un token y su fecha de expiración del usuario del que se pase el id
$router->put('/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el id menos la contraseña
$router->patch('/user/{id}', 'Controllers\\UsuarioController@patch');
 // Actualizar la contraseña de un usuario o modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id 

// Necesidad Reservas
$router->post('/reservas-necesidades/{id_reserva_espacio}/necesidades', 'Controllers\\NecesidadReservaController@store');
$router->get('/reservas-necesidades/{id_reserva}/necesidades',         'Controllers\\NecesidadReservaController@index');
$router->get('/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@show'); // Ver detalle de una necesidad asignada
$router->put('/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@update'); 
$router->put('/reservas-necesidades/{id_reserva_espacio}/necesidades', 'Controllers\\NecesidadReservaController@sync'); // Reemplazar todas las necesidades de una reserva
$router->delete('/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@destroy');

// EDIFICIOS
$router->get('/edificios', 'Controllers\\EdificioController@index');
$router->get('/edificios/{id}', 'Controllers\\EdificioController@show');
$router->post('/edificios', 'Controllers\\EdificioController@store');
$router->put('/edificios/{id}', 'Controllers\\EdificioController@update');
$router->delete('/edificios/{id}', 'Controllers\\EdificioController@destroy');


// AULAS
// RESERVAS
$router->get('/mis-reservas', 'Controllers\\ReservaController@misReservas');

// Caracteristicas

$router->get('/caracteristicas', 'Controllers\\CaracteristicaController@index');
$router->get('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@show');
$router->post('/caracteristicas', 'Controllers\\CaracteristicaController@store');
$router->put('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@update');
$router->delete('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@destroy');

// Espacios

$router->get('/espacios', 'Controllers\\EspacioController@index');
$router->get('/espacios/{id}', 'Controllers\\EspacioController@show');
$router->post('/espacios', 'Controllers\\EspacioController@store');

// $router->get('/espacios/disponibles', 'Controllers\\EspacioController@disponibles');
// $router->put('/espacios/{id}', 'Controllers\\EspacioController@update');
// $router->delete('/espacios/{id}', 'Controllers\\EspacioController@destroy');
// $router->get('/edificios/{id}/espacios', 'Controllers\\EspacioController@findByEdificio');
// $router->get('/espacios/{id}/disponibilidad', 'Controllers\\EspacioController@verificarDisponibilidad');