<?php
    $title='Características de espacios';
    $directorio='../../../';
    $ruta='caracteristicas-espacios';
    $seccion='';
    $style='';
    include '../../../../templates/header.php';
?>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Características de espacios</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear espacio
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle tabla-cabecera">
            <thead>
                <tr>
                    <th>Nombre del espacio</th>
                    <th>Tipo</th>
                    <th>Capacidad</th>
                    <th>Equipamiento</th>
                    <th>Días disponibles</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Aula 101</td>
                    <td>Aula teórica</td>
                    <td>30 personas</td>
                    <td>
                        Proyector, Pizarra digital, Altavoces
                    </td>
                    <td>
                        <span class="badge bg-primary">Lunes</span>
                        <span class="badge bg-primary">Miércoles</span>
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
                    <td>Sala Informática</td>
                    <td>Laboratorio</td>
                    <td>20 personas</td>
                    <td>
                        Ordenadores, Proyector, Impresora
                    </td>
                    <td>
                        <span class="badge bg-primary">Martes</span>
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
<<<<<<< HEAD
?>  
=======
?>
>>>>>>> main
