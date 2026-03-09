// javascript/incidencias/misincidencias.js
console.log('misincidencias.js cargado');

// Función para obtener incidencias del usuario actual
function obtenerMisIncidencias() {
    console.log('obtenerMisIncidencias llamado');
    
    // Obtener ID del usuario de sessionStorage
    const usuario = sessionStorage.getItem("id_usuario");
    console.log('ID Usuario:', usuario);
    
    if (!usuario) {
        console.error('No hay ID de usuario');
        mostrarMensajeError('Usuario no identificado');
        return;
    }

    console.log('ID Usuario:', usuario);
// AÑADE ESTO:
console.log('Token:', localStorage.getItem('token') ? 'Presente' : 'No presente');

// Y modifica el fetch para ver la respuesta exacta:
fetch(window.location.origin + "/API/incidencias/usuario/" + usuario, {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
})
.then(res => {
    console.log('Status:', res.status);
    console.log('Status text:', res.statusText);
    return res.text(); // Cambia a text() para ver la respuesta cruda
})
.then(text => {
    console.log('Respuesta cruda:', text);
    try {
        const data = JSON.parse(text);
        console.log('JSON parseado:', data);
        // Resto del código...
    } catch(e) {
        console.error('No es JSON válido:', e);
    }
})

    // Mostrar loading
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

    // Llamar a la API
    fetch(window.location.origin + "/API/incidencias/usuario/" + usuario, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
    })
    .then(res => {
        console.log('Status respuesta:', res.status);
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        return res.json();
    })
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

// Función para mostrar incidencias en tarjetas
function mostrarIncidencias(incidencias) {
    const contenedor = document.getElementById('tarjetasIncidencias');
    if (!contenedor) return;

    contenedor.innerHTML = '';

    incidencias.forEach(incidencia => {
        const fecha = formatearFecha(incidencia.fecha);
        const prioridadClass = getPrioridadClass(incidencia.prioridad);
        const estadoClass = getEstadoClass(incidencia.estado);

        const col = document.createElement('div');
        col.className = 'col-lg-3 col-6';
        
        const card = document.createElement('div');
        card.className = 'card h-100 shadow-sm incidencia-card';
        card.setAttribute('data-id', incidencia.id_incidencia);
        card.setAttribute('role', 'button');
        card.setAttribute('data-bs-toggle', 'modal');
        card.setAttribute('data-bs-target', '#modalincidencia');
        
        card.innerHTML = `
            <div class="card-body bg-secondary-subtle">
                <p class="fw-bold mb-0">Incidencia #${incidencia.id_incidencia}</p>
                <p class="fw-bold mb-0">${incidencia.titulo}</p>
                <p class="mb-0"><span class="fw-bold">Fecha: </span>${fecha}</p>
                <p class="mb-0"><span class="fw-bold">Prioridad: </span>${capitalizar(incidencia.prioridad)}</p>
                <p class="mb-0"><span class="fw-bold">Estado: </span>${capitalizar(incidencia.estado)}</p>
                <p class="mb-0"><span class="fw-bold">Recurso: </span>${incidencia.id_recurso}</p>
            </div>
        `;
        
        // Añadir indicador de color según estado
        if (incidencia.estado === "Abierta") {
            card.innerHTML += '<div class="estado-incidencia abierta"></div>';
        } else if (incidencia.estado === "Resuelta") {
            card.innerHTML += '<div class="estado-incidencia resuelta"></div>';
        } else {
            card.innerHTML += '<div class="estado-incidencia proceso"></div>';
        }

        // Evento para abrir modal con detalles
        card.addEventListener('click', function() {
            cargarDetalleIncidencia(incidencia);
        });

        col.appendChild(card);
        contenedor.appendChild(col);
    });
}

