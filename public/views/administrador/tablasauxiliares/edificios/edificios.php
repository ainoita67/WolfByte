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
        <a href="../tablasauxiliares.php" class="volver p-2 px-4 text-dark">Volver al menú de tablas auxiliares</a>
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
<!-- MODAL EDITAR (CARD POPUP) -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 bg-transparent">

      <div class="card shadow-lg animate-popup">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Editar edificio</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="card-body">
          <form id="formEditar">
            <input type="hidden" id="editId">

            <div class="mb-3">
              <label for="editNombre" class="form-label">Nombre del edificio</label>
              <input type="text" id="editNombre" class="form-control" required>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary">
                Guardar cambios
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>


<?php
    include '../../../../templates/footer.php';
?>
