import { getPermanentesRecurso, generarHorarioPermanentes } from "/frontend/javascript/salon-actos/horario_permanente.js";

export async function cargarReservas(idRecurso) {
    try {
        const res = await fetch(`${API}/reservaEspacio/espacio/${idRecurso}`, {
            headers: {
                "Authorization": "Bearer " + localStorage.getItem("token")
            }
        });

        if (!res.ok) {
            console.error("Error HTTP:", res.status);
            return [];
        }

        const json = await res.json();

        if (json.status !== "success" || !Array.isArray(json.data)) {
            console.error("Respuesta inválida:", json);
            return [];
        }

        return json.data.map(r => ({
            id: r.id_reserva,

            text: `${r.asignatura} - ${r.grupo}\n${r.profesor} - ${r.actividad}`,

            start: r.inicio.replace(" ", "T"),
            end: r.fin.replace(" ", "T"),

            // IMPORTANTÍSIMO
            asignatura: r.asignatura,
            grupo: r.grupo,
            profesor: r.profesor,
            actividad: r.actividad,

            bloqueBase: false,   // CLAVE

            backColor: "#2a457e",
            borderColor: "#1f3563",
            fontColor: "#ffffff"
        }));

    } catch (error) {
        console.error("Error cargando reservas:", error);
        return [];
    }
}


// generar el calendario completo con reservas permanentes libereaciones y reservas reales
export async function generarEventos(idRecurso) {
    // Cargar reservas permanentes y slot de disponibilidad
    const horario = generarHorarioPermanentes(permanentes);
    const eventos = await generarHorarioRealEventos(horario);
    // Aplicar liberaciones para obtener eventos disponibles
    // Cargar reservas reales
    const eventosReservas = await cargarReservas(idRecurso);
    // Combinar eventos disponibles con reservas reales (reservas reales tienen prioridad)
    const eventoscombinados = combinarDisponiblesYReservas(eventosdisponibles, eventosReservas);

    return eventoscombinados;
}