<?php
    include_once "../../templates/header.php";
?>

<script>
    const menu = "admin";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<script src="/public/assets/js/misReservas.js" defer></script>

<main class="container-fluid mt-4">
    <h2 class="mb-4">Mis reservas</h2>

    <div id="contenedorReservas" class="row g-4">
        <!-- Tarjetas generadas por JS -->
    </div>
</main>

<?php
    include_once "../../templates/footer.php";
?>
