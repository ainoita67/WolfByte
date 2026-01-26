<?php
    $ruta='login';
    $directorio='../public/views/';
    $title='Inicio de sesión';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9MNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <!-- Estilos -->
    <link rel="stylesheet" href="/ALEX/public/assets/css/style.css">
    <!-- Header Nav -->
    <script src="/ALEX/public/assets/js/menu.js"></script>
    <title>Reservas - IES Bajo Aragón</title>
</head>
<body>
    <header></header>
<script>
    const menu = "";
    const rol = "";
    generateHeaderNav(menu, rol);
</script>
<script src=""></script>
<main class="p-5 row align-items-center main-content">
    <form method="post" id="loginForm" class="card col-md-6 offset-md-3 p-5 text-center">
        <h1 class="mb-4 fw-bold fs-4">Inicio de sesión</h1>

        <div class="mb-3 text-start mt-lg-3">
            <label for="usuario">Usuario</label>
            <input type="email" name="usuario" id="usuario" class="form-control" placeholder="&#128100 Usuario" required>
        </div>

        <div class="mb-4 text-start mt-lg-5">
            <label for="clave">Clave</label>
            <input type="password" name="clave" id="clave" class="form-control" placeholder="&#128274 Clave" required>
        </div>

        <button type="submit" id="iniciosesion" class="enviar p-2 mt-5 mt-lg-5 mb-3 fs-6">Iniciar sesión</button>
    </form>
</main>

<script src="/ALEX/public/assets/js/login.js"></script>
<?php
    include '../public/templates/footer.php';
?>