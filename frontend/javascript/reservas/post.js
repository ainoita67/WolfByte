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
        let usuario=2;
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

                alert("Reserva creada correctamente");
                // Recargar
                window.location.reload();
            } else {
                if(response.message){
                    alert(response.message.trim());
                }else{
                    alert("Error al crear la reserva");
                }
            }
        })
        .catch(err => console.error("Error al crear la reserva:", err));
    })
};



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
        modificarReserva(id, fecha, id_recurso, titulo, descripcion, usuario, prioridad, estado, formeditar, modal);
    });
}



//Editar reservas menú administrador
function activarEditarTarjetasReserva() {
    let formeditar = document.getElementById("formReserva");
    if(!formeditar) return;
    formeditar.addEventListener("submit", function (e) {
        e.preventDefault();

        let id = document.getElementById("reserva_id").value;
        let autorizada = document.getElementById("reserva_autorizada").value.trim()||null;;
        if(autorizada=="Pendiente"){
            autorizada=null;
        }else if(autorizada=="Denegada"){
            autorizada=0;
        }else if(autorizada=="Autorizada"){
            autorizada=1;
        }
        let fechacreacion = formatearFecha(document.getElementById("reserva_f_creacion").value);
        let inicio = formatearFecha(document.getElementById("reserva_inicio").value);
        let fin = formatearFecha(document.getElementById("reserva_fin").value);
        let tipo = document.getElementById("reserva_tipo").value;
        let id_recurso = document.getElementById("reserva_espacio_portatil").value;
        let asignatura = document.getElementById("reserva_asignatura").value.trim()||null;
        let grupo = document.getElementById("reserva_grupo").value;
        let profesor = document.getElementById("reserva_profesor").value;
        let usuario = document.getElementById("reserva_id_usuario").value;
        let usuarioautoriza=null
        if(autorizada==1){
            usuarioautoriza = sessionStorage.getItem("id_usuario");
        }
        let observaciones = document.getElementById("reserva_observaciones").value.trim()||null;
        let actividad=null;
        let necesidades=null;
        let unidades=null;
        let espacio_uso=null;
        if(tipo=="Reserva_espacio"){
            actividad = document.getElementById("reserva_actividad").value;
            necesidades = document.getElementById("reserva_necesidades").value;
        }else if(tipo=="Reserva_material"){
            unidades = document.getElementById("reserva_unidades").value;
            espacio_uso = document.getElementById("reserva_espacio_uso").value;
        }else{
            alert("Error al actualizar los datos");
            return;
        }
        if(!id||!fechacreacion||!inicio||!fin||!tipo||!id_recurso||!grupo||!profesor||!usuario){
            alert("Error al actualizar los datos. Campos obligatorios no rellenados.");
            return;
        }

        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalReserva")
        );
        console.log(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal);
        modificarReserva(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal);
    });
}



//API Editar reservas
function modificarReserva(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal){
    if(tipo=="Reserva_espacio"||tipo=="Reserva_portatil"){
        // if(tipo=="Reserva_espacio"){
        //     modificarReservaEspacio(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, observaciones, formeditar, modal);
        // }else if(tipo=="Reserva_material"){
        //     modificarReservaPortatil(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, unidades, espacio_uso, observaciones, formeditar, modal);
        // }
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

                alert("Reserva actualizada correctamente");
                // Recargar
                window.location.reload();
            } else {
                if(response.message){
                    alert(response.message.trim());
                }else{
                    alert("Error al actualizar la reserva");
                }
            }
        })
        .catch(err => console.error("Error al actualizar la reserva:", err));
    }else{
        alert("Error al actualizar la reserva");
    }
}



//API Editar reservas de tipo espacio
function modificarReservaEspacio(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal){
    fetch(window.location.origin+"/API/reservas/"+id, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({autorizada: autorizada, f_creacion: fechacreacion, inicio: inicio, fin: fin, tipo: tipo, id_recurso: id_recurso, asignatura: asignatura, grupo: grupo, profesor: profesor, usuario: usuario, usuarioautoriza: usuarioautoriza, actividad: actividad, necesidades: necesidades, unidades: unidades, espacio_uso: espacio_uso})
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === "success") {
            // Cerrar modal
            modal.hide();

            // Limpiar input
            formeditar.reset();

            alert("Reserva actualizada correctamente");
            // Recargar
            window.location.reload();
        } else {
            if(response.message){
                alert(response.message.trim());
            }else{
                alert("Error al actualizar la reserva");
            }
        }
    })
    .catch(err => console.error("Error al actualizar la reserva:", err));
}



//API Editar reservas de tipo portátil
function modificarReservaPortatil(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal){
    fetch(window.location.origin+"/API/reservas/"+id, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({asignatura: asignatura, autorizada: autorizada, observaciones: observaciones, f_creacion: fechacreacion, inicio: inicio, fin: fin, tipo: tipo, id_recurso: id_recurso, asignatura: asignatura, grupo: grupo, profesor: profesor, usuario: usuario, usuarioautoriza: usuarioautoriza, actividad: actividad, necesidades: necesidades, unidades: unidades, espacio_uso: espacio_uso})
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === "success") {
            // Cerrar modal
            modal.hide();

            // Limpiar input
            formeditar.reset();

            alert("Reserva actualizada correctamente");
            // Recargar
            window.location.reload();
        } else {
            if(response.message){
                alert(response.message.trim());
            }else{
                alert("Error al actualizar la reserva");
            }
        }
    })
    .catch(err => console.error("Error al actualizar la reserva:", err));
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