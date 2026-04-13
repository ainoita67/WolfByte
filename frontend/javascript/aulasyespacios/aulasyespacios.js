// aulasyespacios.js
const API_BASE = `${API}`;

// Variables globales
let edificios = [];
let espaciosGlobal = [];

// ************  OBTENER DATOS ****************** //

async function getCaracteristicasEspacio(id){
    try{
        const response = await fetch(API_BASE+"/espacios/"+id+"/caracteristicas", {
            method: "GET",
            headers: {
                "Accept": "application/json"
            }
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const res = await response.json();

        return res.data;
    } catch (error) {
        console.error("Error obteniendo características:", error);
        throw error;
    }
}


async function getCaracteristicas(){
    try{
        const response = await fetch(API_BASE+"/caracteristicas", {
            method: "GET",
            headers: {
                "Accept": "application/json"
            }
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const res = await response.json();

        return res.data;
    } catch (error) {
        console.error("Error obteniendo características:", error);
        throw error;
    }
}


async function getEspacios() {
    const URL = API_BASE + "/espacios";

    try {
        console.log("🔍 Obteniendo espacios de:", URL);
        
        const response = await fetch(URL, {
            method: "GET",
            headers: {
                "Accept": "application/json"
            }
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const jsonData = await response.json();
        
        let espacios = [];
        if (jsonData.data && Array.isArray(jsonData.data)) {
            espacios = jsonData.data;
        } else if (Array.isArray(jsonData)) {
            espacios = jsonData;
        }
        
        console.log(`${espacios.length} espacios encontrados`);
        if (espacios.length > 0) {
            console.log("Primer espacio:", espacios[0]);
        }
        espaciosGlobal = espacios;
        return espacios;
        
    } catch (error) {
        console.error("Error obteniendo espacios:", error);
        throw error;
    }
}

async function getEdificios() {
    const URL = API_BASE + "/edificios";

    try {
        console.log("🔍 Obteniendo edificios de:", URL);
        
        const response = await fetch(URL, {
            method: "GET",
            headers: {
                "Accept": "application/json"
            }
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const jsonData = await response.json();
        
        if (jsonData.data && Array.isArray(jsonData.data)) {
            edificios = jsonData.data;
        } else if (Array.isArray(jsonData)) {
            edificios = jsonData;
        } else {
            edificios = [];
        }
        
        console.log(`${edificios.length} edificios encontrados:`, edificios);
        return edificios;
        
    } catch (error) {
        console.error("Error obteniendo edificios:", error);
        throw error;
    }
}

// ************  OBTENER NOMBRE DE PLANTA ****************** //

function getNombrePlanta(numeroPlanta) {
    const plantas = {
        0: 'Planta baja',
        1: 'Primera planta',
        2: 'Segunda planta'
    };
    return plantas[numeroPlanta] || `Planta ${numeroPlanta}`;
}

// ************  CARGAR SELECTORES ****************** //

async function cargarSelectEdificios(selectId, valorSeleccionado = null) {
    const select = document.getElementById(selectId);
    if (!select) {
        console.error(`No se encontró el select con id: ${selectId}`);
        return;
    }
    
    select.innerHTML = '<option value="" selected disabled>Seleccionar edificio</option>';
    
    if (!edificios || edificios.length === 0) {
        console.warn("No hay edificios cargados, intentando cargarlos...");
        await getEdificios();
    }
    
    if (!edificios || edificios.length === 0) {
        console.error("No se pudieron cargar los edificios");
        return;
    }
    
    console.log(`Cargando ${edificios.length} edificios en select ${selectId}`);
    console.log("Valor a seleccionar:", valorSeleccionado);
    
    edificios.forEach(edificio => {
        const option = document.createElement('option');
        option.value = edificio.id_edificio;
        option.textContent = edificio.nombre_edificio;
        
        // Comparar correctamente los valores
        if (valorSeleccionado !== null && String(edificio.id_edificio) === String(valorSeleccionado)) {
            option.selected = true;
            console.log(`✅ Edificio seleccionado: ${edificio.nombre_edificio} (ID: ${edificio.id_edificio})`);
            if(selectId=='crearSelect'){
                obtenerPlantas(edificio.id_edificio, 'crear');
            }else if(selectId=='editSelect'){
                obtenerPlantas(edificio.id_edificio, 'editar');
            }
        }
        
        select.appendChild(option);
    });
    
    // Respaldo: también intentar con select.value
    if (valorSeleccionado !== null && select.value !== String(valorSeleccionado)) {
        console.log("Intentando seleccionar con select.value...");
        select.value = String(valorSeleccionado);
        console.log(`Valor después de select.value: ${select.value}`);
    }
}

function obtenerPlantas(edificio, accion, nplanta=0){
    fetch(window.location.origin+"/API/plantas/"+edificio)
    .then(res => res.json())
    .then(response => {
        let plantas = response.data;
        let divplantas;
        let selectplantas;
        if(accion=='crear'){
            divplantas = document.getElementById("divcrearplanta");
            selectplantas = document.getElementById("crearPlanta");
        }else if(accion=='editar'){
            divplantas = document.getElementById("diveditarplanta");
            selectplantas = document.getElementById("editPlanta");
        }else{
            return;
        }
        selectplantas.innerHTML = "";

        if(plantas.length === 0||edificio == ""||!edificio){
            let option=document.createElement("option");
            option.value="";
            option.textContent("Seleccione un edificio primero");
            option.selected=true;
            selectplantas.appendChild(option);
            selectplantas.disabled=true;
        }else{
            selectplantas.disabled=false;

            if(accion=='crear'){
                let optionseleccionar = document.createElement("option");
                optionseleccionar.value = "";
                optionseleccionar.textContent = "Seleccionar planta";
                optionseleccionar.selected = true;
                optionseleccionar.disabled = true;
                selectplantas.appendChild(optionseleccionar);
            }

            plantas.forEach(planta => {
                let optionplanta = document.createElement("option");
                optionplanta.value = planta.numero_planta;
                optionplanta.textContent = 'Planta '+planta.numero_planta;
                if(nplanta==planta.numero_planta){
                    optionplanta.selected=true;
                }else{
                    optionplanta.selected=false;
                }
                selectplantas.appendChild(optionplanta);
            });
        }
    })
    .catch(error => console.error("<p>Error al obtener plantas</p>", error));
}

async function cargarSelectCaracteristicasCrear(){
    let caracteristicas=await getCaracteristicas();

    let selectcrear=document.getElementById('crearCaracteristicas');
    selectcrear.innerHTML = '';
    let ninguna = document.createElement('option');
    ninguna.textContent = 'Ninguna';
    ninguna.value = '';
    ninguna.selected = true;
    ninguna.classList.add("border", "border-primary");
    selectcrear.appendChild(ninguna);
    caracteristicas.forEach(caracteristica => {
        let option = document.createElement('option');
        option.value = caracteristica.id_caracteristica;
        option.textContent = caracteristica.nombre;
        selectcrear.appendChild(option);
    });
}

async function cargarSelectCaracteristicasEditar(id){
    let caracteristicas=await getCaracteristicas();
    let espacios=await getCaracteristicasEspacio(id)

    let selectedit = document.getElementById('editCaracteristicas');
    selectedit.innerHTML = '';
    let ninguna = document.createElement('option');
    ninguna.textContent = 'Ninguna';
    ninguna.value = '';
    selectedit.appendChild(ninguna);
    let seleccionadas=false;
    caracteristicas.forEach(caracteristica => {
        let option = document.createElement('option');
        option.value = caracteristica.id_caracteristica;
        option.textContent = caracteristica.nombre;
        if(espacios.length>0){
            espacios.forEach(espacio => {
                if(espacio.id_caracteristica==caracteristica.id_caracteristica){
                    option.selected=true;
                    option.classList.add("border", "border-primary");
                    seleccionadas=true;
                }
            });
        }
        selectedit.appendChild(option);
    });
    if(!seleccionadas){
        ninguna.selected=true;
        ninguna.classList.add("border", "border-primary");
    }
}

// ************  MOSTRAR ESPACIOS ****************** //

function mostrarEspacios(espacios) {
    const contenedor = document.getElementById('espaciosContainer');
    if (!contenedor) return;
    
    contenedor.innerHTML = '';
    
    if (!espacios || espacios.length === 0) {
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-3">No hay espacios para mostrar</p>
                <button class="btn btn-success mt-2" onclick="window.abrirModalCrear()">
                    <i class="bi bi-plus-circle"></i> Crear primer espacio
                </button>
            </div>
        `;
        return;
    }
    
    // Agrupar espacios por edificio y planta
    const espaciosPorEdificio = {};
    
    espacios.forEach(espacio => {
        const edificio = espacio.nombre_edificio || 'SIN EDIFICIO';
        const planta = espacio.numero_planta ?? 0;
        const nombrePlanta = espacio.nombre_planta || getNombrePlanta(planta);
        
        if (!espaciosPorEdificio[edificio]) {
            espaciosPorEdificio[edificio] = {};
        }
        
        if (!espaciosPorEdificio[edificio][planta]) {
            espaciosPorEdificio[edificio][planta] = {
                nombre: nombrePlanta,
                espacios: []
            };
        }
        
        espaciosPorEdificio[edificio][planta].espacios.push(espacio);
    });
    
    console.log("Espacios organizados:", espaciosPorEdificio);
    
    // Ordenar edificios
    const edificiosOrdenados = Object.keys(espaciosPorEdificio).sort();
    
    for (const edificio of edificiosOrdenados) {
        // Crear tarjeta de edificio
        const edificioCol = document.createElement('div');
        edificioCol.className = 'col-12 mb-4';
        
        let html = `
            <div class="card shadow-sm">
                <div class="card-header bg-blue text-white">
                    <h3 class="h4 mb-0">${edificio}</h3>
                </div>
                <div class="card-body">
        `;
        
        // Ordenar plantas por número
        const plantasOrdenadas = Object.keys(espaciosPorEdificio[edificio])
            .map(Number)
            .sort((a, b) => a - b);
        
        for (const plantaNum of plantasOrdenadas) {
            const plantaData = espaciosPorEdificio[edificio][plantaNum];
            
            html += `
                <div class="mb-4">
                    <h4 class="h5 text-success border-start border-success border-4 ps-2 mb-3">${plantaData.nombre}</h4>
                    <div class="d-flex flex-wrap gap-2">
            `;
            
            // Ordenar espacios por ID
            const espaciosOrdenados = plantaData.espacios.sort((a, b) => 
                (a.id_recurso || '').localeCompare(b.id_recurso || '')
            );
            
            for (const espacio of espaciosOrdenados) {
                const esAula = espacio.es_aula === 1 || espacio.es_aula === true;
                const btnColor = esAula ? 'blue' : 'success';
                const tipoTexto = esAula ? 'Aula' : 'Espacio';
                
                html += `
                    <div class="card" style="width: 180px;">
                        <div class="card-header bg-${btnColor} text-white py-2">
                            <span class="badge bg-light text-dark float-end">${tipoTexto}</span>
                            <h6 class="mb-0">${espacio.id_recurso}</h6>
                        </div>
                        <div class="card-body p-2">
                            <small class="d-block text-muted">${espacio.descripcion || 'Sin descripción'}</small>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="badge ${espacio.activo ? 'bg-success' : 'bg-secondary'}">
                                    ${espacio.activo ? 'Activo' : 'Inactivo'}
                                </span>
                                <div>
                                    <button class="btn btn-sm btn-primary btn-ver" data-id="${espacio.id_recurso}" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning btn-editar" data-id="${espacio.id_recurso}" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            html += `</div></div>`;
        }
        
        html += `</div></div>`;
        edificioCol.innerHTML = html;
        contenedor.appendChild(edificioCol);
    }
    
    // Event listeners
    document.querySelectorAll('.btn-ver').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const id = btn.dataset.id;
            verEspacio(id);
        });
    });
    
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const id = btn.dataset.id;
            abrirModalEditar(id);
        });
    });
    
}

