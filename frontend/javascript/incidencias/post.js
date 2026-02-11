//API Crear incidencias
function activarCrearIncidencia() {
    const formcrear = document.getElementById("formCrearIncidencia");
    if(!formcrear) return;
    formcrear.addEventListener("submit", function (e) {
        e.preventDefault();
        let fecha = document.getElementById("incidencia_fecha").value;
        let id_recurso = document.getElementById("recurso_id").value;
        let titulo = document.getElementById("incidencia_titulo").value;
        let descripcion = document.getElementById("incidencia_descripcion").value;
        let prioridad = 'Media';
        let estado = 'Abierta';
        if (!titulo) return;
        titulo = capitalizar(titulo);
        fetch(window.location.origin+"/API/incidencias/", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ titulo: titulo, descripcion: descripcion, fecha: fecha, prioridad: prioridad, estado: estado, id_usuario: 2, id_recurso: id_recurso })
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
    })
};



//API Editar incidencias
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
        let prioridad = document.getElementById("editPrioridad").value;
        let estado = document.getElementById("editEstado").value;
        if (!id) return;
        fetch(window.location.origin+"/API/incidencias/"+id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ titulo: titulo, descripcion: descripcion, fecha: fecha, prioridad: prioridad, estado: estado, id_usuario: 2, id_recurso: id_recurso })
        })
        .then(res => res.json())
        .then(response => {
            if (response.status === "success") {
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("modalEditar")
                );
                modal.hide();

                // Limpiar input
                document.getElementById("formEditarIncidencia").reset();

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
    })
};