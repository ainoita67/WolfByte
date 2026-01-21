<?php
    require_once dirname(__DIR__) . '/helpers/config.php';
    include_once RAIZ_PUBLIC . "/templates/header.php";
?>

<script>
    const menu = "menu";
    const rol = "admin";
    generateHeaderNav(menu, rol);
</script>

<main class="container-fluid menu row text-center">
    <a href="reservas/aulas/aulas.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Reservar aulas</a>
    <a href="reservas/salondeactos/salondeactos.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Reservar salón de actos</a>
    <a href="reservas/portatiles/portatiles.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Reservar portátiles</a>
    <a href="reservas/espacios/espacios.php" class="menu col-lg-3 mx-lg-5 mb-lg-0 p-5 fs-5">Reservar otros espacios</a>
    <a href="reservas/incidencias/incidencias.php" class="menu col-lg-3 mx-lg-5 mb-lg-0 p-5 fs-5">Incidencias</a>
    <a href="reservas/liberar/liberar.php" class="menu col-lg-3 mx-lg-5 mb-lg-0 p-5 fs-5">Liberar aula</a>
    <!--Superadministrador-->
    <a class="col-lg-3 mx-lg-5 d-none d-lg-block m-0 p-5 fs-5"></a>
    <a class="col-lg-3 mx-lg-5 d-none d-lg-block m-0 p-5 fs-5"></a>
    <a href="administrador/menuadministrador.php" class="menuadministrador col-3 mt-5 mb-0 text-center d-none d-lg-block p-5 p-menu fs-5 text-light">Ir al menú de administrador</a>
    <a href="administrador/menuadministrador.php" class="menuadministrador d-lg-none p-5 fs-5">Ir al menú de administrador</a>
</main>

<?php
    include_once RAIZ_PUBLIC . "/templates/footer.php";
?>