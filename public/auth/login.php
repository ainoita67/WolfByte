<?php
    $ruta='login';
    $directorio='../public/views/';
    $title='Inicio de sesión';
    include '../public/templates/header.php';
?>

<main class="p-5 row align-items-center main-content">
    <form class="card col-md-6 offset-md-3 p-5 text-center" action="" method="post">
        <h1 class="mb-4 fw-bold fs-4">Inicio de sesión</h1>

        <div class="mb-3 text-start mt-lg-3">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" class="form-control" placeholder="&#128100 Usuario" required>
        </div>

        <div class="mb-4 text-start mt-lg-5">
            <label for="clave">Clave</label>
            <input type="password" name="clave" id="clave" class="form-control" placeholder="&#128274 Clave" required>
        </div>

        <button type="submit" id="iniciosesion" class="enviar p-2 mt-5 mt-lg-5 mb-3 fs-6">Iniciar sesión</button>
    </form>
</main>

<?php
    include '../public/templates/footer.php';
?>