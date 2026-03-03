// caracteristicas.js
import { Validator } from "../clases/Validator.js";

const DOMAIN = "http://192.168.13.202:83/API";

// Mensajes
const MENSAJE_CREACION_CORRECTA = "Característica creada correctamente";
const MENSAJE_CREACION_ERROR = "Error al crear la característica";
const MENSAJE_EDICION_CORRECTA = "Característica editada correctamente";
const MENSAJE_EDICION_ERROR = "Error al editar la característica";
const MENSAJE_ELIMINACION_CORRECTA = "Característica eliminada correctamente";
const MENSAJE_ELIMINACION_ERROR = "Error al eliminar la característica";
const REGEX_LETRAS_NUM_ESPACIOS = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-]+$/;

// Variables globales
let caracteristicaAEliminar = {
    id: null,
    nombre: null
};

// ************  VALIDACIONES ****************** //

function validarNombreCaracteristica(nombre) {
    const validator = new Validator();
    
    if (!nombre || nombre.trim() === '') {
        validator.setisValid(false);
        validator.setmessageError("El nombre de la característica es requerido");
        return validator;
    }
    
    if (nombre.length > 100) {
        validator.setisValid(false);
        validator.setmessageError("El nombre no puede exceder los 100 caracteres");
        return validator;
    }
    
    if (nombre.length < 3) {
        validator.setisValid(false);
        validator.setmessageError("El nombre debe tener al menos 3 caracteres");
        return validator;
    }
    
    if (!REGEX_LETRAS_NUM_ESPACIOS.test(nombre)) {
        validator.setisValid(false);
        validator.setmessageError("El nombre solo puede contener letras, números, espacios y guiones");
        return validator;
    }
    
    validator.setisValid(true);
    return validator;
}

// ************  OBTENER CARACTERISTICAS ****************** //

async function getCaracteristicas() {
    const URL = DOMAIN + "/caracteristicas";

    try {
        console.log("Obteniendo características de:", URL);
        
        const response = await fetch(URL, {
            method: "GET",
            headers: {
                "Accept": "application/json"
            }
        });

        console.log("Status:", response.status);

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const jsonData = await response.json();
        console.log("Respuesta completa:", jsonData);
        
        // IMPORTANTE: La API devuelve { status: "success", data: [...] }
        if (jsonData.data && Array.isArray(jsonData.data)) {
            console.log(`${jsonData.data.length} características encontradas`);
            return jsonData.data;
        } else {
            console.warn("Formato inesperado:", jsonData);
            return [];
        }
    } catch (error) {
        console.error("Error:", error);
        throw error;
    }
}

// ************  CREAR CARACTERISTICA ****************** //

async function crearCaracteristica(nombre) {
    const URL = DOMAIN + "/caracteristicas";

    try {
        const response = await fetch(URL, {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ nombre: nombre.trim() })
        });

        const result = await response.json();

        if (!response.ok) {
            if (response.status === 409) {
                throw new Error("Ya existe una característica con ese nombre");
            } else {
                throw new Error(result.message || `Error ${response.status}`);
            }
        }

        mostrarAlerta(`${MENSAJE_CREACION_CORRECTA}`, 'success');
        
        // Cerrar modal
        const modalElement = document.getElementById('modalCrearCaracteristica');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            limpiarBackdrops();
        }
        
        // Recargar lista
        await cargarCaracteristicas();
        
        return true;
        
    } catch (error) {
        console.error("Error creando:", error);
        mostrarAlerta(`${error.message || MENSAJE_CREACION_ERROR}`, 'danger');
        return false;
    }
}

// ************  EDITAR CARACTERISTICA ****************** //

