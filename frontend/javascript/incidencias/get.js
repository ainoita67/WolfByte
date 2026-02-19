function capitalizar(string) {
    return string.charAt(0).toUpperCase()+string.slice(1).toLowerCase();
}



// VER INCIDENCIAS

//API Obtener ver incidencias
function obtenerVerIncidencias(){
    fetch(window.location.origin+"/API/incidencias")
    .then(res => res.json())
    .then(response => {
        let incidencias = response.data;
        mostrarIncidencias(incidencias);
    });
}


function mostrarIncidencias(incidencias){
    incidencias=incidencias.reverse();
    let tablaverincidencias = document.getElementById("verIncidenciasTableBody");
    tablaverincidencias.innerHTML = "";
    if(incidencias.length === 0){
        let numColumnas = document.getElementsByTagName("table")[0].querySelectorAll("thead tr th").length;
        let row = document.createElement("tr");
        row.className = "text-start";
        row.innerHTML = `
            <td colspan="${numColumnas}" class="p-3">No se han encontrado incidencias</td>
        `;
        tablaverincidencias.appendChild(row);
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
}


//API Obtener recursos para filtrar incidencias
function obtenerRecursosSelect(){
    fetch(window.location.origin+"/API/recurso")
    .then(res => res.json())
    .then(response => {
        let recursos = response.data;

        let filtrarRecurso = document.getElementById("filtrarRecurso");
        filtrarRecurso.innerHTML = "";
        let optiontodos=document.createElement("option");
        optiontodos.setAttribute("value", "Todos");
        optiontodos.textContent = "Todos";
        filtrarRecurso.appendChild(optiontodos);

        recursos.forEach(recurso => {
            let option = document.createElement("option");
            option.setAttribute("value", recurso.id_recurso);
            option.textContent = recurso.id_recurso;
            filtrarRecurso.appendChild(option);
        });
    })
    .catch(error => console.error("<p>Error al obtener recursos</p>", error));
}



//API Obtener incidencias para filtrarlas
function activarFiltrarIncidencia(tipo, limite=5){
    let formfiltrar = document.getElementById("formFiltrarIncidencia");
    if(!formfiltrar) return;
    formfiltrar.addEventListener("submit", function(e){
        e.preventDefault();
        fetch(window.location.origin+"/API/incidencias")
            .then(res => res.json())
            .then(response => {
            let incidencias = response.data;

            let recurso = document.getElementById("filtrarRecurso").value;
            let prioridad = document.getElementById("filtrarPrioridad").value;
            let estado = document.getElementById("filtrarEstado").value;
            let fechaInicio = document.getElementById("filtrarFechaInicio").value;
            let fechaFin = document.getElementById("filtrarFechaFin").value;

            incidenciasFiltradas=incidencias.filter(incidencia => {
                // Filtro por recurso
                if(recurso!=="Todos"&&incidencia.id_recurso!=recurso){
                    return false;
                }

                // Filtro por prioridad
                if(prioridad!=="Todos"&&incidencia.prioridad!=prioridad){
                    return false;
                }

                // Filtro por estado
                if(estado!=="Todos"&&incidencia.estado!=estado){
                    return false;
                }

                // Filtro por fechas
                let fechaInc = new Date(incidencia.fecha);

                if(fechaInicio){
                    if(fechaInc < new Date(fechaInicio)){
                        return false;
                    }
                }

                if(fechaFin){
                    if(fechaInc > new Date(fechaFin)){
                        return false;
                    }
                }

                return true;
            })

            if(tipo=="tabla"){
                mostrarIncidencias(incidenciasFiltradas);
            }else if(tipo=="card"){
                mostrarIncidenciasTarjetas(incidenciasFiltradas, limite);
            }
            let modalfiltrar = document.getElementById("modalFiltrarIncidencias");
            let cerrarmodal = bootstrap.Modal.getInstance(modalfiltrar) || new bootstrap.Modal(modalfiltrar);
            cerrarmodal.hide();
        });
    });
}





// CREAR INCIDENCIA

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
                tr.addEventListener("mouseenter", () => {
                    tr.querySelectorAll("td").forEach(td =>
                        td.style.backgroundColor = "#bbbbbb",
                    );
                });
                tr.addEventListener("mouseleave", () => {
                    tr.querySelectorAll("td").forEach(td =>
                        td.style.backgroundColor = "",
                    );
                });

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
    .catch(error => console.error("<p>Error al obtener recursos</p>", error));
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
    .catch(error => console.error("<p>Error al obtener portatiles</p>", error));
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
    .catch(error => console.error("<p>Error al obtener espacios</p>", error));
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
        .catch(error => console.error("<p>Error al obtener espacios por edificio</p>", error));
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
    .catch(error => console.error("<p>Error al obtener edificios</p>", error));
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
    .catch(error => console.error("<p>Error al obtener plantas</p>", error));
}



