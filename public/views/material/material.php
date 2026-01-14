<?php
    $title='Material';
    $directorio='../';
    $ruta='material';
    $seccion='material';
    $style='<link rel="stylesheet" href="'.$directorio.'../assets/css/perfil.css">';
    include '../../templates/header.php';
?>

<main class="container mt-5">
    <h2 class="text-center mb-4">Material</h2>
    <div class="mt-5 container-fluid text-end">
        <a href="../menu.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
    </div>
</main>

<?php
    include '../../templates/footer.php';
?>