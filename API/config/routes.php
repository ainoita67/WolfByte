<?php
declare(strict_types=1);

// config/routes.php

//

//$router->protected('GET', '/usuarios', 'Controllers\\UsuarioController@index');

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


// INCIDENCIAS
$router->protected('GET', '/incidencias', 'Controllers\\IncidenciaController@index', [30, 40]); // Nos devuelven todas la incidencias de la base de datos
$router->protected('GET', '/incidencias/recurso/{id_recurso}', 'Controllers\\IncidenciaController@showByRecurso', [30, 40]); //Nos devuelven todas la incidencias de la base de datos del recurso que  pasemos por parámetro (no implementar)
$router->protected('GET', '/incidencias/usuario/{id_usuario}', 'Controllers\\IncidenciaController@showByUsuario', [30, 40]); // Nos devuelven todas la incidencias de la base de datos que haya creado el usuario del que se pase el id
$router->protected('POST', '/incidencias', 'Controllers\\IncidenciaController@store', [30, 40]); // Se envían los datos de una incidencia para añadirla a nuestra base de datos
$router->protected('PUT', '/incidencias/{id}', 'Controllers\\IncidenciaController@update', [30, 40]); // Se enviaran los datos de una incidencia para modificarla
$router->protected('PATCH', '/incidencias/{id}/prioridad', 'Controllers\\IncidenciaController@updatePrioridad', [30, 40]); // Se enviará la información de la prioridad para una incidencia y se modificara
$router->protected('PATCH', '/incidencias/{id}/estado', 'Controllers\\IncidenciaController@updateEstado', [30, 40]); // Se enviará la información del estado de una incidencia y se modificara

// ROL
$router->protected('GET', '/rol',               'Controllers\\RolController@index', [30, 40]); // Se reciben los datos de los usuarios activos para listarlos

// USUARIOS
$router->protected('GET', '/user', 'Controllers\\UsuarioController@index', [30, 40]); // Se reciben los datos de los usuarios activos para listarlos
$router->protected('GET', '/user/inactivos',     'Controllers\\UsuarioController@indexin', [40]); // Se reciben los datos de los usuarios inactivos para listarlos
$router->protected('GET', '/user/{id}',          'Controllers\\UsuarioController@show', [40]); // Se reciben los datos del usuario con el id que se mande
$router->protected('POST', '/user',              'Controllers\\UsuarioController@store', [40]); // Se envían los datos del usuario desde un formulario para añadirlo a la DDBB
$router->protected('POST', '/user/importar',               'Controllers\\UsuarioController@importar', [40]); // Se importan los datos de los usuario desde un archivo para añadirlo a la DDBB
$router->protected('PUT', '/user/{id}',          'Controllers\\UsuarioController@update', [40]); // Se modifica por completo todos los campos del usuario del que se pase el id
$router->protected('PATCH', '/user/{id}/active',       'Controllers\\UsuarioController@inactive', [40]); // Se modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id
$router->protected('PUT', '/user/{id}',          'Controllers\\UsuarioController@update', [40]); // Se modifica por completo todos los campos del usuario del que se pase el id menos la contraseña
$router->protected('PATCH', '/user/{id}', 'Controllers\\UsuarioController@patch', [40]);
// Actualizar la contraseña de un usuario o modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id 


// Necesidad Reservas
$router->protected('POST', '/reservas-necesidades/{id_reserva_espacio}/necesidades', 'Controllers\\NecesidadReservaController@store', [30, 40]);
$router->protected('GET', '/reservas-necesidades/{id_reserva}/necesidades',         'Controllers\\NecesidadReservaController@index', [30, 40]);
$router->protected('GET', '/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@show', [30, 40]); // Ver detalle de una necesidad asignada
$router->protected('PUT', '/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@update', [30, 40]); 
$router->protected('PUT', '/reservas-necesidades/{id_reserva_espacio}/necesidades', 'Controllers\\NecesidadReservaController@sync', [30, 40]); // Reemplazar todas las necesidades de una reserva
$router->protected('DELETE', '/reservas-necesidades/{id_reserva_espacio}/necesidades/{id_necesidad}', 'Controllers\\NecesidadReservaController@destroy', [30, 40]);

