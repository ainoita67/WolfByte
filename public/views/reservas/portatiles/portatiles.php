<?php
    include_once "../../../templates/header.php";
?>

<script>
    const menu = "portatiles";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container mt-5">
    <div class="card p-5 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Portátiles</h2>
        </div>

        <form action="/public/views/confirmar_reserva.php" method="post" class="row g-4">

            <div class="col-md-4">
                <label class="form-label">Fecha *</label>
                <input type="date" class="form-control" name="fecha" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Hora inicio *</label>
                <select class="form-select" name="horainicio" required>
                    <option value="" selected disabled>Seleccionar hora de inicio</option>
                    <option>8:50</option>
                    <option>9:45</option>
                    <option>10:40</option>
                    <option>12:00</option>
                    <option>12:55</option>
                    <option>13:50</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Hora fin *</label>
                <select class="form-select" name="horafin" required>
                    <option value="" selected disabled>Seleccionar hora de fin</option>
                    <option>9:40</option>
                    <option>10:35</option>
                    <option>11:30</option>
                    <option>12:50</option>
                    <option>13:45</option>
                    <option>14:40</option>
                </select>
            </div>

            <div class="col-md-6 text-center d-flex">
                <a href="/public/views/reservas/portatiles/reserva_portatil.php " class="col-12 border rounded-pill bg-azul w-100 text-light p-2 pb-2 mb-0">Ver Portátiles</a>
            </div>

            <div class="col-md-6 text-center d-flex">
                <a href="/public/views/menu.php" class="col-12 bg-lightgrey w-100 text-dark p-2 pb-0 mb-0">Volver</a>
            </div>

        </form>
    </div>
</main>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<?php
    include '../../../templates/footer.php';
?>