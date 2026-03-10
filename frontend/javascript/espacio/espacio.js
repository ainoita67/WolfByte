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

    // Devolver el objeto del espacio
    return json.data;

  } catch (error) {
    console.error("Error getEspacio:", error);
    return null;
  }
}