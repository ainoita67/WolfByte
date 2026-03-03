// portatiles.js
const API_BASE = 'http://192.168.13.202:83';
const API_PORTATILES_MATERIALES = `${API_BASE}/API/portatiles/materiales`;
const API_EDIFICIOS = `${API_BASE}/API/edificios`;
const API_PLANTAS = `${API_BASE}/API/plantas`;

// Variable para almacenar los edificios obtenidos de la API
let edificios = {};

// Función para esperar a que el DOM esté listo
function ready(fn) {
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

// Función para limpiar backdrops residuales
function limpiarBackdrops() {
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// ============================================
// SISTEMA DE TOASTS CON BOOTSTRAP
// ============================================

function mostrarToast(mensaje, tipo = 'success') {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Crear ID único para el toast
    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    
    // Determinar color e icono según el tipo
    let bgClass = 'bg-success';
    let iconClass = 'bi-check-circle-fill';
    let titulo = 'Éxito';
    
    if (tipo === 'error') {
        bgClass = 'bg-danger';
        iconClass = 'bi-exclamation-triangle-fill';
        titulo = 'Error';
    } else if (tipo === 'warning') {
        bgClass = 'bg-warning';
        iconClass = 'bi-exclamation-circle-fill';
        titulo = 'Advertencia';
    } else if (tipo === 'info') {
        bgClass = 'bg-info';
        iconClass = 'bi-info-circle-fill';
        titulo = 'Información';
    }
    
    // Crear HTML del toast
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${iconClass} me-2"></i>
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Añadir toast al contenedor
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    // Inicializar y mostrar el toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        animation: true,
        autohide: true,
        delay: 3000
    });
    
    toast.show();
    
    // Eliminar del DOM después de ocultarse
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// ============================================
// FUNCIONES DE API
// ============================================

// Función para obtener los edificios de la API
async function cargarEdificios() {
    try {
        console.log('Cargando edificios desde:', API_EDIFICIOS);
        const response = await fetch(API_EDIFICIOS);
        if (!response.ok) throw new Error('Error al cargar edificios');
        const respuesta = await response.json();
        
        console.log('Respuesta edificios completa:', respuesta);
        
        let edificiosArray = [];
        if (respuesta.data && Array.isArray(respuesta.data)) {
            edificiosArray = respuesta.data;
        } else if (Array.isArray(respuesta)) {
            edificiosArray = respuesta;
        }
        
        edificios = {};
        edificiosArray.forEach(edificio => {
            edificios[edificio.id_edificio] = edificio.nombre_edificio;
        });
        
        console.log('Edificios procesados:', edificios);
        actualizarSelectEdificios();
        
        return edificios;
    } catch (error) {
        console.error('Error cargando edificios:', error);
        mostrarToast('Error al cargar edificios: ' + error.message, 'error');
        return {};
    }
}

function obtenerNombreEdificio(idEdificio) {
    return edificios[idEdificio] || 'Edificio ' + idEdificio;
}

// Función para crear un nuevo material
async function crearMaterial(data) {
    try {
        console.log('Creando material en:', API_PORTATILES_MATERIALES);
        console.log('Datos:', data);
        
        const response = await fetch(API_PORTATILES_MATERIALES, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const resultado = await response.json();
        console.log('Respuesta:', resultado);
        
        if (!response.ok) {
            // Extraer mensaje de error de la respuesta
            let mensajeError = 'Error al crear el carro';
            
            if (resultado.error) {
                mensajeError = resultado.error;
            } else if (resultado.message) {
                mensajeError = resultado.message;
            } else if (resultado.errors && Array.isArray(resultado.errors)) {
                mensajeError = resultado.errors.join(', ');
            }
            
            // Personalizar mensajes comunes
            if (mensajeError.includes('Duplicate entry') || mensajeError.includes('duplicado')) {
                mensajeError = `Ya existe un carro con el ID "${data.id_recurso}"`;
            } else if (mensajeError.includes('foreign key') || mensajeError.includes('constraint fails')) {
                if (mensajeError.includes('Planta')) {
                    mensajeError = 'La planta seleccionada no existe en este edificio';
                } else if (mensajeError.includes('Edificio')) {
                    mensajeError = 'El edificio seleccionado no existe';
                } else {
                    mensajeError = 'Error de referencia: el edificio o planta no son válidos';
                }
            } else if (mensajeError.includes('SQLSTATE')) {
                mensajeError = 'Error en la base de datos al crear el carro';
            }
            
            throw new Error(mensajeError);
        }
        
        return true;
    } catch (error) {
        console.error('Error creando material:', error);
        throw error;
    }
}

// Función para actualizar un material
async function actualizarMaterial(id, data) {
    try {
        const url = `${API_PORTATILES_MATERIALES}/${id}`;
        console.log('Actualizando material en:', url);
        console.log('Datos:', data);
        
        const response = await fetch(url, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const resultado = await response.json();
        console.log('Respuesta:', resultado);
        
        if (!response.ok) {
            // Extraer mensaje de error de la respuesta
            let mensajeError = 'Error al actualizar el carro';
            
            if (resultado.error) {
                mensajeError = resultado.error;
            } else if (resultado.message) {
                mensajeError = resultado.message;
            } else if (resultado.errors && Array.isArray(resultado.errors)) {
                mensajeError = resultado.errors.join(', ');
            }
            
            // Personalizar mensajes comunes
            if (mensajeError.includes('Duplicate entry') || mensajeError.includes('duplicado')) {
                mensajeError = `Ya existe otro carro con ese ID`;
            } else if (mensajeError.includes('foreign key') || mensajeError.includes('constraint fails')) {
                mensajeError = 'Error de referencia: los datos no son válidos';
            } else if (mensajeError.includes('SQLSTATE')) {
                mensajeError = 'Error en la base de datos al actualizar';
            } else if (mensajeError.includes('not found') || mensajeError.includes('no encontrado')) {
                mensajeError = 'El carro que intentas actualizar no existe';
            }
            
            throw new Error(mensajeError);
        }
        
        return true;
    } catch (error) {
        console.error('Error actualizando material:', error);
        throw error;
    }
}

// Función para eliminar un material
async function eliminarMaterial(id) {
    try {
        const url = `${API_PORTATILES_MATERIALES}/${id}`;
        console.log('Eliminando material en:', url);
        
        const response = await fetch(url, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' }
        });
        
        console.log('Respuesta status:', response.status);
        
        // 204 No Content es éxito
        if (response.status === 204) {
            return true;
        }
        
        // Intentar parsear respuesta de error
        let mensajeError = `Error al eliminar (código ${response.status})`;
        
        try {
            const resultado = await response.json();
            console.log('Respuesta error:', resultado);
            
            if (resultado.error) {
                mensajeError = resultado.error;
            } else if (resultado.message) {
                mensajeError = resultado.message;
            }
            
            // Personalizar mensajes comunes
            if (mensajeError.includes('foreign key') || mensajeError.includes('constraint fails') || mensajeError.includes('en uso')) {
                mensajeError = 'No se puede eliminar porque tiene reservas asociadas';
            } else if (mensajeError.includes('not found') || mensajeError.includes('no encontrado')) {
                mensajeError = 'El carro que intentas eliminar no existe';
            }
            
        } catch {
            // Si no se puede parsear, usar mensaje por defecto
        }
        
        if (!response.ok) {
            throw new Error(mensajeError);
        }
        
        return true;
    } catch (error) {
        console.error('Error eliminando material:', error);
        throw error;
    }
}

// ============================================
// FUNCIONES DE INTERFAZ
// ============================================

// Función principal para obtener materiales
async function obtenerMateriales() {
    console.log('Ejecutando obtenerMateriales');
    console.log('URL completa:', API_PORTATILES_MATERIALES);
    
    const contenedor = document.getElementById("portatilesContainer");
    if (!contenedor) {
        console.error('ERROR: No se encontró el elemento portatilesContainer');
        return;
    }
    
    contenedor.innerHTML = `
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando materiales...</p>
        </div>
    `;
    
    try {
        const response = await fetch(API_PORTATILES_MATERIALES);
        console.log('Status de respuesta:', response.status);
        
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        
        const respuesta = await response.json();
        console.log('Respuesta completa de materiales:', respuesta);
        
        let materiales = [];
        
        if (respuesta.data && Array.isArray(respuesta.data)) {
            materiales = respuesta.data;
            console.log('Materiales encontrados en data:', materiales);
        } else if (Array.isArray(respuesta)) {
            materiales = respuesta;
            console.log('Materiales encontrados como array directo:', materiales);
        } else {
            console.log('Formato de respuesta no esperado:', respuesta);
        }
        
        console.log('Materiales procesados:', materiales);
        console.log('Número de materiales:', materiales.length);
        
        contenedor.innerHTML = '';
        
        if (materiales.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-laptop fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No hay carros de portátiles registrados</p>
                    <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#modalCrear">
                        <i class="bi bi-plus-circle"></i> Crear primer carro
                    </button>
                </div>
            `;
            return;
        }
        
        materiales.forEach(material => {
            const card = document.createElement("div");
            card.className = "col-12 col-md-6 col-lg-4 mb-4";
            
            const id = material.id || material.id_material || material.id_recurso;
            const descripcion = material.descripcion || `Carro ${id}`;
            const idEdificio = material.id_edificio;
            const nombreEdificio = material.edificio || material.nombre_edificio || obtenerNombreEdificio(idEdificio) || 'Edificio';
            const numeroPlanta = material.numero_planta !== undefined ? material.numero_planta : 0;
            
            let nombrePlanta = material.planta;
            if (!nombrePlanta) {
                if (numeroPlanta === 0) nombrePlanta = 'Planta baja';
                else if (numeroPlanta === 1) nombrePlanta = 'Primera planta';
                else nombrePlanta = `Planta ${numeroPlanta}`;
            }
            
            const unidades = material.unidades || 0;
            const activo = material.activo !== undefined ? material.activo : 1;
            
            const estado = activo ? 'Activo' : 'Inactivo';
            const badgeClass = activo ? 'bg-success' : 'bg-secondary';
            
            card.innerHTML = `
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-blue text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">${descripcion} (${id})</h5>
                        <span class="badge ${badgeClass}">${estado}</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="mb-1"><strong>Edificio:</strong></p>
                                <p class="text-muted">${nombreEdificio}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><strong>Planta:</strong></p>
                                <p class="text-muted">${nombrePlanta}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <p class="mb-1"><strong>Nº portátiles:</strong></p>
                                <p class="fs-4 text-primary">${unidades}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-primary btn-mostrar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalMostrar"
                                data-id="${id}"
                                data-carro="${descripcion}"
                                data-edificio="${nombreEdificio}"
                                data-planta="${nombrePlanta}"
                                data-unidades="${unidades}"
                                data-estado="${estado}">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn btn-sm btn-warning btn-editar"
                                data-id="${id}"
                                data-carro="${descripcion}"
                                data-edificio="${nombreEdificio}"
                                data-id-edificio="${idEdificio}"
                                data-planta="${nombrePlanta}"
                                data-numero-planta="${numeroPlanta}"
                                data-unidades="${unidades}"
                                data-estado="${activo}">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar"
                                data-id="${id}"
                                data-carro="${descripcion}"
                                data-edificio="${nombreEdificio}"
                                data-planta="${nombrePlanta}"
                                data-unidades="${unidades}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            contenedor.appendChild(card);
        });

        configurarBotones();
        console.log('Tarjetas mostradas correctamente');
        
    } catch (error) {
        console.error('Error detallado:', error);
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                <h5 class="mt-3 text-danger">Error de conexión</h5>
                <p class="text-muted">No se pudo conectar con el servidor</p>
                <button class="btn btn-primary mt-2" onclick="obtenerMateriales()">
                    <i class="bi bi-arrow-clockwise"></i> Reintentar
                </button>
            </div>
        `;
        mostrarToast('Error al cargar materiales: ' + error.message, 'error');
    }
}

function configurarBotones() {
    // Configurar botones de editar (MANUALMENTE, sin data-bs-toggle)
    document.querySelectorAll(".btn-editar").forEach(boton => {
        // Quitar cualquier atributo data-bs-toggle que pueda interferir
        boton.removeAttribute('data-bs-toggle');
        boton.removeAttribute('data-bs-target');
        
        boton.addEventListener("click", function(e) {
            e.preventDefault();
            
            // Rellenar el formulario
            document.getElementById("editId").value = this.dataset.id;
            document.getElementById("editCarro").value = this.dataset.carro;
            document.getElementById("editEdificio").value = this.dataset.edificio;
            document.getElementById("editIdEdificio").value = this.dataset.idEdificio;
            document.getElementById("editPlanta").value = this.dataset.planta;
            document.getElementById("editNumeroPlanta").value = this.dataset.numeroPlanta;
            document.getElementById("editUnidades").value = this.dataset.unidades;
            
            const estadoSelect = document.getElementById("editEstado");
            if (estadoSelect) {
                estadoSelect.value = this.dataset.estado;
            }
            
            // Limpiar backdrops residuales
            limpiarBackdrops();
            
            // Abrir modal MANUALMENTE
            const modalElement = document.getElementById("modalEditar");
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            }
        });
    });
    
    // Configurar botones de eliminar (MANUALMENTE, sin data-bs-toggle)
    document.querySelectorAll(".btn-eliminar").forEach(boton => {
        // Quitar cualquier atributo data-bs-toggle que pueda interferir
        boton.removeAttribute('data-bs-toggle');
        boton.removeAttribute('data-bs-target');
        
        boton.addEventListener("click", function(e) {
            e.preventDefault();
            
            // Rellenar el formulario de eliminación
            document.getElementById("deleteId").value = this.dataset.id;
            document.getElementById("deleteCarro").value = this.dataset.carro;
            document.getElementById("deleteEdificio").value = this.dataset.edificio;
            document.getElementById("deletePlanta").value = this.dataset.planta;
            document.getElementById("deleteUnidades").value = this.dataset.unidades;
            
            // Limpiar backdrops residuales
            limpiarBackdrops();
            
            // Abrir modal de eliminación MANUALMENTE
            const modalElement = document.getElementById("modalEliminar");
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            }
        });
    });

    
    
    // Configurar botones de mostrar
    document.querySelectorAll(".btn-mostrar").forEach(boton => {
        boton.addEventListener("click", function() {
            document.getElementById("mostrarCarro").textContent = this.dataset.carro;
            document.getElementById("mostrarEdificio").textContent = this.dataset.edificio;
            document.getElementById("mostrarPlanta").textContent = this.dataset.planta;
            document.getElementById("mostrarUnidades").textContent = this.dataset.unidades;
            document.getElementById("mostrarEstado").textContent = this.dataset.estado;
        });
    });
}

function actualizarSelectEdificios() {
    const edificioSelect = document.getElementById("crearEdificio");
    if (edificioSelect) {
        edificioSelect.innerHTML = '<option value="" selected disabled>Seleccionar edificio</option>';
        
        if (Object.keys(edificios).length === 0) {
            const option = document.createElement('option');
            option.value = "";
            option.textContent = "No hay edificios disponibles";
            option.disabled = true;
            edificioSelect.appendChild(option);
        } else {
            for (const [id, nombre] of Object.entries(edificios)) {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = nombre;
                edificioSelect.appendChild(option);
            }
        }
    }
}

// ============================================
// INICIALIZACIÓN
// ============================================

ready(function() {
    console.log('DOM cargado - inicializando portatiles.js');
    
    // Verificar que el contenedor existe
    const contenedor = document.getElementById("portatilesContainer");
    if (contenedor) {
        console.log('Contenedor portatilesContainer encontrado');
    } else {
        console.error('Contenedor portatilesContainer NO encontrado');
    }
    
    // Cargar edificios
    cargarEdificios();
    
    // Cargar materiales después de un pequeño retraso
    setTimeout(() => {
        obtenerMateriales();
    }, 500);
    
    // Formulario de creación
    const formCrear = document.querySelector("#modalCrear form");
    if (formCrear) {
        console.log('✅ Formulario de creación encontrado');
        
        formCrear.addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const id = document.getElementById("crearId")?.value;
            const carro = document.getElementById("crearCarro")?.value;
            const idEdificio = parseInt(document.getElementById("crearEdificio")?.value);
            const planta = document.getElementById("crearPlanta")?.value;
            const unidades = parseInt(document.getElementById("crearUnidades")?.value);
            
            if (!id || !carro || !idEdificio || !planta || !unidades) {
                mostrarToast('Completa todos los campos', 'warning');
                return;
            }
            
            if (unidades <= 0) {
                mostrarToast('Las unidades deben ser mayores que 0', 'warning');
                return;
            }
            
            // Determinar número de planta
            let numeroPlanta = 0;
            let nombrePlanta = planta;
            
            if (planta.includes('baja')) {
                numeroPlanta = 0;
                nombrePlanta = 'Planta baja';
            } else if (planta.includes('Primera')) {
                numeroPlanta = 1;
                nombrePlanta = 'Primera planta';
            } else if (planta.includes('Segunda')) {
                numeroPlanta = 2;
                nombrePlanta = 'Segunda planta';
            }
            
            const submitBtn = formCrear.querySelector("button[type='submit']");
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creando...';
            }
            
            try {
                // PASO 1: Verificar si la planta existe
                console.log('Verificando planta:', { idEdificio, numeroPlanta });
                
                // Intentar obtener la planta directamente
                const plantaCheckResponse = await fetch(`${API_BASE}/API/plantas/${idEdificio}?numero_planta=${numeroPlanta}`);
                
                if (!plantaCheckResponse.ok && plantaCheckResponse.status === 404) {
                    console.log('Planta no encontrada, creándola...');
                    
                    // Crear la planta
                    const crearPlantaResponse = await fetch(`${API_BASE}/API/plantas/${idEdificio}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            numero_planta: numeroPlanta,
                            nombre_planta: nombrePlanta
                        })
                    });
                    
                    if (!crearPlantaResponse.ok) {
                        const errorData = await crearPlantaResponse.json();
                        let mensajeError = 'Error al crear la planta';
                        
                        if (errorData.error) {
                            if (typeof errorData.error === 'string') {
                                mensajeError = errorData.error;
                            } else if (Array.isArray(errorData.error)) {
                                mensajeError = errorData.error.join(', ');
                            }
                        }
                        
                        // Personalizar mensaje
                        if (mensajeError.includes('Duplicate') || mensajeError.includes('duplicado')) {
                            mensajeError = `La planta ${nombrePlanta} ya existe en este edificio`;
                        }
                        
                        throw new Error(mensajeError);
                    }
                    
                    console.log('Planta creada correctamente');
                } else if (!plantaCheckResponse.ok) {
                    throw new Error('Error al verificar la planta');
                } else {
                    console.log('Planta ya existe');
                }
                
                // PASO 2: Crear el material
                const data = {
                    id_recurso: id,
                    descripcion: carro,
                    id_edificio: idEdificio,
                    numero_planta: numeroPlanta,
                    unidades: unidades,
                    activo: 1,
                    especial: 0
                };
                
                const success = await crearMaterial(data);
                
                if (success) {
                    // Cerrar modal
                    const modalElement = document.getElementById("modalCrear");
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Limpiar backdrop
                    limpiarBackdrops();
                    
                    // Resetear formulario
                    formCrear.reset();
                    
                    // Recargar lista
                    await obtenerMateriales();
                    
                    mostrarToast(`Carro "${carro}" creado correctamente`, 'success');
                }
            } catch (error) {
                console.error('Error detallado:', error);
                mostrarToast(error.message, 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText || '<i class="bi bi-check-lg"></i> Crear carro';
                }
            }
        });
    } else {
        console.error('Formulario de creación NO encontrado');
    }
    
    // Formulario de edición
    const formEditar = document.querySelector("#modalEditar form");
    if (formEditar) {
        console.log('Formulario de edición encontrado');
        
        formEditar.addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const id = document.getElementById("editId")?.value;
            const carro = document.getElementById("editCarro")?.value;
            const unidades = parseInt(document.getElementById("editUnidades")?.value);
            const activo = parseInt(document.getElementById("editEstado")?.value);
            
            if (!id || !carro || !unidades) {
                mostrarToast('Completa todos los campos', 'warning');
                return;
            }
            
            if (unidades <= 0) {
                mostrarToast('Las unidades deben ser mayores que 0', 'warning');
                return;
            }
            
            const submitBtn = formEditar.querySelector("button[type='submit']");
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Actualizando...';
            }
            
            const data = {
                descripcion: carro,
                unidades: unidades,
                activo: activo,
                especial: 0
            };
            
            try {
                const success = await actualizarMaterial(id, data);
                
                if (success) {
                    // Cerrar modal
                    const modalElement = document.getElementById("modalEditar");
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Limpiar backdrop
                    limpiarBackdrops();
                    
                    // Resetear formulario
                    formEditar.reset();
                    
                    // Recargar lista
                    await obtenerMateriales();
                    
                    mostrarToast(`Carro "${carro}" actualizado correctamente`, 'success');
                }
            } catch (error) {
                mostrarToast(error.message, 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText || '<i class="bi bi-check-lg"></i> Actualizar carro';
                }
            }
        });
    } else {
        console.error('Formulario de edición NO encontrado');
    }
    
    // Formulario de eliminación
    const formEliminar = document.querySelector("#modalEliminar form");
    if (formEliminar) {
        console.log('Formulario de eliminación encontrado');
        
        formEliminar.addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const id = document.getElementById("deleteId")?.value;
            const carro = document.getElementById("deleteCarro")?.value;
            
            console.log('Eliminando material ID:', id, 'Carro:', carro);
            
            if (!id) {
                mostrarToast('Error al identificar el material', 'error');
                return;
            }
            
            // Confirmación adicional
            if (!confirm(`¿Estás seguro de que quieres eliminar el carro "${carro}"?`)) {
                return;
            }
            
            const submitBtn = formEliminar.querySelector("button[type='submit']");
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Eliminando...';
            }
            
            try {
                const success = await eliminarMaterial(id);
                
                if (success) {
                    // Cerrar modal
                    const modalElement = document.getElementById("modalEliminar");
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Limpiar backdrop
                    limpiarBackdrops();
                    
                    // Resetear formulario
                    formEliminar.reset();
                    
                    // Recargar lista
                    await obtenerMateriales();
                    
                    mostrarToast('Carro eliminado correctamente', 'success');
                }
            } catch (error) {
                console.error('Error en eliminación:', error);
                mostrarToast(error.message, 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText || '<i class="bi bi-trash"></i> Eliminar';
                }
            }
        });
    } else {
        console.error('Formulario de eliminación NO encontrado');
    }
    
    // Eventos para limpiar cuando se cierran los modales
    const modales = ['modalCrear', 'modalEditar', 'modalEliminar', 'modalMostrar'];
    modales.forEach(id => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                if (form) form.reset();
                
                // Forzar limpieza del backdrop
                limpiarBackdrops();
            });
        }
    });
});

// Exportar funciones globales
window.obtenerMateriales = obtenerMateriales;
window.cargarEdificios = cargarEdificios;