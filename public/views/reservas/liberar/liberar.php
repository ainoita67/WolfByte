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
                <select class="form-select">
                    <option selected>Seleccionar edificio</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Planta *</label>
                <select class="form-select">
                    <option selected>Seleccionar planta</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Aula *</label>
                <select class="form-select">
                    <option selected>Seleccionar aula</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha *</label>
                <input type="date" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Hora inicio *</label>
                <select class="form-select">
                    <option selected>Seleccionar hora</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Hora fin *</label>
                <select class="form-select">
                    <option selected>Seleccionar hora</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" rows="4" placeholder="Observaciones"></textarea>
            </div>

            <div class="col-md-6">
                <button type="submit" class="btn btn-primary w-100 py-2">Liberar</button>
            </div>

            <div class="col-md-6">
                <a href="/public/views/menu.php" class="btn btn-secondary w-100 py-2">Volver</a>
            </div>

        </form>
    </div>

</main>

<?php
    include '../../../templates/footer.php';
?>
