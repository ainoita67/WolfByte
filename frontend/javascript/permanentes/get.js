// RESERVAS PERMANENTES

export async function getReservas() {
  try {
    const response = await fetch(`${API}/reservas_permanentes`);
    if (!response.ok) throw new Error("Error al obtener reservas");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // crear instancias UsuarioSistema
    const reservas = json.data.map(r => {
      return({
        id: r.id_reserva_permanente,
        dia_semana: r.dia_semana,
        inicio: r.inicio,
        fin: r.fin,
        comentario: r.comentario?.trim() || " - ",
        recurso: r.id_recurso,
        unidades: r.unidades != null ? Number(r.unidades) : " - "
      });
    });

    return reservas;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getReservasInactivas() {
  try {
    const response = await fetch(`${API}/reservas_permanentes/inactivas`);console.log(response);
    if (!response.ok) throw new Error("Error al obtener reservas");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // crear instancias UsuarioSistema
    const reservas = json.data.map(r => {
      return({
        id: r.id_reserva_permanente,
        dia_semana: r.dia_semana,
        inicio: r.inicio,
        fin: r.fin,
        comentario: r.comentario?.trim() || " - ",
        recurso: r.id_recurso,
        unidades: r.unidades != null ? Number(r.unidades) : " - "
      });
    });

    return reservas;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getReservasRecurso(id_recurso) {
  try {
    const response = await fetch(`${API}/reservas_permanentes/recurso/${id_recurso}`);
    if (!response.ok) throw new Error("Error al obtener reservas");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // crear instancias UsuarioSistema
    const reservas = json.data.map(r => {
      return({
        id: r.id_reserva_permanente,
        dia_semana: r.dia_semana,
        inicio: r.inicio,
        fin: r.fin,
        comentario: r.comentario?.trim() || " - ",
        recurso: r.id_recurso,
        unidades: r.unidades != null ? Number(r.unidades) : " - "
      });
    });

    return reservas;
  } catch (error) {
    console.error(error);
    return [];
  }
}

