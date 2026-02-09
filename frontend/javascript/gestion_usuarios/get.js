// USUARIOS
import { Usuario } from "../clases/Usuario.js";

let rolesCache = null;

export async function getUsuarios() {
  try {
    const response = await fetch(`${API}/user`);
    if (!response.ok) throw new Error("Error al obtener usuarios");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // roles: {10:"extraescolar",20:"comun"...}
    const rolesMap = await getRoles();

    // crear instancias UsuarioSistema
    const usuarios = json.data.map(u => {
      return new Usuario({
        id_usuario: u.id_usuario,
        nombre: u.nombre,
        correo: u.correo,
        usuario_activo: u.usuario_activo === 1,
        rol: {
          id_rol: u.id_rol,
          rol: rolesMap[Number(u.id_rol)] ?? "desconocido"
        }
      });
    });

    return usuarios;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getInactivos() {
  try {
    const response = await fetch(`${API}/user/inactivos`);
    if (!response.ok) throw new Error("Error al obtener usuarios");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // roles: {10:"extraescolar",20:"comun"...}
    const rolesMap = await getRoles();

    // crear instancias UsuarioSistema
    const usuarios = json.data.map(u => {
      return new Usuario({
        id_usuario: u.id_usuario,
        nombre: u.nombre,
        correo: u.correo,
        usuario_activo: u.usuario_activo === 1,
        rol: {
          id_rol: u.id_rol,
          rol: rolesMap[Number(u.id_rol)] ?? "desconocido"
        }
      });
    });

    return usuarios;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getRoles() {
  if (rolesCache) return rolesCache;
  try {
    const response = await fetch(`${API}/rol`);
    if (!response.ok) throw new Error("Error al obtener roles");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return {};
    }

    // mapear roles
    const mapa = {};
    json.data.forEach(r => {
      mapa[Number(r.id_rol)] = r.rol;
    });

    rolesCache = mapa;
    return rolesCache;

  } catch (error) {
    console.error(error);
    return {};
  }
}
