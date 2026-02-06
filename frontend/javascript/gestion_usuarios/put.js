// /frontend/javascript/gestion_usuarios/put.js (o donde lo tengas)
import { Usuario } from "../clases/Usuario.js";
import { formatErrors } from "./post.js";

/**
 * Editar usuario
 * Endpoint: PUT /user/{id}
 * @param {Usuario|Object} user
 */
export async function updateUser(user) {
  try {
    // Acepta Usuario o un objeto normal
    if (!(user instanceof Usuario)) {
      user = new Usuario(user);
    }

    // id_usuario es obligatorio
    const id = user.id_usuario; 
    if (!id) {
      throw new Error("updateUser: falta id_usuario");
    }

    // Construimos body para editar:
    const body = user.toJSON();

    // Validación mínima antes de enviar
    if (!body.nombre || !body.correo || !body.id_rol) {
      throw new Error("updateUser: faltan campos obligatorios (nombre, correo, id_rol)");
    }

    const response = await fetch(`${API}/user/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify(body)
    });

    const json = await response.json().catch(() => null);

    if (!response.ok) {
      const formatted = formatErrors(json?.data?.errors);

      const msg =
        formatted ||
        json?.message ||
        `Error al editar usuario (HTTP ${response.status})`;

      throw new Error(msg);
    }

    return json;
  } catch (error) {
    console.error("updateUser:", error);
    throw error;
  }
}

export async function updateUserPassword(user) {
  try {
    // Acepta Usuario o un objeto plano
    if (!(user instanceof Usuario)) {
      user = new Usuario(user);
    }

    const id = user.id_usuario;
    if (!id) throw new Error("updateUserPassword: falta id_usuario");

    // Solo si existe contraseña
    if (!user.contrasena) return null;

    const body = { password: user.contrasena };

    const response = await fetch(`${API}/user/${id}`, {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify(body)
    });

    const json = await response.json().catch(() => null);

    if (!response.ok) {
      const formatted = formatErrors(json?.data?.errors);
      const msg =
        formatted ||
        json?.message ||
        `Error al actualizar la contraseña (HTTP ${response.status})`;
      throw new Error(msg);
    }

    return json;
  } catch (error) {
    console.error("updateUserPassword:", error);
    throw error;
  }
}

export async function desactiveUser(user) {
  try {
    // Acepta Usuario o un objeto plano
    if (!(user instanceof Usuario)) {
      user = new Usuario(user);
    }

    const id = user.id_usuario;
    if (!id) throw new Error("desactiveUser: falta id_usuario");

    const response = await fetch(`${API}/user/${id}`, {
      method: "PATCH",
      headers: {
        "Accept": "application/json"
      },
    });

    const json = await response.json().catch(() => null);

    if (!response.ok) {
      const formatted = formatErrors(json?.data?.errors);
      const msg =
        formatted ||
        json?.message ||
        `Error al desactivar usuario (HTTP ${response.status})`;
      throw new Error(msg);
    }

    return json;
  } catch (error) {
    console.error("updateUserPassword:", error);
    throw error;
  }
}
