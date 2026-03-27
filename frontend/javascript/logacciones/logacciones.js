let logsFiltrados=[];
let paginacion=25

//API Obtener logs de acciones
function obtenerLogs(npagina=1){
    let datos={
        'page': npagina,
        'perPage': paginacion
    }

    fetch(window.location.origin+"/API/logacciones", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(response => {
        let logs = response.data;
        mostrarLogs(logs.data);
        mostrarPaginacion(logs.currentPage, logs.totalPages, paginacion);
    });
}



function mostrarLogs(logs){
    let tablaverlogs = document.getElementById("tablalogs");
    tablaverlogs.innerHTML = "";
    if(logs.length === 0){
        let numColumnas = document.getElementsByTagName("table")[0].querySelectorAll("thead tr th").length;
        let row = document.createElement("tr");
        row.className = "text-start";
        row.innerHTML = `
            <td colspan="${numColumnas}" class="p-3">No se han encontrado logs</td>
        `;
        tablaverlogs.appendChild(row);
    }else{
        logs.forEach(log => {
            let recurso='No se ha podido encontrar el recurso';

            if(log.id_usuario!=null){
                recurso='Usuario #'+log.id_usuario;
            }else if(log.id_incidencia!=null){
                recurso='Incidencia #'+log.id_incidencia;
            }else if(log.id_reserva!=null){
                recurso='Reserva #'+log.id_reserva;
            }else if(log.id_recurso!=null){
                recurso='Recurso #'+log.id_recurso;
            }else if(log.id_reserva_permanente!=null){
                recurso='Reserva permanente #'+log.id_reserva_permanente;
            }else if(log.id_liberacion_puntual!=null){
                recurso='Liberación puntual #'+String(log.id_liberacion_puntual);
            }else if(log.usuario!=null){
                recurso=log.usuario;
            }

            let td = document.createElement("tr");
            td.innerHTML = `
                <td class="td-body d-none d-md-table-cell">
                    ${log.id_log}
                </td>
                <td class="td-body d-none d-md-table-cell">
                    ${formatearFecha(log.fecha)}
                </td>
                <td class="td-body">
                    ${log.tipo}
                </td>
                <td class="td-body">
                    ${recurso}
                </td>
                <td class="td-body">
                    ${log.usuarioactor}
                </td>
                <td class="td-body d-md-none">
                    <button class="btn btn-sm bg-primary text-white d-md-none"
                            data-bs-toggle="modal"
                            data-bs-target="#modalMostrar"
                            onclick="verLog(${log.id_log}, '${formatearFecha(log.fecha)}', '${log.tipo}', '${recurso}', '${log.usuarioactor}')">
                        <i class="bi bi-eye"></i> Ver
                    </button>
                </td>
            `;
            tablaverlogs.appendChild(td);
        });
    }
}


function mostrarPaginacion(npagina=1, max=1){
    let divPaginacion=document.getElementById("paginacion");
    divPaginacion.classList.add("ps-3", "mt-3", "mb-5", "d-flex");
    divPaginacion.innerHTML=`
        <button class="btn btn-primary border-0 rounded-pill" id="btninicio"><i class="bi bi-caret-left-fill"></i><i class="bi bi-caret-left-fill"></i></button>
        <button class="btn btn-primary border-0 rounded-pill ms-3" id="btnantes"><i class="bi bi-caret-left-fill"></i></button>
        <p class="p-0 px-2 m-0 mx-2 fs-5">Página ${npagina}</p>
        <button class="btn btn-primary border-0 rounded-pill me-3" id="btndespues"><i class="bi bi-caret-right-fill"></i></button>
        <button class="btn btn-primary border-0 rounded-pill" id="btnfin"><i class="bi bi-caret-right-fill"></i><i class="bi bi-caret-right-fill"></i></button>
    `

    let btninicio=document.getElementById("btninicio");
    let btnantes=document.getElementById("btnantes");
    let btndespues=document.getElementById("btndespues");
    let btnfin=document.getElementById("btnfin");
    
    if(npagina<=1){
        btnantes.disabled=true;
        btninicio.disabled=true;
    }
    if(npagina>=max){
        btndespues.disabled=true;
        btnfin.disabled=true;
    }

    btninicio.addEventListener("click", function(){
        if(npagina>1){
            if(logsFiltrados.length>0){
                obtenerPaginaLogs(1);
            }else{
                obtenerLogs(1);
            }
        }
    })

    btnantes.addEventListener("click", function(){
        if(npagina>1){
            if(logsFiltrados.length>0){
                obtenerPaginaLogs(npagina-1);
            }else{
                obtenerLogs(npagina-1);
            }
        }
    })

    btndespues.addEventListener("click", function(){
        if(npagina<max){
            if(logsFiltrados.length>0){
                obtenerPaginaLogs(npagina+1);
            }else{
                obtenerLogs(npagina+1);
            }
        }
    })

    btnfin.addEventListener("click", function(){
        if(npagina<max){
            if(logsFiltrados.length>0){
                obtenerPaginaLogs(max);
            }else{
                obtenerLogs(max);
            }
        }
    })
}



//API Obtener logs para filtrarlas
function activarFiltrarLog(){
    let formfiltrar = document.getElementById("formFiltrarLog");
    if(!formfiltrar) return;
    formfiltrar.addEventListener("submit", async function(e){
        try{
            e.preventDefault();
            let res=await fetch(window.location.origin+"/API/logacciones");
            let response=await res.json();
            let logs=response.data;

            let tipo = document.getElementById("filtrarTipoLog").value;
            let usuarioactor = document.getElementById("filtrarUsuarioActor").value;
            let fechaInicio = document.getElementById("filtrarFechaInicio").value;
            let fechaFin = document.getElementById("filtrarFechaFin").value;

            logsFiltrados=logs.data.filter(log => {
                // Filtro por tipo
                if(tipo!=="Todos"&&log.id_tipo_log!=tipo){
                    return false;
                }

                // Filtro por prioridad
                if(usuarioactor!=="Todos"&&log.id_usuario_actor!=usuarioactor){
                    return false;
                }

                // Filtro por fechas
                let fechaInc = new Date(log.fecha);

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
            }) ?? [];

            obtenerPaginaLogs(1);

            let modalfiltrar = document.getElementById("modalFiltrar");
            let cerrarmodal = bootstrap.Modal.getInstance(modalfiltrar);
            cerrarmodal.hide();
        } catch(error) {
            console.error("Error al filtrar logs:", error);
        };
    });
}


function obtenerPaginaLogs(npagina=1) {
    let inicio = (npagina - 1) * paginacion;
    let fin = inicio + paginacion;
    let logsPagina = logsFiltrados.slice(inicio, fin);

    mostrarLogs(logsPagina);

    let totalPaginas = Math.ceil(logsFiltrados.length / paginacion);
    mostrarPaginacion(npagina, totalPaginas, paginacion);
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



function formatearFecha(stringFecha){
    let fecha = new Date(stringFecha);

    let anyo = fecha.getFullYear();
    let mes = String(fecha.getMonth() + 1).padStart(2, '0');
    let dia = String(fecha.getDate()).padStart(2, '0');
    let hh = String(fecha.getHours()).padStart(2, '0');
    let mm = String(fecha.getMinutes()).padStart(2, '0');
    let ss = String(fecha.getSeconds()).padStart(2, '0');

    let fechaFormateada = `${dia}/${mes}/${anyo} ${hh}:${mm}:${ss}`;
    return fechaFormateada;
}


//API Obtener tipos de log para filtrar
function obtenerTipoLogsSelect(){
    fetch(window.location.origin+"/API/tipolog")
    .then(res => res.json())
    .then(response => {
        let tiposLog = response.data;

        let select = document.getElementById("filtrarTipoLog")
        select.innerHTML = "";
        let optiontodos=document.createElement("option");
        optiontodos.setAttribute("value", "Todos");
        optiontodos.textContent = "Todos";
        select.appendChild(optiontodos);

        tiposLog.forEach(tipoLog => {
            let option = document.createElement("option");
            option.setAttribute("value", tipoLog.id_tipo_log);
            option.textContent = tipoLog.tipo;
            select.appendChild(option);
        });
    })
    .catch(error => console.error("<p>Error al obtener los tipos de log</p>", error));
}


//API Obtener tipos de log para filtrar
function obtenerUsuariosSelect(){
    fetch(window.location.origin+"/API/user")
    .then(res => res.json())
    .then(response => {
        let usuarios = response.data;

        let select = document.getElementById("filtrarUsuarioActor")
        select.innerHTML = "";
        let optiontodos=document.createElement("option");
        optiontodos.setAttribute("value", "Todos");
        optiontodos.textContent = "Todos";
        select.appendChild(optiontodos);

        usuarios.forEach(usuario => {
            let option = document.createElement("option");
            option.setAttribute("value", usuario.id_usuario);
            option.textContent = usuario.nombre;
            select.appendChild(option);
        });
    })
    .catch(error => console.error("<p>Error al obtener los usuarios</p>", error));
}