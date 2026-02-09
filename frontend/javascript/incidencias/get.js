function capitalizar(string) {
    return string.charAt(0).toUpperCase()+string.slice(1).toLowerCase();
}



//API Obtener ver incidencias
function obtenerVerIncidencias(){
    fetch(window.location.origin+"/API/incidencias")
    .then(res => res.json())
    .then(response => {
        let incidencias = response.data;

        let tablaverincidencias = document.getElementById("verIncidenciasTableBody");
        tablaverincidencias.innerHTML = "";
        if(incidencias.length === 0){
            let card = document.createElement("tr");
            card.innerHTML = `
                <td>No hay incidencias registradas</td>
            `;
            tablaverincidencias.appendChild(card);
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
                tablaverincidencias.appendChild(td);
            });
        }
    });
}



//API Obtener recursos para crear incidencia
function obtenerRecursos(){
    fetch(window.location.origin+"/API/recurso")
    .then(res => res.json())
    .then(response => {
        let recursos = response.data;

        let tablaincidencias = document.getElementById("tablaincidencias");
        tablaincidencias.innerHTML = "";
        if(recursos.length === 0){
            let card = document.createElement("tr");
            card.innerHTML = `
                <td>No hay recursos registrados</td>
            `;
            tablaincidencias.appendChild(card);
        }else{
            recursos.forEach(recurso => {
                let tr = document.createElement("tr");
                tr.className = "card h-100 reserva-card border-0 rounded-0";
                tr.setAttribute("role", "button");
                tr.setAttribute("data-bs-toggle", "modal");
                tr.setAttribute("data-bs-target", "#modalincidencia");
                tr.setAttribute("data-id", recurso.id_recurso);
                tr.setAttribute("data-nombre", recurso.descripcion);

                let td = document.createElement("td");
                td.className = "p-2 text-black";
                td.textContent = recurso.id_recurso;

                tr.appendChild(td);
                tablaincidencias.appendChild(tr);
            });
        }
    })
    .catch(error => console.error("Error al obtener recursos:", error));
}



//API Obtener portatiles para crear incidencia
function obtenerPortatiles(){
    fetch(window.location.origin+"/API/material")
    .then(res => res.json())
    .then(response => {
        let portatiles = response.data;

        let tablaincidencias = document.getElementById("tablaincidencias");
        tablaincidencias.innerHTML = "";
        if(portatiles.length === 0){
            let card = document.createElement("tr");
            card.innerHTML = `
                <td>No hay portatiles registrados</td>
            `;
            tablaincidencias.appendChild(card);
        }else{
            portatiles.forEach(portatil => {
                let tr = document.createElement("tr");
                tr.className = "card h-100 reserva-card border-0 rounded-0";
                tr.setAttribute("role", "button");
                tr.setAttribute("data-bs-toggle", "modal");
                tr.setAttribute("data-bs-target", "#modalincidencia");
                tr.setAttribute("data-id", portatil.id_recurso);
                tr.setAttribute("data-nombre", portatil.descripcion);

                let td = document.createElement("td");
                td.className = "p-2 text-black";
                td.textContent = portatil.id_recurso;

                tr.appendChild(td);
                tablaincidencias.appendChild(tr);
            });
        }
    })
    .catch(error => console.error("Error al obtener portatiles:", error));
}



//API Obtener espacios para crear incidencia
function obtenerEspacios(){
    fetch(window.location.origin+"/API/espacios")
    .then(res => res.json())
    .then(response => {
        let espacios = response.data;

        let tablaincidencias = document.getElementById("tablaincidencias");
        tablaincidencias.innerHTML = "";
        if(espacios.length === 0){
            let card = document.createElement("tr");
            card.innerHTML = `
                <td>No hay espacios registrados</td>
            `;
            tablaincidencias.appendChild(card);
        }else{
            espacios.forEach(espacio => {
                let tr = document.createElement("tr");
                tr.className = "card h-100 reserva-card border-0 rounded-0";
                tr.setAttribute("role", "button");
                tr.setAttribute("data-bs-toggle", "modal");
                tr.setAttribute("data-bs-target", "#modalincidencia");
                tr.setAttribute("data-id", espacio.id_recurso);
                tr.setAttribute("data-nombre", espacio.descripcion);

                let td = document.createElement("td");
                td.className = "p-2 text-black";
                td.textContent = espacio.id_recurso;

                tr.appendChild(td);
                tablaincidencias.appendChild(tr);
            });
        }
    })
    .catch(error => console.error("Error al obtener espacios:", error));
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