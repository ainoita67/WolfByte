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
                    <td class="td-body d-none d-md-table-cell">
                        ${incidencia.id_incidencia}
                    </td>
                    <td class="td-body">
                        ${incidencia.id_recurso}
                    </td>
                    <td class="td-body d-none d-md-table-cell">
                        ${incidencia.fecha}
                    </td>
                    <td class="td-body">
                        ${incidencia.titulo}
                    </td>
                    <td class="td-body d-none d-md-table-cell">
                        ${incidencia.descripcion}
                    </td>
                    <td class="td-body d-none d-md-table-cell">
                        ${incidencia.prioridad}
                    </td>
                    <td class="td-body">
                        ${incidencia.estado}
                    </td>
                    <td class="td-body">
                        <button class="btn btn-sm bg-primary text-white d-md-none"
                                data-bs-toggle="modal"
                                data-bs-target="#modalVer"
                                onclick="verIncidencia(${incidencia.id_incidencia}, '${incidencia.titulo}', '${incidencia.descripcion}', '${incidencia.prioridad}', '${incidencia.estado}', '${incidencia.fecha}', '${incidencia.id_recurso}')">
                            <i class="bi bi-eye"></i> Ver
                        </button>
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
    fetch(window.location.origin+"/API/recurso/activos")
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
                tr.className = "card h-100 border-0 rounded-0";
                tr.setAttribute("role", "button");
                tr.setAttribute("data-bs-toggle", "modal");
                tr.setAttribute("data-bs-target", "#modalIncidencia");
                tr.setAttribute("data-id", recurso.id_recurso);
                tr.setAttribute("data-nombre", recurso.descripcion);

                let td = document.createElement("td");
                td.className = "p-2 text-black cursor-pointer";
                td.textContent = recurso.id_recurso;

                tr.appendChild(td);
                tablaincidencias.appendChild(tr);

                tr.addEventListener("click", () => {
                    anyadirValores(recurso);
                });
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
                tr.className = "card h-100 border-0 rounded-0";
                tr.setAttribute("role", "button");
                tr.setAttribute("data-bs-toggle", "modal");
                tr.setAttribute("data-bs-target", "#modalIncidencia");
                tr.setAttribute("data-id", portatil.id_recurso);
                tr.setAttribute("data-nombre", portatil.descripcion);

                let td = document.createElement("td");
                td.className = "p-2 text-black";
                td.textContent = portatil.id_recurso;

                tr.appendChild(td);
                tablaincidencias.appendChild(tr);

                tr.addEventListener("click", () => {
                    anyadirValores(portatil);
                });
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
                tr.className = "card h-100 border-0 rounded-0";
                tr.setAttribute("role", "button");
                tr.setAttribute("data-bs-toggle", "modal");
                tr.setAttribute("data-bs-target", "#modalIncidencia");
                tr.setAttribute("data-id", espacio.id_recurso);
                tr.setAttribute("data-nombre", espacio.descripcion);

                let td = document.createElement("td");
                td.className = "p-2 text-black";
                td.textContent = espacio.id_recurso;

                tr.appendChild(td);
                tablaincidencias.appendChild(tr);

                tr.addEventListener("click", () => {
                    anyadirValores(espacio);
                });
            });
        }
    })
    .catch(error => console.error("Error al obtener espacios:", error));
}



//API Obtener espacios para crear incidencia
function obtenerEspaciosPorEdificio(edificio){
    if(!edificio||edificio<=0){
        return obtenerEspacios();
    }else{
        fetch(window.location.origin+"/API/recurso/")
        .then(res => res.json())
        .then(response => {
            let espacios = response.data;

            let tablaincidencias = document.getElementById("tablaincidencias");
            tablaincidencias.innerHTML = "";

            let nespacios=0;
            espacios.forEach(espacio => {
                if(espacio.id_edificio==edificio){
                    let tr = document.createElement("tr");
                    tr.className = "card h-100 border-0 rounded-0";
                    tr.setAttribute("role", "button");
                    tr.setAttribute("data-bs-toggle", "modal");
                    tr.setAttribute("data-bs-target", "#modalIncidencia");
                    tr.setAttribute("data-id", espacio.id_recurso);
                    tr.setAttribute("data-nombre", espacio.descripcion);

                    let td = document.createElement("td");
                    td.className = "p-2 text-black";
                    td.textContent = espacio.id_recurso;

                    tr.appendChild(td);
                    tablaincidencias.appendChild(tr);

                    tr.addEventListener("click", () => {
                        anyadirValores(espacio);
                    });

                    nespacios++;
                }
            });
            if(nespacios === 0){
                let card = document.createElement("tr");
                card.className = "h-100 border-0 rounded-0";
                card.innerHTML = `
                    <td class="p-2 text-black">No hay espacios registrados por ese edificio</td>
                `;
                tablaincidencias.appendChild(card);
            }
        })
        .catch(error => console.error("Error al obtener espacios por edificio:", error));
    }
}



