function capitalizar(string) {
    return string.charAt(0).toUpperCase()+string.slice(1).toLowerCase();
}



// Menú administrador tarjetas de reservas

function obtenerReservasAutorizar(){
    fetch(window.location.origin+"/API/reservas-pendientes")
    .then(res => res.json())
    .then(response => {
        let reservas = response.data;
        let tarjetasReservas = document.getElementById("tarjetasReservasAutorizar");
        if(!tarjetasReservas) return;
        mostrarReservasTarjetas(reservas, tarjetasReservas);
    });
}

function obtenerReservasProximas(){
    fetch(window.location.origin+"/API/reservas-proximas")
    .then(res => res.json())
    .then(response => {
        let reservas = response.data;
        let tarjetasReservas = document.getElementById("tarjetasReservasProximas");
        if(!tarjetasReservas) return;
        mostrarReservasTarjetas(reservas, tarjetasReservas);
    });
}

function mostrarReservasTarjetas(reservas, tarjetasReservas){
    tarjetasReservas.innerHTML = "";
    if(!reservas||reservas.length === 0){
        let card = document.createElement("div");
        card.className = "card h-100 reserva-card text-center";
        card.innerHTML = `
            <div class="card-body bg-secondary-subtle">No se han encontrado reservas</div>
        `;
        tarjetasReservas.appendChild(card);
    }else{
        reservas.forEach(reserva => {
            let divReserva = document.createElement("div");
            divReserva.className="card h-100 mb-4 reserva-card";
            divReserva.setAttribute("role", "button");
            divReserva.setAttribute("data-bs-toggle", "modal");
            divReserva.setAttribute("data-bs-target", "#modalReserva");
            divReserva.setAttribute("data-id", reserva.id_reserva);
            divReserva.setAttribute("data-titulo", reserva.titulo);

            let tipo='Portátil';                
            if(reserva.tipo == 'Reserva_espacio'){
                tipo='Espacio';
            }

            divReserva.innerHTML = `
                <div class="card-body bg-secondary-subtle">
                    <p class="fw-bold mb-0">Reserva #${reserva.id_reserva}</p>
                    <p class="mb-0"><span class="fw-bold">${tipo}: </span>${reserva.id_recurso}
                    <p class="mb-0"><span class="fw-bold">Fecha inicio: </span>${formatearFecha(reserva.inicio)}</p>
                    <p class="mb-0"><span class="fw-bold">Fecha fin: </span>${formatearFecha(reserva.fin)}</p>
                    ${reserva.unidades !== null ? `<p class="mb-0"><span class="fw-bold">Unidades: </span>${reserva.unidades}</p>` : ''}
                </div>
            `;

            divReserva.addEventListener("click", function(){
                let autorizada='Denegada';
                if(reserva.autorizada==null){
                    autorizada='Pendiente';
                }else if(reserva.autorizada==1){
                    autorizada='Autorizada';
                }
                document.getElementById("reserva_id").value = reserva.id_reserva;
                document.getElementById("reserva_f_creacion").value = reserva.f_creacion;
                document.getElementById("reserva_inicio").value = reserva.inicio;
                document.getElementById("reserva_fin").value = reserva.fin;
                document.getElementById("reserva_espacio_portatil").value = reserva.id_recurso;
                document.getElementById("reserva_asignatura").value = reserva.asignatura;
                document.getElementById("reserva_grupo").value = reserva.grupo;
                document.getElementById("reserva_profesor").value = reserva.profesor;
                document.getElementById("reserva_usuario").value = reserva.nombreusuario;
                document.getElementById("reserva_unidades").value = reserva.unidades;
                document.getElementById("reserva_espacio_uso").value = reserva.usaenespacio;
                document.getElementById("reserva_actividad").value = reserva.actividad;
                document.getElementById("reserva_necesidades").value = reserva.necesidades;
                document.getElementById("reserva_observaciones").value = reserva.observaciones;
                                
                if (reserva.tipo == 'Reserva_espacio') {
                    document.getElementById("div_reserva_usuario").classList.add('col-lg-6');
                    document.getElementById("div_reserva_unidades").classList.add('d-none');
                    document.getElementById("div_reserva_espacio_uso").classList.add('d-none');
                    document.getElementById("div_reserva_actividad").classList.remove('d-none');
                    document.getElementById("div_reserva_necesidades").classList.remove('d-none');
                } else {
                    document.getElementById("div_reserva_usuario").classList.remove('col-lg-6');
                    document.getElementById("div_reserva_unidades").classList.remove('d-none');
                    document.getElementById("div_reserva_espacio_uso").classList.remove('d-none');
                    document.getElementById("div_reserva_actividad").classList.add('d-none');
                    document.getElementById("div_reserva_necesidades").classList.add('d-none');
                }

                if(reserva.autorizada!=null){
                    document.getElementById("div_botones_autorizar").classList.add('d-none');
                }else{
                    document.getElementById("div_botones_autorizar").classList.remove('d-none');
                }
            });

            tarjetasReservas.appendChild(divReserva);
        });
    }
}



