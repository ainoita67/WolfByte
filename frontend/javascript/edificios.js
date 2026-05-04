document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('contenedorTarjetas');
    
    const API_BASE = window.location.origin;

    // ============================================
    // FUNCIÓN PARA CERRAR MODAL CORRECTAMENTE
    // ============================================
    function cerrarModal(modalId) {
        const modalElement = document.getElementById(modalId);
        
        if (modalElement) {
            // Quitar clases y estilos del modal
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');
            
            // Quitar el backdrop
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Quitar clase modal-open del body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Si hay instancia de Bootstrap, destruirla
            try {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                    // Forzar dispose
                    if (modalInstance.dispose) {
                        modalInstance.dispose();
                    }
                }
            } catch (e) {
                console.error('Error al obtener instancia modal:', e);
            }
        }
    }

    // ============================================
    // CARGAR EDIFICIOS
    // ============================================
    async function cargarEdificios() {
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
                        <p class="text-black text-muted mt-3">No hay edificios registrados</p>
                    </div>
                `;
                return;
            }

            contenedor.innerHTML = '';

            edificios.forEach((edificio, index) => {
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
                                <button class="btn btn-warning btn-sm btnEditar"
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

            asignarEventosBotones();
        } catch (err) {
            console.error('Error al cargar edificios:', err);
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <h5 class="mt-3 text-danger">Error al cargar edificios</h5>
                    <p class="text-black text-muted">${err.message}</p>
                    <button class="btn btn-primary mt-3" onclick="location.reload()">
                        Reintentar
                    </button>
                </div>
            `;
        }
    }

    // ============================================
    // ASIGNAR EVENTOS A BOTONES DE EDITAR
    // ============================================
    function asignarEventosBotones() {
        const editBtns = document.querySelectorAll('.btnEditar');
        
        editBtns.forEach((btn, index) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();                
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre;
                
                document.getElementById('editId').value = id;
                document.getElementById('editNombre').value = nombre;

                const modalElement = document.getElementById('modalEditar');
                
                // Asegurar que no haya backdrops previos
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            });
        });
    }

    // ============================================
    // VERIFICAR QUE EXISTEN LOS ELEMENTOS DEL DOM
    // ============================================
    const modalCrear = document.getElementById('modalCrear');
    
    const modalEditar = document.getElementById('modalEditar');
    
    const formCrearElem = document.getElementById('formCrear');
    
    const formEditarElem = document.getElementById('formEditar');

    // ============================================
    // SOLUCIÓN: VERIFICAR Y ASEGURAR FUNCIONAMIENTO DEL BOTÓN CREAR
    // ============================================

    // Buscar el botón que abre el modal de crear por diferentes métodos
    const btnAbrirCrear = 
        document.querySelector('[data-bs-target="#modalCrear"]') || 
        document.querySelector('[data-target="#modalCrear"]') ||
        document.getElementById('btnCrearEdificio') ||
        document.querySelector('.btn-primary:not(.btnEditar)') ||
        Array.from(document.querySelectorAll('button')).find(btn => 
            btn.textContent.includes('Crear') || 
            btn.textContent.includes('Nuevo') ||
            btn.textContent.includes('Agregar')
        );

    if (btnAbrirCrear) {        
        // Verificar que tenga los atributos correctos de Bootstrap
        if (!btnAbrirCrear.hasAttribute('data-bs-toggle') && !btnAbrirCrear.hasAttribute('data-toggle')) {
            btnAbrirCrear.setAttribute('data-bs-toggle', 'modal');
            btnAbrirCrear.setAttribute('data-bs-target', '#modalCrear');
        }
        
        // Remover event listeners anteriores para evitar duplicados
        const nuevoBoton = btnAbrirCrear.cloneNode(true);
        btnAbrirCrear.parentNode.replaceChild(nuevoBoton, btnAbrirCrear);
        
        // Agregar event listener al nuevo botón
        nuevoBoton.addEventListener('click', (e) => {
            e.preventDefault();            
            // Limpiar backdrops residuales antes de abrir
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Verificar que el modal existe
            const modalElement = document.getElementById('modalCrear');
            if (modalElement) {
                try {
                    // Reiniciar el modal completamente
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                    
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                    
                    // Limpiar el formulario cuando se abre el modal
                    const form = modalElement.querySelector('form');
                    if (form) {
                        form.reset();
                    }
                    
                    modal.show();
                } catch (error) {
                    console.error('Error al abrir modal:', error);
                    mostrarToast('Error al abrir el modal', 'danger');
                }
            } else {
                console.error('Modal crear NO encontrado en el DOM');
                mostrarToast('Error: No se ha encontrado el modal de creación', 'danger');
            }
        });
    } else {
        console.error('No se ha encontrado el botón de crear');
        
        // Crear un botón de crear si no existe (solución de emergencia)
        const toolbar = document.querySelector('.toolbar, .btn-toolbar, .mb-3, .mt-3') || contenedor?.parentNode;
        
        if (toolbar) {
            const nuevoBotonEmergencia = document.createElement('button');
            nuevoBotonEmergencia.className = 'btn btn-primary mb-3';
            nuevoBotonEmergencia.textContent = '➕ Crear Edificio';
            nuevoBotonEmergencia.id = 'btnCrearEdificioEmergencia';
            
            nuevoBotonEmergencia.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Limpiar backdrops residuales
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                
                const modalElement = document.getElementById('modalCrear');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            });
            
            toolbar.insertBefore(nuevoBotonEmergencia, toolbar.firstChild);
        }
    }

    // Verificar que el modal de crear existe    
    const modalCrearElement = document.getElementById('modalCrear');
    if (modalCrearElement) {        
        // Verificar que el formulario dentro del modal existe
        const formCrear = modalCrearElement.querySelector('form');
        if (formCrear) {
        } else {
            console.error('No se ha encontrado formulario dentro del modal crear');
        }
        
        // Verificar los campos del formulario
        const inputNombre = document.getElementById('crearNombre');
        if (inputNombre) {
            console.error('Input nombre NO encontrado');
        }
        
    } else {
        console.error('Modal crear NO encontrado en el DOM');
    }

    // ============================================
    // CREAR EDIFICIO
    // ============================================
    if (formCrearElem) {        
        // Remover event listeners anteriores para evitar duplicados
        const nuevoFormCrear = formCrearElem.cloneNode(true);
        formCrearElem.parentNode.replaceChild(nuevoFormCrear, formCrearElem);
        
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

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || `Error ${res.status}`);
                }

                mostrarToast('Edificio creado correctamente', 'success');
                
                // CERRAR MODAL CORRECTAMENTE usando la función especializada
                cerrarModal('modalCrear');
                
                // Limpiar formulario
                nombreInput.value = '';
                
                await cargarEdificios();
                
            } catch (err) {
                console.error('Error al crear:', err);
                mostrarToast(err.message, 'danger');
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
    // EDITAR EDIFICIO
    // ============================================
    if (formEditarElem) {        
        // Remover event listeners anteriores
        const nuevoFormEditar = formEditarElem.cloneNode(true);
        formEditarElem.parentNode.replaceChild(nuevoFormEditar, formEditarElem);
        
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

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || `Error ${res.status}`);
                }
                
                mostrarToast('Edificio actualizado correctamente', 'success');
                
                // CERRAR MODAL CORRECTAMENTE usando la función especializada
                cerrarModal('modalEditar');
                await cargarEdificios();
                
            } catch (err) {
                console.error('Error al actualizar:', err);
                mostrarToast(err.message, 'danger');
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
            // Usar nuestro método de cierre personalizado en lugar del de Bootstrap
            modal.addEventListener('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                if (form) {
                    form.reset();
                    if (id === 'modalEditar') {
                        const editId = document.getElementById('editId');
                        if (editId) editId.value = '';
                    }
                }
            });
            
            modal.addEventListener('show.bs.modal', function() {
            });
        }
    });

    // ============================================
    // INICIAR CARGA
    // ============================================
    cargarEdificios();
});