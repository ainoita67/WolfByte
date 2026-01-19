<?php
    include_once "../../../templates/header.php";
?>

<script>
    const menu = "liberar";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container mt-5">

    <div class="card p-5 shadow-sm">
        <h2 class="mb-4">Liberar aula</h2>

        <form class="row g-4">

            <div class="col-md-4">
                <label class="form-label">Edificio *</label>
                <select class="form-select" required>
                    <option value="" selected disabled>Seleccionar edificio</option>
                    <option value="RAM">RAM</option>
                    <option value="Loscos">Loscos</option>
                    <option value="Redondo">Redondo</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Planta *</label>
                <select class="form-select" required>
                    <option value="" selected disabled>Seleccionar planta</option>
                    <option value="Planta baja">Planta baja</option>
                    <option value="1ª planta">1ª planta</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Aula *</label>
                <select class="form-select" required>
                    <option value="" selected disabled>Seleccionar aula</option>
                    <option value="R1">R1</option>
                    <option value="R2">R2</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha *</label>
                <input type="date" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Hora inicio *</label>
                <select class="form-select" required>
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
                <select class="form-select" required>
                    <option value="" selected disabled>Seleccionar hora de fin</option>
                    <option>9:40</option>
                    <option>10:35</option>
                    <option>11:30</option>
                    <option>12:50</option>
                    <option>13:45</option>
                    <option>14:40</option>
                </select>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" rows="4" placeholder="Observaciones"></textarea>
            </div>

            <div class="col-md-6">
                <button type="submit" class="enviar w-100 text-light p-2">Liberar</button>
            </div>

            <div class="col-md-6 text-center d-flex">
                <a href="/public/views/menu.php" class="col-12 bg-lightgrey w-100 text-dark p-2 pb-0 mb-0">Volver</a>
            </div>

        </form>
    </div>

</main>

<?php
    include '../../../templates/footer.php';
?>
