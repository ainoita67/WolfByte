import { Usuario } from BASE + "javascript/clases/Usuario.js";
 export async function apiCrearUsuario(usuarioJson) {
  const response = await fetch(`${API}/user`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(usuarioJson)
  });

  // si API devuelve json de error, intentamos leerlo
  let json = null;
  try { json = await response.json(); } catch {}

  if (!response.ok) {
    const msg = json?.message || "Error al crear usuario";
    throw new Error(msg);
  }

  return json; // {status,message,data,...}
}

export async function apiGetUsuarios() {
  const response = await fetch(`${API}/user`);
  if (!response.ok) throw new Error("Error al obtener usuarios");
  return await response.json();
}


import { Usuario } from "/frontend/javascript/clases/Usuario.js";
import { apiCrearUsuario, apiGetUsuarios } from "/frontend/javascript/api/usuarioApi.js";
import { apiGetRoles } from "/frontend/javascript/api/rolApi.js";

export async function getRolesMap() {
  const json = await apiGetRoles();
  if (!Array.isArray(json.data)) return {};

  const mapa = {};
  json.data.forEach(r => mapa[r.id_rol] = r.rol);
  return mapa;
}

export async function getUsuariosModelo() {
  const json = await apiGetUsuarios();
  if (!Array.isArray(json.data)) return [];

  const rolesMap = await getRolesMap();

  return json.data.map(u => new Usuario({
    id_usuario: u.id_usuario,
    nombre: u.nombre,
    correo: u.correo,
    usuario_activo: u.usuario_activo === 1,
    rol: {
      id_rol: u.id_rol,
      rol: rolesMap[u.id_rol] ?? "desconocido"
    }
  }));
}

export async function crearUsuarioDesdeFormulario(form) {
  const nombre = form.querySelector("#nombreCrear").value.trim();
  const correo = form.querySelector("#correoCrear").value.trim();
  const id_rol = Number(form.querySelector("#selectRolCrear").value);

  const pass1 = form.querySelector("#passCrear").value;
  const pass2 = form.querySelector("#pass2Crear").value;

  if (!nombre || !correo || !id_rol || !pass1 || !pass2) {
    throw new Error("Rellena todos los campos obligatorios.");
  }

  if (pass1 !== pass2) {
    throw new Error("Las contraseñas no coinciden.");
  }

  const usuario = new Usuario({
    nombre,
    correo,
    contrasena: pass1,
    usuario_activo: true,
    rol: { id_rol }
  });

  // INSERTAR EN BDD (API)
  const resultado = await apiCrearUsuario(usuario.toJSON());

  return { usuario, resultado };
}
