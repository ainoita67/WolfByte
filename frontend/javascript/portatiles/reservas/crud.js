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
            unidades: Number(r.unidades),
            asignatura: r.asignatura,
            grupo: r.grupo,
            profesor: r.profesor,
            observaciones: r.observaciones,
            autorizada: r.autorizada,
            id_usuario: r.id_usuario,
            autorizada: r.autorizada,
            id_usuario_autoriza: r.id_usuario_autoriza ?? null,
            f_creacion: r.f_creacion,
            descripcion:r.descripcion ?? null,
            activo: r.activo ?? null,
            especial: r.especial ?? null,
            numero_planta: r.numero_planta ?? null,
            planta: r.nombre_planta ?? null,
            id_edificio: r.id_edificio ?? null,
            edificio: r.nombre_edificio ?? null,
            usaenespacio: r.usaenespacio ?? null,
            unidades_libres: r.unidades_libres
        }));

        return eventos; // array de eventos para el calendario
    } catch (error) {
        console.error("Error cargando reservas:", error);
    }
}

// generar el calendario completo con reservas permanentes libereaciones y reservas reales
export async function generarEventos(idRecurso, unidadesRecurso) {
    // Cargar reservas permanentes y slot de disponibilidad
    const permanentes = await getPermanentesRecurso(idRecurso);
    const horario = generarHorarioPermanentes(permanentes, unidadesRecurso);
    const eventos = await generarHorarioRealEventos(horario);
    // Aplicar liberaciones para obtener eventos disponibles
    const liberaciones = await getLiberacionesRecurso(idRecurso);
    const eventosdisponibles = aplicarLiberaciones(eventos, liberaciones, unidadesRecurso);
    // Cargar reservas reales
    const eventosReservas = await cargarReservas(idRecurso) || [];
    // Combinar eventos disponibles con reservas reales (reservas reales tienen prioridad)
    const eventoscombinados = combinarDisponiblesYReservas(eventosdisponibles, eventosReservas, unidadesRecurso);
    return eventoscombinados;
    

}