<?php
    include_once "../../../../templates/header.php";
?>

<script>
    const menu = "admin";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edificios</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear edificio
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle tabla-cabecera">
            <thead>
                <tr>
                    <th>Nombre del edificio</th>
                    <th>Nº de plantas</th>
                    <th>Uso principal</th>
                    <th>Horario disponible</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Edificio Central</td>
                    <td>4</td>
                    <td>Docencia</td>
                    <td>
                        <span class="badge bg-primary">Lunes</span>
                        <span class="badge bg-primary">Martes</span>
                        <span class="badge bg-primary">Miércoles</span>
                        <span class="badge bg-primary">Jueves</span>
                        <span class="badge bg-primary">Viernes</span>
                    </td>
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
                    <td>Edificio Tecnológico</td>
                    <td>3</td>
                    <td>Laboratorios</td>
                    <td>
                        <span class="badge bg-primary">Lunes</span>
                        <span class="badge bg-primary">Martes</span>
                        <span class="badge bg-primary">Miércoles</span>
                        <span class="badge bg-primary">Jueves</span>
                    </td>
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