//API Obtener espacios para crear incidencia
function obtenerEspaciosPorPlanta(edificio, planta=-1){
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
        .catch(error => console.error("<p>Error al obtener espacios por edificio</p>", error));
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



// Menú administrador tarjetas de incidencias

function obtenerIncidenciasTarjetas(limite){
    fetch(window.location.origin+"/API/incidencias")
    .then(res => res.json())
    .then(response => {
        let incidencias = response.data;
        let tarjetasIncidencias = document.getElementById("tarjetasIncidencias");
        tarjetasIncidencias.innerHTML = "";
        if(!incidencias||incidencias.length === 0){
            let card = document.createElement("div");
            card.className = "card h-100 reserva-card text-center";
            card.innerHTML = `
                <div class="card-body bg-secondary-subtle">No se han encontrado incidencias</div>
            `;
            tarjetasIncidencias.appendChild(card);
        }else{
            mostrarIncidenciasTarjetas(incidencias, limite);
        };
    });
}



function mostrarIncidenciasTarjetas(incidencias, limite){
    let num=0;
    let tarjetasIncidencias = document.getElementById("tarjetasIncidencias");
    if(!tarjetasIncidencias) return;
    tarjetasIncidencias.innerHTML = "";
    if(!incidencias||incidencias.length === 0){
        let card = document.createElement("div");
        card.className = "card h-100 reserva-card text-center";
        card.innerHTML = `
            <div class="card-body bg-secondary-subtle">No se han encontrado incidencias</div>
        `;
        tarjetasIncidencias.appendChild(card);
    }else{
        incidencias=incidencias.reverse();
        incidencias.forEach(incidencia => {
            if(num<limite){
                let divIncidencia = document.createElement("div");
                divIncidencia.className="card h-100 mb-4 reserva-card";
                divIncidencia.setAttribute("role", "button");
                divIncidencia.setAttribute("data-bs-toggle", "modal");
                divIncidencia.setAttribute("data-bs-target", "#modalincidencia");
                divIncidencia.setAttribute("data-id", incidencia.id_incidencia);
                divIncidencia.setAttribute("data-titulo", incidencia.titulo);
                divIncidencia.setAttribute("data-id", incidencia.id_incidencia);
                divIncidencia.setAttribute("data-id", incidencia.id_incidencia);
                divIncidencia.setAttribute("data-id", incidencia.id_incidencia);
                divIncidencia.setAttribute("data-id", incidencia.id_incidencia);
                divIncidencia.innerHTML = `
                    <div class="card-body bg-secondary-subtle">
                        <p class="fw-bold mb-0">Incidencia #${incidencia.id_incidencia}</p>
                        <p class="fw-bold mb-0">${incidencia.titulo}</p>
                        <p class="mb-0"><span class="fw-bold">Fecha: </span>${incidencia.fecha}</p>
                        <p class="mb-0"><span class="fw-bold">Prioridad: </span>${capitalizar(incidencia.prioridad)}</p>
                        <p class="mb-0"><span class="fw-bold">Estado: </span>${capitalizar(incidencia.estado)}</p>
                    </div>
                `;
                if(incidencia.estado=="Abierta"){
                    divIncidencia.innerHTML=divIncidencia.innerHTML+'<div class="aceptado-rechazado aceptado"></div>';
                }else if(incidencia.estado=="Resuelta"){
                    divIncidencia.innerHTML=divIncidencia.innerHTML+'<div class="aceptado-rechazado rechazado"></div>';
                }else{
                    divIncidencia.innerHTML=divIncidencia.innerHTML+'<div class="aceptado-rechazado"></div>';
                }

                divIncidencia.addEventListener("click", function(){

                    fetch(window.location.origin+"/API/user/"+incidencia.id_usuario)
                    .then(res => res.json())
                    .then(response => {
                        let usuario = response.data;

                        document.getElementById("incidencia_id").value = incidencia.id_incidencia;
                        document.getElementById("incidencia_titulo").value = incidencia.titulo;
                        document.getElementById("incidencia_descripcion").value = incidencia.descripcion;
                        document.getElementById("incidencia_estado").value = capitalizar(incidencia.estado);
                        document.getElementById("incidencia_prioridad").value = capitalizar(incidencia.prioridad);
                        document.getElementById("incidencia_id_usuario").value = incidencia.id_usuario;
                        document.getElementById("incidencia_usuario").value = usuario.nombre;
                        document.getElementById("incidencia_recurso").value = incidencia.id_recurso;

                        document.getElementById("incidencia_fecha").value = incidencia.fecha.replace(" ", "T");
                    });

                });

                tarjetasIncidencias.appendChild(divIncidencia);
                num++;
            }
        });
    }
}