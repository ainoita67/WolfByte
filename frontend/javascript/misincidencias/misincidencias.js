// javascript/misincidencias/misincidencias.js
console.log('misincidencias.js cargado');

// Variable global para almacenar la incidencia seleccionada
let incidenciaSeleccionada = null;

// ============================================
// FUNCIÓN PARA MOSTRAR TOASTS
// ============================================
function mostrarToast(mensaje, tipo = 'success') {
    console.log('Toast:', mensaje, tipo);
    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    
    let bgClass = 'bg-success';
    if (tipo === 'error') bgClass = 'bg-danger';
    else if (tipo === 'warning') bgClass = 'bg-warning';
    else if (tipo === 'info') bgClass = 'bg-info';
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">${mensaje}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();
}

// ============================================
// OBTENER INCIDENCIAS DEL USUARIO
// ============================================
function obtenerMisIncidencias() {
    console.log('obtenerMisIncidencias llamado');
    
    let usuario = null;
    try {
        const token = localStorage.getItem('token');
        if (token) {
            const payload = JSON.parse(atob(token.split('.')[1]));
            usuario = payload.sub;
            console.log('ID del token (sub):', usuario);
            
            if (usuario) {
                sessionStorage.setItem("id_usuario", usuario);
            }
        }
    } catch (e) {
        console.error('Error al leer token:', e);
    }
    
    if (!usuario) {
        console.error('No hay ID de usuario');
        mostrarMensajeVacio();
        return;
    }

    const contenedor = document.getElementById('tarjetasIncidencias');
    if (contenedor) {
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3">Cargando tus incidencias...</p>
            </div>
        `;
    }

    const API_BASE = window.location.origin;
    
    fetch(`${API_BASE}/API/incidencias/usuario/${usuario}`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
    })
    .then(res => res.json())
    .then(response => {
        console.log('Datos recibidos:', response);
        
        if (response.status === 'success') {
            if (response.data && response.data.length > 0) {
                mostrarIncidencias(response.data);
            } else {
                mostrarMensajeVacio();
            }
        } else {
            mostrarMensajeError('Error en la respuesta del servidor');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensajeError(error.message);
    });
}

// ============================================
// MOSTRAR INCIDENCIAS CON BOTONES EN EL FOOTER
// ============================================
function mostrarIncidencias(incidencias) {
    console.log('Mostrando', incidencias.length, 'incidencias');
    
    const contenedor = document.getElementById('tarjetasIncidencias');
    if (!contenedor) return;

    contenedor.innerHTML = '';

    incidencias.forEach(incidencia => {
        const col = document.createElement('div');
        col.className = 'col-lg-3 col-md-4 col-6 mb-3';
        
        const card = document.createElement('div');
        card.className = 'card h-100 shadow-sm incidencia-card';
        
        // Formatear fecha
        let fechaFormateada = 'Fecha no disponible';
        try {
            fechaFormateada = new Date(incidencia.fecha).toLocaleDateString();
        } catch(e) {
            console.error('Error formateando fecha:', e);
        }
        
        // Determinar clases de badges
        let prioridadClass = 'bg-secondary';
        if (incidencia.prioridad === 'Alta') prioridadClass = 'bg-danger';
        else if (incidencia.prioridad === 'Media') prioridadClass = 'bg-warning';
        else if (incidencia.prioridad === 'Baja') prioridadClass = 'bg-success';
        
        let estadoClass = 'bg-secondary';
        if (incidencia.estado === 'Abierta') estadoClass = 'bg-primary';
        else if (incidencia.estado === 'En proceso') estadoClass = 'bg-warning';
        else if (incidencia.estado === 'Resuelta') estadoClass = 'bg-success';
        
        card.innerHTML = `
            <div class="card-body">
                <h6 class="card-title fw-bold">${incidencia.titulo || 'Sin título'}</h6>
                <p class="card-text small">${incidencia.descripcion ? (incidencia.descripcion.substring(0, 50) + (incidencia.descripcion.length > 50 ? '...' : '')) : 'Sin descripción'}</p>
                <p class="mb-1 small"><strong>Recurso:</strong> ${incidencia.id_recurso || 'N/A'}</p>
                <p class="mb-1 small"><strong>Fecha:</strong> ${fechaFormateada}</p>
                <div class="mt-2">
                    <span class="badge ${prioridadClass} me-1">${incidencia.prioridad || 'Media'}</span>
                    <span class="badge ${estadoClass}">${incidencia.estado || 'Abierta'}</span>
                </div>
            </div>
            <div class="card-footer-actions">
                <button class="btn-footer-action btn-footer-edit" onclick="event.stopPropagation(); editarIncidencia(${incidencia.id_incidencia})">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <button class="btn-footer-action btn-footer-delete" onclick="event.stopPropagation(); confirmarEliminarIncidencia(${incidencia.id_incidencia}, '${incidencia.titulo.replace(/'/g, "\\'")}')">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        `;

        // Hacer toda la tarjeta clickeable para ver detalles (excepto los botones)
        card.addEventListener('click', function(e) {
            // Si el click no fue en un botón, abrir vista de detalles
            if (!e.target.closest('button')) {
                verIncidencia(incidencia);
            }
        });
        
        col.appendChild(card);
        contenedor.appendChild(col);
    });
}

