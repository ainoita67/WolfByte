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

    <p id="jsonData"></p>

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
                <span class="badge estado border rounded-pill">Editar</span>
                <span class="badge estado border rounded-pill">Eliminar</span>
            </td>
        `;

        tbody.appendChild(tr);
    });
}




</script>


<?php
    include '../../../templates/footer.php';
?>
