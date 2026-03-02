// plantas/plantas.js
const API_BASE = 'http://192.168.13.202:83';
const API_PLANTAS = `${API_BASE}/API/plantas`;
const API_EDIFICIOS = `${API_BASE}/API/edificios`;

// Variable para almacenar los edificios obtenidos de la API
let edificios = {};

// Función para obtener los edificios de la API
async function cargarEdificios() {
    try {
        const response = await fetch(API_EDIFICIOS);
        if (!response.ok) throw new Error('Error al cargar edificios');
        const respuesta = await response.json();
        
        // Determinar dónde están los datos
        let edificiosArray = [];
        if (Array.isArray(respuesta)) {
            edificiosArray = respuesta;
        } else if (respuesta.data && Array.isArray(respuesta.data)) {
            edificiosArray = respuesta.data;
        } else {
            edificiosArray = [];
        }
        
        // Transformar el array de edificios a objeto {id: nombre}
        edificios = {};
        edificiosArray.forEach(edificio => {
            edificios[edificio.id_edificio] = edificio.nombre_edificio;
        });
        
        console.log('Edificios cargados:', edificios);
        
        // Actualizar selects después de cargar
        actualizarSelectEdificios();
        actualizarSelectEdificiosEditar();
        
        return edificios;
    } catch (error) {
        console.error('Error cargando edificios:', error);
        return {};
    }
}

function obtenerNombreEdificio(idEdificio) {
    return edificios[idEdificio] || 'Edificio ' + idEdificio;
}