// ============================================
// VER INCIDENCIA (solo lectura)
// ============================================
function verIncidencia(incidencia) {
    console.log('Ver incidencia:', incidencia.id_incidencia);
    
    document.getElementById('incidencia_id').value = incidencia.id_incidencia || '';
    document.getElementById('incidencia_id_display').value = incidencia.id_incidencia || '';
    document.getElementById('incidencia_titulo').value = incidencia.titulo || '';
    document.getElementById('incidencia_descripcion').value = incidencia.descripcion || '';
    document.getElementById('incidencia_estado').value = incidencia.estado || '';
    document.getElementById('incidencia_prioridad').value = incidencia.prioridad || '';
    document.getElementById('incidencia_id_usuario').value = incidencia.id_usuario || '';
    document.getElementById('incidencia_usuario').value = 'Usuario ID: ' + (incidencia.id_usuario || '');
    document.getElementById('incidencia_recurso').value = incidencia.id_recurso || '';
    
    // Formatear fecha
    if (incidencia.fecha) {
        try {
            const fecha = new Date(incidencia.fecha);
            if (!isNaN(fecha.getTime())) {
                const anyo = fecha.getFullYear();
                const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                const dia = String(fecha.getDate()).padStart(2, '0');
                const hh = String(fecha.getHours()).padStart(2, '0');
                const mm = String(fecha.getMinutes()).padStart(2, '0');
                const fechaFormateada = `${anyo}-${mes}-${dia}T${hh}:${mm}`;
                document.getElementById('incidencia_fecha').value = fechaFormateada;
            }
        } catch(e) {
            console.error('Error formateando fecha:', e);
        }
    }
    
    // Configurar modo solo lectura
    document.getElementById('incidencia_titulo').readOnly = true;
    document.getElementById('incidencia_descripcion').readOnly = true;
    document.getElementById('incidencia_estado').disabled = true;
    document.getElementById('incidencia_prioridad').disabled = true;
    document.getElementById('btnGuardarCambios').classList.add('d-none');
    document.getElementById('btnEliminarModal').classList.remove('d-none');
    
    incidenciaSeleccionada = incidencia;
    
    const modal = new bootstrap.Modal(document.getElementById('modalincidencia'));
    modal.show();
}

// ============================================
// EDITAR INCIDENCIA
// ============================================
function editarIncidencia(id) {
    console.log('Editar incidencia:', id);
    
    // Obtener datos completos de la incidencia
    fetch(`${window.location.origin}/API/incidencias/${id}`, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === 'success' && response.data) {
            const incidencia = response.data;
            
            document.getElementById('incidencia_id').value = incidencia.id_incidencia || '';
            document.getElementById('incidencia_id_display').value = incidencia.id_incidencia || '';
            document.getElementById('incidencia_titulo').value = incidencia.titulo || '';
            document.getElementById('incidencia_descripcion').value = incidencia.descripcion || '';
            document.getElementById('incidencia_estado').value = incidencia.estado || '';
            document.getElementById('incidencia_prioridad').value = incidencia.prioridad || '';
            document.getElementById('incidencia_id_usuario').value = incidencia.id_usuario || '';
            document.getElementById('incidencia_usuario').value = 'Usuario ID: ' + (incidencia.id_usuario || '');
            document.getElementById('incidencia_recurso').value = incidencia.id_recurso || '';
            
            // Formatear fecha
            if (incidencia.fecha) {
                try {
                    const fecha = new Date(incidencia.fecha);
                    if (!isNaN(fecha.getTime())) {
                        const anyo = fecha.getFullYear();
                        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                        const dia = String(fecha.getDate()).padStart(2, '0');
                        const hh = String(fecha.getHours()).padStart(2, '0');
                        const mm = String(fecha.getMinutes()).padStart(2, '0');
                        const fechaFormateada = `${anyo}-${mes}-${dia}T${hh}:${mm}`;
                        document.getElementById('incidencia_fecha').value = fechaFormateada;
                    }
                } catch(e) {
                    console.error('Error formateando fecha:', e);
                }
            }
            
            // Configurar modo edición
            document.getElementById('incidencia_titulo').readOnly = false;
            document.getElementById('incidencia_descripcion').readOnly = false;
            document.getElementById('incidencia_estado').disabled = false;
            document.getElementById('incidencia_prioridad').disabled = false;
            document.getElementById('btnGuardarCambios').classList.remove('d-none');
            document.getElementById('btnEliminarModal').classList.add('d-none');
            
            incidenciaSeleccionada = incidencia;
            
            const modal = new bootstrap.Modal(document.getElementById('modalincidencia'));
            modal.show();
        } else {
            mostrarToast('Error al cargar la incidencia', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error de conexión', 'error');
    });
}

