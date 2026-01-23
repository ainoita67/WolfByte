document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('contenedorReservas');

    async function cargarReservas() {
        try {
            const res = await fetch('http://192.168.13.202/API/mis-reservas', {
                credentials: 'include'
            });

            const json = await res.json();

            if (json.status === 'error') {
                contenedor.innerHTML = `<p class="text-danger">${json.message}</p>`;
                return;
            }

            const reservas = json.data;

            if (!reservas || !reservas.length) {
                contenedor.innerHTML = `<p>No tienes reservas</p>`;
                return;
            }

            contenedor.innerHTML = '';

            reservas.forEach(r => {
                const estadoClase = r.autorizada == 1 ? 'estado-verde' : 'estado-rojo';

                const card = document.createElement('div');
                card.className = 'col-12 col-md-6 col-lg-4';

                card.innerHTML = `
                    <div class="reserva-card shadow-sm h-100">
                        <div class="reserva-body p-3">
                            <h6><b>Reserva #${r.id_reserva}</b></h6>
                            <p><b>Espacio / Material:</b> ${r.tipo}</p>
                            <p><b>Asignatura:</b> ${r.asignatura}</p>
                            <p><b>Profesor:</b> ${r.profesor}</p>
                            <p><b>Grupo:</b> ${r.grupo}</p>
                            <p><b>Fecha inicio:</b> ${r.inicio}</p>
                            <p><b>Fecha fin:</b> ${r.fin}</p>
                        </div>
                        <div class="reserva-estado ${estadoClase}"></div>
                    </div>
                `;

                contenedor.appendChild(card);
            });

        } catch (e) {
            contenedor.innerHTML = `<p class="text-danger">Error al cargar reservas</p>`;
        }
    }

    cargarReservas();
});