function abrirModalEdicion(id, nombre) {
    const modalElement = document.getElementById('modalEditarCaracteristica');
    const editId = document.getElementById('editId');
    const editNombre = document.getElementById('editNombre');
    
    if (!modalElement || !editId || !editNombre) {
        mostrarAlerta("Error: No se encontró el modal de edición", "danger");
        return;
    }
    
    editId.value = id;
    editNombre.value = nombre;
    editNombre.classList.remove('is-invalid');
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

async function editarCaracteristica(id, nombre) {
    const URL = DOMAIN + `/caracteristicas/${id}`;

    try {
        const response = await fetch(URL, {
            method: "PUT",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ nombre: nombre.trim() })
        });

        const result = await response.json();

        if (!response.ok) {
            if (response.status === 409) {
                throw new Error("Ya existe otra característica con ese nombre");
            } else if (response.status === 404) {
                throw new Error("La característica no existe");
            } else {
                throw new Error(result.message || `Error ${response.status}`);
            }
        }

        if (result.status === 'no_changes') {
            mostrarAlerta("No se realizaron cambios en el nombre", "info");
        } else {
            mostrarAlerta(`${MENSAJE_EDICION_CORRECTA}`, "success");
        }
        
        // Cerrar modal
        const modalElement = document.getElementById('modalEditarCaracteristica');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            limpiarBackdrops();
        }
        
        // Recargar lista
        await cargarCaracteristicas();
        
        return true;
        
    } catch (error) {
        console.error("Error editando:", error);
        mostrarAlerta(`${error.message}`, 'danger');
        return false;
    }
}

// ************  ELIMINAR CARACTERISTICA ****************** //

function abrirModalConfirmarEliminacion(id, nombre) {
    caracteristicaAEliminar.id = id;
    caracteristicaAEliminar.nombre = nombre;
    
    const nombreSpan = document.getElementById('nombreCaracteristicaEliminar');
    const idInput = document.getElementById('idCaracteristicaEliminar');
    
    if (nombreSpan) nombreSpan.textContent = nombre;
    if (idInput) idInput.value = id;
    
    const modalElement = document.getElementById('modalConfirmarEliminar');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}

async function eliminarCaracteristica(id, nombre) {
    const URL = DOMAIN + `/caracteristicas/${id}`;

    try {
        const response = await fetch(URL, {
            method: "DELETE",
            headers: {
                "Accept": "application/json"
            }
        });

        const result = await response.json();

        if (!response.ok) {
            if (response.status === 409) {
                throw new Error("No se puede eliminar: la característica está siendo utilizada");
            } else if (response.status === 404) {
                throw new Error("La característica no existe");
            } else {
                throw new Error(result.message || `Error ${response.status}`);
            }
        }

        mostrarAlerta(`${nombre} - ${MENSAJE_ELIMINACION_CORRECTA}`, 'success');
        
        // Cerrar modal
        const modalElement = document.getElementById('modalConfirmarEliminar');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            limpiarBackdrops();
        }
        
        // Recargar lista
        await cargarCaracteristicas();
        
    } catch (error) {
        console.error("Error eliminando:", error);
        mostrarAlerta(`${error.message || MENSAJE_ELIMINACION_ERROR}`, 'danger');
    }
}

// ************  MOSTRAR CARACTERISTICAS ****************** //

