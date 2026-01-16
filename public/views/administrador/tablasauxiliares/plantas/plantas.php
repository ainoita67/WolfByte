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
        <h2>Plantas</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear plantas
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle tabla-cabecera">
            <thead>
                <tr>
                    <th>Planta</th>
                    <th>Edificio</th>
                    <th>Aulas</th>
                    <th>Laboratorios</th>
                    <th>Despachos</th>
                    <th>Otros espacios</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Planta Baja</td>
                    <td>Edificio Central</td>
                    <td>5</td>
                    <td>1</td>
                    <td>3</td>
                    <td>Conserjería, Archivo</td>
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
                    <td>Primera Planta</td>
                    <td>Edificio Central</td>
                    <td>10</td>
                    <td>2</td>
                    <td>6</td>
                    <td>Sala profesores</td>
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