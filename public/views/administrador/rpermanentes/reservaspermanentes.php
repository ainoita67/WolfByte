<?php
    $title='Reservas permanentes';
    $directorio='../';
    $ruta='reservaspermanentes';
    $seccion='';
    $style='';
    include '../../templates/header.php';
?>

<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Reservas permanentes</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-plus-circle"></i> Crear Reserva
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle tabla-cabecera">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Unidades</th>
                    <th>Días reservados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Proyector Epson</td>
                    <td>Proyectores</td>
                    <td>5</td>
                    <td>
                        <span class="badge bg-primary">Lunes</span>
                        <span class="badge bg-primary">Miércoles</span>
                        <span class="badge bg-primary">Viernes</span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>Portátil HP</td>
                    <td>Ordenadores</td>
                    <td>10</td>
                    <td>
                        <span class="badge bg-primary">Martes</span>
                        <span class="badge bg-primary">Jueves</span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-5 container-fluid text-end">
        <a href="../menuadministrador.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
    </div>
</main>

<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content ">

      <div class="modal-header">
        <h5 class="modal-title">Crear Reserva Permanente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Categoría</label>
              <select class="form-select" required>
                <option value="">Selecciona</option>
                <option>Proyectores</option>
                <option>Ordenadores</option>
                <option>Sonido</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Unidades</label>
              <input type="number" class="form-control" min="1" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Días reservados</label>
              <div class="d-flex flex-wrap gap-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="Lunes" id="lunes">
                  <label class="form-check-label" for="lunes">Lunes</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="Martes" id="martes">
                  <label class="form-check-label" for="martes">Martes</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="Miércoles" id="miercoles">
                  <label class="form-check-label" for="miercoles">Miércoles</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="Jueves" id="jueves">
                  <label class="form-check-label" for="jueves">Jueves</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="Viernes" id="viernes">
                  <label class="form-check-label" for="viernes">Viernes</label>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-success w-50">
              <i class="bi bi-check-circle"></i> Guardar
            </button>
            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">
              Cancelar
            </button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<?php
    include '../../templates/footer.php';
?>