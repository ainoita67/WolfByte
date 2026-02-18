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
    }));

    return rPermanentes;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export function generarHorarioPermanentes(rPermanentes) {
    const diasSemana = [1, 2, 3, 4, 5]; // lunes a viernes
    const slotsBase = [
        { start: "08:50", end: "09:40" },
        { start: "09:45", end: "10:35" },
        { start: "10:40", end: "11:30" },
        { start: "12:00", end: "12:50" },
        { start: "12:55", end: "13:45" },
        { start: "13:50", end: "14:40" }
    ];

    // Añadir franjas cada 30 min a partir de las 15:00 hasta 21:30
    for (let h = 15; h < 22; h++) {
        slotsBase.push({ start: `${h.toString().padStart(2,'0')}:00`, end: `${h.toString().padStart(2,'0')}:30` });
        slotsBase.push({ start: `${h.toString().padStart(2,'0')}:30`, end: `${(h+1).toString().padStart(2,'0')}:00` });
    }

    // Función para convertir "HH:MM" a minutos
    const tiempoAMinutos = t => {
        const [h, m] = t.split(":").map(Number);
        return h * 60 + m;
    };

    const horario = [];

    diasSemana.forEach(dia => {
        // Copiamos los slotsBase
        let slotsDia = [...slotsBase];

        // Filtramos los slots que se solapan con reservas del día
        const reservasDia = rPermanentes.filter(r => r.dia_semana === dia);

        slotsDia = slotsDia.filter(slot => {
            const slotStart = tiempoAMinutos(slot.start);
            const slotEnd = tiempoAMinutos(slot.end);

            //si almenos una reserva se solapa con el slot "some" devuelve true con ! lo cambiamos a false para que el filter elimine ese slot.
            return !reservasDia.some(reserva => {
                const resStart = tiempoAMinutos(reserva.inicio);
                const resEnd = tiempoAMinutos(reserva.fin);

                // Devuelve true si hay solapamiento
                return slotStart < resEnd && slotEnd > resStart;
            });
        });

        // Añadimos al array final
        horario.push({ dia_semana: dia, slots: slotsDia });
    });

    return horario; // array de objetos { dia_semana, slots: [...] }
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
