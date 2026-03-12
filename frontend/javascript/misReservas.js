function obtenerMisReservas(){
    let usuario = sessionStorage.getItem("id_usuario");
    console.log('ID Usuario:', usuario);
    console.log('URL:', window.location.origin + "/API/mis-reservas/" + usuario);
    
    fetch(window.location.origin + "/API/mis-reservas/" + usuario, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
    })
    .then(res => {
        console.log('Status respuesta:', res.status);
        console.log('Status text:', res.statusText);
        return res.json();
    })
    .then(response => {
        console.log('Respuesta completa de la API:', response);
        console.log('Datos recibidos:', response.data);
        
        let reservas = response.data;
        let tarjetasReservas = document.getElementById("misReservasTarjetas");
        if(!tarjetasReservas) {
            console.error('Elemento misReservasTarjetas no encontrado');
            return;
        }
        
        if(!reservas || reservas.length === 0){
            console.log('No hay reservas para el usuario', usuario);
            tarjetasReservas.innerHTML = `
                <div class="col-12">
                    <div class="card h-100 p-0 mb-4 reserva-card text-center">
                        <div class="card-body bg-secondary-subtle">No se han encontrado reservas</div>
                    </div>
                </div>
            `;
        }else{
            console.log('Reservas encontradas:', reservas.length);
            mostrarMisReservas(reservas, tarjetasReservas);
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
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

//Editar reservas desde el modal
function activarEditarReserva() {
    let formeditar = document.getElementById("formReserva");
    if(!formeditar) return;
    
    // Eliminar event listeners anteriores para evitar duplicados
    const nuevoForm = formeditar.cloneNode(true);
    formeditar.parentNode.replaceChild(nuevoForm, formeditar);
    
    nuevoForm.addEventListener("submit", async function (e) {
        e.preventDefault();
        
        // Obtener todos los valores del formulario
        let id = document.getElementById("reserva_id").value;
        let autorizada = document.getElementById("reserva_autorizada").value;
        let fechacreacion = document.getElementById("reserva_f_creacion").value;
        let inicio = document.getElementById("reserva_inicio").value;
        let fin = document.getElementById("reserva_fin").value;
        let tipo = document.getElementById("reserva_tipo").value;
        let id_recurso = document.getElementById("reserva_espacio_portatil").value;
        let asignatura = document.getElementById("reserva_asignatura").value;
        let grupo = document.getElementById("reserva_grupo").value;
        let profesor = document.getElementById("reserva_profesor").value;
        let usuario = document.getElementById("reserva_id_usuario").value;
        let usuarioautoriza = document.getElementById("reserva_id_usuario_autoriza")?.value || null;
        let actividad = document.getElementById("reserva_actividad")?.value;
        let unidades = document.getElementById("reserva_unidades")?.value;
        let espacio_uso = document.getElementById("reserva_espacio_uso")?.value;
        let observaciones = document.getElementById("reserva_observaciones")?.value;
        
        // Obtener necesidades seleccionadas (para espacios)
        let necesidades = [];
        let selectNecesidades = document.getElementById("reserva_necesidades");
        if (selectNecesidades) {
            for (let i = 0; i < selectNecesidades.options.length; i++) {
                if (selectNecesidades.options[i].selected) {
                    necesidades.push(selectNecesidades.options[i].value);
                }
            }
        }
        
        if (!id) return;
        
        // Obtener instancia del modal
        let modalElement = document.getElementById("modalReserva");
        let modal = bootstrap.Modal.getInstance(modalElement);
        
        // Llamar a la función que modifica la reserva
        await modificarReserva(
            id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, 
            asignatura, grupo, profesor, usuario, usuarioautoriza, 
            actividad, necesidades, unidades, espacio_uso, observaciones, 
            nuevoForm, modal
        );

        // Recargar las reservas después de actualizar
        obtenerMisReservas();
    });
}


//API Editar reservas
async function modificarReserva(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal){
    
    // Convertir autorizada de texto a número
    let valorAutorizada = null;
    if (autorizada === 'Autorizada') valorAutorizada = 1;
    else if (autorizada === 'Denegada') valorAutorizada = 0;
    
    // Formatear fechas correctamente
    inicio = formatearFechaParaAPI(inicio);
    fin = formatearFechaParaAPI(fin);
    fechacreacion = formatearFechaParaAPI(fechacreacion);
    
    console.log("Modificando reserva:", {id, tipo, inicio, fin});
    
    // Primero actualizar los datos generales de la reserva
    try {
        let resGeneral = await fetch(window.location.origin + "/API/reservas/" + id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                asignatura: asignatura, 
                autorizada: valorAutorizada, 
                observaciones: observaciones, 
                grupo: grupo, 
                profesor: profesor, 
                f_creacion: fechacreacion, 
                inicio: inicio, 
                fin: fin, 
                id_usuario: parseInt(usuario), 
                id_usuario_autoriza: usuarioautoriza ? parseInt(usuarioautoriza) : null, 
                tipo: tipo
            })
        });
        
        let responseGeneral = await resGeneral.json();
        console.log("Respuesta actualización general:", responseGeneral);
        
        if (responseGeneral.status !== "success") {
            mostrarToast(responseGeneral.message || "Error al actualizar la reserva", 'danger');
            return;
        }
        
        // Ahora actualizar los datos específicos según el tipo
        if(tipo == "Reserva_espacio"){
            let resultado = await modificarReservaEspacio(id, id_recurso, actividad, necesidades, inicio, fin);
            if(resultado == 1){
                if (modal) modal.hide();
                if (formeditar) formeditar.reset();
                mostrarToast("Reserva actualizada correctamente", 'success');
            }
        } else if(tipo == "Reserva_material" && unidades > 0){
            let resultado = await modificarReservaPortatil(id, id_recurso, unidades, espacio_uso, inicio, fin);
            if(resultado == 1){
                if (modal) modal.hide();
                if (formeditar) formeditar.reset();
                mostrarToast("Reserva actualizada correctamente", 'success');
            }
        }
        
    } catch(err) {
        console.error("Error al actualizar la reserva:", err);
        mostrarToast("Error de conexión al actualizar", 'danger');
    }
}

