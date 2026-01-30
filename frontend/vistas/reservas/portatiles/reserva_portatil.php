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
    <h2 class="mb-4">Carros de portátiles</h2>

    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle text-center tabla-cabecera">
        <thead>
          <tr>
            <th>Carro</th>
            <th>Edificio</th>
            <th>Planta</th>
            <th>Nº portátiles</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Carro 1</td>
            <td>RAM</td>
            <td>Planta baja</td>
            <td>25</td>
            <td>Disponible</td>
            <td>
              <button class="btn bg-azul text-light btn-reservar"
                data-bs-toggle="modal" data-bs-target="#modalReserva"
                data-carro="Carro 1" data-edificio="RAM"
                data-planta="Planta baja" data-portatiles="25">
                Reservar
              </button>
            </td>
          </tr>
          <tr>
            <td>Carro 2</td>
            <td>RAM</td>
            <td>Primera planta</td>
            <td>25</td>
            <td>Disponible</td>
            <td>
              <button class="btn bg-azul text-light btn-reservar"
                data-bs-toggle="modal" data-bs-target="#modalReserva"
                data-carro="Carro 2" data-edificio="RAM"
                data-planta="Primera planta" data-portatiles="25">
                Reservar
              </button>
            </td>
          </tr>
          <tr>
            <td>Carro 3</td>
            <td>Loscos</td>
            <td>Planta baja</td>
            <td>25</td>
            <td>Disponible</td>
            <td>
              <button class="btn bg-azul text-light btn-reservar"
                data-bs-toggle="modal" data-bs-target="#modalReserva"
                data-carro="Carro 3" data-edificio="Loscos"
                data-planta="Planta baja" data-portatiles="25">
                Reservar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 text-end">
      <a href="/public/views/reservas/portatiles/portatiles.php" class="volver p-2 px-4 text-dark">Volver a portátiles</a>
    </div>
  </div>
</main>

<!-- MODAL RESERVA -->
<div class="modal fade mt-5" id="modalReserva" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reservar carro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form class="row px-2">

          <div class="mb-3 col-6 col-lg-4">
            <label>Carro</label>
            <input type="text" id="m_carro" class="form-control" readonly>
          </div>
          <div class="mb-3 col-6 col-lg-4">
            <label>Edificio</label>
            <input type="text" id="m_edificio" class="form-control" readonly>
          </div>
          <div class="mb-3 col-6 col-lg-4">
            <label>Planta</label>
            <input type="text" id="m_planta" class="form-control" readonly>
          </div>
          <div class="mb-3 col-6 col-lg-4">
            <label>Nº portátiles</label>
            <input type="number" id="m_portatiles" class="form-control" readonly>
          </div>

          <div class="mb-3 col-6 col-lg-4">
            <label>Fecha</label>
            <input type="date" class="form-control" name="fecha" required>
          </div>
          <div class="mb-3 col-6 col-lg-4">
            <label>Hora inicio</label>
            <select class="form-control" name="horainicio" required>
              <option selected disabled>Seleccionar</option>
              <option>8:50</option><option>9:45</option><option>10:40</option>
              <option>12:00</option><option>12:55</option><option>13:50</option>
            </select>
          </div>
          <div class="mb-3 col-6 col-lg-4">
            <label>Hora fin</label>
            <select class="form-control" name="horafin" required>
              <option selected disabled>Seleccionar</option>
              <option>9:40</option><option>10:35</option><option>11:30</option>
              <option>12:50</option><option>13:45</option><option>14:40</option>
            </select>
          </div>

          <div class="row mt-4 text-center d-flex justify-content-center">
            <button type="submit" class="btn enviar text-light col-5 mx-3 mb-3">Confirmar</button>
            <button type="button" class="btn bg-lightgrey col-5 mx-3 mb-3 text-black" data-bs-dismiss="modal">Cancelar</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll(".btn-reservar").forEach(boton => {
  boton.addEventListener("click", function () {
    document.getElementById("m_carro").value = this.dataset.carro;
    document.getElementById("m_edificio").value = this.dataset.edificio;
    document.getElementById("m_planta").value = this.dataset.planta;
    document.getElementById("m_portatiles").value = this.dataset.portatiles;
  });
});
</script>

<?php
include '../../../templates/footer.php';
?>
