import { Caracteristica } from "./clases/Caracteristica.js";
import { Validator } from "./clases/Validator.js";

const DOMAIN = "http://192.168.13.202:84/API";

// Mensajes
const MENSAJE_INSERCION_CORRECTA = "Característica insertada correctamente.";
const MENSAJE_INSERCION_INCORRECTA = "Error al insertar la característica.";
const MENSAJE_ERROR_ENCONTRAR_FORMULARIO = "Error de formularios característica";
const MENSAJE_EDICION_CORRECTA = "Característica editada correctamente.";
const MENSAJE_EDICION_INCORRECTA = "Error al editar la característica.";
const MENSAJE_ELIMINACION_CORRECTA = "Característica eliminada correctamente.";
const MENSAJE_ELIMINACION_INCORRECTA = "Error al eliminar la característica.";
const REGEX_LETRAS_NUM_ESPACIOS = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-]+$/;


// Variables globales para almacenar datos de eliminación
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
    
    // Validar solo letras, números y espacios
    if (!REGEX_LETRAS_NUM_ESPACIOS.test(nombre)) {
        validator.setisValid(false);
        validator.setmessageError("El nombre solo puede contener letras, números, espacios y guiones");
        return validator;
    }
    
    validator.setisValid(true);
    return validator;
}

// ************  MOSTRAR CARACTERISTICAS ****************** //

async function getCaracteristicasFromApi() {
    const URL_GET_CARACTERISTICAS = DOMAIN + "/caracteristicas";

    let response = await fetch(URL_GET_CARACTERISTICAS, {
        method: "GET",
        headers: {
            "Accept": "application/json"
        }
    });

    if (!response.ok) {
        throw new Error(response.status);
    }

    let jsonData = await response.json();
    return jsonData.data;
}

function showCaracteristicas(caracteristicas) {
    let contenedor = document.querySelector("#contenedorTarjetas");

    if (!contenedor) {
        console.error("No se encontró el contenedor de tarjetas");
        return;
    }

    contenedor.innerHTML = '';

    if (caracteristicas.length === 0) {
        contenedor.innerHTML = `
            <div class="col-12 text-center">
                <p class="text-muted">No hay características registradas</p>
            </div>
        `;
        return;
    }

    for (let caracteristica of caracteristicas) {
        let tarjeta = document.createElement('div');
        tarjeta.classList.add("col-12", "col-md-6", "col-lg-4");

        // Crear instancia de Caracteristica
        const caracteristicaObj = new Caracteristica(caracteristica.nombre);
        
        tarjeta.innerHTML = `
            <div class="card text-center shadow-sm overflow-hidden h-100">
                <div class="bg-azul card-head rounded-top">
                    <p class="fs-6 text-light m-0 py-2">ID: ${caracteristica.id_caracteristica}</p>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="fs-5 card-title flex-grow-1">${caracteristicaObj.getNombre()}</p>
                    <div class="card-footer text-end border-0 bg-transparent">
                        <button class="btn btn-sm bg-azul text-light btn-editar" 
                                data-id="${caracteristica.id_caracteristica}"
                                data-nombre="${caracteristica.nombre}">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar-modal"
                                data-id="${caracteristica.id_caracteristica}"
                                data-nombre="${caracteristica.nombre}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `;

        contenedor.appendChild(tarjeta);
    }

    // Añadir eventos a los botones después de crear las tarjetas
    addEventListenersToButtons();
}

// ************  EVENTOS BOTONES ****************** //

function addEventListenersToButtons() {
    // Botones eliminar - ahora abren modal de confirmación
    document.querySelectorAll('.btn-eliminar-modal').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            abrirModalConfirmarEliminacion(id, nombre);
        });
    });

    // Botones editar
    document.querySelectorAll('.btn-editar').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            abrirModalEdicion(id, nombre);
        });
    });
}

// ************  MODAL CONFIRMACIÓN ELIMINAR ****************** //

function abrirModalConfirmarEliminacion(id, nombre) {
    // Guardar datos en variable global
    caracteristicaAEliminar.id = id;
    caracteristicaAEliminar.nombre = nombre;
    
    // Actualizar contenido del modal
    document.getElementById('nombreCaracteristicaEliminar').textContent = nombre;
    document.getElementById('idCaracteristicaEliminar').value = id;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    modal.show();
}

// Configurar botón de confirmación de eliminación
document.addEventListener("DOMContentLoaded", function() {
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', function() {
            if (caracteristicaAEliminar.id && caracteristicaAEliminar.nombre) {
                eliminarCaracteristica(caracteristicaAEliminar.id, caracteristicaAEliminar.nombre);
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar'));
                modal.hide();
                
                // Limpiar datos
                caracteristicaAEliminar.id = null;
                caracteristicaAEliminar.nombre = null;
            }
        });
    }
});

// ************  ELIMINAR CARACTERISTICAS ****************** //

async function eliminarCaracteristica(id, nombre) {
    const URL_ELIMINAR_CARACTERISTICA = DOMAIN + `/caracteristicas/${id}`;

    try {
        const response = await fetch(URL_ELIMINAR_CARACTERISTICA, {
            method: "DELETE",
            headers: {
                "Accept": "application/json"
            }
        });

        if (!response.ok) {
            throw new Error(response.status);
        }

        const result = await response.json();
        console.log(MENSAJE_ELIMINACION_CORRECTA, result);
        mostrarAlerta(MENSAJE_ELIMINACION_CORRECTA, "success");
        
        // Recargar la lista
        const caracteristicas = await getCaracteristicasFromApi();
        showCaracteristicas(caracteristicas);
        
    } catch (error) {
        console.error(error);
        mostrarAlerta(MENSAJE_ELIMINACION_INCORRECTA, "danger");
    }
}

