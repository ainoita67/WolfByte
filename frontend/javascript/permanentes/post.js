// /frontend/javascript/gestion_usuarios/post.js

export async function createPermanente(reserva) {
  try {
    const body = {
        dia_semana: reserva.dia_semana,
        inicio: reserva.inicio,
        fin: reserva.fin,
        comentario: reserva.comentario?.trim() || null,
        id_recurso: reserva.recurso,
        unidades: reserva.unidades != null ? Number(reserva.unidades) : null
    };
    
    // Validación mínima antes de enviar
    if (!body.dia_semana || !body.inicio || !body.fin || !body.id_recurso) {
      throw new Error("createPermanente: faltan campos obligatorios (dia_semana, inicio, fin, recurso)");
    }

    const response = await fetch(`${API}/reservas_permanentes`, {
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
    console.error("createPermanente:", error);
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
