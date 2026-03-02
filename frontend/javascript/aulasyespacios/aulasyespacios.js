// espacios.js
import { Validator } from "../clases/Validator.js";

const DOMAIN = "http://192.168.13.202:83/API";

// Mensajes
const MENSAJE_CREACION_CORRECTA = "Espacio creado correctamente";
const MENSAJE_CREACION_ERROR = "Error al crear el espacio";
const MENSAJE_EDICION_CORRECTA = "Espacio actualizado correctamente";
const MENSAJE_EDICION_ERROR = "Error al actualizar el espacio";
const MENSAJE_ELIMINACION_CORRECTA = "Espacio eliminado correctamente";
const MENSAJE_ELIMINACION_ERROR = "Error al eliminar el espacio";

// Variables globales
let espacioAEliminar = {
    id: null,
    nombre: null
};

let edificios = [];
let plantas = [];
let espaciosGlobal = []; // Guardar todos los espacios para usarlos en edición

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
            return edificios;
        } else if (Array.isArray(jsonData)) {
            edificios = jsonData;
            return edificios;
        } else {
            return [];
        }
    } catch (error) {
        console.error("Error obteniendo edificios:", error);
        throw error;
    }
}

async function getPlantas() {
    const URL = DOMAIN + "/plantas";

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
            plantas = jsonData.data;
            return plantas;
        } else if (Array.isArray(jsonData)) {
            plantas = jsonData;
            return plantas;
        } else {
            return [];
        }
    } catch (error) {
        console.error("Error obteniendo plantas:", error);
        throw error;
    }
}

// ************  CARGAR SELECTORES ****************** //

function cargarSelectEdificios(valorSeleccionado = null) {
    const select = document.getElementById('espacioEdificio');
    if (!select) return;
    
    select.innerHTML = '<option value="">Seleccionar edificio...</option>';
    
    edificios.forEach(edificio => {
        const option = document.createElement('option');
        option.value = edificio.id_edificio;
        option.textContent = edificio.nombre_edificio;
        select.appendChild(option);
    });
    
    // Si hay un valor seleccionado, establecerlo
    if (valorSeleccionado !== null) {
        select.value = String(valorSeleccionado);
        console.log("Edificio seleccionado:", select.value);
    }
}

function cargarSelectPlantas(valorSeleccionado = null) {
    const select = document.getElementById('espacioPlanta');
    if (!select) return;
    
    select.innerHTML = '<option value="">Seleccionar planta...</option>';
    
    plantas.forEach(planta => {
        const option = document.createElement('option');
        option.value = planta.numero_planta;
        option.textContent = `${planta.nombre_planta} (Planta ${planta.numero_planta})`;
        select.appendChild(option);
    });
    
    // Si hay un valor seleccionado, establecerlo
    if (valorSeleccionado !== null) {
        select.value = String(valorSeleccionado);
        console.log("Planta seleccionada:", select.value);
    }
}

// ************  VALIDACIONES ****************** //

function validarEspacio(datos) {
    const errores = [];
    
    if (!datos.id_recurso || datos.id_recurso.trim() === '') {
        errores.push("El ID del espacio es requerido");
    } else if (datos.id_recurso.length > 10) {
        errores.push("El ID no puede exceder los 10 caracteres");
    }
    
    if (!datos.descripcion || datos.descripcion.trim() === '') {
        errores.push("La descripción es requerida");
    }
    
    if (!datos.tipo) {
        errores.push("El tipo de espacio es requerido");
    }
    
    if (!datos.id_edificio) {
        errores.push("El edificio es requerido");
    }
    
    if (datos.numero_planta === '' || datos.numero_planta === null || datos.numero_planta === undefined) {
        errores.push("La planta es requerida");
    }
    
    return errores;
}

// ************  CREAR/EDITAR ESPACIO ****************** //