// ============================================
// CONFIRMAR ELIMINACIÓN
// ============================================
function confirmarEliminarIncidencia(id, titulo) {
    console.log('Confirmar eliminar incidencia:', id);
    
    document.getElementById('incidenciaTituloEliminar').textContent = titulo;
    incidenciaSeleccionada = { id_incidencia: id };
    
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    modal.show();
}

// ============================================
// ELIMINAR INCIDENCIA
// ============================================
function eliminarIncidencia() {
    if (!incidenciaSeleccionada || !incidenciaSeleccionada.id_incidencia) {
        mostrarToast('No se ha seleccionado ninguna incidencia', 'error');
        return;
    }
    
    const id = incidenciaSeleccionada.id_incidencia;
    console.log('Eliminando incidencia:', id);
    
    fetch(`${window.location.origin}/API/incidencias/${id}`, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('Respuesta eliminación:', data);
        
        if (data.status === 'success') {
            mostrarToast('Incidencia eliminada correctamente', 'success');
            
            // Cerrar modales
            bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar')).hide();
            const modalIncidencia = bootstrap.Modal.getInstance(document.getElementById('modalincidencia'));
            if (modalIncidencia) modalIncidencia.hide();
            
            // Recargar lista
            obtenerMisIncidencias();
        } else {
            mostrarToast(data.message || 'Error al eliminar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error de conexión', 'error');
    });
}

// ============================================
// GUARDAR CAMBIOS
// ============================================
function guardarCambiosIncidencia(event) {
    event.preventDefault();
    console.log('Guardando cambios...');
    
    const id = document.getElementById('incidencia_id').value;
    const titulo = document.getElementById('incidencia_titulo').value;
    const descripcion = document.getElementById('incidencia_descripcion').value;
    const estado = document.getElementById('incidencia_estado').value;
    const prioridad = document.getElementById('incidencia_prioridad').value;
    
    if (!titulo || !titulo.trim()) {
        mostrarToast('El título no puede estar vacío', 'warning');
        return;
    }
    
    const token = localStorage.getItem('token');
    if (!token) {
        mostrarToast('Sesión no válida', 'error');
        return;
    }
    
    fetch(`${window.location.origin}/API/incidencias/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ 
            titulo: titulo.trim(), 
            descripcion: descripcion || '',
            estado: estado,
            prioridad: prioridad
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Respuesta:', data);
        
        if (data.status === 'success') {
            mostrarToast('Incidencia actualizada correctamente', 'success');
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalincidencia'));
            if (modal) modal.hide();
            
            obtenerMisIncidencias();
        } else {
            mostrarToast(data.message || 'Error al actualizar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error de conexión', 'error');
    });
}

// ============================================
// FUNCIONES DE MENSAJES
// ============================================
function mostrarMensajeVacio() {
    const contenedor = document.getElementById('tarjetasIncidencias');
    if (contenedor) {
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="mt-3 fs-5 text-muted">No tienes incidencias reportadas</p>
                <a href="../incidencias/incidencias.html" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> Crear nueva incidencia
                </a>
            </div>
        `;
    }
}

function mostrarMensajeError(mensaje) {
    const contenedor = document.getElementById('tarjetasIncidencias');
    if (contenedor) {
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                <p class="mt-3 fs-5 text-danger">${mensaje || 'Error al cargar las incidencias'}</p>
                <button class="btn btn-primary mt-3" onclick="obtenerMisIncidencias()">
                    <i class="bi bi-arrow-clockwise"></i> Reintentar
                </button>
            </div>
        `;
    }
}

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Inicializando mis incidencias');
    
    // Configurar formulario
    const form = document.getElementById('formincidencia');
    if (form) {
        const nuevoForm = form.cloneNode(true);
        form.parentNode.replaceChild(nuevoForm, form);
        nuevoForm.addEventListener('submit', guardarCambiosIncidencia);
    }
    
    // Botón eliminar en modal
    const btnEliminar = document.getElementById('btnEliminarModal');
    if (btnEliminar) {
        btnEliminar.addEventListener('click', function() {
            bootstrap.Modal.getInstance(document.getElementById('modalincidencia')).hide();
            setTimeout(() => {
                confirmarEliminarIncidencia(
                    incidenciaSeleccionada?.id_incidencia,
                    incidenciaSeleccionada?.titulo || 'esta incidencia'
                );
            }, 300);
        });
    }
    
    // Botón confirmar eliminar
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', eliminarIncidencia);
    }
    
    // Sincronizar IDs
    const idHidden = document.getElementById('incidencia_id');
    const idDisplay = document.getElementById('incidencia_id_display');
    if (idHidden && idDisplay) {
        idHidden.addEventListener('change', () => {
            idDisplay.value = idHidden.value;
        });
    }
    
    // Cargar incidencias
    setTimeout(obtenerMisIncidencias, 500);
});

// Hacer funciones globales
window.obtenerMisIncidencias = obtenerMisIncidencias;
window.guardarCambiosIncidencia = guardarCambiosIncidencia;
window.mostrarToast = mostrarToast;
window.editarIncidencia = editarIncidencia;
window.confirmarEliminarIncidencia = confirmarEliminarIncidencia;
window.verIncidencia = verIncidencia;