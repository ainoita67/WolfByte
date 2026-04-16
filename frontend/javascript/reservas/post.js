//API Crear reservas
function activarCrearReserva() {
    const formcrear = document.getElementById("formCrearReserva");
    if(!formcrear) return;
    formcrear.addEventListener("submit", function (e) {
        e.preventDefault();
        let fechaFormulario = new Date(document.getElementById("createFecha").value);
        let anyo = fechaFormulario.getFullYear();
        let mes = String(fechaFormulario.getMonth() + 1).padStart(2, '0');
        let dia = String(fechaFormulario.getDate()).padStart(2, '0');
        let hh = String(fechaFormulario.getHours()).padStart(2, '0');
        let mm = String(fechaFormulario.getMinutes()).padStart(2, '0');
        let ss = String(fechaFormulario.getSeconds()).padStart(2, '0');

        let fecha = `${anyo}-${mes}-${dia} ${hh}:${mm}:${ss}`;
        let id_recurso = document.getElementById("createIdRecurso").value;
        let titulo = document.getElementById("createTitulo").value;
        let descripcion = document.getElementById("createDescripcionReserva").value;
        let prioridad = 'Media';
        let estado = 'Abierta';
        let usuario=document.getElementById("reserva_id_usuario").value;
        if (!titulo) return;
        titulo = capitalizar(titulo);
        fetch(window.location.origin+"/API/reservas/", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ titulo: titulo, descripcion: descripcion, fecha: fecha, prioridad: prioridad, estado: estado, id_usuario: usuario, id_recurso: id_recurso })
        })
        .then(res => res.json())
        .then(response => {
            if (response.status === "success") {
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("modalReserva")
                );
                modal.hide();

                // Limpiar input
                document.getElementById("formCrearReserva").reset();

                mostrarToast("Reserva creada correctamente", 'success');
                // Recargar
                window.location.reload();
            } else {
                if(response.message){
                    mostrarToast(response.message.trim(), 'danger');
                }else{
                    mostrarToast("Error al crear la reserva", 'danger');
                }
            }
        })
        .catch(err => console.error("Error al crear la reserva:", err));
    })
};