function abrirModalCrear() {
    console.log("Abriendo modal para crear nuevo espacio");
    
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Nuevo Espacio';
    document.getElementById('formEspacio').reset();
    document.getElementById('espacioId').value = '';
    document.getElementById('espacioActivo').checked = true;
    document.getElementById('espacioEspecial').checked = false;
    document.getElementById('espacioId_recurso').disabled = false;
    document.getElementById('espacioId_recurso').readOnly = false;
    document.getElementById('espacioId_recurso').value = '';
    
    // Recargar selectores sin valores seleccionados
    cargarSelectEdificios();
    cargarSelectPlantas();
    
    const modal = new bootstrap.Modal(document.getElementById('modalEspacio'));
    modal.show();
}

function abrirModalEditar(id) {
    console.log("Abriendo modal para editar espacio ID:", id);
    
    // Buscar el espacio en la lista global
    const espacio = espaciosGlobal.find(e => e.id_recurso === id);
    
    if (!espacio) {
        console.error("No se encontró el espacio con ID:", id);
        mostrarAlerta("Error: No se encontraron los datos del espacio", "danger");
        return;
    }
    
    console.log("Espacio encontrado:", espacio);
    
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square"></i> Editar Espacio';
    document.getElementById('espacioId').value = espacio.id_recurso;
    document.getElementById('espacioId_recurso').value = espacio.id_recurso;
    document.getElementById('espacioDescripcion').value = espacio.descripcion || '';
    document.getElementById('espacioActivo').checked = espacio.activo === 1 || espacio.activo === true;
    document.getElementById('espacioEspecial').checked = espacio.especial === 1 || espacio.especial === true;
    
    // Recargar selectores con los valores del espacio
    cargarSelectEdificios(espacio.id_edificio);
    cargarSelectPlantas(espacio.numero_planta);
    
    // Seleccionar tipo
    const tipoSelect = document.getElementById('espacioTipo');
    if (espacio.es_aula === 1 || espacio.es_aula === true) {
        tipoSelect.value = 'aula';
    } else {
        tipoSelect.value = 'espacio';
    }
    
    // Deshabilitar ID en edición
    document.getElementById('espacioId_recurso').disabled = true;
    document.getElementById('espacioId_recurso').readOnly = true;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEspacio'));
    modal.show();
}

async function guardarEspacio(evento) {
    evento.preventDefault();
    
    const id = document.getElementById('espacioId').value;
    const id_recurso = document.getElementById('espacioId_recurso').value;
    const descripcion = document.getElementById('espacioDescripcion').value.trim();
    const tipo = document.getElementById('espacioTipo').value;
    const id_edificio = document.getElementById('espacioEdificio').value;
    const numero_planta = document.getElementById('espacioPlanta').value;
    const activo = document.getElementById('espacioActivo').checked;
    const especial = document.getElementById('espacioEspecial').checked;
    const es_aula = tipo === 'aula';
    
    console.log("Datos del formulario:", {
        id, id_recurso, descripcion, tipo, id_edificio, numero_planta, activo, especial, es_aula
    });
    
    // Validaciones
    const datosValidar = {
        id_recurso, descripcion, tipo, id_edificio, numero_planta
    };
    
    const errores = validarEspacio(datosValidar);
    if (errores.length > 0) {
        mostrarAlerta(errores.join("<br>"), "warning");
        return;
    }
    
    const datos = {
        id_recurso: id_recurso,
        descripcion: descripcion,
        activo: activo ? 1 : 0,
        especial: especial ? 1 : 0,
        numero_planta: parseInt(numero_planta),
        id_edificio: parseInt(id_edificio),
        es_aula: es_aula ? 1 : 0
    };
    
    console.log("Enviando datos:", datos);
    
    try {
        let response;
        let url;
        
        if (id) {
            // Actualizar
            url = `${DOMAIN}/espacios/${id}`;
            console.log("Actualizando espacio en:", url);
            
            response = await fetch(url, {
                method: "PUT",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            });
        } else {
            // Crear
            url = `${DOMAIN}/espacios`;
            console.log("Creando espacio en:", url);
            
            response = await fetch(url, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            });
        }
        
        console.log("Respuesta status:", response.status);
        
        const result = await response.json();
        console.log("Respuesta:", result);
        
        if (!response.ok) {
            // Mostrar errores de validación si existen
            if (result.errors) {
                const mensajes = Object.values(result.errors).join("<br>");
                throw new Error(mensajes);
            } else {
                throw new Error(result.message || `Error ${response.status}`);
            }
        }
        
        mostrarAlerta(id ? `${MENSAJE_EDICION_CORRECTA}` : `${MENSAJE_CREACION_CORRECTA}`, "success");
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEspacio'));
        if (modal) {
            modal.hide();
            limpiarBackdrops();
        }
        
        // Recargar datos
        await cargarTodosLosDatos();
        
    } catch (error) {
        console.error("Error guardando espacio:", error);
        mostrarAlerta(`${error.message}`, "danger");
    }
}

