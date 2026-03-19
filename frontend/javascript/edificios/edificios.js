// edificios.js
console.log('edificios.js cargado');

const API_BASE = window.location.origin;

// ============================================
// SISTEMA DE TOASTS CON BOOTSTRAP (IGUAL QUE EN PORTÁTILES)
// ============================================
function mostrarToast(mensaje, tipo = 'success') {
    console.log('Toast:', mensaje, tipo);
    
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
    
    // Determinar color según el tipo
    let bgClass = 'bg-success';
    let textColor = 'text-white';
    
    if (tipo === 'error'){
        bgClass = 'bg-danger';
    }else if (tipo === 'warning'){
        bgClass = 'bg-warning';
        textColor = 'text-dark';
    }else if (tipo === 'info'){
        bgClass = 'bg-info';
    }
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center ${textColor} ${bgClass} border-0 fs-6" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">${mensaje}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        animation: true,
        autohide: true,
        delay: 3000
    });
    
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// ============================================
// FUNCIÓN PARA LIMPIAR BACKDROPS (IGUAL QUE EN PORTÁTILES)
// ============================================
function limpiarBackdrops() {
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// ============================================
// CARGAR EDIFICIOS
// ============================================
async function cargarEdificios() {
    console.log('Iniciando carga de edificios...');
    
    const contenedor = document.getElementById('contenedorTarjetas');
    if (!contenedor) {
        console.error('Contenedor no encontrado');
        return;
    }
    
    contenedor.innerHTML = `
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando edificios...</p>
        </div>
    `;
    
    try {
        const res = await fetch(`${API_BASE}/API/edificios`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        console.log('Respuesta status:', res.status);
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const data = await res.json();
        console.log('Datos recibidos:', data);

        const edificios = data.data || data;
        console.log('Edificios procesados:', edificios);

        if (!edificios || edificios.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-building fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No hay edificios registrados</p>
                    <button class="btn btn-success mt-2" onclick="abrirModalCrear()">
                        <i class="bi bi-plus-circle"></i> Crear primer edificio
                    </button>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = '';

        edificios.forEach(edificio => {
            const tarjeta = document.createElement('div');
            tarjeta.classList.add('col-12', 'col-md-6', 'col-lg-4', 'mb-4');

            tarjeta.innerHTML = `
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-blue text-white">
                        <h5 class="card-title mb-0">${edificio.nombre_edificio}</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>ID:</strong> ${edificio.id_edificio}
                        </p>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button class="btn btn-warning btn-sm btn-editar"
                                data-id="${edificio.id_edificio}"
                                data-nombre="${edificio.nombre_edificio}">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            `;

            contenedor.appendChild(tarjeta);
        });

        configurarBotonesEditar();

    } catch (err) {
        console.error('Error al cargar edificios:', err);
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                <h5 class="mt-3 text-danger">Error de conexión</h5>
                <p class="text-muted">${err.message}</p>
                <button class="btn btn-primary mt-3" onclick="cargarEdificios()">
                    <i class="bi bi-arrow-clockwise"></i> Reintentar
                </button>
            </div>
        `;
        mostrarToast('Error al cargar edificios: ' + err.message, 'error');
    }
}

// ============================================
// CONFIGURAR BOTONES DE EDITAR (MANUALMENTE, IGUAL QUE EN PORTÁTILES)
// ============================================
function configurarBotonesEditar() {
    document.querySelectorAll(".btn-editar").forEach(boton => {
        // Quitar cualquier atributo data-bs-toggle que pueda interferir
        boton.removeAttribute('data-bs-toggle');
        boton.removeAttribute('data-bs-target');
        
        boton.addEventListener("click", function(e) {
            e.preventDefault();
            
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            
            console.log('Editando edificio:', id, nombre);
            
            // Rellenar el formulario
            document.getElementById('editId').value = id;
            document.getElementById('editNombre').value = nombre;
            
            // Limpiar backdrops residuales
            limpiarBackdrops();
            
            // Abrir modal MANUALMENTE
            const modalElement = document.getElementById('modalEditar');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            }
        });
    });
}

// ============================================
// FUNCIÓN PARA ABRIR MODAL DE CREAR
// ============================================
function abrirModalCrear() {
    console.log('Abriendo modal de crear');
    
    // Limpiar backdrops residuales
    limpiarBackdrops();
    
    // Limpiar formulario
    const inputNombre = document.getElementById('crearNombre');
    if (inputNombre) inputNombre.value = '';
    
    // Abrir modal MANUALMENTE
    const modalElement = document.getElementById('modalCrear');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    } else {
        console.error('Modal crear no encontrado');
        mostrarToast('Error: No se encontró el modal de creación', 'error');
    }
}

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Inicializando edificios.js');
    
    // Verificar elementos del DOM
    const contenedor = document.getElementById('contenedorTarjetas');
    console.log('Contenedor encontrado:', contenedor);
    
    const modalCrear = document.getElementById('modalCrear');
    const modalEditar = document.getElementById('modalEditar');
    const formCrear = document.getElementById('formCrear');
    const formEditar = document.getElementById('formEditar');
    
    console.log('modalCrear:', modalCrear ? 'OK' : 'NO ENCONTRADO');
    console.log('modalEditar:', modalEditar ? 'OK' : 'NO ENCONTRADO');
    console.log('formCrear:', formCrear ? 'OK' : 'NO ENCONTRADO');
    console.log('formEditar:', formEditar ? 'OK' : 'NO ENCONTRADO');
    
    // Configurar botón de crear (si existe por defecto)
    const btnCrear = document.querySelector('[data-bs-target="#modalCrear"]') || 
                     document.getElementById('btnCrearEdificio');
    
    if (btnCrear) {
        console.log('Botón crear encontrado, configurando...');
        btnCrear.removeAttribute('data-bs-toggle');
        btnCrear.removeAttribute('data-bs-target');
        btnCrear.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalCrear();
        });
    } else {
        console.log('No se encontró botón crear por defecto, buscando en toolbar...');
        // Buscar en el toolbar o crear uno nuevo
        const toolbar = document.querySelector('.d-flex.justify-content-between');
        if (toolbar) {
            const nuevoBoton = document.createElement('button');
            nuevoBoton.className = 'btn btn-success';
            nuevoBoton.innerHTML = '<i class="bi bi-plus-circle"></i> Crear edificio';
            nuevoBoton.addEventListener('click', abrirModalCrear);
            toolbar.appendChild(nuevoBoton);
            console.log('Botón crear añadido manualmente');
        }
    }
    
    // ============================================
    // FORMULARIO CREAR EDIFICIO
    // ============================================
    if (formCrear) {
        console.log('✅ Formulario crear encontrado');
        
        // Remover event listeners anteriores
        const nuevoFormCrear = formCrear.cloneNode(true);
        formCrear.parentNode.replaceChild(nuevoFormCrear, formCrear);
        
        nuevoFormCrear.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Submit formulario crear');
            
            const nombreInput = document.getElementById('crearNombre');
            if (!nombreInput) {
                console.error('Input nombre no encontrado');
                mostrarToast('Error en el formulario', 'error');
                return;
            }
            
            const nombre = nombreInput.value.trim();
            console.log('Nombre ingresado:', nombre);
            
            if (!nombre) {
                mostrarToast('El nombre del edificio es obligatorio', 'warning');
                return;
            }

            const submitBtn = nuevoFormCrear.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : 'Crear';
            
            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creando...';
                }
                
                console.log('Enviando POST a:', `${API_BASE}/API/edificios`);
                console.log('Datos:', { nombre_edificio: nombre });

                const res = await fetch(`${API_BASE}/API/edificios`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ nombre_edificio: nombre })
                });

                console.log('Respuesta status:', res.status);
                
                let data;
                let mensajeError;
                
                try {
                    data = await res.json();
                    console.log('Respuesta data:', data);
                } catch {
                    mensajeError = 'Error al procesar la respuesta del servidor';
                    throw new Error(mensajeError);
                }

                if (!res.ok) {
                    mensajeError = data.message || data.error || `Error ${res.status}`;
                    
                    // Personalizar mensajes comunes
                    if (mensajeError.includes('Duplicate') || mensajeError.includes('duplicado')) {
                        mensajeError = `Ya existe un edificio con el nombre "${nombre}"`;
                    }
                    
                    throw new Error(mensajeError);
                }

                console.log('✅ Edificio creado correctamente');
                mostrarToast('Edificio creado correctamente', 'success');
                
                // Cerrar modal
                const modalElement = document.getElementById('modalCrear');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                
                // Limpiar backdrop
                limpiarBackdrops();
                
                // Limpiar formulario
                nombreInput.value = '';
                
                // Recargar edificios
                await cargarEdificios();
                
            } catch (err) {
                console.error('❌ Error al crear:', err);
                mostrarToast(err.message, 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        });
        
        console.log('✅ Event listener del formulario crear configurado');
    } else {
        console.error('❌ No se encontró el formulario de crear');
    }

    // ============================================
    // FORMULARIO EDITAR EDIFICIO
    // ============================================
    if (formEditar) {
        console.log('✅ Formulario editar encontrado');
        
        // Remover event listeners anteriores
        const nuevoFormEditar = formEditar.cloneNode(true);
        formEditar.parentNode.replaceChild(nuevoFormEditar, formEditar);
        
        nuevoFormEditar.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Submit formulario editar');

            const idInput = document.getElementById('editId');
            const nombreInput = document.getElementById('editNombre');
            
            if (!idInput || !nombreInput) {
                console.error('Inputs no encontrados');
                mostrarToast('Error en el formulario', 'error');
                return;
            }
            
            const id = idInput.value;
            const nombre = nombreInput.value.trim();
            
            console.log('ID:', id, 'Nombre:', nombre);
            
            if (!nombre) {
                mostrarToast('El nombre del edificio es obligatorio', 'warning');
                return;
            }

            const submitBtn = nuevoFormEditar.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : 'Actualizar';

            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Actualizando...';
                }
                
                console.log('Enviando PUT a:', `${API_BASE}/API/edificios/${id}`);
                console.log('Datos:', { nombre_edificio: nombre });

                const res = await fetch(`${API_BASE}/API/edificios/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ nombre_edificio: nombre })
                });

                console.log('Respuesta status:', res.status);
                
                let data;
                let mensajeError;
                
                try {
                    data = await res.json();
                    console.log('Respuesta data:', data);
                } catch {
                    mensajeError = 'Error al procesar la respuesta del servidor';
                    throw new Error(mensajeError);
                }

                if (!res.ok) {
                    mensajeError = data.message || data.error || `Error ${res.status}`;
                    
                    // Personalizar mensajes comunes
                    if (mensajeError.includes('Duplicate') || mensajeError.includes('duplicado')) {
                        mensajeError = `Ya existe un edificio con el nombre "${nombre}"`;
                    } else if (mensajeError.includes('not found') || mensajeError.includes('no encontrado')) {
                        mensajeError = 'El edificio que intentas actualizar no existe';
                    }
                    
                    throw new Error(mensajeError);
                }

                console.log('✅ Edificio actualizado correctamente');
                mostrarToast('Edificio actualizado correctamente', 'success');
                
                // Cerrar modal
                const modalElement = document.getElementById('modalEditar');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                
                // Limpiar backdrop
                limpiarBackdrops();
                
                // Recargar edificios
                await cargarEdificios();
                
            } catch (err) {
                console.error('❌ Error al actualizar:', err);
                mostrarToast(err.message, 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        });
        
        console.log('✅ Event listener del formulario editar configurado');
    } else {
        console.error('❌ No se encontró el formulario de editar');
    }

    // ============================================
    // LIMPIAR MODALES AL CERRAR
    // ============================================
    const modales = ['modalCrear', 'modalEditar'];
    modales.forEach(id => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                console.log('Modal', id, 'cerrado, limpiando...');
                const form = this.querySelector('form');
                if (form) {
                    form.reset();
                    if (id === 'modalEditar') {
                        const editId = document.getElementById('editId');
                        if (editId) editId.value = '';
                    }
                }
                limpiarBackdrops();
            });
        }
    });

    // ============================================
    // INICIAR CARGA
    // ============================================
    console.log('Iniciando carga de edificios...');
    setTimeout(() => {
        cargarEdificios();
    }, 500);
});

// Hacer funciones globales
window.cargarEdificios = cargarEdificios;
window.abrirModalCrear = abrirModalCrear;
window.mostrarToast = mostrarToast;