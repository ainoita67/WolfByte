function capitalizar(string) {
    return string.charAt(0).toUpperCase()+string.slice(1).toLowerCase();
}



//API Obtener necesidades
function obtenerNecesidades(){
    fetch(window.location.origin+"/API/necesidades")
    .then(res => res.json())
    .then(response => {
        const necesidades = response.data;

        const contenedor = document.getElementById("necesidadesReservaContainer");
        contenedor.innerHTML = "";

        necesidades.forEach(necesidad => {
            const card = document.createElement("div");
            card.className = "col-12 col-md-6 col-lg-4 mt-5 mb-5";
            card.innerHTML = `
                <div class="card text-center shadow-sm overflow-hidden h-100">
                    <div class="bg-blue card-head rounded-top">
                        <p class="fs-6 text-light m-0 py-2">ID: ${necesidad.id_necesidad}</p>
                    </div>
                    <div class="card-body">
                        <p class="fs-5 card-title">${necesidad.nombre}</p>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-sm bg-warning text-black"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar"
                                onclick="editarNecesidad(${necesidad.id_necesidad}, '${necesidad.nombre}')">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </div>
            `;
            contenedor.appendChild(card);
        });
    });
}



//API Crear necesidades
document.getElementById("formCrear").addEventListener("submit", function (e) {
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
            document.getElementById("formCrear").reset();

            // Recargar tarjetas
            obtenerNecesidades();
            alert("Necesidad creada correctamente");
        } else {
            if(response.message){
                alert(response.message.trim());
            }else{
                alert("Error al crear la necesidad");
            }
        }
    })
    .catch(err => console.error("Error al crear la necesidad:", err));
});





obtenerNecesidades();

let necesidadSeleccionadaId = null;

function editarNecesidad(id, nombre) {
    necesidadSeleccionadaId = id;
    document.getElementById("editNombre").value = nombre;
}