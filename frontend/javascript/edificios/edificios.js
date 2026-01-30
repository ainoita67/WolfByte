document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('contenedorTarjetas');

    async function cargarEdificios() {
        try {
            const res = await fetch('http://192.168.13.202:80/API/edificios');
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
                console.log('EDIFICIO:', edificio);
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

            // EVENTOS botones
            document.querySelectorAll('.btnEditar').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = e.target.dataset.id;
                    const nombre = e.target.dataset.nombre;

                    document.getElementById('editId').value = id;
                    document.getElementById('editNombre').value = nombre;

                    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
                    modal.show();
                });
            });

            document.querySelectorAll('.btnBorrar').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = e.currentTarget.dataset.id;

                    if (!confirm('¿Seguro que quieres borrar este edificio?')) return;

                    console.log('ID a borrar:', id);


                    const res = await fetch(`http://192.168.13.202/API/edificios/${id}`, {
                        method: 'DELETE'
                    });

                    if (res.status === 204) {
                        alert('Edificio borrado');
                        cargarEdificios();
                        return;
                    }

                    // SOLO si no es 204 intentas leer JSON
                    const data = await res.json();

                    if (data.status === 'error') {
                        alert(data.message);
}

                });
            });


        } catch (err) {
            contenedor.innerHTML = `<p class="text-danger">Error al cargar edificios</p>`;
        }
    }

    // CREAR EDIFICIO
    document.getElementById('formCrear').addEventListener('submit', async (e) => {
        e.preventDefault();
        const nombre = document.getElementById('crearNombre').value;

        const res = await fetch('http://192.168.13.202/API/edificios', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ nombre_edificio: nombre })
        });

        const data = await res.json();

        if (data.status === 'error') {
            alert(data.message);
            return;
        }

        alert('Edificio creado');
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrear'));
        modal.hide();
        cargarEdificios();
    });

    // EDITAR EDIFICIO
    document.getElementById('formEditar').addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('editId').value;
        const nombre = document.getElementById('editNombre').value;

        const res = await fetch(`http://192.168.13.202/API/edificios/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ nombre_edificio: nombre })
        });

        const data = await res.json();

        if (data.status === 'error') {
            alert(data.message);
            return;
        }

        alert('Edificio actualizado');
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditar'));
        modal.hide();
        cargarEdificios();
        
    });

    cargarEdificios();
});
