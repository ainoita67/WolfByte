document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('contenedorTarjetas');

    async function cargarEdificios() {
        try {
            const res = await apiFetch('/edificios');
            if (!res) return;

            const data = await res.json();

            if (data.status === 'error') {
                contenedor.innerHTML = `<p class="text-danger">${data.message}</p>`;
                return;
            }

            const edificios = data.data || data;

            if (!edificios.length) {
                contenedor.innerHTML = `<p>No hay edificios</p>`;
                return;
            }

            contenedor.innerHTML = '';

            edificios.forEach(edificio => {
                const tarjeta = document.createElement('div');
                tarjeta.classList.add('col-12', 'col-md-6', 'col-lg-4');

                tarjeta.innerHTML = `
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">${edificio.nombre_edificio}</h5>
                            <p class="card-text"><b>ID:</b> ${edificio.id_edificio}</p>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm me-2 btnEditar"
                                    data-id="${edificio.id_edificio}"
                                    data-nombre="${edificio.nombre_edificio}">
                                    Editar
                                </button>
                                <button class="btn btn-danger btn-sm btnBorrar"
                                    data-id="${edificio.id_edificio}">
                                    Borrar
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                contenedor.appendChild(tarjeta);
            });

            // EDITAR
            document.querySelectorAll('.btnEditar').forEach(btn => {
                btn.addEventListener('click', e => {
                    document.getElementById('editId').value = e.target.dataset.id;
                    document.getElementById('editNombre').value = e.target.dataset.nombre;

                    new bootstrap.Modal(document.getElementById('modalEditar')).show();
                });
            });

            // BORRAR
            document.querySelectorAll('.btnBorrar').forEach(btn => {
                btn.addEventListener('click', async e => {
                    const id = e.currentTarget.dataset.id;

                    if (!confirm('¿Seguro que quieres borrar este edificio?')) return;

                    const res = await apiFetch(`/edificios/${id}`, {
                        method: 'DELETE'
                    });
                    if (!res) return;

                    if (res.status === 204) {
                        alert('Edificio borrado');
                        cargarEdificios();
                        return;
                    }

                    const data = await res.json();
                    alert(data.message);
                });
            });

        } catch (err) {
            console.error(err);
            contenedor.innerHTML = `<p class="text-danger">Error al cargar edificios</p>`;
        }
    }

    // CREAR
    document.getElementById('formCrear').addEventListener('submit', async e => {
        e.preventDefault();

        const nombre = document.getElementById('crearNombre').value;

        const res = await apiFetch('/edificios', {
            method: 'POST',
            body: JSON.stringify({ nombre_edificio: nombre })
        });
        if (!res) return;

        const data = await res.json();

        if (data.status === 'error') {
            alert(data.message);
            return;
        }

        bootstrap.Modal.getInstance(document.getElementById('modalCrear')).hide();
        cargarEdificios();
    });

    // EDITAR
    document.getElementById('formEditar').addEventListener('submit', async e => {
        e.preventDefault();

        const id = document.getElementById('editId').value;
        const nombre = document.getElementById('editNombre').value;

        const res = await apiFetch(`/edificios/${id}`, {
            method: 'PUT',
            body: JSON.stringify({ nombre_edificio: nombre })
        });
        if (!res) return;

        const data = await res.json();

        if (data.status === 'error') {
            alert(data.message);
            return;
        }

        bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
        cargarEdificios();
    });

    cargarEdificios();
});