// ************  FUNCIONES CRUD ****************** //

async function abrirModalCrear() {
    console.log("Abriendo modal crear");
    
    // Asegurar que los edificios están cargados
    if (edificios.length === 0) {
        console.log("Cargando edificios...");
        await getEdificios();
    }
    
    console.log("Edificios cargados:", edificios);
    
    // Resetear formulario - TODOS LOS CAMPOS VACÍOS
    const form = document.getElementById('formCrearEspacio');
    if (form) form.reset();
    
    // Cargar select de edificios (SIN valor seleccionado)
    await cargarSelectEdificios('crearEdificio');
    await cargarSelectCaracteristicasCrear();
    
    // Mostrar modal
    const modalElement = document.getElementById('modalCrear');
    if (!modalElement) {
        console.error("No se encontró el modal con id 'modalCrear'");
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

async function abrirModalEditar(id) {
    console.log("Abriendo modal editar para ID:", id);
    
    const espacio = espaciosGlobal.find(e => e.id_recurso === id);
    if (!espacio) {
        mostrarAlerta("Error: No se encontraron los datos del espacio", "danger");
        return;
    }
    
    console.log("Espacio encontrado:", espacio);
    console.log("ID Edificio a seleccionar:", espacio.id_edificio);
    console.log("Tipo de dato:", typeof espacio.id_edificio);
    
    // Asegurar que los edificios están cargados
    if (edificios.length === 0) {
        console.log("Cargando edificios...");
        await getEdificios();
    }
    await cargarSelectCaracteristicasEditar(id);
    
    // Rellenar el formulario - TODOS LOS DATOS DEL ESPACIO
    document.getElementById('editId').value = espacio.id_recurso;
    document.getElementById('editIdDisplay').value = espacio.id_recurso;
    document.getElementById('editDescripcion').value = espacio.descripcion || '';
    
    // Estado: activo/inactivo
    const estadoSelect = document.getElementById('editEstado');
    if (estadoSelect) {
        estadoSelect.value = espacio.activo ? "1" : "0";
        console.log("Estado seleccionado:", estadoSelect.value);
    }

    // Especial: sí/no
    const especialSelect = document.getElementById('editEspecial');
    if (especialSelect) {
        especialSelect.value = espacio.especial ? "1" : "0";
        console.log("Especial seleccionado:", especialSelect.value);
    }
    
    // Tipo: aula u otro espacio
    const tipoSelect = document.getElementById('editTipo');
    if (tipoSelect) {
        tipoSelect.value = espacio.es_aula ? "1" : "0";
        console.log("Tipo seleccionado:", tipoSelect.value);
    }
    
    // Cargar select de edificios CON el valor seleccionado
    console.log("Cargando select de edificios con valor:", espacio.id_edificio);
    await cargarSelectEdificios('editEdificio', espacio.id_edificio);
    
    // Cargar planta
    const plantaSelect = document.getElementById('editPlanta');
    if (plantaSelect) {
        console.log("Valor planta a seleccionar:", espacio.numero_planta);
        if (espacio.numero_planta !== undefined && espacio.numero_planta !== null) {
            plantaSelect.value = String(espacio.numero_planta);
            console.log("Planta seleccionada:", plantaSelect.value);
            obtenerPlantas(espacio.id_edificio, 'editar', espacio.numero_planta);
        } else {
            plantaSelect.value = "";
        }
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}

async function verEspacio(id) {
    const espacio = espaciosGlobal.find(e => e.id_recurso === id);
    if (!espacio) return;
    
    const nombreEdificio = espacio.nombre_edificio || 'Sin edificio';
    const nombrePlanta = espacio.nombre_planta || getNombrePlanta(espacio.numero_planta);
    
    document.getElementById('verId').textContent = espacio.id_recurso;
    document.getElementById('verDescripcion').textContent = espacio.descripcion || 'Sin descripción';
    document.getElementById('verEdificio').textContent = nombreEdificio;
    document.getElementById('verPlanta').textContent = nombrePlanta;
    document.getElementById('verTipo').textContent = espacio.es_aula ? 'Aula' : 'Otro espacio';
    document.getElementById('verEstado').textContent = espacio.activo ? 'Activo' : 'Inactivo';
    document.getElementById('verEspecial').textContent = espacio.especial ? 'Sí' : 'No';
    let verCaracteristicas = document.getElementById('verCaracteristicas');
    verCaracteristicas.innerHTML = '';
    let caracteristicas=await getCaracteristicasEspacio(espacio.id_recurso);
    if (caracteristicas && caracteristicas.length > 0) {
        let ul = document.createElement('ul');
        caracteristicas.forEach(caracteristica => {
            let li = document.createElement("li");
            li.textContent = caracteristica.nombre;
            ul.appendChild(li);
        });
        verCaracteristicas.appendChild(ul);
    } else {
        verCaracteristicas.textContent = 'No';
    }

    const btnEditar = document.getElementById('btnEditarDesdeVer');
    btnEditar.dataset.id = id;
    btnEditar.onclick = () => {
        const modalVer = bootstrap.Modal.getInstance(document.getElementById('modalVer'));
        modalVer.hide();
        setTimeout(() => abrirModalEditar(id), 500);
    };
    
    const modal = new bootstrap.Modal(document.getElementById('modalVer'));
    modal.show();
}

async function guardarEspacio(evento) {
    evento.preventDefault();
    
    const esCreacion = evento.target.id === 'formCrearEspacio';
    
    let usuario=sessionStorage.getItem("id_usuario");
    let id, id_recurso, descripcion, tipo, id_edificio, numero_planta, activo, especial, es_aula;
    let caracteristicas=[];
    let arraycaracteristicas=[];
    
    if (esCreacion) {
        id_recurso = document.getElementById('crearId')?.value;
        descripcion = document.getElementById('crearDescripcion')?.value.trim();
        tipo = document.getElementById('crearTipo')?.value;
        id_edificio = document.getElementById('crearEdificio')?.value;
        numero_planta = document.getElementById('crearPlanta')?.value;
        activo = document.getElementById('crearEstado')?.value === "1";
        especial = document.getElementById('crearEspecial')?.value === "1";
        caracteristicas = Array.from(document.getElementById('crearCaracteristicas').selectedOptions).map(opt => opt.value)
        .filter(valor => valor !== '')   // opcional: quitar "Ninguna"
        .map(valor => Number(valor));

        arraycaracteristicas = caracteristicas.map(id => ({
            id_caracteristica: id
        }));
        console.log("CREANDO - Datos del formulario:", {id_recurso, descripcion, tipo, id_edificio, numero_planta, activo, especial});
    } else {
        id = document.getElementById('editId')?.value;
        id_recurso = document.getElementById('editIdDisplay')?.value;
        descripcion = document.getElementById('editDescripcion')?.value.trim();
        tipo = document.getElementById('editTipo')?.value;
        id_edificio = document.getElementById('editEdificio')?.value;
        numero_planta = document.getElementById('editPlanta')?.value;
        activo = document.getElementById('editEstado')?.value === "1";
        especial = document.getElementById('editEspecial')?.value === "1";
        
        caracteristicas = Array.from(document.getElementById("editCaracteristicas").selectedOptions).map(opt => opt.value)
        .filter(valor => valor !== '')   // opcional: quitar "Ninguna"
        .map(valor => Number(valor));

        arraycaracteristicas = caracteristicas.map(id => ({
            id_caracteristica: id
        }));
        console.log("EDITANDO - Datos del formulario:", {id, id_recurso, descripcion, tipo, id_edificio, numero_planta, activo, especial, arraycaracteristicas});
    }
    
    es_aula = tipo === "1";
    
    if (!id_recurso || !descripcion || !id_edificio || numero_planta === '') {
        mostrarAlerta("Por favor, complete todos los campos requeridos", "warning");
        return;
    }
    
    const datos = {
        id_recurso: id_recurso,
        descripcion: descripcion,
        activo: activo ? 1 : 0,
        especial: especial ? 1 : 0,
        numero_planta: parseInt(numero_planta),
        id_edificio: parseInt(id_edificio),
        es_aula: es_aula ? 1 : 0,
        id_usuario: usuario
    };
    
    console.log("Enviando datos:", datos);
    
    try {
        let response;
        let responsecaracteristicas;
        let url;
        let cambios=false;
        
        if (!esCreacion && id) {
            url = `${API}/espacios/${id}`;
            console.log("Actualizando espacio en:", url);
            response = await fetch(url, {
                method: "PUT",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            });
            
            let caracteristicasantes=await getCaracteristicasEspacio(id);
            console.log("CARACTERÍSTICAS");
            console.log(caracteristicasantes);
            for (let caracteristica of caracteristicasantes) {
                url = `${API}/espacios/${id}/caracteristicas`;
                console.log("Creando espacio en:", url);
                responsecaracteristicas = await fetch(url, {
                    method: "DELETE",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({id_caracteristica: caracteristica.id_caracteristica, id_usuario: usuario})
                });
            }

            for (let caracteristica of arraycaracteristicas) {
                url = `${API}/espacios/${id}/caracteristicas`;
                console.log("Creando espacio en:", url);
                responsecaracteristicas = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({id_caracteristica: caracteristica.id_caracteristica, id_usuario: usuario})
                });
            }
            let caracteristicasdespues=await getCaracteristicasEspacio(id);

            if(JSON.stringify(caracteristicasantes) !== JSON.stringify(caracteristicasdespues)) {
                mostrarAlerta(
                    "Características actualizadas correctamente",
                    "success"
                );
                cambios=true;
            }
        } else {
            url = `${API}/espacios`;
            console.log("Creando espacio en:", url);
            response = await fetch(url, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            });

            for (let caracteristica of arraycaracteristicas) {
                url = `${API}/espacios/${datos.id_recurso}/caracteristicas`;
                console.log("Creando espacio en:", url);
                responsecaracteristicas = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({id_caracteristica: caracteristica.id_caracteristica, id_usuario: usuario})
                });
            }
        }
        
        console.log("Respuesta status:", response.status);
        const result = await response.json();
        console.log("Respuesta del servidor:", result);
        
        if (!response.ok) {
            if (result.errors) {
                const mensajes = Object.values(result.errors).join("<br>");
                throw new Error(mensajes);
            } else {
                throw new Error(result.message || `Error ${response.status}`);
            }
        }
        
        if(response.status==200&&!cambios){
            mostrarAlerta(
                "No han habido cambios",
                "warning"
            );
        }else if(response.status!=200){
            mostrarAlerta(
                esCreacion ? "Espacio creado correctamente" : "Espacio actualizado correctamente",
                "success"
            );
        }
        
        const modalId = esCreacion ? 'modalCrear' : 'modalEditar';
        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modal) modal.hide();
        
        await cargarTodosLosDatos();
        
    } catch (error) {
        console.error("Error guardando espacio:", error);
        mostrarAlerta(error.message, "danger");
    }
}