// ************  EDITAR CARACTERISTICAS ****************** //

function abrirModalEdicion(id, nombre) {
    // Llenar el formulario de edición con los datos actuales
    document.getElementById('editId').value = id;
    document.getElementById('editNombre').value = nombre;
    
    // Mostrar modal de edición
    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}

async function editarCaracteristica(id, nombre) {
    // Usar Validator para validar datos
    const validator = validarNombreCaracteristica(nombre);
    
    if (!validator.getisValid()) {
        mostrarAlerta(validator.getmessageError(), "warning");
        return;
    }

    // Crear instancia de Caracteristica con datos validados
    const caracteristicaObj = new Caracteristica(nombre.trim());
    
    // Crear objeto con los datos actualizados
    const datosActualizados = {
        nombre: caracteristicaObj.getNombre()
    };

    console.log("Enviando datos actualizados:", datosActualizados);

    try {
        const response = await fetch(`${DOMAIN}/caracteristicas/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(datosActualizados)
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        console.log(MENSAJE_EDICION_CORRECTA, result);
        mostrarAlerta(MENSAJE_EDICION_CORRECTA, "success");
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditar'));
        modal.hide();
        
        // Recargar la lista de características
        const caracteristicas = await getCaracteristicasFromApi();
        showCaracteristicas(caracteristicas);
        
    } catch (error) {
        console.error(error);
        mostrarAlerta(MENSAJE_EDICION_INCORRECTA, "danger");
    }
}

// Configurar el formulario de edición cuando se carga la página
document.addEventListener("DOMContentLoaded", function () {
    // Configurar formulario de edición
    const formEditar = document.getElementById('formEditarCaracteristica');
    if (formEditar) {
        formEditar.addEventListener('submit', function(evento) {
            evento.preventDefault();
            
            const id = document.getElementById('editId').value;
            const nombre = document.getElementById('editNombre').value;
            
            editarCaracteristica(id, nombre);
        });
    }
});

// ************  CREAR CARACTERISTICAS ****************** //

document.addEventListener("DOMContentLoaded", function () {
    // Cargar características al inicio
    getCaracteristicasFromApi()
        .then(caracteristicas => {
            showCaracteristicas(caracteristicas);
        })
        .catch(error => {
            console.error("Error cargando características:", error);
            mostrarAlerta("Error al cargar las características", "danger");
        });

    // Configurar formulario de creación
    let formulario = document.querySelector("#formCrearCaracteristica");

    if (!formulario) {
        console.error(MENSAJE_ERROR_ENCONTRAR_FORMULARIO);
        return;
    }

    formulario.addEventListener("submit", manejarEnvioFormulario);
});

function manejarEnvioFormulario(evento) {
    evento.preventDefault();

    let formulario = evento.target;
    let inputNombre = formulario.querySelector('input[type="text"]');

    if (!inputNombre) {
        console.error("No se encontró el campo de nombre");
        return;
    }

    let nombreCaracteristica = inputNombre.value.trim();

    // Usar Validator para validar datos
    const validator = validarNombreCaracteristica(nombreCaracteristica);
    
    if (!validator.getisValid()) {
        mostrarAlerta(validator.getmessageError(), "warning");
        return;
    }

    // Crear instancia de Caracteristica con datos validados
    const caracteristicaObj = new Caracteristica(nombreCaracteristica);
    
    // Crear objeto para enviar al servidor
    let objetoCaracteristica = {
        nombre: caracteristicaObj.getNombre()
    };

    console.log("Objeto característica creado:", objetoCaracteristica);

    enviarDatosCaracteristicas(objetoCaracteristica)
        .then(response => {
            console.log("Respuesta:", response);
            mostrarAlerta(MENSAJE_INSERCION_CORRECTA, "success");

            formulario.reset();
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearCaracteristica'));
            modal.hide();
            
            // Recargar la lista de características
            return getCaracteristicasFromApi();
        })
        .then(caracteristicas => {
            showCaracteristicas(caracteristicas);
        })
        .catch(error => {
            console.error(error);
            mostrarAlerta(MENSAJE_INSERCION_INCORRECTA, "danger");
        });
}

function enviarDatosCaracteristicas(datosCaracteristica) {
    const URL_INSERTAR_CARACTERISTICA = DOMAIN + "/caracteristicas";

    return fetch(URL_INSERTAR_CARACTERISTICA, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify(datosCaracteristica)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(jsonResponse => {
        console.log("Respuesta del servidor:", jsonResponse);
        return jsonResponse;
    });
}

// ************ ALERTAS  ****************** //

function mostrarAlerta(mensaje, tipo = "info") {
    // Crear contenedor de alertas si no existe
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.className = 'position-fixed top-0 end-0 p-3';
        alertContainer.style.zIndex = '1055';
        document.body.appendChild(alertContainer);
    }

    // Crear alerta
    const alertDiv = document.createElement('div');

    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    alertContainer.appendChild(alertDiv);

    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }
    }, 3000);
}