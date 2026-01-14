<?php
    $title='Materiales';
    $directorio='../../';
    $ruta='materiales';
    $seccion='';
    $style='';
    include '../../../templates/header.php';
?>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Materiales</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear material
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle tabla-cabecera">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Unidades</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Proyector Epson</td>
                    <td>Proyectores</td>
                    <td>5</td>
                    <td>Disponible</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditar">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>Portátil HP</td>
                    <td>Ordenadores</td>
                    <td>10</td>
                    <td>En uso</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditar">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-5 container-fluid text-end">
        <a href="../menuadministrador.php" class="volver p-2 px-4 text-dark">Volver al menú de administrador</a>
    </div>
</main>

<!-- Modal Crear -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Categoría</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Unidades</label>
                        <input type="number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select class="form-control">
                            <option>Disponible</option>
                            <option>En uso</option>
                            <option>Averiado</option>
                        </select>
                    </div>
                    <button class="btn btn-success w-100">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" class="form-control" id="editNombre">
                    </div>
                    <div class="mb-3">
                        <label>Categoría</label>
                        <input type="text" class="form-control" id="editCategoria">
                    </div>
                    <div class="mb-3">
                        <label>Unidades</label>
                        <input type="number" class="form-control" id="editUnidades">
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select class="form-control" id="editEstado">
                            <option>Disponible</option>
                            <option>En uso</option>
                            <option>Averiado</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Guardar cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll(".btn-editar").forEach(boton => {
    boton.addEventListener("click", function () {
        const fila = this.closest("tr");
        const celdas = fila.querySelectorAll("td");

        document.getElementById("editNombre").value = celdas[0].textContent.trim();
        document.getElementById("editCategoria").value = celdas[1].textContent.trim();
        document.getElementById("editUnidades").value = celdas[2].textContent.trim();
        document.getElementById("editEstado").value = celdas[3].textContent.trim();
    });
});
</script>

<?php
    include '../../../templates/footer.php';
?>