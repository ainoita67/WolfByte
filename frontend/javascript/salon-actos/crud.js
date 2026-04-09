import { obtenerSeptiembre } from "/frontend/javascript/reservas/horario_permanente.js";

import { 
    getPermanentesRecurso, 
    generarHorarioPermanentes 
} from "/frontend/javascript/reservas/horario_permanente.js";

import { 
    generarHorarioRealEventos, 
    getLiberacionesRecurso, 
    aplicarLiberaciones, 
    combinarDisponiblesYReservas 
} from "/frontend/javascript/reservas/disponibilidad.js";

/* ======================================================
   CARGAR RESERVAS REALES (COMÚN PARA TODOS LOS RECURSOS)
====================================================== */

export async function cargarReservas(idRecurso) {
    try {
        const res = await fetch(`${API}/reservaEspacio/espacio/${idRecurso}`, {
            headers: {
                "Authorization": "Bearer " + localStorage.getItem("token")
            }
        });

        const text = await res.text();

        let json;
        try {
            json = JSON.parse(text);
        } catch {
            console.error("Respuesta no es JSON:", text);
            return [];
        }

        if (json.status !== "success") {
            console.error("Error API:", json);
            return [];
        }

        if (!json.data || json.data.length === 0) {
            return [];
        }

        const eventos = json.data.map(r => ({
            id: r.id_reserva,
            text: `${r.asignatura} - ${r.grupo}\n${r.profesor} - ${r.actividad}`,
            start: r.inicio.replace(" ", "T"),
            end: r.fin.replace(" ", "T"),
            autorizada: r.autorizada
        }));

        return eventos;

    } catch (error) {
        console.error("Error cargando reservas:", error);
        return [];
    }
}


/* ======================================================
   GENERADOR COMPLETO PARA AULAS (CON HORARIO BASE)
====================================================== */

export async function generarEventos(idRecurso) {

    // 1️⃣ Reservas permanentes
    const permanentes = await getPermanentesRecurso(idRecurso);

    // 2️⃣ Generar slots disponibles base
    const horario = generarHorarioPermanentes(permanentes);

    // 3️⃣ Convertir a eventos reales desde septiembre hasta septiembre
    const eventosBase = generarHorarioRealEventos(horario);

    // 4️⃣ Aplicar liberaciones puntuales
    const liberaciones = await getLiberacionesRecurso(idRecurso);
    const eventosDisponibles = aplicarLiberaciones(eventosBase, liberaciones);

    // 5️⃣ Cargar reservas reales
    const reservasReales = await cargarReservas(idRecurso);

    // 6️⃣ Combinar (reservas reales tienen prioridad)
    const eventosFinales = combinarDisponiblesYReservas(
        eventosDisponibles,
        reservasReales
    );

    return eventosFinales;
}


/* ======================================================
   GENERADOR SIMPLE PARA SALÓN DE ACTOS
   (SIN PERMANENTES NI LIBERACIONES)
====================================================== */

export async function generarEventosSalon(idRecurso) {

    // 1️⃣ Generar todos los disponibles base
    const disponibles = generarSlotsSalon();

    // 2️⃣ Cargar reservas reales
    const reservas = await cargarReservas(idRecurso);

    // 3️⃣ Quitar disponibles que estén ocupados
    const final = combinarDisponiblesYReservas(disponibles, reservas);

    return final;
}


/* ======================================================
   GENERADOR INTELIGENTE (AUTO-DETECTA SALÓN)
   👉 Usa esta función desde tu HTML
====================================================== */

export async function generarEventosSegunRecurso(idRecurso) {

    // Si el salón tiene id "salon"
    if (idRecurso === "salon") {
        return await generarEventosSalon(idRecurso);
    }

    // Si es cualquier otro recurso (aula normal)
    return await generarEventos(idRecurso);
}

function generarSlotsSalon() {

    const { ultimo, proximo } = obtenerSeptiembre();

    const slotsBase = [
        { start: "08:50", end: "09:40" },
        { start: "09:45", end: "10:35" },
        { start: "10:40", end: "11:30" },
        { start: "12:00", end: "12:50" },
        { start: "12:55", end: "13:45" },
        { start: "13:50", end: "14:40" }
    ];

    for (let h = 15; h < 22; h++) {
        slotsBase.push({ start: `${h.toString().padStart(2,'0')}:00`, end: `${h.toString().padStart(2,'0')}:30` });
        slotsBase.push({ start: `${h.toString().padStart(2,'0')}:30`, end: `${(h+1).toString().padStart(2,'0')}:00` });
    }

    const events = [];
    let current = new Date(ultimo);
    let idCounter = 0;

    const formatDate = (date) => {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, "0");
        const d = String(date.getDate()).padStart(2, "0");
        return `${y}-${m}-${d}`;
    };

    while (current < proximo) {

        const jsDay = current.getDay();

        if (jsDay >= 1 && jsDay <= 5) {

            const dayStr = formatDate(current);

            slotsBase.forEach(slot => {
                events.push({
                    start: `${dayStr}T${slot.start}:00`,
                    end: `${dayStr}T${slot.end}:00`,
                    id: `salon-slot-${idCounter++}`,
                    text: "Disponible",
                    bloqueBase: true,
                    backColor: "#dcdcdc",
                    borderColor: "#c3c3c3",
                    fontColor: "#212529",
                    barHidden: true
                });
            });
        }

        current.setDate(current.getDate() + 1);
    }

    return events;
}