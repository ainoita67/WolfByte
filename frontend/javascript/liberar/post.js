//API Crear liberaciones
function activarLiberar() {
    let formcrear = document.getElementById("formLiberar");
    if(!formcrear) return;
    formcrear.addEventListener("submit", function (e) {
        e.preventDefault();
        let edificio = document.getElementById("selectedificio").value;
        let planta = document.getElementById("selectplanta").value;
        let aula = document.getElementById("selectaula").value;

        let fechaFormulario = document.getElementById("fecha").value;
        let horaInicio = document.getElementById("hora_inicio").value;
        let horaFin = document.getElementById("hora_fin").value;

        let observaciones = document.getElementById("observaciones").value ?? null;
        if (!observaciones||observaciones=="") {
            observaciones = null;
        }

        if (!edificio||!planta||!aula||!fechaFormulario||!horaInicio||!horaFin){
            mostrarToast("No se han rellenado los campos obligatorios", "danger");
        }else{
            liberarReserva(observaciones, aula, fechaFormulario, horaInicio, horaFin);
        }
    });
}



async function obtenerReservas(){
    let res=await fetch(window.location.origin+"/API/reservas_permanentes");
    let response = await res.json();
    return response.data;
}



async function obtenerLiberaciones(){
    let res=await fetch(window.location.origin+"/API/liberaciones");
    let response = await res.json();
    return response.data;
}



async function comprobarLiberacion(fecha, reservaLiberar){
    let liberaciones=await obtenerLiberaciones()
    let n=liberaciones.filter(liberacion => {
        return (
            liberacion.inicio==fecha+" "+reservaLiberar.inicio||
            liberacion.fin==fecha+" "+reservaLiberar.fin||
            liberacion.id_reserva_permanente==reservaLiberar.id_reserva_permanente||
            liberacion.unidades==reservaLiberar.unidades
        );
    });
    if(n.length>0){
        return false;
    }else{
        return true;
    }
}



async function liberarReserva(observaciones=null, aula, fecha, horaInicio, horaFin){
    let reservas=await obtenerReservas()

    let fechaInicio=formatearFecha(fecha, horaInicio);
    let fechaFin=formatearFecha(fecha, horaFin);

    let inicio=new Date(fechaInicio);
    let fin=new Date(fechaFin)
    if(inicio<new Date()||fin<new Date()){
        mostrarToast("Las fechas deben ser posteriores al día de hoy", "warning");
        mostrarToast("Error al crear la liberación", "danger");
    }else{
        let diasemana=inicio.getDay();

        if(inicio<fin){
            console.log(reservas);
            reservasEntreFechas=[];
            reservas.forEach(reserva => {
                if(diasemana==reserva.dia_semana&&aula==reserva.id_recurso){
                    let rinicio=new Date(fecha+" "+reserva.inicio);
                    let rfin=new Date(fecha+" "+reserva.fin);
                    if(inicio<=rinicio&&fin>=rfin){
                        reservasEntreFechas.push(reserva);
                    }
                }
            });

            if(reservasEntreFechas.length==0){
                mostrarToast("No se han encontrado reservas permanentes entre esas fechas", "warning");
            }else{
                let nliberaciones=0;
                reservasEntreFechas.forEach(async (reservaLiberar) => {
                    if(await comprobarLiberacion(fecha, reservaLiberar)){
                        fetch(window.location.origin+"/API/liberaciones/", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({inicio: fecha+" "+reservaLiberar.inicio, fin: fecha+" "+reservaLiberar.fin, comentario: observaciones, id_reserva: null, id_reserva_permanente: reservaLiberar.id_reserva_permanente, unidades: reservaLiberar.unidades})
                        })
                        .then(res => res.json())
                        .then(response => {
                            if (response.status === "success") {
                                nliberaciones++;

                                // Limpiar input
                                document.getElementById("formLiberar").reset();

                                mostrarToast("Liberación creada correctamente<br>Inicio: "+invertirFecha(fecha, reservaLiberar.inicio)+"<br>Fin: "+invertirFecha(fecha, reservaLiberar.fin), "success");
                                // Recargar
                                let selectedificio = document.getElementById("selectedificio");
                                selectedificio.getElementsByTagName("option")[0].selected=true;
                                document.getElementById("divplanta").classList.add("d-none");
                                document.getElementById("divaula").classList.add("d-none");

                                let divfechas = document.querySelectorAll(".divfechahora");
                                divfechas.forEach(divfecha => {
                                    divfecha.classList.remove("d-block");
                                    divfecha.classList.add("d-none");
                                });

                                obtenerEdificios();
                            } else {
                                mostrarToast("Error al crear la liberación<br>Inicio: "+invertirFecha(fecha, reservaLiberar.inicio)+"<br>Fin: "+invertirFecha(fecha, reservaLiberar.fin), "danger");
                            }
                        })
                        .catch(err => console.error("Error al crear la liberación:", err));
                    }
                });
                if(nliberaciones==0){
                    mostrarToast("La liberación para esa reserva permanente ya estaba creada", "warning");
                }
            }
        }else{
            mostrarToast("Las fechas introducidas no son correctas", "warning");
            mostrarToast("Error al crear la liberación", "danger");
        }
    }
}



function formatearFecha(fechaInicial, hora){
    let fecha = new Date(`${fechaInicial}T${hora}`);
    let anyo = fecha.getFullYear();
    let mes = String(fecha.getMonth() + 1).padStart(2, '0');
    let dia = String(fecha.getDate()).padStart(2, '0');
    let hh = String(fecha.getHours()).padStart(2, '0');
    let mm = String(fecha.getMinutes()).padStart(2, '0');
    let ss = String(fecha.getSeconds()).padStart(2, '0');

    return `${anyo}-${mes}-${dia} ${hh}:${mm}:${ss}`;
}



function invertirFecha(fechaInicial, hora){
    let fecha = new Date(`${fechaInicial}T${hora}`);
    let anyo = fecha.getFullYear();
    let mes = String(fecha.getMonth() + 1).padStart(2, '0');
    let dia = String(fecha.getDate()).padStart(2, '0');
    let hh = String(fecha.getHours()).padStart(2, '0');
    let mm = String(fecha.getMinutes()).padStart(2, '0');
    let ss = String(fecha.getSeconds()).padStart(2, '0');

    return `${dia}-${mes}-${anyo} ${hh}:${mm}:${ss}`;
}



function mostrarToast(mensaje, tipo = 'success') {    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    let toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    
    let bgClass = 'bg-success';
    let textColor = 'text-white';
    
    if (tipo === 'error'||tipo === 'danger') {
        bgClass = 'bg-danger';
    } else if (tipo === 'warning') {
        bgClass = 'bg-warning';
        textColor = 'text-black';
    } else if (tipo === 'info') {
        bgClass = 'bg-info';
    }
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center ${textColor} ${bgClass} border-0 fs-6" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    let toastElement = document.getElementById(toastId);
    let toast = new bootstrap.Toast(toastElement, {
        animation: true,
        autohide: true,
        delay: 5000
    });
    
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}