//API Obtener edificios para crear incidencia
function obtenerEdificios(){
    fetch(window.location.origin+"/API/edificios")
    .then(res => res.json())
    .then(response => {
        let edificios = response.data;

        let selectedificios = document.getElementById("selectedificio");
        selectedificios.innerHTML = "";
        if(edificios.length === 0){
            let option = document.createElement("option");
            option.value = "";
            option.textContent = "No hay edificios registrados";
            option.selected = true;
            option.disabled = true;
            selectedificios.appendChild(option);
        }else{
            let optionseleccionar = document.createElement("option");
            optionseleccionar.value = "";
            optionseleccionar.textContent = "Seleccionar edificio";
            optionseleccionar.selected = true;
            optionseleccionar.disabled = true;
            selectedificios.appendChild(optionseleccionar);

            let optiontodos = document.createElement("option");
            optiontodos.value = "";
            optiontodos.textContent = "Todos los espacios";
            selectedificios.appendChild(optiontodos);

            edificios.forEach(edificio => {
                let optionedificio = document.createElement("option");
                optionedificio.value = edificio.id_edificio;
                optionedificio.textContent = edificio.nombre_edificio;
                selectedificios.appendChild(optionedificio);
            });
        }
    })
    .catch(error => console.error("Error al obtener edificios:", error));
}



//API Obtener plantas para crear incidencia
function obtenerPlantas(edificio){
    fetch(window.location.origin+"/API/plantas/"+edificio)
    .then(res => res.json())
    .then(response => {
        let plantas = response.data;
        let divplantas = document.getElementById("divplanta");
        let selectplantas = document.getElementById("selectplanta");
        selectplantas.innerHTML = "";

        if(plantas.length === 0||edificio == ""||!edificio){
            divplantas.classList.remove("d-block");
            divplantas.classList.add("d-none");
        }else{
            divplantas.classList.remove("d-none");
            divplantas.classList.add("d-block");

            let optionseleccionar = document.createElement("option");
            optionseleccionar.value = "";
            optionseleccionar.textContent = "Seleccionar planta";
            optionseleccionar.selected = true;
            optionseleccionar.disabled = true;
            selectplantas.appendChild(optionseleccionar);

            let optiontodos = document.createElement("option");
            optiontodos.value = -1;
            optiontodos.textContent = "Todas las plantas";
            selectplantas.appendChild(optiontodos);

            plantas.forEach(planta => {
                let optionplanta = document.createElement("option");
                optionplanta.value = planta.numero_planta;
                optionplanta.textContent = 'Planta '+planta.numero_planta;
                selectplantas.appendChild(optionplanta);
            });
        }
    })
    .catch(error => console.error("Error al obtener plantas:", error));
}



//API Obtener espacios para crear incidencia
function obtenerEspaciosPorPlanta(edificio, planta=-1){
    console.log(planta);
    if(!edificio||edificio<=0){
        return obtenerEspacios();
    }else if(!planta||planta<0){
        return obtenerEspaciosPorEdificio(edificio);
    }else{
        fetch(window.location.origin+"/API/recurso/")
        .then(res => res.json())
        .then(response => {
            let espacios = response.data;

            let tablaincidencias = document.getElementById("tablaincidencias");
            tablaincidencias.innerHTML = "";

            let nespacios=0;
            espacios.forEach(espacio => {
                if(espacio.id_edificio==edificio&&espacio.numero_planta==planta){
                    let tr = document.createElement("tr");
                    tr.className = "card h-100 border-0 rounded-0";
                    tr.setAttribute("role", "button");
                    tr.setAttribute("data-bs-toggle", "modal");
                    tr.setAttribute("data-bs-target", "#modalIncidencia");
                    tr.setAttribute("data-id", espacio.id_recurso);
                    tr.setAttribute("data-nombre", espacio.descripcion);

                    let td = document.createElement("td");
                    td.className = "p-2 text-black";
                    td.textContent = espacio.id_recurso;

                    tr.appendChild(td);
                    tablaincidencias.appendChild(tr);

                    tr.addEventListener("click", () => {
                        anyadirValores(espacio);
                    });

                    nespacios++;
                }
            });
            if(nespacios === 0){
                let card = document.createElement("tr");
                card.className = "h-100 border-0 rounded-0";
                card.innerHTML = `
                    <td class="p-2 text-black">No hay espacios registrados por esa planta</td>
                `;
                tablaincidencias.appendChild(card);
            }
        })
        .catch(error => console.error("Error al obtener espacios por edificio:", error));
    }
}



function anyadirValores(elemento){
    let fechaActual = new Date();

    let anyo = fechaActual.getFullYear();
    let mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
    let dia = String(fechaActual.getDate()).padStart(2, '0');
    let hh = String(fechaActual.getHours()).padStart(2, '0');
    let mm = String(fechaActual.getMinutes()).padStart(2, '0');
    let ss = String(fechaActual.getSeconds()).padStart(2, '0');

    let fechaFormateada = `${anyo}-${mes}-${dia}T${hh}:${mm}:${ss}`;
    document.getElementById("createIdRecurso").value = elemento.id_recurso;
    document.getElementById("createRecurso").value = elemento.descripcion;
    document.getElementById("createFecha").value = fechaFormateada;
}