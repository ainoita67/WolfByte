<?php
    $title='Características espacio';
    $directorio='../../../';
    $ruta='característicasespacio';
    $seccion='';
    $style='<link rel="stylesheet" href="'.$directorio.'../assets/css/usuario.css">';
    include '../../../../templates/header.php';
?>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Características espacio</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear material
        </button>
    </div>


    <div class="mt-5 container-fluid text-end">
        <a href="../../menuadministrador.php" class="volver p-2 px-4 text-dark">Volver al menú de administrador</a>
    </div>
</main>

<?php
    include '../../../../templates/footer.php';
?>