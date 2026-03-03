document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado - Iniciando script de edificios');
    
    const contenedor = document.getElementById('contenedorTarjetas');
    console.log('Contenedor encontrado:', contenedor);
    
    const API_BASE = window.location.origin;
    console.log('API Base:', API_BASE);

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
            console.log('Contenedor de toasts creado');
        }
        
        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        
        let bgClass = 'bg-success';
        
        if (tipo === 'error') {
            bgClass = 'bg-danger';
        } else if (tipo === 'warning') {
            bgClass = 'bg-warning';
        } else if (tipo === 'info') {
            bgClass = 'bg-info';
        }
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        ${mensaje}
                    </div>
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
    // FUNCIÓN PARA CERRAR MODAL CORRECTAMENTE
    // ============================================
    function cerrarModal(modalId) {
        console.log('Cerrando modal:', modalId);
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
                console.log('Error al obtener instancia modal:', e);
            }
        }
    }

    // ============================================
    // CARGAR EDIFICIOS
    // ============================================
    async function cargarEdificios() {
        console.log('Iniciando carga de edificios...');
        
        try {
            console.log('Fetching:', `${API_BASE}/API/edificios`);
            
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
                console.log('No hay edificios');
                contenedor.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <p class="text-muted mt-3">No hay edificios registrados</p>
                    </div>
                `;
                return;
            }

            console.log('Mostrando', edificios.length, 'edificios');
            contenedor.innerHTML = '';

            edificios.forEach((edificio, index) => {
                console.log('Edificio', index + 1, ':', edificio);
                
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
                                <button class="btn btn-warning btn-sm btnEditar"
                                    data-id="${edificio.id_edificio}"
                                    data-nombre="${edificio.nombre_edificio}">
                                    Editar
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                contenedor.appendChild(tarjeta);
            });

            console.log('Asignando eventos a botones de editar...');
            asignarEventosBotones();
            console.log('Eventos de editar asignados');

        } catch (err) {
            console.error('Error al cargar edificios:', err);
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <h5 class="mt-3 text-danger">Error al cargar edificios</h5>
                    <p class="text-muted">${err.message}</p>
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
        console.log('Buscando botones de editar...');
        const editBtns = document.querySelectorAll('.btnEditar');
        console.log('Encontrados', editBtns.length, 'botones de editar');
        
        editBtns.forEach((btn, index) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Click en boton editar', index + 1, ':', btn.dataset);
                
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre;

                console.log('Editando ID:', id, 'Nombre:', nombre);
                
                document.getElementById('editId').value = id;
                document.getElementById('editNombre').value = nombre;

                const modalElement = document.getElementById('modalEditar');
                console.log('Modal editar encontrado:', modalElement);
                
                // Asegurar que no haya backdrops previos
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('Modal editar mostrado');
            });
        });
    }

    // ============================================
    // VERIFICAR QUE EXISTEN LOS ELEMENTOS DEL DOM
    // ============================================
    console.log('Verificando elementos del DOM:');
    
    const modalCrear = document.getElementById('modalCrear');
    console.log('modalCrear:', modalCrear ? 'OK' : 'NO ENCONTRADO');
    
    const modalEditar = document.getElementById('modalEditar');
    console.log('modalEditar:', modalEditar ? 'OK' : 'NO ENCONTRADO');
    
    const formCrearElem = document.getElementById('formCrear');
    console.log('formCrear:', formCrearElem ? 'OK' : 'NO ENCONTRADO');
    
    const formEditarElem = document.getElementById('formEditar');
    console.log('formEditar:', formEditarElem ? 'OK' : 'NO ENCONTRADO');

    // ============================================
    // SOLUCIÓN: VERIFICAR Y ASEGURAR FUNCIONAMIENTO DEL BOTÓN CREAR
    // ============================================
    console.log('============================================');
    console.log('VERIFICANDO BOTÓN DE CREAR...');
    console.log('============================================');

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
        console.log('✅ Botón de crear encontrado:', {
            texto: btnAbrirCrear.textContent.trim(),
            id: btnAbrirCrear.id,
            clases: btnAbrirCrear.className,
            dataset: btnAbrirCrear.dataset
        });
        
        // Verificar que tenga los atributos correctos de Bootstrap
        if (!btnAbrirCrear.hasAttribute('data-bs-toggle') && !btnAbrirCrear.hasAttribute('data-toggle')) {
            console.log('⚠️ Botón no tiene atributo data-bs-toggle, agregándolo manualmente');
            btnAbrirCrear.setAttribute('data-bs-toggle', 'modal');
            btnAbrirCrear.setAttribute('data-bs-target', '#modalCrear');
        }
        
        // Remover event listeners anteriores para evitar duplicados
        const nuevoBoton = btnAbrirCrear.cloneNode(true);
        btnAbrirCrear.parentNode.replaceChild(nuevoBoton, btnAbrirCrear);
        
        // Agregar event listener al nuevo botón
        nuevoBoton.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('🖱️ Click en botón crear - Abriendo modal');
            
            // Limpiar backdrops residuales antes de abrir
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Verificar que el modal existe
            const modalElement = document.getElementById('modalCrear');
            if (modalElement) {
                console.log('✅ Modal crear encontrado, abriendo...');
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
                    console.log('Modal mostrar llamado');
                } catch (error) {
                    console.error('Error al abrir modal:', error);
                    mostrarToast('Error al abrir el modal', 'error');
                }
            } else {
                console.error('❌ Modal crear NO encontrado en el DOM');
                mostrarToast('Error: No se encontró el modal de creación', 'error');
            }
        });
        
        console.log('✅ Event listener del botón crear configurado correctamente');
    } else {
        console.error('❌ No se encontró el botón de crear');
        
        // Listar todos los botones para debug
        console.log('Botones disponibles en la página:');
        document.querySelectorAll('button, .btn').forEach((btn, i) => {
            console.log(`Botón ${i + 1}:`, {
                texto: btn.textContent?.trim() || 'sin texto',
                clases: btn.className,
                id: btn.id || 'sin id',
                tipo: btn.tagName
            });
        });
        
        // Crear un botón de crear si no existe (solución de emergencia)
        console.log('⚠️ Creando botón de crear automáticamente...');
        const toolbar = document.querySelector('.toolbar, .btn-toolbar, .mb-3, .mt-3') || contenedor?.parentNode;
        
        if (toolbar) {
            const nuevoBotonEmergencia = document.createElement('button');
            nuevoBotonEmergencia.className = 'btn btn-primary mb-3';
            nuevoBotonEmergencia.textContent = '➕ Crear Edificio';
            nuevoBotonEmergencia.id = 'btnCrearEdificioEmergencia';
            
            nuevoBotonEmergencia.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Click en botón crear de emergencia');
                
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
            console.log('✅ Botón de crear de emergencia creado');
        }
    }

    // Verificar que el modal de crear existe
    console.log('============================================');
    console.log('VERIFICANDO MODAL DE CREAR...');
    console.log('============================================');
    
    const modalCrearElement = document.getElementById('modalCrear');
    if (modalCrearElement) {
        console.log('✅ Modal crear encontrado en el DOM');
        
        // Verificar que el formulario dentro del modal existe
        const formCrear = modalCrearElement.querySelector('form');
        if (formCrear) {
            console.log('✅ Formulario de crear encontrado dentro del modal');
            console.log('Form ID:', formCrear.id);
            console.log('Form action:', formCrear.action);
        } else {
            console.error('❌ No se encontró formulario dentro del modal crear');
        }
        
        // Verificar los campos del formulario
        const inputNombre = document.getElementById('crearNombre');
        if (inputNombre) {
            console.log('✅ Input nombre encontrado:', inputNombre);
        } else {
            console.error('❌ Input nombre NO encontrado');
        }
        
    } else {
        console.error('❌ Modal crear NO encontrado en el DOM');
    }

    console.log('============================================');
    console.log('VERIFICACIÓN COMPLETADA');
    console.log('============================================');

    // ============================================
    // CREAR EDIFICIO
    // ============================================
    if (formCrearElem) {
        console.log('✅ Formulario crear encontrado, añadiendo evento submit...');
        
        // Remover event listeners anteriores para evitar duplicados
        const nuevoFormCrear = formCrearElem.cloneNode(true);
        formCrearElem.parentNode.replaceChild(nuevoFormCrear, formCrearElem);
        
        nuevoFormCrear.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('✅ Submit formulario crear');
            
            const nombreInput = document.getElementById('crearNombre');
            if (!nombreInput) {
                console.error('❌ Input nombre no encontrado');
                mostrarToast('Error en el formulario', 'error');
                return;
            }
            
            const nombre = nombreInput.value.trim();
            console.log('Nombre ingresado:', nombre);
            
            if (!nombre) {
                console.log('Nombre vacio');
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
                const data = await res.json();
                console.log('Respuesta data:', data);

                if (!res.ok) {
                    throw new Error(data.message || `Error ${res.status}`);
                }

                console.log('✅ Edificio creado correctamente');
                mostrarToast('Edificio creado correctamente', 'success');
                
                // CERRAR MODAL CORRECTAMENTE usando la función especializada
                cerrarModal('modalCrear');
                
                // Limpiar formulario
                nombreInput.value = '';
                
                console.log('Recargando edificios...');
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
        console.error('❌ No se encontro el formulario de crear');
    }

    // ============================================
    // EDITAR EDIFICIO
    // ============================================
    if (formEditarElem) {
        console.log('✅ Formulario editar encontrado, añadiendo evento submit...');
        
        // Remover event listeners anteriores
        const nuevoFormEditar = formEditarElem.cloneNode(true);
        formEditarElem.parentNode.replaceChild(nuevoFormEditar, formEditarElem);
        
        nuevoFormEditar.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('✅ Submit formulario editar');

            const idInput = document.getElementById('editId');
            const nombreInput = document.getElementById('editNombre');
            
            if (!idInput || !nombreInput) {
                console.error('❌ Inputs no encontrados');
                mostrarToast('Error en el formulario', 'error');
                return;
            }
            
            const id = idInput.value;
            const nombre = nombreInput.value.trim();
            
            console.log('ID:', id, 'Nombre:', nombre);
            
            if (!nombre) {
                console.log('Nombre vacio');
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
                const data = await res.json();
                console.log('Respuesta data:', data);

                if (!res.ok) {
                    throw new Error(data.message || `Error ${res.status}`);
                }

                console.log('✅ Edificio actualizado correctamente');
                mostrarToast('Edificio actualizado correctamente', 'success');
                
                // CERRAR MODAL CORRECTAMENTE usando la función especializada
                cerrarModal('modalEditar');
                
                console.log('Recargando edificios...');
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
        console.error('❌ No se encontro el formulario de editar');
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
                console.log('Modal', id, 'cerrado por Bootstrap, limpiando...');
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
                console.log('Modal', id, 'abriéndose');
            });
        }
    });

    // ============================================
    // INICIAR CARGA
    // ============================================
    console.log('============================================');
    console.log('Iniciando carga de edificios...');
    console.log('============================================');
    cargarEdificios();
});