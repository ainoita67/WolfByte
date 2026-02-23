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
        tarjetasReservas.innerHTML = "";
        if(!reservas||reservas.length === 0){
            let card = document.createElement("div");
            card.className = "card h-100 reserva-card text-center";
            card.innerHTML = `
                <div class="card-body bg-secondary-subtle">No se han encontrado reservas</div>
            `;
            tarjetasReservas.appendChild(card);
        }else{
            mostrarReservasAutorizarTarjetas(reservas, tarjetasReservas);
        }
    });
}

function obtenerReservasProximas(){
    fetch(window.location.origin+"/API/reservas-proximas")
    .then(res => res.json())
    .then(response => {
        let reservas = response.data;
        let tarjetasReservas = document.getElementById("tarjetasReservasProximas");
        if(!tarjetasReservas) return;
        tarjetasReservas.innerHTML = "";
        if(!reservas||reservas.length === 0){
            let card = document.createElement("div");
            card.className = "card h-100 reserva-card text-center";
            card.innerHTML = `
                <div class="card-body bg-secondary-subtle">No se han encontrado reservas</div>
            `;
            tarjetasReservas.appendChild(card);
        }else{
            mostrarReservasAutorizarTarjetas(reservas, tarjetasReservas);
        }
    });
}

function mostrarReservasAutorizarTarjetas(reservas, tarjetasReservas){
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
            divReserva.innerHTML = `
                <div class="card-body bg-secondary-subtle">
                    <p class="fw-bold mb-0">Reserva #${reserva.id_reserva}</p>
                    <p class="mb-0"><span class="fw-bold">Espacio/Material: </span>${reserva.id_recurso}
                    <p class="mb-0"><span class="fw-bold">Fecha inicio: </span>${formatearFecha(reserva.inicio)}</p>
                    <p class="mb-0"><span class="fw-bold">Fecha fin: </span>${formatearFecha(reserva.fin)}</p>
                    ${reserva.unidades !== null ? `<p class="mb-0"><span class="fw-bold">Unidades: </span>${reserva.unidades}</p>` : ''}
                </div>
            `;
            if(reserva.estado=="Abierta"){
                divReserva.innerHTML=divReserva.innerHTML+'<div class="estado-reserva-abierta abierta"></div>';
            }else if(reserva.estado=="Resuelta"){
                divReserva.innerHTML=divReserva.innerHTML+'<div class="estado-reserva-abierta resuelta"></div>';
            }else{
                divReserva.innerHTML=divReserva.innerHTML+'<div class="estado-reserva-abierta proceso"></div>';
            }

            divReserva.addEventListener("click", function(){
                let autorizada='Denegada';
                if(reserva.autorizada==null){
                    autorizada='Pendiente';
                }else if(reserva.autorizada==1){
                    autorizada='Autorizada';
                }
                document.getElementById("reserva_id").value = reserva.id_reserva;
                document.getElementById("reserva_autorizada").value = autorizada;
                document.getElementById("reserva_f_creacion").value = reserva.f_creacion;
                document.getElementById("reserva_inicio").value = reserva.inicio;
                document.getElementById("reserva_fin").value = reserva.fin;
                document.getElementById("reserva_espacio_portatil").value = reserva.id_recurso;
                document.getElementById("reserva_asignatura").value = reserva.asignatura;
                document.getElementById("reserva_grupo").value = reserva.grupo;
                document.getElementById("reserva_profesor").value = reserva.profesor;
                document.getElementById("reserva_unidades").value = reserva.unidades;
                document.getElementById("reserva_espacio_uso").value = reserva.usaenespacio;
                document.getElementById("reserva_actividad").value = reserva.actividad;
                document.getElementById("reserva_necesidades").value = reserva.necesidades;
                document.getElementById("reserva_observaciones").value = reserva.observaciones;
                                
                if (reserva.tipo == 'Reserva_espacio') {
                    document.getElementById("div_reserva_unidades").classList.add('d-none');
                    document.getElementById("div_reserva_espacio_uso").classList.add('d-none');
                    document.getElementById("div_reserva_actividad").classList.remove('d-none');
                    document.getElementById("div_reserva_necesidades").classList.remove('d-none');
                } else {
                    document.getElementById("div_reserva_unidades").classList.remove('d-none');
                    document.getElementById("div_reserva_espacio_uso").classList.remove('d-none');
                    document.getElementById("div_reserva_actividad").classList.add('d-none');
                    document.getElementById("div_reserva_necesidades").classList.add('d-none');
                }
            });

            tarjetasReservas.appendChild(divReserva);
        });
    }
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