//Editar reservas menú administrador
function activarEditarTarjetasReserva() {
    let formeditar = document.getElementById("formReserva");
    if(!formeditar) return;

    //EDITAR
    formeditar.addEventListener("submit", function (e) {
        e.preventDefault();
        let reserva=obtenerDatosReserva();
        if(!reserva.id||!reserva.fechacreacion||!reserva.inicio||!reserva.fin||!reserva.tipo||!reserva.id_recurso||!reserva.grupo||!reserva.profesor||!reserva.usuario){
            mostrarToast("Error al actualizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }

        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalReserva")
        );

        modificarReserva(reserva.id, reserva.autorizada, reserva.fechacreacion, reserva.inicio, reserva.fin, reserva.tipo, reserva.id_recurso, reserva.asignatura, reserva.grupo, reserva.profesor, reserva.usuario, reserva.usuarioautoriza, reserva.actividad, reserva.necesidades, reserva.unidades, reserva.espacio_uso, reserva.observaciones, formeditar, modal);
    });

    let btnAutorizar = document.getElementById("btnAutorizarReserva");
    let btnDenegar = document.getElementById("btnDenegarReserva");

    //AUTORIZAR
    btnAutorizar.addEventListener("click", function (e) {
        e.preventDefault();
        let reserva=obtenerDatosReserva(sessionStorage.getItem("id_usuario"));
        if(!reserva.id||!reserva.fechacreacion||!reserva.inicio||!reserva.fin||!reserva.tipo||!reserva.id_recurso||!reserva.grupo||!reserva.profesor||!reserva.usuario||!reserva.usuarioautoriza){
            mostrarToast("Error al autorizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }

        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalReserva")
        );
        
        reserva.autorizada=1;
        modificarReserva(reserva.id, reserva.autorizada, reserva.fechacreacion, reserva.inicio, reserva.fin, reserva.tipo, reserva.id_recurso, reserva.asignatura, reserva.grupo, reserva.profesor, reserva.usuario, reserva.usuarioautoriza, reserva.actividad, reserva.necesidades, reserva.unidades, reserva.espacio_uso, reserva.observaciones, formeditar, modal);
    });

    //DENEGAR
    btnDenegar.addEventListener("click", function (e) {
        e.preventDefault();
        let reserva=obtenerDatosReserva(sessionStorage.getItem("id_usuario"));
        if(!reserva.id||!reserva.fechacreacion||!reserva.inicio||!reserva.fin||!reserva.tipo||!reserva.id_recurso||!reserva.grupo||!reserva.profesor||!reserva.usuario||!reserva.usuarioautoriza){
            mostrarToast("Error al autorizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }

        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalReserva")
        );
        
        reserva.autorizada=0;
        modificarReserva(reserva.id, reserva.autorizada, reserva.fechacreacion, reserva.inicio, reserva.fin, reserva.tipo, reserva.id_recurso, reserva.asignatura, reserva.grupo, reserva.profesor, reserva.usuario, reserva.usuarioautoriza, reserva.actividad, reserva.necesidades, reserva.unidades, reserva.espacio_uso, reserva.observaciones, formeditar, modal);
    });
}



function obtenerDatosReserva(usuarioautoriza=null){
    let id = document.getElementById("reserva_id").value;
    let autorizada = document.getElementById("reserva_autorizada").value.trim()||null;
    if(autorizada=="Pendiente"){
        autorizada=null;
    }else if(autorizada=="Denegada"){
        autorizada=0;
    }else if(autorizada=="Autorizada"){
        autorizada=1;
    }
    let fechacreacion = anyadirFecha(document.getElementById("reserva_f_creacion").value);
    let inicio = anyadirFecha(document.getElementById("reserva_inicio").value);
    let fin = anyadirFecha(document.getElementById("reserva_fin").value);
    let tipo = document.getElementById("reserva_tipo").value;
    let id_recurso = document.getElementById("reserva_espacio_portatil").value;
    let asignatura = document.getElementById("reserva_asignatura").value.trim()||null;
    let grupo = document.getElementById("reserva_grupo").value;
    let profesor = document.getElementById("reserva_profesor").value;
    let usuario = document.getElementById("reserva_id_usuario").value;
    if(!usuarioautoriza||usuarioautoriza==null){
        usuarioautoriza = document.getElementById("reserva_id_usuario_autoriza").value;
    }
    if(autorizada!=null&&(!usuarioautoriza||usuarioautoriza==null)){
        mostrarToast("Error al actualizar los datos", 'danger');
        return;
    }
    let observaciones = document.getElementById("reserva_observaciones").value.trim()||null;
    let actividad=null;
    let necesidades=null;
    let unidades=null;
    let espacio_uso=null;
    if(tipo=="Reserva_espacio"){
        actividad = document.getElementById("reserva_actividad").value;
        necesidades = Array.from(document.getElementById("reserva_necesidades").selectedOptions).map(opt => opt.value);
        if(actividad==null||actividad.trim()==''){
            mostrarToast("Error al actualizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }
    }else if(tipo=="Reserva_material"){
        unidades = document.getElementById("reserva_unidades").value;
        espacio_uso = document.getElementById("reserva_espacio_uso").value;
        if(unidades==null||unidades<=0||espacio_uso==null){
            mostrarToast("Error al actualizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }
    }else{
        mostrarToast("Error al actualizar los datos", 'danger');
        return;
    }
    return {id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones}
}



//API Editar reservas
async function modificarReserva(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal){
    let fechaactual=new Date();
    let fechainicio=new Date(inicio);
    if(fechaactual>=fechainicio){
        mostrarToast("No se puede modificar una reserva pasada", 'danger');
    }else{
        if(tipo=="Reserva_espacio"||tipo=="Reserva_material"){
            let resultado=0;
            if(tipo=="Reserva_espacio"){
                resultado=await modificarReservaEspacio(id, autorizada, id_recurso, asignatura, actividad, necesidades, fechacreacion, inicio, fin, grupo, profesor, usuario, usuarioautoriza, observaciones);
            }else if(tipo=="Reserva_material"&&unidades>0){
                resultado=await modificarReservaPortatil(id, autorizada, id_recurso, asignatura, unidades, espacio_uso, fechacreacion, inicio, fin, grupo, profesor, usuario, usuarioautoriza, observaciones);
            }
            if(resultado!=1){
                mostrarToast("Error al actualizar la reserva", 'danger');
            }else{
                // Cerrar modal
                modal.hide();

                // Limpiar input
                formeditar.reset();

                mostrarToast("Reserva actualizada correctamente", 'success');
                // Recargar
                obtenerReservasAutorizar();
                obtenerReservasProximas();
            }
        }else{
            mostrarToast("Error al actualizar la reserva", 'danger');
        }
    }
}



//API Editar reservas de tipo espacio
async function modificarReservaEspacio(id, autorizada, id_recurso, asignatura, actividad, necesidades, fechacreacion, inicio, fin, grupo, profesor, usuario, usuarioautoriza, observaciones){
    try{
        let arraynecesidades = (necesidades||[]).filter(valor => valor !== '').map(id => ({ id_necesidad: id }));
        let res=await fetch(window.location.origin+"/API/reservaEspacio/"+id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_espacio: id_recurso,
                autorizada: autorizada,
                asignatura: asignatura,
                actividad: actividad,
                necesidades: arraynecesidades,
                f_creacion: fechacreacion,
                inicio: inicio,
                fin: fin,
                grupo: grupo,
                profesor: profesor,
                id_usuario: usuario,
                id_usuario_autoriza: usuarioautoriza,
                id_usuario_actor: sessionStorage.getItem("id_usuario"),
                observaciones: observaciones,
                tipo: "Reserva_espacio"
            })
        })
        let response = await res.json();
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
async function modificarReservaPortatil(id, autorizada, id_recurso, asignatura, unidades, espacio_uso, fechacreacion, inicio, fin, grupo, profesor, usuario, usuarioautoriza, observaciones){
    try{
        let res = await fetch(window.location.origin+"/API/portatiles/reservas/"+id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_material: id_recurso,
                autorizada: autorizada,
                asignatura: asignatura,
                unidades: unidades,
                usaenespacio: espacio_uso,
                f_creacion: fechacreacion,
                inicio: inicio,
                fin: fin,
                grupo: grupo,
                profesor: profesor,
                id_usuario: usuario,
                id_usuario_autoriza: usuarioautoriza,
                id_usuario_actor: sessionStorage.getItem("id_usuario"),
                observaciones: observaciones,
                tipo: "Reserva_material"
            })
        })
        let response = await res.json();
        console.log("Respuesta al modificar reserva portátil:", response);
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



function anyadirFecha(fecha){
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