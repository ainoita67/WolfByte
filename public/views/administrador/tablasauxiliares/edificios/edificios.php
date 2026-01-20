<?php
    include_once "../../../../templates/header.php";
?>

<script>
    const menu = "";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>
<script src="/public/assets/js/edificios.js" defer></script>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edificios</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear edificio
        </button>
    </div>

    <!-- Aquí irán las tarjetas -->
    <div id="contenedorTarjetas" class="row g-4 mt-3">
        <!-- tarjetas generadas por JS -->
    </div>

    <div class="mt-5 container-fluid text-end">
        <a href="../tablasauxiliares.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
    </div>
</main>

<!-- MODAL CREAR -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear edificio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCrear">
          <div class="mb-3">
            <label for="crearNombre" class="form-label">Nombre edificio</label>
            <input type="text" id="crearNombre" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-success">Crear</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar edificio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditar">
          <input type="hidden" id="editId">
          <div class="mb-3">
            <label for="editNombre" class="form-label">Nombre edificio</label>
            <input type="text" id="editNombre" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
    include '../../../../templates/footer.php';
?>
