<?php
    include_once "../../../templates/header.php";
?>

<script>
    const menu = "liberar";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container mt-5">
    <h2 class="text-center mb-4">Liberar aulas</h2>
    <div class="mt-5 container-fluid text-end">
        <a href="/public/views/menu.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
    </div>
</main>

<?php
    include '../../../templates/footer.php';
?>