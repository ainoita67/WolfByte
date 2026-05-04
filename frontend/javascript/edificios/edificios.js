import { mostrarToast } from "/frontend/javascript/reservas/crud.js";

// edificios.js

const API_BASE = window.location.origin;

// ============================================
// SISTEMA DE TOASTS CON BOOTSTRAP (IGUAL QUE EN PORTÁTILES)
// ============================================

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
    
    const contenedor = document.getElementById('contenedorTarjetas');
    if (!contenedor) {
        console.error('Contenedor no encontrado');
        return;
    }
    
    contenedor.innerHTML = `
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden text-black">Cargando...</span>
            </div>
            <p class="mt-2">Cargando edificios...</p>
        </div>
    `;
    
    try {
        const res = await fetch(`${API_BASE}/API/edificios`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token}`
            }
        });
        
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const data = await res.json();

        const edificios = data.data || data;

        if (!edificios || edificios.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-building fs-1 text-muted"></i>
                    <p class="text-black text-muted mt-3">No hay edificios registrados</p>
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
                        <p class="text-black card-text">
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
                <p class="text-black text-muted">${err.message}</p>
                <button class="btn btn-primary mt-3" onclick="cargarEdificios()">
                    <i class="bi bi-arrow-clockwise"></i> Reintentar
                </button>
            </div>
        `;
        mostrarToast('Error al cargar edificios: ' + err.message, 'danger');
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
        mostrarToast('Error: No se ha encontrado el modal de creación', 'danger');
    }
}

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Verificar elementos del DOM
    const contenedor = document.getElementById('contenedorTarjetas');
    
    const modalCrear = document.getElementById('modalCrear');
    const modalEditar = document.getElementById('modalEditar');
    const formCrear = document.getElementById('formCrear');
    const formEditar = document.getElementById('formEditar');
    
    // Configurar botón de crear (si existe por defecto)
    const btnCrear = document.querySelector('[data-bs-target="#modalCrear"]') || 
                     document.getElementById('btnCrearEdificio');
    
    if (btnCrear) {
        btnCrear.removeAttribute('data-bs-toggle');
        btnCrear.removeAttribute('data-bs-target');
        btnCrear.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalCrear();
        });
    } else {
        // Buscar en el toolbar o crear uno nuevo
        const toolbar = document.querySelector('.d-flex.justify-content-between');
        if (toolbar) {
            const nuevoBoton = document.createElement('button');
            nuevoBoton.className = 'btn btn-success';
            nuevoBoton.innerHTML = '<i class="bi bi-plus-circle"></i> Crear edificio';
            nuevoBoton.addEventListener('click', abrirModalCrear);
            toolbar.appendChild(nuevoBoton);
        }
    }
    
    // ============================================
    // FORMULARIO CREAR EDIFICIO
    // ============================================
    if (formCrear) {
        
        // Remover event listeners anteriores
        const nuevoFormCrear = formCrear.cloneNode(true);
        formCrear.parentNode.replaceChild(nuevoFormCrear, formCrear);
        
        nuevoFormCrear.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            
            const nombreInput = document.getElementById('crearNombre');
            if (!nombreInput) {
                console.error('Input nombre no encontrado');
                mostrarToast('Error en el formulario', 'danger');
                return;
            }
            
            const nombre = nombreInput.value.trim();
            
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
                

                const res = await fetch(`${API_BASE}/API/edificios`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${token}`
                    },
                    body: JSON.stringify({ nombre_edificio: nombre })
                });

                
                let data;
                let mensajeError;
                
                try {
                    data = await res.json();
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
                console.error('Error al crear:', err);
                mostrarToast('Error al crear el edificio', 'danger');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        });
        
    } else {
        console.error('No se ha encontrado el formulario de crear');
    }

    // ============================================
    // FORMULARIO EDITAR EDIFICIO
    // ============================================
    if (formEditar) {
        
        // Remover event listeners anteriores
        const nuevoFormEditar = formEditar.cloneNode(true);
        formEditar.parentNode.replaceChild(nuevoFormEditar, formEditar);
        
        nuevoFormEditar.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            

            const idInput = document.getElementById('editId');
            const nombreInput = document.getElementById('editNombre');
            
            if (!idInput || !nombreInput) {
                console.error('Inputs no encontrados');
                mostrarToast('Error en el formulario', 'danger');
                return;
            }
            
            const id = idInput.value;
            const nombre = nombreInput.value.trim();
            
            
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
                

                const res = await fetch(`${API_BASE}/API/edificios/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${token}`
                    },
                    body: JSON.stringify({ nombre_edificio: nombre })
                });

                
                let data;
                let mensajeError;
                
                try {
                    data = await res.json();

                    if(data.data.message.includes('existe')){
                        mostrarToast('El edificio con ese nombre ya existe', 'warning');
                    }
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
                
                if(data.data.status === "no_changes"){
                    mostrarToast('No han habido cambios', 'warning');
                }else{
                    mostrarToast('Edificio actualizado correctamente', 'success');
                }
                
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
                mostrarToast('Error al actualizar el edificio', 'danger');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        });
        
    } else {
        console.error('No se ha encontrado el formulario de editar');
    }

    // ============================================
    // LIMPIAR MODALES AL CERRAR
    // ============================================
    const modales = ['modalCrear', 'modalEditar'];
    modales.forEach(id => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
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
    setTimeout(() => {
        cargarEdificios();
    }, 500);
});

// Hacer funciones globales
window.cargarEdificios = cargarEdificios;
window.abrirModalCrear = abrirModalCrear;
window.mostrarToast = mostrarToast;