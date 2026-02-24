export async function getEspacio(id) {
  try {
    const response = await fetch(`${API}/espacios/${id}`, {
      headers: {
        "Authorization": "Bearer " + localStorage.getItem("token")
      }
    });

    if (!response.ok) throw new Error("Error al obtener espacio");

    const json = await response.json();

    if (json.status !== "success") {
      console.error("API error:", json.message);
      return null;
    }

    const e = json.data;

    const espacio = {
        id: e.id_recurso,
        descripcion: e.descripcion,
        autorizacion: e.especial,
        planta: e.nombre_planta,
        edificio: e.nombre_edificio,
        caracteristicas: e.caracteristicas
    };

    return espacio;

  } catch (error) {
    console.error("Error getEspacio:", error);
    return null;
  }
}

export async function getOtrosEspacios() {
  try {
    const response = await fetch(`${API}/otrosespacios`);
    if (!response.ok) throw new Error("Error al obtener Otros Espacios");

    const json = await response.json();

    // Mantener la estructura: edificio -> planta -> Otros Espacios[]
    return json.data;

  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getOtrosEspaciosDisponibles(fecha, hora_inicio, hora_fin) {
  try {
    const response = await fetch(`${API}/otrosespacios/disponibles`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        fecha: fecha,
        hora_inicio: hora_inicio,
        hora_fin: hora_fin
      })
    });
    if (!response.ok) throw new Error("Error al obtener aulas disponibles");

    const json = await response.json();

    // Mantener la estructura: edificio -> planta -> aulas[]
    return json.data;

  } catch (error) {
    console.error(error);
    return [];
  }
}