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
        unidades: r.unidades
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
                        unidades_libres: slot.unidades_libres,
                        text: `Disponible (${slot.unidades_libres} libres)`,
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

export function aplicarLiberaciones(events, liberaciones, unidadesRecurso) {

    const slotsBase = [
        { start: "08:50", end: "09:40" },
        { start: "09:45", end: "10:35" },
        { start: "10:40", end: "11:30" },
        { start: "12:00", end: "12:50" },
        { start: "12:55", end: "13:45" },
        { start: "13:50", end: "14:40" }
    ];

    const toMin = (t) => {
        const [h, m] = t.split(":").map(Number);
        return h * 60 + m;
    };

    let idCounter = events.length;

    liberaciones.forEach(lib => {

        const startDate = new Date(lib.inicio);
        const endDate = new Date(lib.fin);

        const formatDate = (date) => {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, "0");
            const d = String(date.getDate()).padStart(2, "0");
            return `${y}-${m}-${d}`;
        };

        const dayStr = formatDate(startDate);
        
        const libStart = startDate.getHours() * 60 + startDate.getMinutes();
        const libEnd = endDate.getHours() * 60 + endDate.getMinutes();

        slotsBase.forEach(slot => {

            const slotStart = toMin(slot.start);
            const slotEnd = toMin(slot.end);

            const overlap = slotStart < libEnd && slotEnd > libStart;
            if (!overlap) return;

            const eventStart = `${dayStr}T${slot.start}:00`;
            const eventEnd = `${dayStr}T${slot.end}:00`;

            const existingEvent = events.find(e =>
                e.start === eventStart && e.end === eventEnd
            );

            if (existingEvent) {

                // Si ya existe → sumar unidades
                const actuales = Number(existingEvent.unidades_libres || 0);
                const nuevas = Math.min(
                    actuales + Number(lib.unidades),
                    unidadesRecurso
                );
                existingEvent.unidades_libres = nuevas;
                existingEvent.text = `Disponible (${nuevas} libres)`;

            } else {

                // Si no existe → crear nuevo slot con unidades liberadas
                events.push({
                    start: eventStart,
                    end: eventEnd,
                    id: `lib-${idCounter++}`,
                    unidades_libres: Number(lib.unidades),
                    text: `Disponible (${lib.unidades} libres)`,
                    backColor: "#dcdcdc",
                    borderColor: "#c3c3c3",
                    fontColor: "#212529",
                    barHidden: true
                });

            }

        });

    });

    return events;
}

export function combinarDisponiblesYReservas(disponibles, reservas, unidadesRecurso) {

    const toTime = (d) => new Date(d).getTime();

    const reservasProcesadas = reservas.map(r => ({
        start: toTime(r.start),
        end: toTime(r.end),
        unidades: Number(r.unidades || 1)
    }));

    const disponiblesActualizados = disponibles
        .map(slot => {

            const slotStart = toTime(slot.start);
            const slotEnd = toTime(slot.end);

            // sumar unidades reservadas que se solapan
            const unidadesReservadas = reservasProcesadas
                .filter(reserva =>
                    slotStart < reserva.end && slotEnd > reserva.start
                )
                .reduce((acc, r) => acc + r.unidades, 0);

            const actuales = Number(slot.unidades_libres || unidadesRecurso);
            const nuevas = actuales - unidadesReservadas;

            if (nuevas <= 0) return null;

            return {
                ...slot,
                unidades_libres: nuevas,
                text: `Disponible (${nuevas} libres)`
            };
        })
        .filter(Boolean);

    return [
        ...reservas,
        ...disponiblesActualizados
    ];
}

