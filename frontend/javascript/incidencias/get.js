function capitalizar(string) {
    return string.charAt(0).toUpperCase()+string.slice(1).toLowerCase();
}



//API Obtener incidencias
function obtenerIncidencias(){
    fetch(window.location.origin+"/API/incidencias")
    .then(res => res.json())
    .then(response => {
        let incidencias = response.data;

        let contenedor = document.getElementById("incidenciasTableBody");
        contenedor.innerHTML = "";
        if(incidencias.length === 0){
            let card = document.createElement("tr");
            card.innerHTML = `
                <td>No hay incidencias registradas</td>
            `;
            contenedor.appendChild(card);
        }else{
            incidencias.forEach(incidencia => {
                let td = document.createElement("tr");
                td.innerHTML = `
                    <td class="td-body d-none">
                        ${incidencia.id_incidencia}
                    </td>
                    <td class="td-body">
                        ${incidencia.id_recurso}
                    </td>
                    <td class="td-body">
                        ${incidencia.fecha}
                    </td>
                    <td class="td-body">
                        ${incidencia.titulo}
                    </td>
                    <td class="td-body">
                        ${incidencia.descripcion}
                    </td>
                    <td class="td-body">
                        ${incidencia.prioridad}
                    </td>
                    <td class="td-body">
                        ${incidencia.estado}
                    </td>
                    <td class="td-body">
                        <button class="btn btn-sm bg-warning text-black"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar"
                                onclick="editarIncidencia(${incidencia.id_incidencia}, '${incidencia.titulo}', '${incidencia.descripcion}', '${incidencia.prioridad}', '${incidencia.estado}', '${incidencia.fecha}', '${incidencia.id_recurso}')">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </td>
                `;
                contenedor.appendChild(td);
            });
        }
    });
}



//API Crear incidencias
document.getElementById("formCrear").addEventListener("submit", function (e) {
    e.preventDefault();

    let titulo = document.getElementById("crearIncidencia").value.trim();
    if (!titulo) return;
    titulo = capitalizar(titulo);
    fetch(window.location.origin+"/API/incidencias", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            titulo: titulo
        })
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === "success") {
            // Cerrar modal
            let modal = bootstrap.Modal.getInstance(
                document.getElementById("modalCrear")
            );
            modal.hide();

            // Limpiar input
            document.getElementById("formCrear").reset();

            // Recargar tarjetas
            obtenerIncidencias();
            alert("Incidencia creada correctamente");
        } else {
            if(response.message){
                alert(response.message.trim());
            }else{
                alert("Error al crear la incidencia");
            }
        }
    })
    .catch(err => console.error("Error al crear la incidencia:", err));
});