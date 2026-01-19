<?php
    include_once "../../../templates/header.php";
?>

<script>
    const menu = "incidencias";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container mt-5">
    <h2 class="text-center mb-4">Incidencias</h2>
    <div class="p-4 row">
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
                <div class="col-md-5">
                    <label class="form-label">Ubicación</label>
                    <select class="form-select">
                        <option value="" selected disabled>Seleccionar ubicación</option>
                        <option value="RAM">Edificio Ram</option>
                        <option value="Loscos">Edificio Loscos</option>
                        <option value="Redondo">Edificio Redondo</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">&nbsp;</label>
                    <select class="form-select">
                        <option value="" selected disabled>Seleccionar planta</option>
                        <option>Primera planta</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mt-3 mt-md-0">
            <table class="rounded-1 overflow-hidden table-striped col-12" id="tablaincidencias">
                <tr>
                    <td class="p-2">R1</td>
                </tr>
                <tr>
                    <td class="p-2">R2</td>
                </tr>
                <tr>
                    <td class="p-2">R3</td>
                </tr>
                <tr>
                    <td class="p-2">R4</td>
                </tr>
                <tr>
                    <td class="p-2">R5</td>
                </tr>
                <tr>
                    <td class="p-2">R6</td>
                </tr>
                <tr>
                    <td class="p-2">R7</td>
                </tr>
                <tr>
                    <td class="p-2">R8</td>
                </tr>
                <tr>
                    <td class="p-2">R9</td>
                </tr>
                <tr>
                    <td class="p-2">R10</td>
                </tr>
                <tr>
                    <td class="p-2">R11</td>
                </tr>
                <tr>
                    <td class="p-2">R12</td>
                </tr>
                <tr>
                    <td class="p-2">R13</td>
                </tr>
            </table>
        </div>

    </div>

    <div class="mt-5 container-fluid text-end">
        <a href="/public/views/menu.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
    </div>

</main>

<?php
    include '../../../templates/footer.php';
?>