// ************  ELIMINAR ESPACIO ****************** //

function abrirModalConfirmarEliminacion(id, nombre) {
    espacioAEliminar.id = id;
    espacioAEliminar.nombre = nombre;
    
    document.getElementById('espacioEliminarNombre').textContent = nombre;
    document.getElementById('espacioEliminarId').value = id;
    
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    modal.show();
}

async function eliminarEspacio() {
    const id = espacioAEliminar.id;
    const nombre = espacioAEliminar.nombre;
    
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
        
        const result = await response.json();
        
        if (!response.ok) {
            if (response.status === 409) {
                throw new Error("No se puede eliminar: el espacio está siendo utilizado en reservas");
            } else {
                throw new Error(result.message || `Error ${response.status}`);
            }
        }
        
        mostrarAlerta(`${nombre} - ${MENSAJE_ELIMINACION_CORRECTA}`, "success");
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar'));
        if (modal) {
            modal.hide();
            limpiarBackdrops();
        }
        
        // Recargar datos
        await cargarTodosLosDatos();
        
    } catch (error) {
        console.error("Error eliminando espacio:", error);
        mostrarAlerta(`${error.message}`, "danger");
    } finally {
        espacioAEliminar.id = null;
        espacioAEliminar.nombre = null;
    }
}

// ************  MOSTRAR ESPACIOS ****************** //

function mostrarEspacios(espacios, contenedorId) {
    const contenedor = document.getElementById(contenedorId);
    if (!contenedor) return;
    
    if (!espacios || espacios.length === 0) {
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-3">No hay espacios para mostrar</p>
            </div>
        `;
        return;
    }
    
    contenedor.innerHTML = '';
    
    espacios.forEach(espacio => {
        const tarjeta = document.createElement('div');
        tarjeta.classList.add("col-12", "col-md-6", "col-lg-4", "mb-4");
        
        const tipoEspacio = espacio.es_aula ? 'Aula' : 'Espacio';
        let estado = '';
        if (espacio.activo === 1 || espacio.activo === true) {
            estado = '<span class="badge bg-success">Activo</span>';
        } else {
            estado = '<span class="badge bg-secondary">Inactivo</span>';
        }
        
        const especial = (espacio.especial === 1 || espacio.especial === true) ? 
            '<span class="badge bg-info">Especial</span>' : '';
        
        tarjeta.innerHTML = `
            <div class="card text-center shadow-sm overflow-hidden h-100 border-0">
                <div class="bg-blue card-head rounded-top py-2">
                    <p class="fs-6 text-light m-0">
                        <i class="bi bi-tag"></i> ${espacio.id_recurso}
                    </p>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="flex-grow-1">
                        <i class="bi ${espacio.es_aula ? 'bi-building' : 'bi-door-open'} text-warning fs-1"></i>
                        <p class="fs-5 card-title mt-3 fw-bold">${espacio.descripcion || 'Sin descripción'}</p>
                        <p class="text-muted small">
                            <i class="bi bi-building"></i> ${espacio.nombre_edificio || 'Sin edificio'}<br>
                            <i class="bi bi-layers"></i> ${espacio.nombre_planta || 'Planta ' + espacio.numero_planta || 'Sin planta'}
                        </p>
                        <p class="mb-2">${estado} ${especial}</p>
                        <p class="text-muted small">${tipoEspacio}</p>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button class="btn btn-sm btn-warning btn-editar-espacio"
                                data-id="${espacio.id_recurso}">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        contenedor.appendChild(tarjeta);
    });
    
    // Eventos para botones de editar
    document.querySelectorAll('.btn-editar-espacio').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            console.log("Botón editar clickeado para ID:", id);
            abrirModalEditar(id);
        });
    });
    
    // Eventos para botones de eliminar
    document.querySelectorAll('.btn-eliminar-espacio').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            abrirModalConfirmarEliminacion(id, nombre);
        });
    });
}