// EDIFICIOS
$router->protected('GET', '/edificios', 'Controllers\\EdificioController@index', [30, 40]); //seleccionar todos los edificios
$router->protected('GET', '/edificios/{id}', 'Controllers\\EdificioController@show', [30, 40]); // ver info de un edificio por id
$router->protected('POST', '/edificios', 'Controllers\\EdificioController@store', [30, 40]); // insertar nuevo edificio
$router->protected('PUT', '/edificios/{id}', 'Controllers\\EdificioController@update', [30, 40]); // actualizar edificio por id 
$router->protected('DELETE', '/edificios/{id}', 'Controllers\\EdificioController@destroy', [30, 40]); // eliminar edificio por id
 

// Características

$router->protected('GET', '/caracteristicas', 'Controllers\\CaracteristicaController@index', [30, 40]);
$router->protected('GET', '/caracteristicas/{id}', 'Controllers\\CaracteristicaController@show', [30, 40]);
$router->protected('POST', '/caracteristicas', 'Controllers\\CaracteristicaController@store', [30, 40]);
$router->protected('PUT', '/caracteristicas/{id}', 'Controllers\\CaracteristicaController@update', [30, 40]);
$router->protected('DELETE', '/caracteristicas/{id}', 'Controllers\\CaracteristicaController@destroy', [30, 40]);

// MATERIALES 
$router ->get('/material', 'Controllers\\MaterialController@index', [30, 40]); // Nos devuelve los todos materiales con todas sus características 
$router ->get('/material/activos', 'Controllers\\MaterialController@indexActivos', [30, 40]); // Nos devuelve los materiales activos con todas sus características 
$router ->get('/material/{id}', 'Controllers\\MaterialController@show', [30, 40]); // Nos devuelve los datos del material que pasemos el id 
$router ->patch('/material/{id}', 'Controllers\\MaterialController@update', [30, 40]); // Modifica el material que pasemos el ID 
$router->protected('POST', '/material', 'Controllers\\MaterialController@store', [30, 40]); // Crea un nuevo material  
$router ->get('/material/{id}/disponibilidad', 'Controllers\\MaterialController@disponibilidad', [30, 40]); // Devuelve la disponibilidad de un material en una fecha específica

// Caracteristicas
$router->protected('GET', '/caracteristicas', 'Controllers\\CaracteristicaController@index', [30, 40]);
$router->protected('GET', '/caracteristicas/{id}', 'Controllers\\CaracteristicaController@show', [30, 40]);
$router->protected('POST', '/caracteristicas', 'Controllers\\CaracteristicaController@store', [30, 40]);
$router->protected('PUT', '/caracteristicas/{id}', 'Controllers\\CaracteristicaController@update', [30, 40]);
$router->protected('DELETE', '/caracteristicas/{id}', 'Controllers\\CaracteristicaController@destroy', [30, 40]);

// Espacios 
// RESERVAS
$router->protected('GET', '/mis-reservas/{id_usuario}','Controllers\\ReservaController@misReservas', [30, 40]); // Devuelve las reservas del usuario autenticado
$router->protected('GET', '/reservas','Controllers\\ReservaController@index', [30, 40]); // Devuelve todas las reservas (para admin o listado general)
$router->protected('GET', '/reservas-pendientes','Controllers\\ReservaController@pendientes', [30, 40]); // Devuelve las reservas sin autorizar (para admin o listado general)
$router->protected('GET', '/reservas-proximas','Controllers\\ReservaController@proximas', [30, 40]); // Devuelve las reservas próximas (para admin o listado general)
$router->protected('GET', '/reservas/{id}','Controllers\\ReservaController@show', [30, 40]); // Devuelve los detalles de una reserva específica por ID
$router->protected('POST', '/reservas','Controllers\\ReservaController@store', [30, 40]); // Crea una nueva reserva
$router->protected('PUT', '/reservas/{id}','Controllers\\ReservaController@update', [30, 40]); // Actualiza una reserva existente por ID
$router->protected('DELETE', '/reservas/{id}','Controllers\\ReservaController@destroy', [30, 40]); // Elimina una reserva por ID

// Espacios
//Reservas de portatiles
$router->protected('GET', '/reservas-portatiles', 'Controllers\\ReservaPortatilController@index', [30, 40]);
$router->protected('GET', '/reservas-portatiles/{id}', 'Controllers\\ReservaPortatilController@show', [30, 40]);
$router->protected('POST', '/reservas-portatiles', 'Controllers\\ReservaPortatilController@store', [30, 40]);
$router->protected('GET', '/reservaMaterial/material/{id}','Controllers\\ReservaMaterialController@showMaterial', [30, 40]); // Devuelve las reservas de un carrito específico por ID de carrito


