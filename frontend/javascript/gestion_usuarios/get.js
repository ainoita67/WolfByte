
// USUARIOS

async function getUsuarios() {
    try {
        const response = await fetch(`${API}/user`);
        if (!response.ok) throw new Error('Error al obtener usuarios');

        const json = await response.json();

        if (!json.data || !Array.isArray(json.data)) {
            console.error("JSON.data no es un array", json.data);
            return [];
        }

        // eliminar inactivos(usuario_activo = 0)
        const usuariosActivos = json.data.filter(usuario => usuario.usuario_activo === 1);

        //roles
        const roles = await getRoles();

        // mapear y eliminar campos innecesarios
        const usuarios = usuariosActivos.map(u => ({
            id_usuario: u.id_usuario,
            nombre: u.nombre,
            correo: u.correo,
            rol: roles[u.id_rol] || "Desconocido"
        }));

        return usuarios;
    } catch (error) {
        console.error(error);
        return [];
    }
}

async function getRoles() {
  try {
    const response = await fetch(`${API}/rol`);
    if (!response.ok) throw new Error("Error al obtener roles");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // mapear roles
    const mapa = {};
    json.data.forEach(r => {
        mapa[r.id_rol] = r.rol;
    });
    return mapa;
  } catch (error) {
    console.error(error);
    return [];
  }
}

