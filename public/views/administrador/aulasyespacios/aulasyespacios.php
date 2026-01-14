<?php
    $title='Aulas y espacios';
    $directorio='../../';
    $ruta='aulasyespacios';
    $seccion='';
    $style='<link rel="stylesheet" href="'.$directorio.'../assets/css/usuario.css">';
    include '../../../templates/header.php';
?>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Aulas y Espacios</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear aula
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle tabla-cabecera">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Capacidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Aula 101</td>
                    <td>Aula</td>
                    <td>30</td>
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
                    <td>Salón de actos</td>
                    <td>Espacio</td>
                    <td>200</td>
                    <td>Ocupado</td>
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

<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Crear aula/espacio</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form>
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" class="form-control">
            </div>
            <div class="mb-3">
                <label>Tipo</label>
                <select class="form-control">
                <option>Aula</option>
                <option>Espacio</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Capacidad</label>
                <input type="number" class="form-control">
            </div>
            <div class="mb-3">
                <label>Estado</label>
                <select class="form-control">
                <option>Disponible</option>
                <option>Ocupado</option>
                </select>
            </div>
            <button class="btn btn-success w-100">Guardar</button>
            </form>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Editar aula/espacio</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form>
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" class="form-control" id="editNombre">
            </div>
            <div class="mb-3">
                <label>Tipo</label>
                <select class="form-control" id="editTipo">
                <option>Aula</option>
                <option>Espacio</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Capacidad</label>
                <input type="number" class="form-control" id="editCapacidad">
            </div>
            <div class="mb-3">
                <label>Estado</label>
                <select class="form-control" id="editEstado">
                <option>Disponible</option>
                <option>Ocupado</option>
                </select>
            </div>
            <button class="btn btn-primary w-100">Guardar cambios</button>
            </form>
        </div>
        </div>
            </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
crossorigin="anonymous"></script>

<script>
    document.querySelectorAll(".btn-editar").forEach(boton => {
        boton.addEventListener("click", function () {
            const fila = this.closest("tr");
            const celdas = fila.querySelectorAll("td");

            document.getElementById("editNombre").value = celdas[0].textContent.trim();
            document.getElementById("editTipo").value = celdas[1].textContent.trim();
            document.getElementById("editCapacidad").value = celdas[2].textContent.trim();
            document.getElementById("editEstado").value = celdas[3].textContent.trim();
        });
    });
</script>

<?php
<<<<<<<< HEAD:public/views/administrador/aulasyespacios/aulasyespacios.php
    include '../../../templates/footer.php';
?>
========
    include '../../templates/footer.php'
?>
>>>>>>>> origin/panel-de-administrador:public/views/administrador/espacios/aulasyespacios.php