// ************  CARGAR TODOS LOS DATOS ****************** //

async function cargarTodosLosDatos() {
    try {
        // Mostrar loading en todos los contenedores
        const contenedores = ['contenedorAulas', 'contenedorOtrosEspacios', 'contenedorTodos'];
        contenedores.forEach(id => {
            const cont = document.getElementById(id);
            if (cont) {
                cont.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando espacios...</p>
                    </div>
                `;
            }
        });
        
        const espacios = await getEspacios();
        console.log("Espacios cargados:", espacios);
        
        // Filtrar por tipo
        const aulas = espacios.filter(e => e.es_aula === 1 || e.es_aula === true);
        const otrosEspacios = espacios.filter(e => (e.es_aula === 0 || e.es_aula === false));
        
        console.log(`Aulas: ${aulas.length}, Otros: ${otrosEspacios.length}, Total: ${espacios.length}`);
        
        // Mostrar en cada pestaña
        mostrarEspacios(aulas, 'contenedorAulas');
        mostrarEspacios(otrosEspacios, 'contenedorOtrosEspacios');
        mostrarEspacios(espacios, 'contenedorTodos');
        
    } catch (error) {
        console.error("Error cargando datos:", error);
        mostrarAlerta("Error al cargar los espacios", "danger");
        
        // Mostrar error en los contenedores
        const contenedores = ['contenedorAulas', 'contenedorOtrosEspacios', 'contenedorTodos'];
        contenedores.forEach(id => {
            const cont = document.getElementById(id);
            if (cont) {
                cont.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>
                        <p class="text-danger mt-3">Error al cargar los espacios</p>
                        <button class="btn btn-primary mt-2" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Reintentar
                        </button>
                    </div>
                `;
            }
        });
    }
}

// ************  LIMPIAR BACKDROPS ****************** //

function limpiarBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// ************  INICIALIZACIÓN ****************** //

document.addEventListener("DOMContentLoaded", async function () {
    console.log("Inicializando gestor de espacios...");
    
    try {
        // Cargar edificios y plantas para los selectores
        await getEdificios();
        await getPlantas();
        
        // Inicializar selectores sin valores
        cargarSelectEdificios();
        cargarSelectPlantas();
        
        // Cargar espacios
        await cargarTodosLosDatos();
        
        // Botón crear
        const btnCrear = document.getElementById('btnCrear');
        if (btnCrear) {
            btnCrear.addEventListener('click', () => {
                abrirModalCrear();
            });
        }
        
        // Formulario de espacio
        const formEspacio = document.getElementById('formEspacio');
        if (formEspacio) {
            formEspacio.addEventListener('submit', guardarEspacio);
        }
        
        // Botón de confirmación de eliminación
        const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
        if (btnConfirmarEliminar) {
            btnConfirmarEliminar.addEventListener('click', eliminarEspacio);
        }
        
        // Limpiar backdrops cuando se cierren modales
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                limpiarBackdrops();
                // Habilitar campo ID al cerrar el modal de creación
                if (this.id === 'modalEspacio' && !document.getElementById('espacioId').value) {
                    document.getElementById('espacioId_recurso').disabled = false;
                    document.getElementById('espacioId_recurso').readOnly = false;
                }
            });
        });
        
        console.log("Inicialización completada");
        
    } catch (error) {
        console.error("Error en inicialización:", error);
        mostrarAlerta("Error al inicializar la página", "danger");
    }
});

// ************ ALERTAS ****************** //

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