// Función para mostrar notificaciones toast
function mostrarToast(mensaje, tipo = 'exito') {
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const bgColor = tipo === 'exito' ? 'bg-success' : 'bg-danger';
    const icono = tipo === 'exito' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${icono} me-2"></i>
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Función principal para obtener plantas
function obtenerPlantas() {
    const contenedor = document.getElementById("plantasContainer");
    if (!contenedor) {
        return;
    }
    
    contenedor.innerHTML = `
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando plantas...</p>
        </div>
    `;
    
    fetch(API_PLANTAS)
        .then(response => {
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            return response.json();
        })
        .then(respuesta => {
            // La respuesta puede ser directamente el array
            const plantas = Array.isArray(respuesta) ? respuesta : (respuesta.data || []);
            contenedor.innerHTML = "";

            if (plantas.length === 0) {
                contenedor.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-building fs-1 text-muted"></i>
                        <p class="text-muted mt-3">No hay plantas registradas</p>
                        <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#modalCrear">
                            <i class="bi bi-plus-circle"></i> Crear primera planta
                        </button>
                    </div>
                `;
                return;
            }

            plantas.forEach(planta => {
                const card = document.createElement("div");
                card.className = "col-12 col-md-6 col-lg-4 mb-4";
                
                const numeroPlanta = planta.numero_planta || 'N/A';
                const nombrePlanta = planta.nombre_planta || 'Sin nombre';
                const idEdificio = planta.id_edificio;
                const nombreEdificio = planta.nombre_edificio || obtenerNombreEdificio(idEdificio);
                const totalEspacios = planta.total_espacios || 0;
                
                card.innerHTML = `
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-blue text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">${nombreEdificio}</h5>
                            <span class="badge bg-light text-dark">Planta ${numeroPlanta}</span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-muted">${nombrePlanta}</h6>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="mb-1"><strong>ID Edificio:</strong></p>
                                    <p class="fs-5 text-muted">${idEdificio}</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>Espacios:</strong></p>
                                    <p class="fs-5 text-primary">${totalEspacios}</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-warning btn-editar" 
                                    data-numero="${numeroPlanta}"
                                    data-edificio="${idEdificio}"
                                    data-nombre="${nombrePlanta}"
                                    data-nombre-edificio="${nombreEdificio}">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                contenedor.appendChild(card);
            });

            // Añadir event listeners a los botones de editar
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const numero = this.dataset.numero;
                    const idEdificio = this.dataset.edificio;
                    const nombre = this.dataset.nombre;
                    const nombreEdificio = this.dataset.nombreEdificio;
                    
                    abrirModalEditar(numero, idEdificio, nombre, nombreEdificio);
                });
            });
        })
        .catch(error => {
            console.error('Error:', error);
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                    <h5 class="mt-3 text-danger">Error de conexión</h5>
                    <p class="text-muted">${error.message}</p>
                    <button class="btn btn-primary mt-2" onclick="obtenerPlantas()">
                        <i class="bi bi-arrow-clockwise"></i> Reintentar
                    </button>
                </div>
            `;
        });
}

// Función para abrir el modal de edición con los datos de la planta
function abrirModalEditar(numero, idEdificio, nombre, nombreEdificio) {
    // Cargar edificios si es necesario
    actualizarSelectEdificiosEditar();
    
    // Rellenar el formulario con los datos actuales
    document.getElementById('editar_numero_original').value = numero;
    document.getElementById('editar_edificio_original').value = idEdificio;
    document.getElementById('editarNumeroPlanta').value = numero;
    document.getElementById('editarNombrePlanta').value = nombre;
    
    // Seleccionar el edificio en el select (aunque esté disabled)
    const selectEdificio = document.getElementById('editarSelectEdificio');
    selectEdificio.value = idEdificio;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}

// Función para actualizar el select de edificios en el modal de edición
function actualizarSelectEdificiosEditar() {
    const edificioSelect = document.getElementById('editarSelectEdificio');
    if (edificioSelect) {
        edificioSelect.innerHTML = '<option value="">Seleccionar edificio</option>';
        
        const entries = Object.entries(edificios);
        if (entries.length === 0) {
            const option = document.createElement('option');
            option.value = "";
            option.textContent = "No hay edificios disponibles";
            option.disabled = true;
            edificioSelect.appendChild(option);
        } else {
            for (const [id, nombre] of entries) {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = nombre;
                edificioSelect.appendChild(option);
            }
        }
    }
}

// Función para actualizar el select de edificios en el modal de creación
function actualizarSelectEdificios() {
    const edificioSelect = document.querySelector("#formCrearPlanta select[name='edificio']");
    if (edificioSelect) {
        const valorActual = edificioSelect.value;
        edificioSelect.innerHTML = '<option value="">Seleccionar edificio</option>';
        
        const entries = Object.entries(edificios);
        if (entries.length === 0) {
            const option = document.createElement('option');
            option.value = "";
            option.textContent = "No hay edificios disponibles";
            option.disabled = true;
            edificioSelect.appendChild(option);
        } else {
            for (const [id, nombre] of entries) {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = nombre;
                edificioSelect.appendChild(option);
            }
        }
        
        if (valorActual && edificios[valorActual]) {
            edificioSelect.value = valorActual;
        }
        
        console.log('Select actualizado con', Object.keys(edificios).length, 'edificios');
    }
}

// Configurar formulario de creación
document.addEventListener("DOMContentLoaded", function() {
    // Cargar edificios para el select (no bloquea)
    cargarEdificios();
    
    // Cargar plantas inmediatamente
    obtenerPlantas();
    
    // Formulario de creación
    const formCrear = document.getElementById("formCrearPlanta");
    if (formCrear) {
        formCrear.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const edificioSelect = this.querySelector("select[name='edificio']");
            const numeroInput = this.querySelector("input[name='numero_planta']");
            const nombreInput = this.querySelector("input[name='nombre_planta']");
            
            const idEdificio = parseInt(edificioSelect?.value);
            const numero = numeroInput?.value;
            const nombrePlanta = nombreInput?.value;
            
            if (!idEdificio || !numero || !nombrePlanta) {
                mostrarToast('Completa todos los campos', 'error');
                return;
            }
            
            const numeroInt = parseInt(numero);
            if (isNaN(numeroInt) || numeroInt < -10 || numeroInt > 100) {
                mostrarToast('El número de planta debe estar entre -10 y 100', 'error');
                return;
            }
            
            const submitBtn = this.querySelector("button[type='submit']");
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creando...';
            }
            
            const url = `${API_PLANTAS}/${idEdificio}`;
            
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    numero_planta: numeroInt,
                    nombre_planta: nombrePlanta
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    const errorMsg = data.error || data.message || `Error ${res.status}`;
                    throw new Error(errorMsg);
                }
                return data;
            })
            .then(() => {
                const nombreEdificio = edificios[idEdificio] || 'desconocido';
                mostrarToast(`Planta ${numero} (${nombrePlanta}) creada correctamente en ${nombreEdificio}`, 'exito');
                
                // Cerrar modal
                const modalElement = document.getElementById("modalCrear");
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                
                this.reset();
                
                setTimeout(() => {
                    obtenerPlantas();
                }, 100);
            })
            .catch(err => {
                console.error('Error:', err);
                mostrarToast(err.message, 'error');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Crear planta';
                }
            });
        });
    }
    
    // Formulario de edición
    const formEditar = document.getElementById("formEditarPlanta");
    if (formEditar) {
        formEditar.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const numeroOriginal = document.getElementById('editar_numero_original').value;
            const idEdificio = parseInt(document.getElementById('editar_edificio_original').value);
            const nuevoNumero = parseInt(document.getElementById('editarNumeroPlanta').value);
            const nuevoNombre = document.getElementById('editarNombrePlanta').value;
            
            if (!nuevoNumero || !nuevoNombre) {
                mostrarToast('Completa todos los campos', 'error');
                return;
            }
            
            if (isNaN(nuevoNumero) || nuevoNumero < -10 || nuevoNumero > 100) {
                mostrarToast('El número de planta debe estar entre -10 y 100', 'error');
                return;
            }
            
            const submitBtn = this.querySelector("button[type='submit']");
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Actualizando...';
            }
            
            // Construir URL con query parameter para el número original
            const url = `${API_PLANTAS}/${idEdificio}?numero_planta=${numeroOriginal}`;
            
            // Preparar datos para actualizar
            const datosActualizar = {};
            if (nuevoNumero !== parseInt(numeroOriginal)) {
                datosActualizar.nuevo_numero_planta = nuevoNumero;
            }
            if (nuevoNombre) {
                datosActualizar.nombre_planta = nuevoNombre;
            }
            
            fetch(url, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosActualizar)
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    const errorMsg = data.error || data.message || `Error ${res.status}`;
                    throw new Error(errorMsg);
                }
                return data;
            })
            .then(() => {
                const nombreEdificio = edificios[idEdificio] || 'desconocido';
                mostrarToast(`Planta actualizada correctamente en ${nombreEdificio}`, 'exito');
                
                // Cerrar modal
                const modalElement = document.getElementById("modalEditar");
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                
                this.reset();
                
                setTimeout(() => {
                    obtenerPlantas();
                }, 100);
            })
            .catch(err => {
                console.error('Error:', err);
                mostrarToast(err.message, 'error');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Actualizar planta';
                }
            });
        });
    }
    
    // Modales
    const modalCrear = document.getElementById('modalCrear');
    if (modalCrear) {
        modalCrear.addEventListener('show.bs.modal', function() {
            actualizarSelectEdificios();
        });
        
        modalCrear.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById("formCrearPlanta");
            if (form) form.reset();
            
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
        });
    }
    
    const modalEditar = document.getElementById('modalEditar');
    if (modalEditar) {
        modalEditar.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById("formEditarPlanta");
            if (form) form.reset();
            
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
        });
    }
});

window.obtenerPlantas = obtenerPlantas;
window.cargarEdificios = cargarEdificios;
window.abrirModalEditar = abrirModalEditar;