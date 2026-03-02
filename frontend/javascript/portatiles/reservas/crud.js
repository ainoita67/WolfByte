import { getPermanentesRecurso, generarHorarioPermanentes } from "./horario_permanente.js";
import { generarHorarioRealEventos, getLiberacionesRecurso, aplicarLiberaciones, combinarDisponiblesYReservas } from "./disponibilidad.js";

export async function cargarReservas(idRecurso) {
    try {
        const res = await fetch(`${API}/reservaMaterial/material/${idRecurso}`, {
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
            text: ` ${r.unidades} unidades - ${r.asignatura} - ${r.grupo} ${r.profesor}`,
            start: r.inicio.replace(" ", "T"),
            end: r.fin.replace(" ", "T"),
            unidades: Number(r.unidades)
        }));

        return eventos; // array de eventos para el calendario
    } catch (error) {
        console.error("Error cargando reservas:", error);
    }
}

// generar el calendario completo con reservas permanentes libereaciones y reservas reales
export async function generarEventos(idRecurso, unidadesRecurso) {
    console.log("Generando eventos para recurso ID:", idRecurso, "con unidades:", unidadesRecurso);
    // Cargar reservas permanentes y slot de disponibilidad
    const permanentes = await getPermanentesRecurso(idRecurso);
    console.log("Reservas permanentes:", permanentes);
    const horario = generarHorarioPermanentes(permanentes, unidadesRecurso);
    console.log("Horario permanente generado:", horario);
    const eventos = await generarHorarioRealEventos(horario);
    console.log("Eventos horarios permanentes:", eventos);
    // Aplicar liberaciones para obtener eventos disponibles
    const liberaciones = await getLiberacionesRecurso(idRecurso);
    console.log("Liberaciones obtenidas:", liberaciones);
    const eventosdisponibles = aplicarLiberaciones(eventos, liberaciones, unidadesRecurso);
    console.log("Eventos disponibles tras aplicar liberaciones:", eventosdisponibles);
    // Cargar reservas reales
    const eventosReservas = await cargarReservas(idRecurso) || [];
    console.log("Reservas reales cargadas:", eventosReservas);
    // Combinar eventos disponibles con reservas reales (reservas reales tienen prioridad)
    const eventoscombinados = combinarDisponiblesYReservas(eventosdisponibles, eventosReservas, unidadesRecurso);
    console.log("Eventos combinados (disponibles + reservas reales):", eventoscombinados);

    return eventoscombinados;
}