//Espacios

$router->protected('GET', '/espacios', 'Controllers\\EspacioController@index', [30, 40]);
$router->protected('GET', '/espacios/activos', 'Controllers\\EspacioController@indexActivos', [30, 40]);
$router->protected('GET', '/espacios/{id}', 'Controllers\\EspacioController@show', [30, 40]);
$router->protected('POST', '/espacios', 'Controllers\\EspacioController@store', [30, 40]);
$router->protected('GET', '/espacios/disponibles', 'Controllers\\EspacioController@disponibles', [30, 40]);
$router->protected('PUT', '/espacios/{id}', 'Controllers\\EspacioController@update', [30, 40]);
$router->protected('DELETE', '/espacios/{id}', 'Controllers\\EspacioController@destroy', [30, 40]);
$router->protected('GET', '/edificios/{id}/espacios', 'Controllers\\EspacioController@findByEdificio', [30, 40]);
$router->protected('GET', '/espacios/{id}/disponibilidad', 'Controllers\\EspacioController@verificarDisponibilidad', [30, 40]);

$router->protected('GET', '/aulas', 'Controllers\\EspacioController@indexAulas', [30, 40]); // Devuelve solo los espacios que son aulas organizadas por edificio y planta
$router->protected('POST', '/aulas/disponibles', 'Controllers\\EspacioController@indexAulasDisponibles', [30, 40]); // Devuelve solo los espacios que son aulas organizadas por edificio y planta y que estén disponibles en el rango de fecha y hora especificado
$router->protected('GET', '/otrosespacios', 'Controllers\\EspacioController@indexOtrosEspacios', [30, 40]); // Devuelve solo los espacios que no son aulas ni salon de actos organizadas por edificio y planta
$router->protected('POST', '/otrosespacios/disponibles', 'Controllers\\EspacioController@indexOtrosEspaciosDisponibles', [30, 40]); // Devuelve solo los espacios que no son aulas ni salon de actos organizadas por edificio y planta y que estén disponibles en el rango de fecha y hora especificado
$router->protected('POST', '/portatiles/disponibles', 'Controllers\\MaterialController@indexCarritos', [30, 40]); // Devuelve solo los espacios que son salon de actos organizados por edificio y planta

// RESERVAS ESPACIOS
$router->protected('GET', '/reservaEspacio','Controllers\\ReservaEspacioController@index', [30, 40]); // Devuelve todas las reservas de tipo “espacio”
$router->protected('GET', '/mis-reservas-espacio','Controllers\\ReservaEspacioController@misReservas', [30, 40]); // Devuelve todas las reservas de espacio de un usuario autenticado
$router->protected('POST', '/reservaEspacio','Controllers\\ReservaEspacioController@store', [30, 40]); // Añade una nueva reserva de un espacio
$router->protected('GET', '/reservaEspacio/{id}','Controllers\\ReservaEspacioController@show', [30, 40]); // Devuelve informacion de una reserva de espacio por ID de reserva
$router->protected('GET', '/reservaEspacio/espacio/{id}','Controllers\\ReservaEspacioController@showEspacio', [30, 40]); // Devuelve las reservas de un espacio específico por ID de espacio
$router->protected('PUT', '/reservaEspacio/{id}','Controllers\\ReservaEspacioController@update', [30, 40]); // Cambia los datos de una reserva de espacio (comprobar disponibilidad)
$router->protected('PATCH', '/reservaEspacio/{id}','Controllers\\ReservaEspacioController@cambiarFechas', [30, 40]); // Cambia el rango de fechas de una reserva de espacio (comprobar disponibilidad)

