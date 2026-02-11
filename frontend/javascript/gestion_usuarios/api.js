// URL de tu API
const API_URL = "http://192.168.13.202/IKER";


// USUARIOS
//$router->get('/user',               'Controllers\\UsuarioController@index'); // Se reciben los datos de los usuarios para listarlos

async function getUsuarios() {
    try {
        const response = await fetch(`${API_URL}/user`);
        if (!response.ok) throw new Error('Error al obtener usuarios');

        const json = await response.json();

        if (!json.data || !Array.isArray(json.data)) {
            console.error("JSON.data no es un array", json.data);
            return [];
        }

        // eliminar inactivos(usuario_activo = 0)
        const usuariosActivos = json.data.filter(usuario => usuario.usuario_activo === 1);

        // mapear y eliminar campos innecesarios
        const usuarios = usuariosActivos.map(u => ({
            id_usuario: u.id_usuario,
            nombre: u.nombre,
            correo: u.correo,
            rol: obtenerNombreRol(u.id_rol)
        }));

        console.log("usuarios filtrados y mapeados", usuarios);
        return usuarios;
    } catch (error) {
        console.error(error);
        return [];
    }
}

function obtenerNombreRol(idRol) {
    switch (idRol) {
        case 1: return "Comun";
        case 2: return "Administrador";
        case 3: return "Superadministrador";
        default: return "Desconocido";
    }
}

// $router->get('/user/{id}',          'Controllers\\UsuarioController@show'); // Se reciben los datos del usuario con el id que se mande
// $router->get('/user/{id}/nombre',   'Controllers\\UsuarioController@showName'); // Se recibe el nombre del usuario del que se pase el id
// $router->get('/user/{id}/correo',   'Controllers\\UsuarioController@showEmail'); // Se recibe el correo del usuario del que se pase el id
// $router->get('/user/{id}/rol',      'Controllers\\UsuarioController@showRol'); // Se recibe el rol del usuario del que se pase el id
// $router->get('/user/{$id}/token',   'Controllers\\UsuarioController@showToken'); // Se recibe el token  y su fecha de expiración del usuario del que se pase el id
// $router->post('/user',              'Controllers\\UsuarioController@store'); // Se envían los datos del usuario desde un formulario para añadirlo a la DDBB
// $router->put('/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el id
// $router->patch('/user/{id}/active',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id
// $router->patch('/user/{id}/token',       'Controllers\\UsuarioController@setToken'); // Se guarda un token y su fecha de expiración del usuario del que se pase el id
