<?php
    $title='Tablas auxiliares';
    $directorio='../../';
    $ruta='tablasauxiliares';
    $seccion='';
    $style='';
    include '../../../templates/header.php';
?>

<main class="container-fluid menu row text-center">
    <a href="caracteristicasespacio/caracteristicasespacio.php" class="menu col-lg-5 mx-lg-5 p-5 fs-5">Características de espacio</a>
    <a href="necesidadesreserva/necesidadesreserva.php" class="menu col-lg-5 mx-lg-5 p-5 fs-5">Necesidades de reserva</a>
    <a href="edificios/edificios.php" class="menu col-lg-5 mx-lg-5 mb-lg-0 p-5 fs-5">Edificios</a>
    <a href="plantas/plantas.php" class="menu col-lg-5 mx-lg-5 mb-lg-0 p-5 fs-5">Plantas</a>
    <!--Superadministrador-->
    <a class="col-lg-3 mx-lg-5 d-none d-lg-block m-0 p-5 fs-5"></a>
    <a class="col-lg-3 mx-lg-5 d-none d-lg-block m-0 p-5 fs-5"></a>
    <a href="../menuadministrador.php" class="menu bg-lightgrey col-3 mt-5 mb-0 text-center d-none d-lg-block p-5 p-menu fs-5 text-dark">Volver al menú de administrador</a>
    <a href="../menuadministrador.php" class="menu bg-lightgrey d-lg-none p-5 fs-5 text-dark">Volver al menú de administrador</a>
</main>

<?php
    include '../../../templates/footer.php';
?>