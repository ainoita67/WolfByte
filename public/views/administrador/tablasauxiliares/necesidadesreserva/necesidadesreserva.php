<?php
include_once "../../../../templates/header.php";
?>

<script>
    const menu = "no";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Necesidades de Reservas</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear necesidades
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle tabla-cabecera">
            <thead>
                <tr>
                    <th>Necesidad</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Clase Matemáticas</td>
                    <td>Aula con proyector para clase teórica</td>
                    <td>15/02/2026</td>
                    <td>08:00 - 10:00</td>
                    <td><span class="badge bg-danger">Alta</span></td>
                    <td><span class="badge bg-success">Aprobada</span></td>
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
                    <td>Práctica Informática</td>
                    <td>Sala con 20 ordenadores</td>
                    <td>18/02/2026</td>
                    <td>10:00 - 12:00</td>
                    <td><span class="badge bg-warning text-dark">Media</span></td>
                    <td><span class="badge bg-secondary">Pendiente</span></td>
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
        <a href="../tablasauxiliares.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
    </div>
</main>

<?php
    include '../../../../templates/footer.php';
?>