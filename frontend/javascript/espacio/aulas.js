export async function getAulas() {
  try {
    const response = await fetch(`${API}/aulas`);
    if (!response.ok) throw new Error("Error al obtener aulas");

    const json = await response.json();

    // Mantener la estructura: edificio -> planta -> aulas[]
    return json.data;

  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getAulasDisponibles(fecha, hora_inicio, hora_fin) {
  try {
    const response = await fetch(`${API}/aulas/disponibles`, {
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