//API Obtener reservas para filtrarlas
function activarFiltrarReservasAutorizar(){
    let formfiltrar = document.getElementById("formFiltrarReservasAutorizar");
    if(!formfiltrar) return;
    formfiltrar.addEventListener("submit", function(e){
    e.preventDefault();
    fetch(window.location.origin+"/API/reservas-pendientes")
        .then(res => res.json())
        .then(response => {
            let reservas = response.data;
            let tarjetasReservas = document.getElementById("tarjetasReservasAutorizar");
            let modalfiltrar = document.getElementById("modalFiltrarReservasAutorizar");
            let recurso = document.getElementById("filtrarRecursoAutorizar").value;
            let fechaInicio = document.getElementById("filtrarFechaInicioAutorizar").value;
            let fechaFin = document.getElementById("filtrarFechaFinAutorizar").value;
            filtrarReservas(reservas, modalfiltrar, tarjetasReservas, recurso, fechaInicio, fechaFin);
        });
    });
}



//API Obtener reservas para filtrarlas
function activarFiltrarReservasProximas(){
    let formfiltrar = document.getElementById("formFiltrarReservasProximas");
    if(!formfiltrar) return;
    formfiltrar.addEventListener("submit", function(e){
    e.preventDefault();
    fetch(window.location.origin+"/API/reservas-proximas")
        .then(res => res.json())
        .then(response => {
            let reservas = response.data;
            let tarjetasReservas = document.getElementById("tarjetasReservasProximas");
            let modalfiltrar = document.getElementById("modalFiltrarReservasProximas");
            let recurso = document.getElementById("filtrarRecursoProximas").value;
            let fechaInicio = document.getElementById("filtrarFechaInicioProximas").value;
            let fechaFin = document.getElementById("filtrarFechaFinProximas").value;
            let tipo = document.getElementById("filtrarTipoProximas").value;
            filtrarReservas(reservas, modalfiltrar, tarjetasReservas, recurso, fechaInicio, fechaFin, tipo);
        });
    });
}



function filtrarReservas(reservas, modalfiltrar, tarjetasReservas, recurso, fechaInicio, fechaFin, tipo=null){
    if(tipo=='Espacio'){
        tipo='Reserva_espacio';
    }else if(tipo=='Portatil'){
        tipo='Reserva_material';
    }
    reservasFiltradas=reservas.filter(reserva => {
        // Filtro por recurso
        if(recurso!=="Todos"&&reserva.id_recurso!=recurso){
            return false;
        }

        // Filtro por tipo
        if(tipo!=null){
            if(tipo!=="Todos"&&reserva.tipo!=tipo){
                return false;
            }
        }

        // Filtro por fechas
        let fechaInc = new Date(reserva.inicio);

        if(fechaInicio){
            if(fechaInc < new Date(fechaInicio)){
                return false;
            }
        }

        if(fechaFin){
            if(fechaInc > new Date(fechaFin)){
                return false;
            }
        }

        return true;
    })

    mostrarReservasTarjetas(reservasFiltradas, tarjetasReservas);
    let cerrarmodal = bootstrap.Modal.getInstance(modalfiltrar) || new bootstrap.Modal(modalfiltrar);
    cerrarmodal.hide();
}



function formatearFecha(stringFecha){
    let fecha = new Date(stringFecha);

    let anyo = fecha.getFullYear();
    let mes = String(fecha.getMonth() + 1).padStart(2, '0');
    let dia = String(fecha.getDate()).padStart(2, '0');
    let hh = String(fecha.getHours()).padStart(2, '0');
    let mm = String(fecha.getMinutes()).padStart(2, '0');
    let ss = String(fecha.getSeconds()).padStart(2, '0');

    let fechaFormateada = `${dia}/${mes}/${anyo} ${hh}:${mm}:${ss}`;
    return fechaFormateada;
}