// RESERVAS PERMANENTES
$router->protected('GET', '/reservas_permanentes', 'Controllers\\ReservaPermanenteController@index', [30, 40]); //consultar todas las reservas permanentes activas
$router->protected('GET', '/reservas_permanentes/inactivas', 'Controllers\\ReservaPermanenteController@indexInactivas', [30, 40]); //consultar todas las reservas permanentes inactivas
$router->protected('GET', '/reservas_permanentes/recurso/{id_recurso}', 'Controllers\\ReservaPermanenteController@showActivasRecurso', [30, 40]); //consultar todas las reservas permanentes activas de un recurso
$router->protected('POST', '/reservas_permanentes', 'Controllers\\ReservaPermanenteController@store', [30, 40]); //crear una reserva permanente
$router->protected('POST', '/reservas_permanentes/importar', 'Controllers\\ReservaPermanenteController@importar', [30, 40]); //importar reservas permanentes
$router->protected('PATCH', '/reservas_permanentes/{id}/activar', 'Controllers\\ReservaPermanenteController@activate', [30, 40]); //activar o desactivar una reserva permanente
$router->protected('PUT', '/reservas_permanentes/{id}', 'Controllers\\ReservaPermanenteController@update', [30, 40]); //editar una reserva permanente
$router->protected('GET', '/reservas_permanentes/{id}', 'Controllers\\ReservaPermanenteController@show', [30, 40]); //ver una reserva permanente por id
$router->protected('PATCH', '/reservas_permanentes/desactivar_todo', 'Controllers\\ReservaPermanenteController@deactivate', [30, 40]); //desactivar todas las reservas permanentes

// NECESIDAD 
$router ->get('/necesidades', 'Controllers\\NecesidadController@index', [30, 40]); // Devuelve las necesidades que tenemos en la base de datos 
$router->protected('POST', '/necesidades', 'Controllers\\NecesidadController@store', [30, 40]); // Añade una nueva necesidad 
$router->protected('PUT', '/necesidades/{id}', 'Controllers\\NecesidadController@update', [30, 40]); // Modifica los datos de una necesidad 

// NECESIDAD RESERVA
$router->protected('GET', '/necesidad-reservas', 'Controllers\\NecesidadReservaController@index', [30, 40]);
$router->protected('GET', '/necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@show', [30, 40]);
$router->protected('POST', '/necesidad-reservas', 'Controllers\\NecesidadReservaController@store', [30, 40]);
$router->protected('PUT', '/necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@update', [30, 40]);
$router->protected('DELETE', '/  necesidad-reservas/{id}', 'Controllers\\NecesidadReservaController@destroy', [30, 40]);

//RESERVA DE ESPACIO
$router->protected('GET', '/reservas-salon-actos', 'Controllers\\ReservaSalonActosController@index', [30, 40]);
$router->protected('PUT', '/reservas/{id}/fechas', 'Controllers\\ReservaController@updateFechas', [30, 40]);
$router->protected('POST', '/reservas/verificar-disponibilidad', 'Controllers\\ReservaController@verificarDisponibilidad', [30, 40]);

// PLANTAS 
$router ->get('/plantas', 'Controllers\\PlantaController@index', [30, 40]); //Devuelve las plantas y al edificio que pertenecen 
$router ->get('/plantas/{id_edificio}', 'Controllers\\PlantaController@showByEdificio', [30, 40]); //Devuelve las plantas de un edificio 
$router->protected('POST', '/plantas/{id_edificio}', 'Controllers\\PlantaController@store', [30, 40]); //Agrega una planta al edificio que pongamos 
$router->protected('PUT', '/plantas/{id_edificio}', 'Controllers\\PlantaController@update', [30, 40]); //Modifica los datos de la planta de un edificio 

// MATERIALES (CARROS DE PORTÁTILES)
$router->protected('GET', '/portatiles/materiales',                 'Controllers\\PortatilController@materiales', [30, 40]); // Listar materiales
$router->protected('GET', '/portatiles/materiales/{id}',            'Controllers\\PortatilController@material', [30, 40]); // Ver material por ID
$router->protected('POST', '/portatiles/materiales',                'Controllers\\PortatilController@createMaterial', [30, 40]); // Crear material
$router->protected('PUT', '/portatiles/materiales/{id}',            'Controllers\\PortatilController@updateMaterial', [30, 40]); // Actualizar material
$router->protected('DELETE', '/portatiles/materiales/{id}',         'Controllers\\PortatilController@deleteMaterial', [30, 40]); // Eliminar material

