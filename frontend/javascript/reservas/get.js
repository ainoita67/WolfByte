function capitalizar(string) {
    return string.charAt(0).toUpperCase()+string.slice(1).toLowerCase();
}



// Menú administrador tarjetas de reservas

function obtenerReservasTarjetas(limite=5){
    fetch(window.location.origin+"/API/reservas")
    .then(res => res.json())
    .then(response => {
        let reservas = response.data;
        let tarjetasReservas = document.getElementById("tarjetasReservas");
        tarjetasReservas.innerHTML = "";
        if(!reservas||reservas.length === 0){
            let card = document.createElement("div");
            card.className = "card h-100 reserva-card text-center";
            card.innerHTML = `
                <div class="card-body bg-secondary-subtle">No se han encontrado reservas</div>
            `;
            tarjetasReservas.appendChild(card);
        }else{
            mostrarReservasTarjetas(reservas, limite);
        };
    });
}



function mostrarReservasTarjetas(reservas, limite){
    let num=0;
    let tarjetasReservas = document.getElementById("tarjetasReservas");
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
        reservas=reservas.reverse();
        reservas.forEach(reserva => {
            if(num<limite){
                let divIncidencia = document.createElement("div");
                divIncidencia.className="card h-100 mb-4 reserva-card";
                divIncidencia.setAttribute("role", "button");
                divIncidencia.setAttribute("data-bs-toggle", "modal");
                divIncidencia.setAttribute("data-bs-target", "#modalreserva");
                divIncidencia.setAttribute("data-id", reserva.id_reserva);
                divIncidencia.setAttribute("data-titulo", reserva.titulo);
                divIncidencia.innerHTML = `
                    <div class="card-body bg-secondary-subtle">
                        <p class="fw-bold mb-0">Incidencia #${reserva.id_reserva}</p>
                        <p class="fw-bold mb-0">${reserva.titulo}</p>
                        <p class="mb-0"><span class="fw-bold">Fecha: </span>${formatearFecha(reserva.fecha)}</p>
                        <p class="mb-0"><span class="fw-bold">Prioridad: </span>${capitalizar(reserva.prioridad)}</p>
                        <p class="mb-0"><span class="fw-bold">Estado: </span>${capitalizar(reserva.estado)}</p>
                    </div>
                `;
                if(reserva.estado=="Abierta"){
                    divIncidencia.innerHTML=divIncidencia.innerHTML+'<div class="estado-reserva-abierta abierta"></div>';
                }else if(reserva.estado=="Resuelta"){
                    divIncidencia.innerHTML=divIncidencia.innerHTML+'<div class="estado-reserva-abierta resuelta"></div>';
                }else{
                    divIncidencia.innerHTML=divIncidencia.innerHTML+'<div class="estado-reserva-abierta proceso"></div>';
                }

                divIncidencia.addEventListener("click", function(){

                    fetch(window.location.origin+"/API/user/"+reserva.id_usuario)
                    .then(res => res.json())
                    .then(response => {
                        let usuario = response.data;

                        document.getElementById("reserva_id").value = reserva.id_reserva;
                        document.getElementById("reserva_titulo").value = reserva.titulo;
                        document.getElementById("reserva_descripcion").value = reserva.descripcion;
                        document.getElementById("reserva_estado").value = capitalizar(reserva.estado);
                        document.getElementById("reserva_prioridad").value = capitalizar(reserva.prioridad);
                        document.getElementById("reserva_id_usuario").value = reserva.id_usuario;
                        document.getElementById("reserva_usuario").value = usuario.nombre;
                        document.getElementById("reserva_recurso").value = reserva.id_recurso;

                        document.getElementById("reserva_fecha").value = reserva.fecha.replace(" ", "T");
                    });

                });

                tarjetasReservas.appendChild(divIncidencia);
                num++;
            }
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