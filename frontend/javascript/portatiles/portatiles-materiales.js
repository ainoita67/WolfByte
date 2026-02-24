// portatiles-materiales.js
const API_BASE = 'http://192.168.13.202:83';
const API_PORTATILES_MATERIALES = `${API_BASE}/API/portatiles/materiales`;
const API_EDIFICIOS = `${API_BASE}/API/edificios`;

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

// Función para obtener los edificios de la API
async function cargarEdificios() {
    try {
        console.log('Cargando edificios desde:', API_EDIFICIOS);
        const response = await fetch(API_EDIFICIOS);
        if (!response.ok) throw new Error('Error al cargar edificios');
        const respuesta = await response.json();
        
        let edificiosArray = [];
        if (Array.isArray(respuesta)) {
            edificiosArray = respuesta;
        } else if (respuesta.data && Array.isArray(respuesta.data)) {
            edificiosArray = respuesta.data;
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
        return {};
    }
}

function obtenerNombreEdificio(idEdificio) {
    return edificios[idEdificio] || 'Edificio ' + idEdificio;
}

function mostrarToast(mensaje, tipo = 'exito') {
    alert(mensaje);
}

// Función principal para obtener materiales
async function obtenerMateriales() {
    console.log('Ejecutando obtenerMateriales');
    console.log('URL completa:', API_PORTATILES_MATERIALES);
    
    const contenedor = document.getElementById("portatilesContainer");
    if (!contenedor) {
        console.error('ERROR: No se encontró el elemento portatilesContainer');
        return;
    }
    
    // Mostrar loading
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
        console.log('Respuesta completa:', respuesta);
        
        // La API devuelve directamente el array (según lo que viste en Postman)
        const materiales = Array.isArray(respuesta) ? respuesta : [];
        
        console.log('Materiales procesados:', materiales);
        console.log('Número de materiales:', materiales.length);
        
        // Limpiar contenedor
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
        
        // Mostrar tarjetas
        materiales.forEach(material => {
            console.log('Procesando material:', material);
            
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
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar"
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
                                data-bs-toggle="modal"
                                data-bs-target="#modalEliminar"
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
                <p class="text-muted">${error.message}</p>
                <p class="text-muted">URL: ${API_PORTATILES_MATERIALES}</p>
                <button class="btn btn-primary mt-2" onclick="obtenerMateriales()">
                    <i class="bi bi-arrow-clockwise"></i> Reintentar
                </button>
            </div>
        `;
    }
}

function configurarBotones() {
    document.querySelectorAll(".btn-editar").forEach(boton => {
        boton.addEventListener("click", function() {
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
        });
    });
    
    document.querySelectorAll(".btn-eliminar").forEach(boton => {
        boton.addEventListener("click", function() {
            document.getElementById("deleteId").value = this.dataset.id;
            document.getElementById("deleteCarro").value = this.dataset.carro;
            document.getElementById("deleteEdificio").value = this.dataset.edificio;
            document.getElementById("deletePlanta").value = this.dataset.planta;
            document.getElementById("deleteUnidades").value = this.dataset.unidades;
        });
    });
    
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
            const errorMsg = resultado.error || resultado.message || `Error ${response.status}`;
            throw new Error(errorMsg);
        }
        
        mostrarToast(`Carro ${data.descripcion} creado correctamente`, 'exito');
        return true;
    } catch (error) {
        console.error('Error creando material:', error);
        mostrarToast(error.message, 'error');
        return false;
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
            const errorMsg = resultado.error || resultado.message || `Error ${response.status}`;
            throw new Error(errorMsg);
        }
        
        mostrarToast(`Carro ${data.descripcion} actualizado correctamente`, 'exito');
        return true;
    } catch (error) {
        console.error('Error actualizando material:', error);
        mostrarToast(error.message, 'error');
        return false;
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
        
        if (response.status === 204) {
            mostrarToast('Material eliminado correctamente', 'exito');
            return true;
        }
        
        const resultado = await response.json();
        console.log('Respuesta:', resultado);
        
        if (!response.ok) {
            const errorMsg = resultado.error || resultado.message || `Error ${response.status}`;
            throw new Error(errorMsg);
        }
        
        mostrarToast('Material eliminado correctamente', 'exito');
        return true;
    } catch (error) {
        console.error('Error eliminando material:', error);
        mostrarToast(error.message, 'error');
        return false;
    }
}

// Inicialización cuando el DOM esté listo
ready(function() {
    console.log('✅ DOM cargado - inicializando portatiles-materiales.js');
    
    // Cargar edificios
    cargarEdificios();
    
    // Cargar materiales después de un pequeño retraso
    setTimeout(() => {
        obtenerMateriales();
    }, 500);
    
    // Formulario de creación
    const formCrear = document.querySelector("#modalCrear form");
    if (formCrear) {
        formCrear.addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const id = document.getElementById("crearId")?.value;
            const carro = document.getElementById("crearCarro")?.value;
            const idEdificio = parseInt(document.getElementById("crearEdificio")?.value);
            const planta = document.getElementById("crearPlanta")?.value;
            const unidades = parseInt(document.getElementById("crearUnidades")?.value);
            
            if (!id || !carro || !idEdificio || !planta || !unidades) {
                mostrarToast('Completa todos los campos', 'error');
                return;
            }
            
            if (unidades <= 0) {
                mostrarToast('Las unidades deben ser mayores que 0', 'error');
                return;
            }
            
            // Determinar número de planta
            let numeroPlanta = 0;
            if (planta.includes('baja')) numeroPlanta = 0;
            else if (planta.includes('Primera')) numeroPlanta = 1;
            else if (planta.includes('Segunda')) numeroPlanta = 2;
            
            const submitBtn = formCrear.querySelector("button[type='submit']");
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creando...';
            }
            
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
                const modalElement = document.getElementById("modalCrear");
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
                formCrear.reset();
                obtenerMateriales();
            }
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Crear carro';
            }
        });
    }
    
    // Formulario de edición
    const formEditar = document.querySelector("#modalEditar form");
    if (formEditar) {
        formEditar.addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const id = document.getElementById("editId")?.value;
            const carro = document.getElementById("editCarro")?.value;
            const unidades = parseInt(document.getElementById("editUnidades")?.value);
            const activo = parseInt(document.getElementById("editEstado")?.value);
            
            if (!id || !carro || !unidades) {
                mostrarToast('Completa todos los campos', 'error');
                return;
            }
            
            if (unidades <= 0) {
                mostrarToast('Las unidades deben ser mayores que 0', 'error');
                return;
            }
            
            const submitBtn = formEditar.querySelector("button[type='submit']");
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
            
            const success = await actualizarMaterial(id, data);
            
            if (success) {
                const modalElement = document.getElementById("modalEditar");
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
                formEditar.reset();
                obtenerMateriales();
            }
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Actualizar carro';
            }
        });
    }
    
    // Formulario de eliminación
    const formEliminar = document.querySelector("#modalEliminar form");
    if (formEliminar) {
        formEliminar.addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const id = document.getElementById("deleteId")?.value;
            const carro = document.getElementById("deleteCarro")?.value;
            
            if (!id) {
                mostrarToast('Error al identificar el material', 'error');
                return;
            }
            
            const submitBtn = formEliminar.querySelector("button[type='submit']");
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Eliminando...';
            }
            
            const success = await eliminarMaterial(id);
            
            if (success) {
                const modalElement = document.getElementById("modalEliminar");
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
                formEliminar.reset();
                obtenerMateriales();
            }
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-trash"></i> Eliminar';
            }
        });
    }
    
    // Limpiar modales al cerrarlos
    const modales = ['modalCrear', 'modalEditar', 'modalEliminar'];
    modales.forEach(id => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                if (form) form.reset();
            });
        }
    });
});

// Exportar funciones globales
window.obtenerMateriales = obtenerMateriales;
window.cargarEdificios = cargarEdificios;