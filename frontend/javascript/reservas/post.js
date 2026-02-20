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
    let formeditar = document.getElementById("formreserva");
    if(!formeditar) return;
    formeditar.addEventListener("submit", function (e) {
        e.preventDefault();

        let id = document.getElementById("reserva_id").value;
        let fecha = document.getElementById("reserva_fecha").value;
        let id_recurso = document.getElementById("reserva_recurso").value;
        let titulo = document.getElementById("reserva_titulo").value;
        let descripcion = document.getElementById("reserva_descripcion").value;
        let usuario = document.getElementById("reserva_id_usuario").value;
        let prioridad = document.getElementById("reserva_prioridad").value;
        let estado = document.getElementById("reserva_estado").value;
        if (!id) return;
        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalreserva")
        );
        modificarReserva(id, fecha, id_recurso, titulo, descripcion, usuario, prioridad, estado, formeditar, modal);
    });
}



//API Editar reservas
function modificarReserva(id, fecha, id_recurso, titulo, descripcion, usuario, prioridad, estado, formeditar, modal){
    fetch(window.location.origin+"/API/reservas/"+id, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ titulo: titulo, descripcion: descripcion, fecha: fecha, prioridad: prioridad, estado: estado, id_usuario: usuario, id_recurso: id_recurso })
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