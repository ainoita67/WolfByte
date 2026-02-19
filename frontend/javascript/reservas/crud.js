import { getPermanentesRecurso, generarHorarioPermanentes } from "/frontend/javascript/reservas/horario_permanente.js";
import { generarHorarioRealEventos, getLiberacionesRecurso, aplicarLiberaciones, combinarDisponiblesYReservas } from "/frontend/javascript/reservas/disponibilidad.js";

export async function cargarReservas(idRecurso) {
    try {
        const res = await fetch(`${API}/reservaEspacio/espacio/${idRecurso}`, {
            headers: {
                "Authorization": "Bearer " + localStorage.getItem("token")
            }
        });

        const text = await res.text(); // primero leemos como texto
        let json;
        try {
            json = JSON.parse(text); // luego intentamos parsear a JSON
        } catch {
            console.error("Respuesta no es JSON:", text);
            return [];
        }

        if (json.status !== "success") {
            console.error("Error API:", json);
            return [];
        }

        // Si no hay reservas, devolver array vacío
        if (!json.data || json.data.length === 0) {
            return [];
        }

        const eventos = json.data.map(r => ({
            id: r.id_reserva,
            text: `${r.asignatura} - ${r.grupo}\n${r.profesor} - ${r.actividad}`,
            start: r.inicio.replace(" ", "T"),
            end: r.fin.replace(" ", "T")
        }));

        return eventos; // array de eventos para el calendario
    } catch (error) {
        console.error("Error cargando reservas:", error);
    }
}

// generar el calendario completo con reservas permanentes libereaciones y reservas reales
export async function generarEventos(idRecurso) {
    // Cargar reservas permanentes y slot de disponibilidad
    const permanentes = await getPermanentesRecurso(idRecurso);
    const horario = generarHorarioPermanentes(permanentes);
    const eventos = await generarHorarioRealEventos(horario);
    // Aplicar liberaciones para obtener eventos disponibles
    const liberaciones = await getLiberacionesRecurso(idRecurso);
    const eventosdisponibles = aplicarLiberaciones(eventos, liberaciones);
    // Cargar reservas reales
    const eventosReservas = await cargarReservas(idRecurso) || [];
    // Combinar eventos disponibles con reservas reales (reservas reales tienen prioridad)
    const eventoscombinados = combinarDisponiblesYReservas(eventosdisponibles, eventosReservas);

    return eventoscombinados;
}