// RESERVAS DE PORTÁTILES
$router->protected('GET', '/portatiles/reservas',                   'Controllers\\PortatilController@reservas', [30, 40]); // Listar todas las reservas
$router->protected('GET', '/portatiles/reservas/usuario/{id_usuario}', 'Controllers\\PortatilController@reservasByUsuario', [30, 40]); // Reservas por usuario
$router->protected('GET', '/portatiles/reservas/{id}',              'Controllers\\PortatilController@reserva', [30, 40]); // Ver reserva por ID
$router->protected('POST', '/portatiles/reservas',                  'Controllers\\PortatilController@createReserva', [30, 40]); // Crear reserva
$router->protected('POST', '/portatiles/reservas/disponibilidad',   'Controllers\\PortatilController@disponibilidad', [30, 40]); // Verificar disponibilidad
$router->protected('PUT', '/portatiles/reservas/{id}',              'Controllers\\PortatilController@updateReserva', [30, 40]); // Actualizar reserva
$router->protected('PATCH', '/portatiles/reservas/{id}',            'Controllers\\PortatilController@patchReserva', [30, 40]); // Actualizar parcialmente
$router->protected('PATCH', '/portatiles/reservas/{id}/unidades',   'Controllers\\PortatilController@patchUnidades', [30, 40]); // Actualizar unidades
$router->protected('DELETE', '/portatiles/reservas/{id}',           'Controllers\\PortatilController@deleteReserva', [30, 40]); // Eliminar reserva
// RECURSO
$router->protected('GET', '/recurso', 'Controllers\\RecursoController@index', [30, 40]); //Nos devuelve id y descripción de todos los recursos que estén en la base de datos
$router->protected('GET', '/recurso/activos', 'Controllers\\RecursoController@indexActivos', [30, 40]); //Nos devuelve id y descripción del recurso por id
$router->protected('GET', '/recurso/{id}', 'Controllers\\RecursoController@show', [30, 40]); //Nos devuelve id y descripción de todos los recursos que estén en la base de datos
$router->protected('PATCH', '/recurso/{id}/activo', 'Controllers\\RecursoController@updateActivar', [30, 40]); //Modifica el estado de activo a desactivo y viceversa

// LIBERACIÓN PUNTUAL
$router->protected('GET', '/liberaciones', 'Controllers\\LiberacionPuntualController@index', [30, 40]); //Consultar todas las liberaciones puntuales
$router->protected('GET', '/liberaciones/recurso/{id_recurso}', 'Controllers\\LiberacionPuntualController@showByRecurso', [30, 40]); //Consultar todas las liberaciones puntuales de un recurso
$router->protected('GET', '/liberaciones/usuario/{id_usuario}', 'Controllers\\LiberacionPuntualController@showByUsuario', [30, 40]); //Consultar las liberaciones puntuales de un usuario
$router->protected('POST', '/liberaciones', 'Controllers\\LiberacionPuntualController@store', [30, 40]); //Añadir una liberación puntual
$router->protected('POST', '/liberaciones/reserva/{id_reserva}', 'Controllers\\LiberacionPuntualController@storeByReserva', [30, 40]); //Añadir una liberación puntual ligada a una reserva
$router->protected('PUT', '/liberaciones/{id}', 'Controllers\\LiberacionPuntualController@update', [30, 40]); //Editar una liberación puntual
$router->protected('DELETE', '/liberaciones/{id}', 'Controllers\\LiberacionPuntualController@destroy', [30, 40]); //Eliminar una liberación puntual

// CARACTERÍSTICAS DE ESPACIOS
$router->protected('GET', '/caracteristicasEspacios', 'Controllers\\CaracteristicaEspacioController@index', [30, 40]); // Listar todas las características de espacios
$router->protected('GET', '/espacios/{id}/caracteristicas', 'Controllers\\CaracteristicaEspacioController@showByEspacio', [30, 40]); // Listar características de un espacio específico
$router->protected('GET', '/espacios/{id}/caracteristicas/disponibles', 'Controllers\\CaracteristicaEspacioController@showDisponibles', [30, 40]); // Listar características disponibles para un espacio específico
$router->protected('POST', '/espacios/{id}/caracteristicas', 'Controllers\\CaracteristicaEspacioController@asignar', [30, 40]); // Asignar una característica a un espacio específico 
$router->protected('DELETE', '/espacios/{id}/caracteristicas', 'Controllers\\CaracteristicaEspacioController@quitar', [30, 40]); // Asignar una característica a un espacio específico 

// LOG DE ACCIONES
$router->protected('GET', '/logacciones', 'Controllers\\LogAccionesController@index', [30, 40]);
$router->protected('POST', '/logacciones', 'Controllers\\LogAccionesController@indexPaginado', [30, 40]);
$router->protected('GET', '/tipolog', 'Controllers\\LogAccionesController@indexTipoLog', [30, 40]);