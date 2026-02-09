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
// $router->get('/profesores/{id}', 'Controllers\\ProfesorController@show'); // ver info de un profesor por ID

// $router->post('/profesores', 'Controllers\\ProfesorController@store'); // insertar nuevo profesor
// $router->put('/profesores/{id}', 'Controllers\\ProfesorController@update'); // actualizar profesor por ID
// $router->delete('/profesores/{id}', 'Controllers\\ProfesorController@destroy'); // eliminar profesor por ID
// $router->patch('/profesores/{id}/email','Controllers\\ProfesorController@updateEmail'); // actualizar email de profesor


// INCIDENCIAS
$router->get('/incidencias', 'Controllers\\IncidenciaController@index'); // Nos devuelven todas la incidencias de la base de datos
$router->get('/incidencias/recurso/{id_recurso}', 'Controllers\\IncidenciaController@showByRecurso'); //Nos devuelven todas la incidencias de la base de datos del recurso que  pasemos por parámetro (no implementar)
$router->get('/incidencias/usuario/{id_usuario}', 'Controllers\\IncidenciaController@showByUsuario'); // Nos devuelven todas la incidencias de la base de datos que haya creado el usuario del que se pase el id
$router ->post('/incidencias', 'Controllers\\IncidenciaController@store'); // Se envían los datos de una incidencia para añadirla a nuestra base de datos
$router ->put('/incidencias/{id}', 'Controllers\\IncidenciaController@update'); // Se enviaran los datos de una incidencia para modificarla
$router ->patch ('/incidencias/{id}/prioridad', 'Controllers\\IncidenciaController@updatePrioridad'); // Se enviará la información de la prioridad para una incidencia y se modificara
$router ->patch ('/incidencias/{id}/estado', 'Controllers\\IncidenciaController@updateEstado'); // Se enviará la información del estado de una incidencia y se modificara

// ROL
$router->get('/rol',               'Controllers\\RolController@index'); // Se reciben los datos de los usuarios activos para listarlos

