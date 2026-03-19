//API Crear necesidades
document.getElementById("formCrearNecesidad").addEventListener("submit", function (e) {
    e.preventDefault();

    let nombre = document.getElementById("crearNecesidad").value.trim();
    if (!nombre) return;
    nombre = capitalizar(nombre);
    fetch(window.location.origin+"/API/necesidades", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
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
            mostrarToast('Necesidad actualizada correctamente', 'success');
        } else {
            if(response.message){
                mostrarToast(response.message.trim(), 'danger');
            }else{
                mostrarToast('Necesidad actualizada correctamente', 'success');
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
            mostrarToast("Necesidad actualizada correctamente", 'success');
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



function mostrarToast(mensaje, tipo = 'success') {
    console.log('Toast:', mensaje, tipo);
    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
        console.log('Contenedor de toasts creado');
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
        <div id="${toastId}" class="toast align-items-center ${textColor} ${bgClass} border-0 fs-6" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
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