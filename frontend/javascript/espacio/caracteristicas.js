export async function getCaracteristicas() {
  try {
    const response = await fetch(`${API}/caracteristicas`);
    if (!response.ok) throw new Error("Error al obtener caracteristicas");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }
    // Convertimos cada reserva a un objeto plano
    const caracteristicas = json.data.map(r => ({
        id: r.id_caracteristica,
        nombre: r.nombre,
    }));

    return caracteristicas;
  } catch (error) {
    console.error(error);
    return [];
  }
}