// ************  CARGAR DATOS ****************** //

async function cargarTodosLosDatos() {
    try {
        console.log("Iniciando carga de datos...");
        
        const contenedor = document.getElementById('espaciosContainer');
        if (contenedor) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando espacios...</p>
                </div>
            `;
        }
        
        await getEdificios();
        const espacios = await getEspacios();
        mostrarEspacios(espacios);
        
        console.log("Carga completada");
        
    } catch (error) {
        console.error("Error en carga:", error);
        
        const contenedor = document.getElementById('espaciosContainer');
        if (contenedor) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>
                    <p class="text-danger mt-3">Error al cargar los espacios</p>
                    <p class="text-muted">${error.message}</p>
                    <button class="btn btn-primary mt-2" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Reintentar
                    </button>
                </div>
            `;
        }
    }
}

// ************  LIMPIAR BACKDROPS ****************** //

function limpiarBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// ************  ALERTAS ****************** //

function mostrarAlerta(mensaje, tipo = "info") {
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.className = 'position-fixed top-0 end-0 p-3';
        alertContainer.style.zIndex = '9999';
        document.body.appendChild(alertContainer);
    }

    const alertDiv = document.createElement('div');
    
    const bgClass = tipo === 'success' ? 'bg-success' : 
                    tipo === 'danger' ? 'bg-danger' : 
                    tipo === 'warning' ? 'bg-warning' : 'bg-info';
    
    const textClass = tipo === 'warning' ? 'text-dark' : 'text-white';

    alertDiv.className = `alert ${bgClass} ${textClass} alert-dismissible fade show shadow-lg`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi ${tipo === 'success' ? 'bi-check-circle-fill' : 
                           tipo === 'danger' ? 'bi-exclamation-triangle-fill' : 
                           tipo === 'warning' ? 'bi-exclamation-circle-fill' : 'bi-info-circle-fill'} me-2"></i>
            <div>${mensaje}</div>
            <button type="button" class="btn-close ${textClass}" data-bs-dismiss="alert"></button>
        </div>
    `;

    alertContainer.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }
    }, 5000);
}

// ************  INICIALIZACIÓN ****************** //

document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM cargado, iniciando...");
    cargarTodosLosDatos();
    
    const formCrear = document.getElementById('formCrearEspacio');
    if (formCrear) {
        formCrear.addEventListener('submit', guardarEspacio);
    }
    
    const formEditar = document.getElementById('formEditarEspacio');
    if (formEditar) {
        formEditar.addEventListener('submit', guardarEspacio);
    }
    
    const formEliminar = document.getElementById('formEliminarEspacio');
    if (formEliminar) {
        formEliminar.addEventListener('submit', (e) => {
            e.preventDefault();
            eliminarEspacio();
        });
    }
    
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', limpiarBackdrops);
    });
});

// Exportar funciones para uso global
window.abrirModalCrear = abrirModalCrear;
window.abrirModalEditar = abrirModalEditar;
window.verEspacio = verEspacio;