function mostrarCaracteristicas(caracteristicas) {
    const contenedor = document.getElementById('contenedorCaracteristicas');
    
    if (!contenedor) {
        console.error("No se encontró el elemento #contenedorCaracteristicas");
        return;
    }
    
    console.log("Mostrando características:", caracteristicas);
    
    if (!caracteristicas || caracteristicas.length === 0) {
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-3">No hay características registradas</p>
                <button class="btn btn-success mt-2" id="btnCrearDesdeVacio">
                    <i class="bi bi-plus-circle"></i> Crear primera característica
                </button>
            </div>
        `;
        
        const btnCrearVacio = document.getElementById('btnCrearDesdeVacio');
        if (btnCrearVacio) {
            btnCrearVacio.addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('modalCrearCaracteristica'));
                modal.show();
            });
        }
        return;
    }
    
    contenedor.innerHTML = '';
    
    caracteristicas.forEach(caracteristica => {
        // La API devuelve id_caracteristica y nombre
        const id = caracteristica.id_caracteristica;
        const nombre = caracteristica.nombre;
        
        if (!id || !nombre) {
            console.warn("Característica con formato incorrecto:", caracteristica);
            return;
        }
        
        const tarjeta = document.createElement('div');
        tarjeta.classList.add("col-12", "col-md-6", "col-lg-4", "mb-4");
        
        tarjeta.innerHTML = `
            <div class="card text-center shadow-sm overflow-hidden h-100 border-0">
                <div class="bg-blue card-head rounded-top py-2">
                    <p class="fs-6 text-light m-0">
                        <i class="bi bi-tag"></i> ID: ${id}
                    </p>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="flex-grow-1">
                        <i class="bi bi-star-fill text-warning fs-1"></i>
                        <p class="fs-5 card-title mt-3 fw-bold">${nombre}</p>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button class="btn btn-sm btn-warning btn-editar"
                                data-id="${id}"
                                data-nombre="${nombre}">
                            <i class="bi bi-pencil"></i> Editar
                        </button>   
                    </div>
                </div>
            </div>
        `;
        
        contenedor.appendChild(tarjeta);
    });
    
    // Eventos para botones de editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            abrirModalEdicion(id, nombre);
        });
    });
    
    // Eventos para botones de eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            abrirModalConfirmarEliminacion(id, nombre);
        });
    });
}

// ************  CARGAR CARACTERISTICAS ****************** //

async function cargarCaracteristicas() {
    const contenedor = document.getElementById('contenedorCaracteristicas');
    if (!contenedor) {
        console.error("No se encontró el contenedor");
        return;
    }
    
    try {
        console.log("Cargando características...");
        
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando características...</p>
            </div>
        `;
        
        const caracteristicas = await getCaracteristicas();
        console.log("Características obtenidas:", caracteristicas);
        mostrarCaracteristicas(caracteristicas);
        
    } catch (error) {
        console.error("Error cargando características:", error);
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>
                <p class="text-danger mt-3">Error al cargar las características</p>
                <p class="text-muted">${error.message}</p>
                <button class="btn btn-primary mt-2" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Reintentar
                </button>
            </div>
        `;
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
    console.log("Inicializando gestor de características...");
    
    // Cargar características
    await cargarCaracteristicas();
    
    // Botón crear
    const btnCrear = document.getElementById('btnCrear');
    if (btnCrear) {
        btnCrear.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalCrearCaracteristica'));
            modal.show();
        });
    }
    
    // Formulario de creación
    const formCrear = document.getElementById('formCrearCaracteristica');
    if (formCrear) {
        formCrear.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const inputNombre = document.getElementById('crearNombre');
            const nombre = inputNombre.value.trim();
            
            const validator = validarNombreCaracteristica(nombre);
            if (!validator.getisValid()) {
                mostrarAlerta(validator.getmessageError(), "warning");
                return;
            }
            
            await crearCaracteristica(nombre);
            inputNombre.value = '';
        });
    }
    
    // Formulario de edición
    const formEditar = document.getElementById('formEditarCaracteristica');
    if (formEditar) {
        formEditar.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const id = document.getElementById('editId').value;
            const nombre = document.getElementById('editNombre').value.trim();
            
            const validator = validarNombreCaracteristica(nombre);
            if (!validator.getisValid()) {
                mostrarAlerta(validator.getmessageError(), "warning");
                return;
            }
            
            await editarCaracteristica(id, nombre);
        });
    }
    
    // Botón de confirmación de eliminación
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', async function() {
            const id = caracteristicaAEliminar.id;
            const nombre = caracteristicaAEliminar.nombre;
            
            if (id && nombre) {
                await eliminarCaracteristica(id, nombre);
                caracteristicaAEliminar.id = null;
                caracteristicaAEliminar.nombre = null;
            }
        });
    }
    
    // Limpiar backdrops cuando se cierren modales
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            limpiarBackdrops();
        });
    });
    
    // Limpiar formularios al cerrar modales
    const modalCrear = document.getElementById('modalCrearCaracteristica');
    if (modalCrear) {
        modalCrear.addEventListener('hidden.bs.modal', function() {
            document.getElementById('crearNombre').value = '';
        });
    }
    
    const modalEditar = document.getElementById('modalEditarCaracteristica');
    if (modalEditar) {
        modalEditar.addEventListener('hidden.bs.modal', function() {
            document.getElementById('editNombre').value = '';
            document.getElementById('editNombre').classList.remove('is-invalid');
        });
    }
    
    console.log("Inicialización completada");
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
    }, 3000);
}