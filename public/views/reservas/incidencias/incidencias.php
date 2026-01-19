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
        <div class="col-lg-6">
            <table class="border rounded overflow-hidden col-12">
                <tr>
                    <td class="p-2 bg-secondary text-white">R1</td>
                </tr>
                <tr>
                    <td class="p-2">R2</td>
                </tr>
                <tr>
                    <td class="p-2 bg-secondary text-white">R3</td>
                </tr>
                <tr>
                    <td class="p-2">R4</td>
                </tr>
                <tr>
                    <td class="p-2 bg-secondary text-white">R5</td>
                </tr>
                <tr>
                    <td class="p-2">R6</td>
                </tr>
                <tr>
                    <td class="p-2 bg-secondary text-white">R7</td>
                </tr>
                <tr>
                    <td class="p-2">R8</td>
                </tr>
                <tr>
                    <td class="p-2 bg-secondary text-white">R9</td>
                </tr>
                <tr>
                    <td class="p-2">R10</td>
                </tr>
                <tr>
                    <td class="p-2 bg-secondary text-white">R11</td>
                </tr>
                <tr>
                    <td class="p-2">R12</td>
                </tr>
                <tr>
                    <td class="p-2 bg-secondary text-white">R13</td>
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
