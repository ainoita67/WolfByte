function obtenerMisReservas(){
    usuario=sessionStorage.getItem("id_usuario");
    console.log(window.location.origin+"/API/mis-reservas/"+usuario);
    fetch(window.location.origin+"/API/mis-reservas/"+usuario)
    .then(res => res.json())
    .then(response => {
        let reservas = response.data;
        let tarjetasReservas = document.getElementById("misReservasTarjetas");
        if(!tarjetasReservas) return;
        mostrarMisReservas(reservas, tarjetasReservas);
    });
}

function mostrarMisReservas(reservas, tarjetasReservas){
    tarjetasReservas.innerHTML = "";
    if(!reservas||reservas.length === 0){
        let card = document.createElement("div");
        card.className = "card h-100 p-0 mb-4 reserva-card text-center";
        card.innerHTML = `
            <div class="card-body bg-secondary-subtle">No se han encontrado reservas</div>
        `;
        tarjetasReservas.appendChild(card);
    }else{
        reservas.forEach(reserva => {
            let div = document.createElement("div");
            div.className="col-lg-3 col-6";
            let divReserva = document.createElement("div");
            divReserva.className="card h-100 p-0 mb-4 reserva-card";
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
            if(reserva.autorizada=="1"){
                divReserva.innerHTML=divReserva.innerHTML+'<div class="aceptado-rechazado aceptado"></div>';
            }else if(reserva.autorizada=="0"){
                divReserva.innerHTML=divReserva.innerHTML+'<div class="aceptado-rechazado rechazado"></div>';
            }else{
                divReserva.innerHTML=divReserva.innerHTML+'<div class="aceptado-rechazado"></div>';
            }

            divReserva.addEventListener("click", function(){
                mostrarDatosModal(reserva);
            });

            div.appendChild(divReserva);
            tarjetasReservas.appendChild(div);
        });
    }
}


function mostrarDatosModal(reserva){
    console.log(reserva);
    let selectNecesidades = document.getElementById("reserva_necesidades");

    if(reserva.tipo == "Reserva_espacio"){
        let necesidades=[];

        if(typeof reserva.necesidades === "string"){
            necesidades = reserva.necesidades ? reserva.necesidades.split(',').map(n => n.trim()) : [];
        }

        for (let i = 0; i < selectNecesidades.options.length; i++) {
            selectNecesidades.options[i].selected = false;
            selectNecesidades.options[i].classList.remove("border", "border-primary");
        }

        necesidades.forEach(nec => {
            for (let i = 0; i < selectNecesidades.options.length; i++) {
                if (selectNecesidades.options[i].value === nec) {
                    selectNecesidades.options[i].selected = true;
                    selectNecesidades.options[i].classList.add("border", "border-primary");
                }
            }
        });
    }

    let autorizada='Denegada';
    if(reserva.autorizada==null){
        autorizada='Pendiente';
    }else if(reserva.autorizada==1){
        autorizada='Autorizada';
    }
    document.getElementById("reserva_autorizada").value = autorizada;
    document.getElementById("reserva_id").value = reserva.id_reserva;
    document.getElementById("reserva_f_creacion").value = reserva.f_creacion;
    document.getElementById("reserva_inicio").value = reserva.inicio;
    document.getElementById("reserva_fin").value = reserva.fin;
    document.getElementById("reserva_tipo").value = reserva.tipo;
    document.getElementById("reserva_espacio_portatil").value = reserva.id_recurso;
    document.getElementById("reserva_asignatura").value = reserva.asignatura;
    document.getElementById("reserva_grupo").value = reserva.grupo;
    document.getElementById("reserva_profesor").value = reserva.profesor;
    document.getElementById("reserva_id_usuario").value = reserva.id_usuario;
    document.getElementById("reserva_usuario").value = reserva.nombreusuario;
    document.getElementById("reserva_id_usuario_autoriza").value = reserva.id_usuario_autoriza;
    
    document.getElementById("reserva_unidades").value = reserva.unidades;
    document.getElementById("reserva_espacio_uso").value = reserva.usaenespacio;
    document.getElementById("reserva_actividad").value = reserva.actividad;
    document.getElementById("reserva_observaciones").value = reserva.observaciones;
                    
    if (reserva.tipo == 'Reserva_espacio') {
        document.getElementById("div_reserva_usuario").classList.add('col-lg-6');
        document.getElementById("div_reserva_unidades").classList.add('d-none');
        document.getElementById("reserva_unidades").required = false;
        document.getElementById("div_reserva_espacio_uso").classList.add('d-none');
        document.getElementById("reserva_espacio_uso").required = false;
        document.getElementById("div_reserva_actividad").classList.remove('d-none');
        document.getElementById("reserva_actividad").required = true;
        document.getElementById("div_reserva_necesidades").classList.remove('d-none');
    } else {
        document.getElementById("div_reserva_usuario").classList.remove('col-lg-6');
        document.getElementById("div_reserva_unidades").classList.remove('d-none');
        document.getElementById("reserva_unidades").required = true;
        document.getElementById("div_reserva_espacio_uso").classList.remove('d-none');
        document.getElementById("reserva_espacio_uso").required = true;
        document.getElementById("div_reserva_actividad").classList.add('d-none');
        document.getElementById("reserva_actividad").required = false;
        document.getElementById("div_reserva_necesidades").classList.add('d-none');
    }
}



