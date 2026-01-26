<!DOCTYPE html>
<html lang="es">
    <head>
        <!-- Header Nav -->
        <script src="/ALEX/frontend/javascript/includes.js"></script>
        <script src="/ALEX/frontend/javascript/menu.js"></script>
        <title>Menú administrador - IES Bajo Aragón</title>
    </head>
    <body>
        <script>
            const menu = "admin";
            const rol = "admin";
            generarPagina(menu, rol);
        </script>

        <div id="header"></div>
        <main class="container-fluid menu row text-center">
            <a href="usuarios/usuarios.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Usuarios</a>
            <a href="logacciones/logacciones.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Log de acciones</a>
            <a href="aulasyespacios/aulasyespacios.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5">Aulas y espacios</a>
            <a href="reservaspermanentes/reservaspermanentes.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5 mb-lg-0">Reservas permanentes</a>
            <a href="materiales/materiales.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5 mb-lg-0">Materiales</a>
            <a href="tablasauxiliares/tablasauxiliares.php" class="menu col-lg-3 mx-lg-5 p-5 fs-5 mb-lg-0">Tablas auxiliares</a>
            <a class="col-lg-3 mx-lg-5 d-none d-lg-block m-0 p-5 fs-5"></a>
            <a class="col-lg-3 mx-lg-5 d-none d-lg-block m-0 p-5 fs-5"></a>
            <a href="../menu.php" class="menu bg-lightgrey col-3 mt-5 mb-0 text-center d-none d-lg-block p-5 p-menu fs-5 text-dark">Volver al menú principal</a>
            <a href="../menu.php" class="menu bg-lightgrey d-lg-none p-5 fs-5 text-dark">Volver al menú principal</a>
        </main>
        <footer id="footer"></footer>
    </body>
</html>