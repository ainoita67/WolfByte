import { obtenerSeptiembre } from "./horario_permanente.js";

export async function getLiberacionesRecurso(idRecurso) {
  try {
    const response = await fetch(`${API}/liberaciones/recurso/${idRecurso}`);
    if (!response.ok) throw new Error("Error al obtener liberaciones");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }
    // Convertimos cada reserva a un objeto plano
    const liberaciones = json.data.map(r => ({
        id: r.id,
        inicio: r.inicio,
        fin: r.fin,
        comentario: r.comentario,
        id_reserva: r.id_reserva,
        id_reserva_permanente: r.id_reserva_permanente,
    }));

    return liberaciones;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export function generarHorarioRealEventos(horarioPermanente) {
    const { ultimo, proximo } = obtenerSeptiembre();

    const events = [];
    let current = new Date(ultimo);
    let idCounter = 0;

    // Helper: formato YYYY-MM-DD
    const formatDate = (date) => {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, "0");
        const d = String(date.getDate()).padStart(2, "0");
        return `${y}-${m}-${d}`;
    };

    while (current < proximo) {
        // JS: 0=domingo, 1=lunes...6=sábado
        const jsDay = current.getDay();

        // Convertimos a tu formato: lunes=1 ... viernes=5
        if (jsDay >= 1 && jsDay <= 5) {
            const diaSemana = jsDay;

            // Buscar slots de ese día en el horario permanente
            const diaHorario = horarioPermanente.find(d => d.dia_semana === diaSemana);

            if (diaHorario && diaHorario.slots.length > 0) {
                const dayStr = formatDate(current);

                diaHorario.slots.forEach(slot => {
                    events.push({
                        start: `${dayStr}T${slot.start}:00`,
                        end: `${dayStr}T${slot.end}:00`,
                        id: `slot-${idCounter++}`,
                        text: "Disponible",
                        backColor: "#dcdcdc",
                        borderColor: "#c3c3c3",
                        fontColor: "#212529",
                        barHidden: true
                    });
                });
            }
        }

        // Siguiente día
        current.setDate(current.getDate() + 1);
    }

    return events;
}

export function aplicarLiberaciones(events, liberaciones) {

    // === slots base (mismo patrón que usas en permanente) ===
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

    // Convertir HH:MM a minutos
    const toMin = (t) => {
        const [h, m] = t.split(":").map(Number);
        return h * 60 + m;
    };

    let idCounter = events.length;

    liberaciones.forEach(lib => {

        // inicio y fin vienen en formato ISO (YYYY-MM-DD HH:MM o similar)
        const startDate = new Date(lib.inicio);
        const endDate = new Date(lib.fin);

        const dayStr = startDate.toISOString().slice(0,10);

        const libStart = startDate.getHours() * 60 + startDate.getMinutes();
        const libEnd = endDate.getHours() * 60 + endDate.getMinutes();

        // Buscar qué slots del patrón entran dentro de la liberación
        slotsBase.forEach(slot => {

            const slotStart = toMin(slot.start);
            const slotEnd = toMin(slot.end);

            // solapamiento
            const overlap = slotStart < libEnd && slotEnd > libStart;

            if (!overlap) return;

            const eventStart = `${dayStr}T${slot.start}:00`;
            const eventEnd = `${dayStr}T${slot.end}:00`;

            // Evitar duplicados (muy importante)
            const exists = events.some(e =>
                e.start === eventStart && e.end === eventEnd
            );

            if (!exists) {
                events.push({
                    start: eventStart,
                    end: eventEnd,
                    id: `lib-${idCounter++}`,
                    text: "Disponible (liberado)",
                    backColor: "#dcdcdc",
                    borderColor: "#c3c3c3",
                    fontColor: "#212529",
                    barColor: "#2a457eb6",
                    barHidden: false
                });
            }
        });

    });

    return events;
}

export function combinarDisponiblesYReservas(disponibles, reservas) {

    const toTime = (d) => new Date(d).getTime();

    // Preprocesar reservas (más eficiente)
    const reservasProcesadas = reservas.map(r => ({
        start: toTime(r.start),
        end: toTime(r.end)
    }));

    // 1. Eliminar disponibles que estén ocupados
    const disponiblesFiltrados = disponibles.filter(slot => {

        const slotStart = toTime(slot.start);
        const slotEnd = toTime(slot.end);

        const ocupado = reservasProcesadas.some(reserva =>
            slotStart < reserva.end && slotEnd > reserva.start
        );

        return !ocupado;
    });

    // 2. Concatenar reservas reales
    const resultado = [
        ...disponiblesFiltrados,
        ...reservas
    ];

    return resultado;
}


