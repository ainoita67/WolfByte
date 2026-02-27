export async function getPermanentesRecurso(idRecurso) {
  try {
    const response = await fetch(`${API}/reservas_permanentes/recurso/${idRecurso}`);
    if (!response.ok) throw new Error("Error al obtener reservas permanentes");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // Convertimos cada reserva a un objeto plano
    const rPermanentes = json.data.map(r => ({
        dia_semana: r.dia_semana,
        inicio: r.inicio,
        fin: r.fin,
        unidades: r.unidades
    }));

    return rPermanentes;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export function generarHorarioPermanentes(rPermanentes, unidadesRecurso) {
    const diasSemana = [1, 2, 3, 4, 5]; // lunes a viernes
    const slotsBase = [
        { start: "08:50", end: "09:40" },
        { start: "09:45", end: "10:35" },
        { start: "10:40", end: "11:30" },
        { start: "12:00", end: "12:50" },
        { start: "12:55", end: "13:45" },
        { start: "13:50", end: "14:40" }
    ];

    const tiempoAMinutos = t => {
        const [h, m] = t.split(":").map(Number);
        return h * 60 + m;
    };

    const horario = [];

    diasSemana.forEach(dia => {

        const reservasDia = rPermanentes.filter(r => r.dia_semana === dia);

        const slotsDia = slotsBase
            .map(slot => {

                const slotStart = tiempoAMinutos(slot.start);
                const slotEnd = tiempoAMinutos(slot.end);

                // Sumamos unidades de reservas que se solapan
                const unidadesReservadas = reservasDia
                    .filter(reserva => {
                        const resStart = tiempoAMinutos(reserva.inicio);
                        const resEnd = tiempoAMinutos(reserva.fin);
                        return slotStart < resEnd && slotEnd > resStart;
                    })
                    .reduce((total, reserva) => total + Number(reserva.unidades), 0);

                const unidadesLibres = unidadesRecurso - unidadesReservadas;

                // Si no quedan unidades, no devolvemos slot
                if (unidadesLibres <= 0) return null;

                return {
                    start: slot.start,
                    end: slot.end,
                    unidades_libres: unidadesLibres
                };
            })
            .filter(Boolean); // elimina null

        horario.push({
            dia_semana: dia,
            slots: slotsDia
        });
    });

    return horario;
}

export function obtenerSeptiembre() {
  const hoy = new Date();
  const anio = hoy.getFullYear();

  // 1 de septiembre de este año
  const septiembreActual = new Date(anio, 8, 1); // meses en JS: 0=enero, 8=septiembre

  let ultimoSeptiembre, proximoSeptiembre;

  if (hoy >= septiembreActual) {
    // hoy es después o igual al 1 de septiembre -> último = este año, próximo = año siguiente
    ultimoSeptiembre = septiembreActual;
    proximoSeptiembre = new Date(anio + 1, 8, 1);
  } else {
    // hoy es antes del 1 de septiembre -> último = año anterior, próximo = este año
    ultimoSeptiembre = new Date(anio - 1, 8, 1);
    proximoSeptiembre = septiembreActual;
  }

  return {
    ultimo: ultimoSeptiembre,
    proximo: proximoSeptiembre
  };
}