// Función para cargar detalles en el modal
function cargarDetalleIncidencia(incidencia) {
    // Obtener datos del usuario que creó la incidencia
    fetch(window.location.origin + "/API/user/" + incidencia.id_usuario)
    .then(res => res.json())
    .then(response => {
        const usuario = response.data || { nombre: 'Desconocido' };
        
        document.getElementById("incidencia_id").value = incidencia.id_incidencia;
        document.getElementById("incidencia_titulo").value = incidencia.titulo;
        document.getElementById("incidencia_descripcion").value = incidencia.descripcion || '';
        document.getElementById("incidencia_estado").value = capitalizar(incidencia.estado);
        document.getElementById("incidencia_prioridad").value = capitalizar(incidencia.prioridad);
        document.getElementById("incidencia_id_usuario").value = incidencia.id_usuario;
        document.getElementById("incidencia_usuario").value = usuario.nombre || 'Desconocido';
        document.getElementById("incidencia_recurso").value = incidencia.id_recurso;
        
        // Formatear fecha para input datetime-local
        const fechaObj = new Date(incidencia.fecha);
        const fechaFormateada = fechaObj.toISOString().slice(0, 16);
        document.getElementById("incidencia_fecha").value = fechaFormateada;
        
        // Configurar botones según estado
        configurarBotonesModal(incidencia);
    })
    .catch(error => {
        console.error('Error al cargar usuario:', error);
        alert('Error al cargar detalles de la incidencia');
    });
}

// Función para configurar botones del modal
function configurarBotonesModal(incidencia) {
    const btnGuardar = document.getElementById('btnGuardarCambios');
    
    // Si la incidencia está resuelta, no permitir edición
    if (incidencia.estado === 'Resuelta') {
        document.getElementById('incidencia_titulo').readOnly = true;
        document.getElementById('incidencia_descripcion').readOnly = true;
        if (btnGuardar) btnGuardar.classList.add('d-none');
    } else {
        document.getElementById('incidencia_titulo').readOnly = false;
        document.getElementById('incidencia_descripcion').readOnly = false;
        if (btnGuardar) btnGuardar.classList.remove('d-none');
    }
}

// Función para guardar cambios
async function guardarCambiosIncidencia(event) {
    event.preventDefault();
    
    const id = document.getElementById('incidencia_id').value;
    const titulo = document.getElementById('incidencia_titulo').value;
    const descripcion = document.getElementById('incidencia_descripcion').value;
    
    if (!titulo.trim()) {
        alert('El título no puede estar vacío');
        return;
    }
    
    try {
        const response = await fetch(`${API}/incidencias/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: JSON.stringify({
                titulo: titulo,
                descripcion: descripcion
            })
        });

        const data = await response.json();
        
        if (response.ok && data.status === 'success') {
            alert('Incidencia actualizada correctamente');
            bootstrap.Modal.getInstance(document.getElementById('modalincidencia')).hide();
            obtenerMisIncidencias(); // Recargar lista
        } else {
            alert('Error al actualizar: ' + (data.message || 'Error desconocido'));
        }

    } catch (error) {
        console.error('Error:', error);
        alert('Error al conectar con el servidor');
    }
}

// Funciones auxiliares
function getPrioridadClass(prioridad) {
    switch(prioridad?.toLowerCase()) {
        case 'alta': return 'bg-danger';
        case 'media': return 'bg-warning';
        case 'baja': return 'bg-success';
        default: return 'bg-secondary';
    }
}

function getEstadoClass(estado) {
    switch(estado?.toLowerCase()) {
        case 'abierta': return 'bg-primary';
        case 'en proceso': return 'bg-warning text-dark';
        case 'resuelta': return 'bg-success';
        default: return 'bg-secondary';
    }
}

function formatearFecha(stringFecha) {
    if (!stringFecha) return '';
    let fecha = new Date(stringFecha);
    let anyo = fecha.getFullYear();
    let mes = String(fecha.getMonth() + 1).padStart(2, '0');
    let dia = String(fecha.getDate()).padStart(2, '0');
    let hh = String(fecha.getHours()).padStart(2, '0');
    let mm = String(fecha.getMinutes()).padStart(2, '0');
    return `${dia}/${mes}/${anyo} ${hh}:${mm}`;
}

function capitalizar(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

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

function mostrarMensajeError(mensaje = 'Error al cargar las incidencias') {
    const contenedor = document.getElementById('tarjetasIncidencias');
    if (contenedor) {
        contenedor.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                <p class="mt-3 fs-5 text-danger">${mensaje}</p>
                <button class="btn btn-primary mt-3" onclick="obtenerMisIncidencias()">
                    <i class="bi bi-arrow-clockwise"></i> Reintentar
                </button>
            </div>
        `;
    }
}

// Hacer funciones globales
window.obtenerMisIncidencias = obtenerMisIncidencias;
window.guardarCambiosIncidencia = guardarCambiosIncidencia;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, esperando 500ms para obtener incidencias...');
    setTimeout(obtenerMisIncidencias, 500);
});