<?php
declare(strict_types=1);

// config/routes.php

//
$router->post('/login', 'Controllers\\AuthSessionController@login');
$router->get('/logout', 'Controllers\\AuthSessionController@logout');
$router->post('/logout', 'Controllers\\AuthSessionController@logout');

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


// RESERVAS
$router->get('/necesidad-reservas','Controllers\\NecesidadReservaController@index'); // Devuelve todas las necesidades de reservas
$router->get('/necesidad-reservas/{id}','Controllers\\NecesidadReservaController@show'); // Devuelve las necesidades de una reserva de un espacio por ID
$router->post('/necesidad-reservas','Controllers\\NecesidadReservaController@store'); // Crea una nueva necesidad de reserva de un espacio
$router->put('/necesidad-reservas/{id}','Controllers\\NecesidadReservaController@update'); // Actualiza una necesidad de reserva de un espacio por ID
$router->delete('/necesidad-reservas/{id}','Controllers\\NecesidadReservaController@destroy'); // Elimina una necesidad de reserva de un espacio por ID

// RESERVAS PERMANENTES
$router->get('/reservas_permanentes', 'Controllers\\ReservaPermanenteController@index'); //consultar todas las reservas permanentes activas
$router->get('/reservas_permanentes/{id_recurso}', 'Controllers\\ReservaPermanenteController@showActivas'); //consultar todas las reservas permanentes activas de un recurso
$router->post('/reservas_permanentes', 'Controllers\\ReservaPermanenteController@store'); //crear una reserva permanente
$router ->patch ('/reservas_permanentes/{id}/activar', 'Controllers\\ReservaPermanenteController@updateActivar'); //activar o desactivar una reserva permanente
$router->put('/reservas_permanentes/{id}', 'Controllers\\ReservaPermanenteController@update'); //editar una reserva permanente
$router->get('/reservas_permanentes/{id}', 'Controllers\\ReservaPermanenteController@show'); //ver una reserva permanente por id
$router ->patch ('/reservas_permanentes/desactivar_todo', 'Controllers\\ReservaPermanenteController@deactivate'); //desactivar todas las reservas permanentes


// NECESIDAD RESERVA
$router->get('/necesidad-reservas', 'Controllers\\NecesidadReservaController@index');
$router->get('/necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@show');
$router->post('/necesidad-reservas', 'Controllers\\NecesidadReservaController@store');
$router->put('/necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@update');
$router->delete('/necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@destroy');
