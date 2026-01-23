<?php
    include_once "../../../templates/header.php";
?>

<script>
    const menu = "no";
    const rol = "5";
    generateHeaderNav(menu, rol);
</script>

<main class="container mt-5">
    <h2 class="text-center mb-4">Listado de usuarios</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle text-center tabla-cabecera">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="usuariosTableBody">

            </tbody>
        </table>
    </div>
    <div class="mt-5 container-fluid text-end">
        <a href="../menuadministrador.php" class="volver p-2 px-4 text-dark">Volver al menú de administrador</a>
    </div>

     <button type="button" class="open-modal btn btn-primary bg-verde text-white" data-open="modal1">
                    <i class="bi bi-pencil-square"></i> Editar
                </button>	


<!-- Modal -->
<div class="modal" id="modal1">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h2 class="modal-title">Editar usuario</h2>
        <button class="close-modal" aria-label="Cerrar modal" data-close="modal1">✕</button>
      </div>

      <!-- Body / Formulario -->
      <div class="modal-body">
        <form class="row g-4">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" placeholder="Nombre">
          </div>

          <div class="col-md-6">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" placeholder="Correo Electrónico">
          </div>

          <div class="col-md-6">
            <label class="form-label">Rol *</label>
            <select class="form-select" required>
              <option value="" selected disabled>Seleccionar Rol</option>
              <option value="1">Comun</option>
              <option value="2">Administrador</option>
              <option value="3">Superadministrador</option>
            </select>
          </div>

          <div class="col-md-6"></div>

          <div class="col-md-6">
            <label class="form-label">Nueva Contraseña (opcional)</label>
            <input type="password" class="form-control" placeholder="Nueva Contraseña">
          </div>

          <div class="col-md-6">
            <label class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" placeholder="Confirmar Contraseña">
          </div>

          <div class="col-md-6">
            <button type="submit" class="enviar w-100 text-light p-2">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>








</main>
<script>
(async () => {
    const response = await getUsuarios();
    imprimirUsuariosEnTabla(response);
})();


function imprimirUsuariosEnTabla(usuarios) {
    const tbody = document.getElementById("usuariosTableBody");
    tbody.innerHTML = ""; // Limpiar tabla

    if (!Array.isArray(usuarios)) {
        console.error("Usuarios no es un array", usuarios);
        return;
    }

    usuarios.forEach(usuario => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td>${usuario.id_usuario}</td>
            <td>${usuario.nombre}</td>
            <td>
                <span class="badge estado border rounded-pill ${usuario.rol}">
                    ${usuario.rol}
                </span>
            </td>
            <td>${usuario.correo}</td>
            <td>
                <button type="button" class="open-modal btn btn-primary bg-verde text-white" data-open="modal1">
                    <i class="bi bi-pencil-square"></i> Editar
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    });
}


const openEls = document.querySelectorAll("[data-open]");
const isVisible = "is-visible";

for(const el of openEls) {
  el.addEventListener("click", function() {
    const modalId = this.dataset.open;
    document.getElementById(modalId).classList.add(isVisible);
  });
}

const closeEls = document.querySelectorAll("[data-close]");

for (const el of closeEls) {
  el.addEventListener("click", function() {
    this.parentElement.parentElement.parentElement.parentElement.classList.remove(isVisible);
  });
}

document.addEventListener("click", e => {
  if (e.target == document.querySelector(".modal.is-visible")) {
    document.querySelector(".modal.is-visible").classList.remove(isVisible);
  }
});


document.addEventListener("keyup", e => {
  if (e.key == "Escape" && document.querySelector(".modal.is-visible")) {
    document.querySelector(".modal.is-visible").classList.remove(isVisible);
  }
});


</script>


<?php
    include '../../../templates/footer.php';
?>
