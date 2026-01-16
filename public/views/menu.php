<?php
    include_once "../templates/header.php";
?>

<script>
    const menu = "menu";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container-fluid menu row text-center">
    <a href="aulas/aulas.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Aulas</a>
    <a href="salonactos/salonactos.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Salón de actos</a>
    <a href="material/material.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Material</a>
    <a href="espacios/espacios.php" class="menu col-lg-3 mx-lg-5 mb-lg-0 p-5 fs-5">Otros espacios</a>
    <a href="incidencias/incidencias.php" class="menu col-lg-3 mx-lg-5 mb-lg-0 p-5 fs-5">Incidencias</a>
    <a href="liberar/liberar.php" class="menu col-lg-3 mx-lg-5 mb-lg-0 p-5 fs-5">Liberar aula</a>
    <!--Superadministrador-->
    <a class="col-lg-3 mx-lg-5 d-none d-lg-block m-0 p-5 fs-5"></a>
    <a class="col-lg-3 mx-lg-5 d-none d-lg-block m-0 p-5 fs-5"></a>
    <a href="administrador/menuadministrador.php" class="menu bg-verde col-3 mt-5 mb-0 text-center d-none d-lg-block p-5 p-menu fs-5 text-light">Ir al menú de administrador</a>
    <a href="administrador/menuadministrador.php" class="menu bg-verde d-lg-none p-5 fs-5">Ir al menú de administrador</a>
</main>

<?php
    include_once "../templates/footer.php";
?>