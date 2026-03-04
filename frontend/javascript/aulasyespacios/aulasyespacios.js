// aulasyespacios.js
const DOMAIN = "http://192.168.13.202:83/API";

// Variables globales
let edificios = [];
let espaciosGlobal = [];
let edificiosMap = new Map(); // Mapa para acceder rápidamente a los nombres de edificios

// ************  OBTENER DATOS ****************** //

async function getEspacios() {
    const URL = DOMAIN + "/espacios";

    try {
        console.log("Obteniendo espacios de:", URL);
        
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
            console.log(`${jsonData.data.length} espacios encontrados`);
            espaciosGlobal = jsonData.data;
            return espaciosGlobal;
        } else if (Array.isArray(jsonData)) {
            console.log(`${jsonData.length} espacios encontrados`);
            espaciosGlobal = jsonData;
            return espaciosGlobal;
        } else {
            console.warn("Formato inesperado:", jsonData);
            return [];
        }
    } catch (error) {
        console.error("Error obteniendo espacios:", error);
        throw error;
    }
}

async function getEdificios() {
    const URL = DOMAIN + "/edificios";

    try {
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
        
        // Crear mapa para acceso rápido
        edificiosMap.clear();
        edificios.forEach(edificio => {
            edificiosMap.set(edificio.id_edificio, edificio.nombre_edificio);
        });
        
        console.log("Edificios cargados:", edificios);
        console.log("Mapa de edificios:", edificiosMap);
        
        return edificios;
        
    } catch (error) {
        console.error("Error obteniendo edificios:", error);
        throw error;
    }
}

// ************  CARGAR SELECTORES ****************** //

function cargarSelectEdificios(selectId, valorSeleccionado = null) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '<option value="">Seleccionar edificio...</option>';
    
    edificios.forEach(edificio => {
        const option = document.createElement('option');
        option.value = edificio.id_edificio;
        option.textContent = edificio.nombre_edificio;
        select.appendChild(option);
    });
    
    if (valorSeleccionado !== null) {
        select.value = String(valorSeleccionado);
    }
}

// ************  OBTENER NOMBRE DE EDIFICIO ****************** //

function getNombreEdificio(idEdificio) {
    if (!idEdificio) return 'Sin edificio';
    return edificiosMap.get(idEdificio) || `Edificio ${idEdificio}`;
}

// ************  OBTENER NOMBRE DE PLANTA ****************** //

function getNombrePlanta(numeroPlanta) {
    const plantas = {
        0: 'Planta baja',
        1: 'Primera planta',
        2: 'Segunda planta',
        3: 'Tercera planta'
    };
    return plantas[numeroPlanta] || `Planta ${numeroPlanta}`;
}

// ************  ORGANIZAR ESPACIOS POR EDIFICIO Y PLANTA ****************** //

function organizarEspacios(espacios) {
    const organizado = {};
    
    espacios.forEach(espacio => {
        const edificioId = espacio.id_edificio;
        const edificioNombre = getNombreEdificio(edificioId);
        const planta = espacio.numero_planta ?? 0;
        const nombrePlanta = espacio.nombre_planta || getNombrePlanta(planta);
        
        // Usar el nombre del edificio como clave para agrupar
        if (!organizado[edificioNombre]) {
            organizado[edificioNombre] = {
                id: edificioId,
                nombre: edificioNombre.toUpperCase(),
                plantas: {}
            };
        }
        
        if (!organizado[edificioNombre].plantas[planta]) {
            organizado[edificioNombre].plantas[planta] = {
                nombre: nombrePlanta,
                espacios: []
            };
        }
        
        organizado[edificioNombre].plantas[planta].espacios.push(espacio);
    });
    
    return organizado;
}

// ************  MOSTRAR ESPACIOS ****************** //

