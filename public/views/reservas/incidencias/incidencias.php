<?php
    include_once "../../../templates/header.php";
?>

<script>
    const menu = "incidencias";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container-fluid mt-4">

    <div class="p-4 row" style="background:#eaf6f3;">
        <div class="col-lg-6">
            <p class="fw-bold mb-3">Selecciona un recurso para notificar su incidencia:</p>

            <div class="mb-3">
                <label class="me-3">
                    <input type="radio" name="tipo" checked> Espacio
                </label>
                <label>
                    <input type="radio" name="tipo"> Material
                </label>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Ubicación</label>
                    <select class="form-select">
                        <option>Edificio Ram</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <select class="form-select">
                        <option>Primera planta</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row col-lg-6">
            <div class=" ms-auto">
                <div class="border rounded overflow-hidden">

                    <div class="p-2 bg-secondary text-white">R1</div>
                    <div class="p-2">R2</div>
                    <div class="p-2 bg-secondary text-white">R3</div>
                    <div class="p-2">R4</div>
                    <div class="p-2 bg-secondary text-white">R5</div>
                    <div class="p-2">R6</div>
                    <div class="p-2 bg-secondary text-white">R7</div>
                    <div class="p-2">R8</div>
                    <div class="p-2 bg-secondary text-white">R9</div>
                    <div class="p-2">R10</div>
                    <div class="p-2 bg-secondary text-white">R11</div>
                    <div class="p-2">R12</div>
                    <div class="p-2 bg-secondary text-white">R13</div>

                </div>
            </div>
        </div>

    </div>

    <div class="mt-5 container-fluid text-end">
        <a href="/public/views/menu.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
    </div>

</main>

<?php
    include '../../../templates/footer.php';
?>