//API Editar reservas de tipo espacio - VERSIÓN PUT (sin eliminar)
async function modificarReservaEspacio(id, id_recurso, actividad, necesidades, inicio, fin){
    try{
        console.log("Actualizando reserva espacio:", id);
        let arraynecesidades = necesidades.map(id => ({ id_necesidad: id }));
        
        let res = await fetch(window.location.origin + "/API/reservaEspacio/" + id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_espacio: id_recurso, 
                actividad: actividad, 
                necesidades: arraynecesidades, 
                inicio: inicio, 
                fin: fin
            })
        });
        
        let response = await res.json();
        console.log("Respuesta actualización espacio:", response);
        
        if (response.status == "success"){
            return 1;
        } else {
            let mensaje = response.message || "Error al actualizar la reserva de espacio";
            mostrarToast(mensaje, 'warning');
            return -1;
        }
    } catch(err){
        console.error("Error al actualizar la reserva espacio:", err);
        mostrarToast("Error de conexión al actualizar", 'danger');
        return -1;
    }
}

//API Editar reservas de tipo portátil - VERSIÓN PUT (sin eliminar)
async function modificarReservaPortatil(id, id_recurso, unidades, espacio_uso, inicio, fin){
    try{
        console.log("Actualizando reserva portátil - ID:", id);
        
        let res = await fetch(window.location.origin + "/API/reservaPortatil/" + id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_material: id_recurso, 
                unidades: unidades, 
                usaenespacio: espacio_uso, 
                inicio: inicio, 
                fin: fin
            })
        });
        
        let response = await res.json();
        console.log("Respuesta actualización portátil:", response);
        
        if (response.status == "success") {
            return 1;
        } else {
            let mensaje = response.message || "Error al actualizar la reserva de portátiles";
            mostrarToast(mensaje, 'warning');
            return -1;
        }
    } catch(err){
        console.error("Error al actualizar la reserva portátil:", err);
        mostrarToast("Error de conexión al actualizar", 'danger');
        return -1;
    }
}

// Función auxiliar para formatear fechas
function formatearFechaParaAPI(fecha) {
    if (!fecha) return null;
    // Si la fecha viene en formato datetime-local (con T), convertir a espacio
    if (fecha.includes('T')) {
        return fecha.replace('T', ' ');
    }
    return fecha;
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

// Inicializar cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Inicializando mis reservas');
    activarEditarReserva();
    obtenerMisReservas();
});