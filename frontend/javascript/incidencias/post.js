//API Crear incidencias
function activarCrearIncidencia() {
    const formcrear = document.getElementById("formCrearIncidencia");
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
        let descripcion = document.getElementById("createDescripcionIncidencia").value;
        let prioridad = 'Media';
        let estado = 'Abierta';
        let usuario=2;
        if (!titulo) return;
        titulo = capitalizar(titulo);
        fetch(window.location.origin+"/API/incidencias/", {
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
                    document.getElementById("modalIncidencia")
                );
                modal.hide();

                // Limpiar input
                document.getElementById("formCrearIncidencia").reset();

                alert("Incidencia creada correctamente");
                // Recargar
                window.location.reload();
            } else {
                if(response.message){
                    alert(response.message.trim());
                }else{
                    alert("Error al crear la incidencia");
                }
            }
        })
        .catch(err => console.error("Error al crear la incidencia:", err));
    })
};



//Editar incidencias
function activarEditarIncidencia() {
    let formeditar = document.getElementById("formEditarIncidencia");
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
        modificarIncidencia(id, fecha, id_recurso, titulo, descripcion, usuario, prioridad, estado, formeditar, modal);
    });
}



//Editar incidencias menú administrador
function activarEditarTarjetasIncidencia() {
    let formeditar = document.getElementById("formincidencia");
    if(!formeditar) return;
    formeditar.addEventListener("submit", function (e) {
        e.preventDefault();

        let id = document.getElementById("incidencia_id").value;
        let fecha = document.getElementById("incidencia_fecha").value;
        let id_recurso = document.getElementById("incidencia_recurso").value;
        let titulo = document.getElementById("incidencia_titulo").value;
        let descripcion = document.getElementById("incidencia_descripcion").value;
        let usuario = document.getElementById("incidencia_id_usuario").value;
        let prioridad = document.getElementById("incidencia_prioridad").value;
        let estado = document.getElementById("incidencia_estado").value;
        if (!id) return;
        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalincidencia")
        );
        modificarIncidencia(id, fecha, id_recurso, titulo, descripcion, usuario, prioridad, estado, formeditar, modal);
    });
}



//API Editar incidencias
function modificarIncidencia(id, fecha, id_recurso, titulo, descripcion, usuario, prioridad, estado, formeditar, modal){
    fetch(window.location.origin+"/API/incidencias/"+id, {
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

            alert("Incidencia actualizada correctamente");
            // Recargar
            window.location.reload();
        } else {
            if(response.message){
                alert(response.message.trim());
            }else{
                alert("Error al actualizar la incidencia");
            }
        }
    })
    .catch(err => console.error("Error al actualizar la incidencia:", err));
}