<?php
    $title='Edificios';
    $directorio='../../../';
    $ruta='edificios';
    $seccion='';
    $style='';
    include '../../../../templates/header.php';
?>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edificios</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear material
        </button>
    </div>

    <div class="mt-5 container-fluid text-end">
        <a href="../tablasauxiliares.php" class="volver p-2 px-4 text-dark">Volver al menú de tablas auxiliares</a>
    </div>
</main>

<?php
    include '../../../../templates/footer.php';
?>