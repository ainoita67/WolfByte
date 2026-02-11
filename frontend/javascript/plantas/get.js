// plantas/get.js
const API_BASE = localStorage.getItem('API_BASE') || 'http://192.168.13.202:80';
const API_PLANTAS = `${API_BASE}/API/plantas`;

console.log('API Plantas URL:', API_PLANTAS);

// Obtener lista de edificios (para mapear nombres a IDs)
async function obtenerIdEdificio(nombreEdificio) {
    const edificios = {
        'Loscos': 1,
        'Ram': 2
        // Ajusta estos IDs según tu base de datos
    };
    return edificios[nombreEdificio] || 1;
}

// Obtener nombre del edificio por ID
function obtenerNombreEdificio(idEdificio) {
    const edificios = {
        1: 'Loscos',
        2: 'Ram'
    };
    return edificios[idEdificio] || 'Edificio ' + idEdificio;
}

function obtenerPlantas() {
    console.log("Obteniendo plantas desde:", API_PLANTAS);
    
    const contenedor = document.getElementById("plantasContainer");
    if (!contenedor) {
        console.error("No se encontró el contenedor plantasContainer");
        return;
    }
    
    contenedor.innerHTML = `
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando plantas...</p>
        </div>
    `;
    
    fetch(API_PLANTAS)
        .then(res => {
            if (!res.ok) throw new Error(`Error HTTP: ${res.status}`);
            return res.json();
        })
        .then(response => {
            console.log("Respuesta de API:", response);
            
            // La API devuelve directamente el array de plantas
            const plantas = Array.isArray(response) ? response : [];
            
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
                card.className = "col-12 col-md-6 col-lg-4";
                
                // Usar los campos que vienen de tu API
                const numeroPlanta = planta.numero_planta || 'N/A';
                const idEdificio = planta.id_edificio;
                const nombreEdificio = planta.nombre_edificio || obtenerNombreEdificio(idEdificio);
                const totalEspacios = planta.total_espacios || 0;
                
                card.innerHTML = `
                    <div class="card text-center h-100">
                        <div class="card-head rounded-top py-3">
                            <h6 class="text-light m-0">
                                <i class="bi bi-layers"></i> Planta ${numeroPlanta}
                            </h6>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-building"></i> ${nombreEdificio}
                            </h5>
                            <p class="mb-1">
                                <i class="bi bi-door-open"></i> ID Edificio: 
                                <span class="badge bg-secondary">${idEdificio}</span>
                            </p>
                            <p class="mb-1">
                                <i class="bi bi-grid"></i> Espacios: 
                                <span class="badge bg-primary">${totalEspacios}</span>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-sm btn-warning me-2"
                                    onclick="cargarDatosPlantaParaEditar(${idEdificio}, '${numeroPlanta}')">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger"
                                    onclick="eliminarPlanta(${idEdificio}, '${numeroPlanta}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                contenedor.appendChild(card);
            });
            
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        })
        .catch(err => {
            console.error("Error al obtener plantas:", err);
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                    <h5 class="mt-3 text-danger">Error al cargar las plantas</h5>
                    <p class="text-muted">${err.message}</p>
                    <button class="btn btn-primary mt-2" onclick="obtenerPlantas()">
                        <i class="bi bi-arrow-clockwise"></i> Reintentar
                    </button>
                </div>
            `;
        });
}

function cargarDatosPlantaParaEditar(idEdificio, numeroPlanta) {
    console.log(`Cargando datos - Edificio: ${idEdificio}, Planta: ${numeroPlanta}`);
    
    const modalBody = document.getElementById("modalEditarBody");
    if (!modalBody) return;
    
    modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando datos de la planta...</p>
        </div>
    `;
    
    const modalElement = document.getElementById('modalEditar');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
    
    // Llamar al endpoint de detalles de tu API
    fetch(`${API_PLANTAS}/${idEdificio}/detalles?numero_planta=${numeroPlanta}`)
        .then(res => {
            if (!res.ok) throw new Error(`Error HTTP: ${res.status}`);
            return res.json();
        })
        .then(planta => {
            console.log("Datos de la planta:", planta);
            
            modalBody.innerHTML = `
                <form id="formEditarPlanta">
                    <input type="hidden" id="edificioIdHidden" value="${idEdificio}">
                    <input type="hidden" id="numeroPlantaOriginal" value="${numeroPlanta}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Edificio</label>
                        <select class="form-select" id="edificioSelect" required>
                            <option value="">Seleccionar edificio</option>
                            <option value="1">Loscos</option>
                            <option value="2">Ram</option>
                        </select>
                        <small class="text-muted">No se puede cambiar el edificio, solo la planta</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Número de Planta</label>
                        <input type="number" class="form-control" id="numeroPlantaInput" 
                               value="${planta.numero_planta}" required>
                        <div class="form-text">Ingresa el nuevo número de planta</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Información adicional</label>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-1"><strong>ID Edificio:</strong> ${planta.id_edificio}</p>
                            <p class="mb-1"><strong>Edificio:</strong> ${planta.nombre_edificio}</p>
                            <p class="mb-1"><strong>Total recursos:</strong> ${planta.total_recursos || 0}</p>
                            <p class="mb-1"><strong>Total espacios:</strong> ${planta.total_espacios || 0}</p>
                            <p class="mb-0"><strong>Total materiales:</strong> ${planta.total_materiales || 0}</p>
                        </div>
                    </div>
                </form>
                <div class="modal-footer px-0 pb-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="guardarCambiosPlanta(${idEdificio}, '${numeroPlanta}')">
                        <i class="bi bi-check-lg"></i> Guardar cambios
                    </button>
                </div>
            `;
            
            // Asignar valores después de actualizar el DOM
            setTimeout(() => {
                const edificioSelect = document.getElementById("edificioSelect");
                if (edificioSelect) {
                    edificioSelect.value = idEdificio.toString();
                    edificioSelect.disabled = true; // No permitir cambiar edificio
                }
            }, 100);
        })
        .catch(err => {
            console.error("Error al obtener detalles:", err);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Error al cargar los datos: ${err.message}
                </div>
                <div class="text-center">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cerrar
                    </button>
                </div>
            `;
        });
}

// Inicializar
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM cargado, obteniendo plantas...");
    setTimeout(obtenerPlantas, 300);
});