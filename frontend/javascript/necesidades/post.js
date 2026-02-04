//API Editar necesidades
document.getElementById("formEditarNecesidad").addEventListener("submit", function (e) {
    e.preventDefault();

    let nombre = document.getElementById("editNombre").value.trim();
    if (!nombre) return;
    nombre = capitalizar(nombre);
    fetch(window.location.origin+"/API/necesidades/"+necesidadSeleccionadaId, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ nombre: nombre })
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === "success") {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalEditar")
            );
            modal.hide();

            // Recargar tarjetas
            obtenerNecesidades();
            alert("Necesidad actualizada correctamente");
        } else {
            if(response.message){
                alert(response.message.trim());
            }else{
                alert("Error al actualizar la necesidad");
            }
        }
    })
    .catch(err => console.error("Error al actualizar la necesidad:", err));
});