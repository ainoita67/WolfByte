export async function getCarrito(id) {
  try {
    const response = await fetch(`${API}/material/${id}`, {
      headers: {
        "Authorization": "Bearer " + localStorage.getItem("token")
      }
    });

    if (!response.ok) throw new Error("Error al obtener carrito");

    const json = await response.json();

    if (json.status !== "success") {
      console.error("API error:", json.message);
      return null;
    }

    const c = json.data;

    const carrito = {
        id: c.id_recurso,
        descripcion: c.descripcion,
        autorizacion: c.especial,
        planta: c.nombre_planta,
        edificio: c.nombre_edificio,
        unidades: c.unidades
    };

    return carrito;

  } catch (error) {
    console.error("Error getCarrito:", error);
    return null;
  }
}

export async function getCarritosDisponibles(fecha, hora_inicio, hora_fin) {
  try {
    const response = await fetch(`${API}/portatiles/disponibles`, {
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

    return json.data.map(item => {
      const portatiles = parseInt(item.portatiles, 10) || 0;
      const reservados = parseInt(item.reservados, 10) || 0;
      const disponibles = Math.max(portatiles - reservados, 0);

      return {
        id: item.id_recurso,
        descripcion: item.descripcion || "",
        edificio: item.edificio || "Sin edificio",
        planta: item.planta || "Sin planta",
        totales: portatiles,
        reservados: reservados,
        disponibles: disponibles,
      };
    });

  } catch (error) {
    console.error(error);
    return [];
  }
}