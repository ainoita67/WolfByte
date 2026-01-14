<?php
    $title='Mis datos';
    $directorio='../';
    $ruta='misdatos';
    $seccion='';
    $style='<link rel="stylesheet" href="'.$directorio.'../assets/css/perfil.css">';
    include '../../templates/header.php';
?>

<main class="row justify-content-center align-items-center main-content">
    <div class="col-11 col-sm-8 col-md-6 col-lg-4">
        <div class="card shadow p-4 text-center">
            <h5 class="mb-4 fw-bold">Mis datos</h5>

            <div class="mb-3 text-start">
                <label class="form-label fw-semibold">Usuario:</label>
                <input type="text" class="form-control" readonly>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label fw-semibold">Correo:</label>
                <input type="email" class="form-control" readonly>
            </div>

            <div class="mb-4 text-start">
                <label class="form-label fw-semibold">Rol:</label>
                <input type="text" class="form-control" readonly>
            </div>

            <div class="d-flex justify-content-center gap-5 mb-3 px-4">
                <a href="reserva.php" class="enviar p-2 px-4 col-6">Mis reservas</a>
                <a href="#" class="enviar p-2 px-4 col-6">Mis incidencias</a>
            </div>

            <a href="../menu.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
        </div>
    </div>
</main>

<?php
    include '../../templates/footer.php';
?>