// /frontend/javascript/gestion_usuarios/put.js (o donde lo tengas)
import { formatErrors } from "./post.js";

export async function updatePermanente(reserva) {
  try {
    const permanente = ({
        dia_semana: reserva.dia_semana,
        inicio: reserva.inicio,
        fin: reserva.fin,
        comentario: reserva.comentario?.trim() || null,
        id_recurso: reserva.recurso,
        unidades: reserva.unidades != null ? Number(reserva.unidades) : null,
        id_usuario: sessionStorage.getItem("id_usuario")
      });

    // id_usuario es obligatorio
    if (!reserva.id) {
      throw new Error("updatepermanente: falta id_reserva_permanente");
    }

    // Construimos body para editar:
    const body = permanente;

    // Validación mínima antes de enviar
    if (!body.dia_semana || !body.id_recurso || !body.inicio || !body.fin || reserva.id == null) {
      throw new Error("Faltan campos obligatorios (recurso, dia de la semana, hora de inicio y hora de fin)");
    }

    console.log("Enviando PUT con body:",reserva.id, body);

    const response = await fetch(`${API}/reservas_permanentes/${reserva.id}`, {
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
        `Error al editar reserva (HTTP ${response.status})`;

      throw new Error(msg);
    }

    return json;
  } catch (error) {
    console.error("updatePermanente:", error);
    throw error;
  }
}

export async function desactivePermanente(id) {
  try {
    if (!id) throw new Error("desactivePermanente: falta id_reserva_permanente");

    let usuario = sessionStorage.getItem("id_usuario");
    const response = await fetch(`${API}/reservas_permanentes/${id}/activar`, {
      method: "PATCH",
      headers: {
        "Accept": "application/json"
      },
      body: JSON.stringify({ id_usuario: usuario })
    });

    const json = await response.json().catch(() => null);

    if (!response.ok) {
      const formatted = formatErrors(json?.data?.errors);
      const msg =
        formatted ||
        json?.message ||
        `Error al desactivar reserva (HTTP ${response.status})`;
      throw new Error(msg);
    }

    return json;
  } catch (error) {
    console.error("desactivePermanente:", error);
    throw error;
  }
}

export async function desactivarTodo() {
  try {
    let usuario = sessionStorage.getItem("id_usuario");
    const response = await fetch(`${API}/reservas_permanentes/desactivar_todo`, {
      method: "PATCH",
      headers: {
        "Accept": "application/json"
      },
      body: JSON.stringify({ id_usuario: usuario })
    });

    const json = await response.json().catch(() => null);

    if (!response.ok) {
      const formatted = formatErrors(json?.data?.errors);
      const msg =
        formatted ||
        json?.message ||
        `Error al desactivar reserva (HTTP ${response.status})`;
      throw new Error(msg);
    }

    return json;
  } catch (error) {
    console.error("desactivarTodo:", error);
    throw error;
  }
}