function mostrarEspacios(espacios) {
    const contenedor = document.getElementById('espaciosContainer');
    if (!contenedor) return;
    
    contenedor.innerHTML = ''; // Limpiar contenedor
    
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
    
    const organizado = organizarEspacios(espacios);
    console.log("Espacios organizados por edificio:", organizado);
    
    // Ordenar edificios por nombre
    const edificiosOrdenados = Object.keys(organizado).sort((a, b) => 
        a.localeCompare(b)
    );
    
    for (const edificioNombre of edificiosOrdenados) {
        const edificioData = organizado[edificioNombre];
        
        // Tarjeta de edificio
        const edificioCol = document.createElement('div');
        edificioCol.className = 'col-12 mb-4';
        
        let edificioHtml = `
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h3 class="h4 mb-0">${edificioData.nombre}</h3>
                </div>
                <div class="card-body">
        `;
        
        // Ordenar plantas por número
        const plantasOrdenadas = Object.entries(edificioData.plantas).sort((a, b) => 
            parseInt(a[0]) - parseInt(b[0])
        );
        
        for (const [plantaNum, plantaData] of plantasOrdenadas) {
            edificioHtml += `
                <div class="mb-4">
                    <h4 class="h5 text-success border-start border-success border-4 ps-2 mb-3">${plantaData.nombre}</h4>
                    <div class="d-flex flex-wrap gap-2">
            `;
            
            // Ordenar espacios por ID
            const espaciosOrdenados = plantaData.espacios.sort((a, b) => 
                a.id_recurso.localeCompare(b.id_recurso)
            );
            
            for (const espacio of espaciosOrdenados) {
                // Diferentes colores según tipo: azul para aula, verde para otros espacios
                const btnColor = espacio.es_aula ? 'btn-primary' : 'btn-success';
                const tipoTexto = espacio.es_aula ? 'Aula' : 'Espacio';
                const estadoBadge = espacio.activo ? 
                    '<span class="badge bg-success">Activo</span>' : 
                    '<span class="badge bg-secondary">Inactivo</span>';
                
                edificioHtml += `
                    <div class="position-relative" style="width: 180px;">
                        <div class="card">
                            <div class="card-header bg-${btnColor.replace('btn-', '')} text-white py-2">
                                <span class="badge bg-light text-dark float-end">${tipoTexto}</span>
                                <h6 class="mb-0">${espacio.id_recurso}</h6>
                            </div>
                            <div class="card-body p-2">
                                <small class="d-block text-muted">${espacio.descripcion || 'Sin descripción'}</small>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge ${espacio.activo ? 'bg-success' : 'bg-secondary'}">${espacio.activo ? 'Activo' : 'Inactivo'}</span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info btn-ver" data-id="${espacio.id_recurso}" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-editar" data-id="${espacio.id_recurso}" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-eliminar" data-id="${espacio.id_recurso}" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            edificioHtml += `</div></div>`;
        }
        
        edificioHtml += `</div></div>`;
        edificioCol.innerHTML = edificioHtml;
        contenedor.appendChild(edificioCol);
    }
    
    // Añadir event listeners a los botones
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
    
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const id = btn.dataset.id;
            const espacio = espaciosGlobal.find(e => e.id_recurso === id);
            const nombre = espacio ? espacio.descripcion || id : id;
            confirmarEliminar(id, nombre);
        });
    });
}

// ************  FUNCIONES PARA ESPACIOS ****************** //

function abrirModalCrear() {
    document.getElementById('formCrearEspacio').reset();
    document.getElementById('crearEstado').value = "1";
    document.getElementById('crearTipo').value = "1";
    
    cargarSelectEdificios('crearEdificio');
    
    const modal = new bootstrap.Modal(document.getElementById('modalCrear'));
    modal.show();
}

function abrirModalEditar(id) {
    const espacio = espaciosGlobal.find(e => e.id_recurso === id);
    if (!espacio) {
        mostrarAlerta("Error: No se encontraron los datos del espacio", "danger");
        return;
    }
    
    document.getElementById('editId').value = espacio.id_recurso;
    document.getElementById('editIdDisplay').value = espacio.id_recurso;
    document.getElementById('editDescripcion').value = espacio.descripcion || '';
    document.getElementById('editEstado').value = espacio.activo ? "1" : "0";
    document.getElementById('editTipo').value = espacio.es_aula ? "1" : "0";
    
    cargarSelectEdificios('editEdificio', espacio.id_edificio);
    document.getElementById('editPlanta').value = espacio.numero_planta ?? '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}

function verEspacio(id) {
    const espacio = espaciosGlobal.find(e => e.id_recurso === id);
    if (!espacio) return;
    
    const nombreEdificio = getNombreEdificio(espacio.id_edificio);
    const nombrePlanta = espacio.nombre_planta || getNombrePlanta(espacio.numero_planta);
    
    document.getElementById('verId').textContent = espacio.id_recurso;
    document.getElementById('verDescripcion').textContent = espacio.descripcion || 'Sin descripción';
    document.getElementById('verEdificio').textContent = nombreEdificio;
    document.getElementById('verPlanta').textContent = nombrePlanta;
    document.getElementById('verTipo').textContent = espacio.es_aula ? 'Aula' : 'Otro espacio';
    document.getElementById('verEstado').textContent = espacio.activo ? 'Activo' : 'Inactivo';
    
    // Configurar botón de editar desde el modal de ver
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

function confirmarEliminar(id, nombre) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteEspacio').value = id;
    document.getElementById('deleteDescripcion').value = nombre;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
    modal.show();
}

async function guardarEspacio(evento) {
    evento.preventDefault();
    
    const esCreacion = evento.target.id === 'formCrearEspacio';
    
    let id, id_recurso, descripcion, tipo, id_edificio, numero_planta, activo, es_aula;
    
    if (esCreacion) {
        id_recurso = document.getElementById('crearId').value;
        descripcion = document.getElementById('crearDescripcion').value.trim();
        tipo = document.getElementById('crearTipo').value;
        id_edificio = document.getElementById('crearEdificio').value;
        numero_planta = document.getElementById('crearPlanta').value;
        activo = document.getElementById('crearEstado').value === "1";
        id = null;
    } else {
        id = document.getElementById('editId').value;
        id_recurso = document.getElementById('editIdDisplay').value;
        descripcion = document.getElementById('editDescripcion').value.trim();
        tipo = document.getElementById('editTipo').value;
        id_edificio = document.getElementById('editEdificio').value;
        numero_planta = document.getElementById('editPlanta').value;
        activo = document.getElementById('editEstado').value === "1";
    }
    
    es_aula = tipo === "1";
    
    // Validaciones básicas
    if (!id_recurso || !descripcion || !id_edificio || numero_planta === '') {
        mostrarAlerta("Por favor, complete todos los campos requeridos", "warning");
        return;
    }
    
    const datos = {
        id_recurso: id_recurso,
        descripcion: descripcion,
        activo: activo ? 1 : 0,
        especial: 0,
        numero_planta: parseInt(numero_planta),
        id_edificio: parseInt(id_edificio),
        es_aula: es_aula ? 1 : 0
    };
    
    try {
        let response;
        let url;
        
        if (!esCreacion && id) {
            url = `${DOMAIN}/espacios/${id}`;
            response = await fetch(url, {
                method: "PUT",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            });
        } else {
            url = `${DOMAIN}/espacios`;
            response = await fetch(url, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            });
        }
        
        const result = await response.json();
        console.log("Respuesta guardar:", result);
        
        if (!response.ok) {
            if (result.errors) {
                const mensajes = Object.values(result.errors).join("<br>");
                throw new Error(mensajes);
            } else {
                throw new Error(result.message || `Error ${response.status}`);
            }
        }
        
        mostrarAlerta(
            esCreacion ? "Espacio creado correctamente" : "Espacio actualizado correctamente",
            "success"
        );
        
        // Cerrar modal
        const modalId = esCreacion ? 'modalCrear' : 'modalEditar';
        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modal) modal.hide();
        
        // Recargar datos
        await cargarTodosLosDatos();
        
    } catch (error) {
        console.error("Error guardando espacio:", error);
        mostrarAlerta(error.message, "danger");
    }
}

async function eliminarEspacio() {
    const id = document.getElementById('deleteId').value;
    
    if (!id) return;
    
    try {
        const url = `${DOMAIN}/espacios/${id}`;
        console.log("Eliminando espacio en:", url);
        
        const response = await fetch(url, {
            method: "DELETE",
            headers: {
                "Accept": "application/json"
            }
        });
        
        if (response.status === 204) {
            mostrarAlerta("Espacio eliminado correctamente", "success");
        } else {
            const result = await response.json();
            console.log("Respuesta eliminar:", result);
            if (!response.ok) {
                if (response.status === 409) {
                    throw new Error("No se puede eliminar: el espacio está siendo utilizado en reservas");
                } else {
                    throw new Error(result.message || `Error ${response.status}`);
                }
            }
            mostrarAlerta("Espacio eliminado correctamente", "success");
        }
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminar'));
        if (modal) modal.hide();
        
        // Recargar datos
        await cargarTodosLosDatos();
        
    } catch (error) {
        console.error("Error eliminando espacio:", error);
        mostrarAlerta(error.message, "danger");
    }
}

// ************  CARGAR TODOS LOS DATOS ****************** //

async function cargarTodosLosDatos() {
    try {
        // Mostrar loading
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
        
        // Cargar edificios primero
        await getEdificios();
        console.log("Edificios cargados:", edificios);
        console.log("Mapa de edificios:", edificiosMap);
        
        // Cargar espacios
        const espacios = await getEspacios();
        console.log("Espacios cargados:", espacios);
        
        // Mostrar espacios
        mostrarEspacios(espacios);
        
    } catch (error) {
        console.error("Error cargando datos:", error);
        mostrarAlerta("Error al cargar los espacios", "danger");
        
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

document.addEventListener("DOMContentLoaded", async function () {
    console.log("Inicializando gestor de espacios...");
    
    try {
        // Cargar datos
        await cargarTodosLosDatos();
        
        // Botón crear
        const btnCrear = document.querySelector('[data-bs-target="#modalCrear"]');
        if (btnCrear) {
            btnCrear.addEventListener('click', abrirModalCrear);
        }
        
        // Formularios
        const formCrear = document.getElementById('formCrearEspacio');
        if (formCrear) formCrear.addEventListener('submit', guardarEspacio);
        
        const formEditar = document.getElementById('formEditarEspacio');
        if (formEditar) formEditar.addEventListener('submit', guardarEspacio);
        
        const formEliminar = document.getElementById('formEliminarEspacio');
        if (formEliminar) {
            formEliminar.addEventListener('submit', (e) => {
                e.preventDefault();
                eliminarEspacio();
            });
        }
        
        // Limpiar backdrops cuando se cierren modales
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', limpiarBackdrops);
        });
        
        console.log("Inicialización completada");
        
    } catch (error) {
        console.error("Error en inicialización:", error);
        mostrarAlerta("Error al inicializar la página", "danger");
    }
});

// Exportar funciones necesarias para uso global
window.abrirModalCrear = abrirModalCrear;
window.abrirModalEditar = abrirModalEditar;
window.verEspacio = verEspacio;
window.confirmarEliminar = confirmarEliminar;