// USUARIOS
$router->get('/user',               'Controllers\\UsuarioController@index'); // Se reciben los datos de los usuarios activos para listarlos
$router->get('/user/inactivos',     'Controllers\\UsuarioController@indexin'); // Se reciben los datos de los usuarios inactivos para listarlos
$router->get('/user/{id}',          'Controllers\\UsuarioController@show'); // Se reciben los datos del usuario con el ID que se mande
$router->get('/user/{id}/nombre',   'Controllers\\UsuarioController@showName'); // Se recibe el nombre del usuario del que se pase el ID
$router->get('/user/{id}/correo',  'Controllers\\UsuarioController@showEmail'); // Se recibe el correo del usuario del que se pase el ID
$router->get('/user/{id}/rol',      'Controllers\\UsuarioController@showRol'); // Se recibe el rol del usuario del que se pase el ID
$router->post('/user',              'Controllers\\UsuarioController@store'); // Se envían los datos del usuario desde un formulario para añadirlo a la DDBB
$router->put('/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el ID
$router->patch('/user/{id}/active',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive o de inactive a active del usuario del que se pase el ID
$router->patch('/user/{id}/token',       'Controllers\\UsuarioController@setToken'); // Se guarda un token y su fecha de expiración del usuario del que se pase el ID
$router->put('/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el ID menos la contraseña
$router->patch('/user/{id}', 'Controllers\\UsuarioController@patch');
 // Actualizar la contraseña de un usuario o modifica el campo de active a incactive o de inactive a active del usuario del que se pase el ID 

// Necesidad Reservas
$router->post('/reservas-necesidades/{id_reserva_espacio}/necesidades', 'Controllers\\NecesidadReservaController@store');
$router->get('/reservas-necesidades/{id_reserva}/necesidades',         'Controllers\\NecesidadReservaController@index');
$router->get('/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@show'); // Ver detalle de una necesidad asignada
$router->put('/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@update'); 
$router->put('/reservas-necesidades/{id_reserva_espacio}/necesidades', 'Controllers\\NecesidadReservaController@sync'); // Reemplazar todas las necesidades de una reserva
$router->delete('/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@destroy');

// EDIFICIOS
$router->protected( 'GET', '/edificios', 'Controllers\\EdificioController@index'); //seleccionar todos los edificios
$router->protected( 'GET', '/edificios/{id}', 'Controllers\\EdificioController@show'); // ver info de un edificio por ID
$router->post('/edificios', 'Controllers\\EdificioController@store'); // insertar nuevo edificio
$router->put('/edificios/{id}', 'Controllers\\EdificioController@update'); // actualizar edificio por ID 
$router->delete('/edificios/{id}', 'Controllers\\EdificioController@destroy'); // eliminar edificio por ID
 

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

// MATERIALES 
$router ->get('/material', 'Controllers\\MaterialController@index'); // Nos devuelve los materiales con todas sus características 
$router ->get('/material/{id}', 'Controllers\\MaterialController@show'); // Nos devuelve los datos del material que pasemos el id 
$router ->patch('/material/{id}', 'Controllers\\MaterialController@update'); // Modifica el material que pasemos el ID 
$router ->post('/material', 'Controllers\\MaterialController@store'); // Crea un nuevo material  
$router ->get('/material/{id}/disponibilidad', 'Controllers\\MaterialController@disponibilidad'); // Devuelve la disponibilidad de un material en una fecha específica

// RESERVAS
$router->get('/mis-reservas', 'Controllers\\ReservaController@misReservas');

$router->get('/caracteristicas', 'Controllers\\CaracteristicaController@index');
$router->get('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@show');
$router->post('/caracteristicas', 'Controllers\\CaracteristicaController@store');
$router->put('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@update');
$router->delete('/caracteristicas/{id}', 'Controllers\\CaracteristicaController@destroy');

// Caracteristicas
// RESERVAS
$router->get('/mis-reservas','Controllers\\ReservaController@misReservas'); // Devuelve las reservas del usuario autenticado
$router->get('/reservas','Controllers\\ReservaController@index'); // Devuelve todas las reservas (para admin o listado general)
$router->get('/reservas/{id}','Controllers\\ReservaController@show'); // Devuelve los detalles de una reserva específica por ID
$router->post('/reservas','Controllers\\ReservaController@store'); // Crea una nueva reserva
$router->put('/reservas/{id}','Controllers\\ReservaController@update'); // Actualiza una reserva existente por ID
$router->delete('/reservas/{id}','Controllers\\ReservaController@destroy'); // Elimina una reserva por ID

// Espacios
//Reservas de portatiles
$router->get('/reservas-portatiles', 'Controllers\\ReservaPortatilController@index');
$router->get('/reservas-portatiles/{id}', 'Controllers\\ReservaPortatilController@show');
$router->post('/reservas-portatiles', 'Controllers\\ReservaPortatilController@store');

//Espacios

$router->get('/espacios', 'Controllers\\EspacioController@index');
$router->get('/espacios/{id}', 'Controllers\\EspacioController@show');
$router->post('/espacios', 'Controllers\\EspacioController@store');
$router->get('/espacios/disponibles', 'Controllers\\EspacioController@disponibles');
$router->put('/espacios/{id}', 'Controllers\\EspacioController@update');
$router->delete('/espacios/{id}', 'Controllers\\EspacioController@destroy');
$router->get('/edificios/{id}/espacios', 'Controllers\\EspacioController@findByEdificio');
$router->get('/espacios/{id}/disponibilidad', 'Controllers\\EspacioController@verificarDisponibilidad');


// RESERVAS ESPACIOS
$router->get('/reservaEspacio','Controllers\\ReservaEspacioController@index'); // Devuelve todas las reservas de tipo “espacio”
$router->get('/mis-reservas-espacio','Controllers\\ReservaEspacioController@misReservas'); // Devuelve todas las reservas de espacio de un usuario autenticado
$router->post('/reservaEspacio','Controllers\\ReservaEspacioController@store'); // Añade una nueva reserva de un espacio
$router->get('/reservaEspacio/{id}','Controllers\\ReservaEspacioController@show'); // Devuelve las reservas de un espacio específico por ID de espacio
$router->put('/reservaEspacio/{id}','Controllers\\ReservaEspacioController@update'); // Cambia los datos de una reserva de espacio (comprobar disponibilidad)
$router->patch('/reservaEspacio/{id}','Controllers\\ReservaEspacioController@cambiarFechas'); // Cambia el rango de fechas de una reserva de espacio (comprobar disponibilidad)

// RESERVAS ESPACIOS (completas) - RUTAS UNIFICADAS 
$router->get('/reserva-espacio', 'Controllers\\ReservaEspacioController@index'); // Todas las reservas 
$router->get('/mis-reservas-espacio', 'Controllers\\ReservaEspacioController@misReservas'); // Mis reservas 
$router->get('/reserva-espacio/{id}', 'Controllers\\ReservaEspacioController@show'); // Reserva específica 
$router->get('/reserva-espacio/espacio/{id}', 'Controllers\\ReservaEspacioController@showByEspacio'); // Reservas por espacio 
$router->post('/reserva-espacio', 'Controllers\\ReservaEspacioController@store'); // Crear reserva 
$router->put('/reserva-espacio/{id}', 'Controllers\\ReservaEspacioController@update'); // Actualizar reserva completa 
$router->patch('/reserva-espacio/{id}/fechas', 'Controllers\\ReservaEspacioController@cambiarFechas'); // Cambiar solo fechas 
$router->delete('/reserva-espacio/{id}', 'Controllers\\ReservaEspacioController@destroy'); // Eliminar reserva 

// RESERVAS PERMANENTES
$router->get('/reservas_permanentes', 'Controllers\\ReservaPermanenteController@index'); //consultar todas las reservas permanentes activas
$router->get('/reservas_permanentes/recurso/{id_recurso}', 'Controllers\\ReservaPermanenteController@showActivas'); //consultar todas las reservas permanentes activas de un recurso
$router->post('/reservas_permanentes', 'Controllers\\ReservaPermanenteController@store'); //crear una reserva permanente
$router ->patch ('/reservas_permanentes/{id}/activar', 'Controllers\\ReservaPermanenteController@activate'); //activar o desactivar una reserva permanente
$router->put('/reservas_permanentes/{id}', 'Controllers\\ReservaPermanenteController@update'); //editar una reserva permanente
$router->get('/reservas_permanentes/{id}', 'Controllers\\ReservaPermanenteController@show'); //ver una reserva permanente por ID
$router ->patch ('/reservas_permanentes/desactivar_todo', 'Controllers\\ReservaPermanenteController@deactivate'); //desactivar todas las reservas permanentes

// NECESIDAD
$router ->get('/necesidades', 'Controllers\\NecesidadController@index'); // Devuelve las necesidades que tenemos en la base de datos
$router ->post('/necesidades', 'Controllers\\NecesidadController@store'); // Añade una nueva necesidad
$router ->put('/necesidades/{id}', 'Controllers\\NecesidadController@update'); // Modifica los datos de una necesidad

// NECESIDAD RESERVA
$router->get('/necesidad-reservas', 'Controllers\\NecesidadReservaController@index');
$router->get('/necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@show');
$router->post('/necesidad-reservas', 'Controllers\\NecesidadReservaController@store');
$router->put('/necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@update');
$router->delete('/necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@destroy');

//RESERVA DE ESPACIO
$router->get('/reservas-salon-actos', 'Controllers\\ReservaSalonActosController@index');
$router->put('/reservas/{id}/fechas', 'Controllers\\ReservaController@updateFechas');
$router->post('/reservas/verificar-disponibilidad', 'Controllers\\ReservaController@verificarDisponibilidad');

// RECURSO
$router->get('/recurso', 'Controllers\\RecursoController@index'); //Nos devuelve ID y descripción de todos los recursos que estén en la base de datos
$router->get('/recurso/activos', 'Controllers\\RecursoController@activos'); //Nos devuelve ID y descripción de todos los recursos que estén activos
$router->patch('/recurso/{id}/activo', 'Controllers\\RecursoController@updateActivar'); //Modifica el estado de activo a desactivo y viceversa

// LIBERACIÓN PUNTUAL
$router->get('/liberaciones', 'Controllers\\LiberacionPuntualController@index'); //Consultar todas las liberaciones puntuales
$router->get('/liberaciones/recurso/{id_recurso}', 'Controllers\\LiberacionPuntualController@showByRecurso'); //Consultar todas las liberaciones puntuales de un recurso
$router->get('/liberaciones/usuario/{id_usuario}', 'Controllers\\LiberacionPuntualController@showByUsuario'); //Consultar las liberaciones puntuales de un usuario
$router->post('/liberaciones', 'Controllers\\LiberacionPuntualController@store'); //Añadir una liberación puntual
$router->post('/liberaciones/reserva/{id_reserva}', 'Controllers\\LiberacionPuntualController@storeByReserva'); //Añadir una liberación puntual ligada a una reserva
$router->put('/liberaciones/{id}', 'Controllers\\LiberacionPuntualController@update'); //Editar una liberación puntual
$router->delete('/liberaciones/{id}', 'Controllers\\LiberacionPuntualController@destroy'); //Eliminar una liberación puntual