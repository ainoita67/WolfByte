<?php
declare(strict_types=1);

// config/routes.php

//
$router->post('/login', 'Controllers\\AuthSessionController@login');
$router->get('/logout', 'Controllers\\AuthSessionController@logout');
$router->post('/logout', 'Controllers\\AuthSessionController@logout');

//aqui se redirigen las peticiones hechas con el protocolo que sea (get, post ...) a la direccion (primer parametro)  y ejecuta la funcion (segundo parametro). La funcion es carpeta\\directorio@funcion.
$router->get('/profesores', 'Controllers\\ProfesorController@index'); //seleccionar todos los profesores
$router->get('/profesores/{id}', 'Controllers\\ProfesorController@show'); // ver info de un profesor por id

$router->post('/profesores', 'Controllers\\ProfesorController@store'); // insertar nuevo profesor
$router->put('/profesores/{id}', 'Controllers\\ProfesorController@update'); // actualizar profesor por id
$router->delete('/profesores/{id}', 'Controllers\\ProfesorController@destroy'); // eliminar profesor por id
$router->patch('/profesores/{id}/email','Controllers\\ProfesorController@updateEmail'); // actualizar email de profesor


// INCIDENCIAS
$router->get('/incidencias', 'Controllers\\IncidenciaController@index'); //seleccionar todos los Incidenciaes
$router->post('/incidencias', 'Controllers\\IncidenciaController@store'); // insertar nuevo Incidencia
$router->put('/incidencias/{id}', 'Controllers\\IncidenciaController@update'); // actualizar Incidencia por id
$router->delete('/incidencias/{id}', 'Controllers\\IncidenciaController@destroy'); // eliminar Incidencia por id

