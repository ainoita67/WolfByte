//API Crear necesidades
document.getElementById("formCrearNecesidad").addEventListener("submit", function (e) {
    e.preventDefault();

    let nombre = document.getElementById("crearNecesidad").value.trim();
    if (!nombre) return;
    nombre = capitalizar(nombre);
    fetch(window.location.origin+"/API/necesidades", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({
            nombre: nombre
        })
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === "success") {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalCrear")
            );
            modal.hide();

            // Limpiar input
            document.getElementById("formCrearNecesidad").reset();

            // Recargar tarjetas
            obtenerNecesidades();
            mostrarToast('Necesidad creada correctamente', 'success');
        } else {
            if(response.message){
                mostrarToast(response.message.trim(), 'danger');
            }else{
                mostrarToast('Necesidad creada correctamente', 'success');
            }
        }
    })
    .catch(err => console.error("Error al crear la necesidad:", err));
});



//API Editar necesidades
document.getElementById("formEditarNecesidad").addEventListener("submit", function (e) {
    e.preventDefault();

    let nombre = document.getElementById("editNombre").value.trim();
    if (!nombre) return;
    nombre = capitalizar(nombre);
    
    fetch(window.location.origin+"/API/necesidades/"+necesidadSeleccionadaId, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({ nombre: nombre })
    })
    .then(res => res.json())
    .then(response => {
        if (response.status == "success") {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalEditar")
            );
            modal.hide();

            // Recargar tarjetas
            obtenerNecesidades();
            if(response.data.status == "no_changes"){
                mostrarToast("No han habido cambios", 'warning');
            }else{
                mostrarToast("Necesidad actualizada correctamente", 'success');
            }
        } else {
            if(response.message){
                mostrarToast(response.message.trim(), 'danger');
            }else{
                mostrarToast("Error al actualizar la necesidad", 'danger');
            }
        }
    })
    .catch(err => console.error("Error al actualizar la necesidad:", err));
});