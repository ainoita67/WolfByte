<?php
    $title='Mis datos';
    $directorio='../../';
    $ruta='misdatos';
    $seccion='';

    $style='';
    include '../../../templates/header.php';
?>

<main class="container mt-5">
    <h2 class="text-center mb-4">Listado de usuarios</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle text-center tabla-cabecera">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Email</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Juan Pérez</td>
                    <td>Administrador</td>
                    <td>juan@correo.com</td>
                    <td>
                        <span class="badge estado border rounded-pill">Activo</span>
                    </td>
                </tr>
                <tr>
                    <td>María López</td>
                    <td>Superadministrador</td>
                    <td>maria@correo.com</td>
                    <td>
                        <span class="badge estado border rounded-pill">Activo</span>
                    </td>
                </tr>
                <tr>
                    <td>Carlos Gómez</td>
                    <td>Departamento</td>
                    <td>carlos@correo.com</td>
                    <td>
                        <span class="badge estado border rounded-pill">Inactivo</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-5 container-fluid text-end">
        <a href="../menuadministrador.php" class="volver p-2 px-4 text-dark">Volver al menú de administrador</a>
    </div>
</main>
<script>
    document.querySelectorAll(".estado").forEach(td=>{
        const texto=td.textContent.trim().toLowerCase();

        td.classList.remove("activo", "inactivo");

        if(texto==="activo"){
            td.classList.add("activo");
        }else{
            td.classList.add("inactivo");
        }
    });
</script>

<?php
    include '../../../templates/footer.php';
?>
