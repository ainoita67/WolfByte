// /frontend/javascript/gestion_usuarios/post.js
import { Usuario } from "../clases/Usuario.js";

export async function insertUser(user) {
  try {
    // Acepta Usuario o un objeto normal
    const body = user.toJSONcreate();

    // Validación mínima antes de enviar
    if (!body.nombre || !body.correo || !body.contrasena || !body.id_rol) {
      throw new Error("insertUser: faltan campos obligatorios (nombre, correo, contrasena, id_rol)");
    }

    const response = await fetch(`${API}/user`, {
      method: "POST",
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
        `Error al insertar usuario (HTTP ${response.status})`;

      throw new Error(msg);
    }

    return json;
  } catch (error) {
    console.error("insertUser:", error);
    throw error;
  }
}

// helper para convertir errors a string
export function formatErrors(errors) {
  if (!errors) return null;

  // { campo: ["msg1","msg2"], otro: ["msg"] }
  if (typeof errors === "object") {
    return Object.entries(errors)
      .flatMap(([field, msgs]) =>
        (Array.isArray(msgs) ? msgs : [String(msgs)]).map(m => `• ${m}`)
      )
      .join("\n");
  }

  // si fuera string
  return String(errors);
}