//Editar reservas
function activarEditarReserva() {
    let formeditar = document.getElementById("formEditarReserva");
    if(!formeditar) return;
    formeditar.addEventListener("submit", function (e) {
        e.preventDefault();

        let id = document.getElementById("editId").value;
        let fecha = document.getElementById("editFecha").value;
        let id_recurso = document.getElementById("editRecurso").value;
        let titulo = document.getElementById("editTitulo").value;
        let descripcion = document.getElementById("editDescripcion").value;
        let usuario = 2;
        let prioridad = document.getElementById("editPrioridad").value;
        let estado = document.getElementById("editEstado").value;
        if (!id) return;
        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalEditar")
        );
        modificarReserva(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal);

        // Permite que la página vuelva a interactuar
        document.querySelectorAll('.modal-backdrop').forEach(elemento => elemento.remove());
        document.body.classList.remove('modal-open');

        obtenerMisReservas();
    });
}



//API Editar reservas
async function modificarReserva(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal){
    if(tipo=="Reserva_espacio"||tipo=="Reserva_material"){
        let resultado=0;
        if(tipo=="Reserva_espacio"){
            resultado=await modificarReservaEspacio(id, id_recurso, actividad, necesidades, inicio, fin);
        }else if(tipo=="Reserva_material"&&unidades>0){
            resultado=await modificarReservaPortatil(id, id_recurso, unidades, espacio_uso, inicio, fin);
        }
        if(resultado!=1){
            mostrarToast("Error al actualizar la reserva", 'danger');
        }else{
            fetch(window.location.origin+"/API/reservas/"+id, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({asignatura: asignatura, autorizada: autorizada, observaciones: observaciones, grupo: grupo, profesor: profesor, f_creacion: fechacreacion, inicio: inicio, fin: fin, id_usuario: usuario, id_usuario_autoriza: usuarioautoriza, tipo: tipo})
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === "success") {
                    // Cerrar modal
                    modal.hide();

                    // Limpiar input
                    formeditar.reset();

                    mostrarToast("Reserva actualizada correctamente", 'success');
                    // Recargar
                    obtenerReservasAutorizar();
                    obtenerReservasProximas();
                } else {
                    if(response.message){
                        mostrarToast(response.message.trim(), 'danger');
                    }else{
                        mostrarToast("Error al actualizar la reserva", 'danger');
                    }
                }
            })
            .catch(err => console.error("Error al actualizar la reserva:", err));
        }
    }else{
        mostrarToast("Error al actualizar la reserva", 'danger');
    }
}



//API Editar reservas de tipo espacio
async function modificarReservaEspacio(id, id_recurso, actividad, necesidades, inicio, fin){
    try{
        let arraynecesidades = necesidades.map(id => ({ id_necesidad: id }));
        let res=await fetch(window.location.origin+"/API/reservaEspacio/"+id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({id_espacio: id_recurso, actividad: actividad, necesidades: arraynecesidades, inicio: inicio, fin: fin})
        })
        let response = await res.json();
        console.log(response);
        if (response.status == "success"){
            return 1;
        }else{
            mostrarToast("Ya hay una reserva entre esas horas", 'warning');
            return -1;
        }
    }catch(err){
        console.error("Error al actualizar la reserva:", err);
        return -1;
    }
}



//API Editar reservas de tipo portátil
async function modificarReservaPortatil(id, id_recurso, unidades, espacio_uso, inicio, fin){
    try{
        console.log("Modificar portátil");
        let res = await fetch(window.location.origin+"/API/reservaPortatil/"+id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({id_material: id_recurso, unidades: unidades, usaenespacio: espacio_uso, inicio: inicio, fin: fin})
        })
        console.log("API hecha");
        let response = await res.json();
        
        console.log("Respuesta "+response);
        if (response.status == "success") {
            return 1;
        } else {
            mostrarToast("No hay suficientes portátiles disponibles entre esas horas", 'warning')
            return -1;
        }
    }catch(err){
        console.error("Error al actualizar la reserva:", err);
        return -1;
    }
}



function formatearFecha(fecha){
    if (!fecha) return null;
    let fechaFormulario = new Date(fecha);
    let anyo = fechaFormulario.getFullYear();
    let mes = String(fechaFormulario.getMonth() + 1).padStart(2, '0');
    let dia = String(fechaFormulario.getDate()).padStart(2, '0');
    let hh = String(fechaFormulario.getHours()).padStart(2, '0');
    let mm = String(fechaFormulario.getMinutes()).padStart(2, '0');
    let ss = String(fechaFormulario.getSeconds()).padStart(2, '0');

    return `${anyo}-${mes}-${dia} ${hh}:${mm}:${ss}`;
}



function mostrarToast(mensaje, tipo = 'success') {    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    
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
        <div id="${toastId}" class="toast align-items-center ${textColor} ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        animation: true,
        autohide: true,
        delay: 3000
    });
    
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}