<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../../assets/css/style.css">
        <link rel="stylesheet" href="../../assets/css/usuario.css">
        <title>IES Bajo Aragón</title>
    </head>
    <body class="container-fluid menu">
        <header>
            <nav class="row" id="menuordenador">
                <ul class="col-12 d-none d-lg-grid text-center fs-5 pt-3">

                    <li class="col-12"><a href="../menuadministrador.html"><img src="../../assets/imagenes/ieslogo.png" alt="Logo"></a></li>

                    <li></li>
                    <li class="pt-5 pb-5 d-none d-lg-block ms-5"><a href="#">Aulas</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Salón de actos</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Material</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Otros espacios</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Incidencias</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Liberar aula</a></li>
                    <li class="list-group-item pt-5 pb-5 me-5 d-none d-lg-block" id="perfil"><a href="#" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle fs-1"></i></a>
                        <ul class="dropdown-menu" data-target="#menudesplegable">
                            <a href="../perfil/datos.html"><li class="dropdown-item">Mis datos</li></a>
                            <a href="../perfil/reserva.html"><li class="dropdown-item">Mis reservas</li></a>
                            <a href="../perfil/misincidencias.html"><li class="dropdown-item">Mis incidencias</li></a>
                            <a href="../../../auth/logout.php"><li class="dropdown-item">Cerrar sesión</li></a>
                        </ul>
                    </li>
                </ul>
            </nav>
            <nav class="row" id="menumovil">
                <ul class="col-12 d-flex d-lg-none text-center fs-5 pt-3">
                    <li class="col-2"><a href="../menuadministrador.html"><img src="../../assets/imagenes/ieslogo.png" alt="Logo"></a></li>
                    <li class="offset-6 offset-sm-7"></li>
                    <li class="mx-3 list-group-item pt-5 pb-5 d-lg-none ms-5" id="perfil"><a href="#" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-list fs-1"></i></a>
                        <ul class="dropdown-menu" data-target="#menudesplegable" class="bg-light">
                            <a href="#"><li class="dropdown-item bg-light">Aulas</li></a>
                            <a href="#"><li class="dropdown-item bg-light">Salón de actos</li></a>
                            <a href="#"><li class="dropdown-item bg-light">Material</li></a>
                            <a href="#"><li class="dropdown-item bg-light">Otros espacios</li></a>
                            <a href="#"><li class="dropdown-item bg-light">Incidencias</li></a>
                            <a href="#"><li class="dropdown-item bg-light">Liberar aula</li></a>
                        </ul>
                    </li>
                    <li class="list-group-item pt-5 pb-5 d-lg-none" id="perfil"><a href="#" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle fs-1"></i></a>
                        <ul class="dropdown-menu" data-target="#menudesplegable" class="bg-light">
                            <a href="../perfil/datos.html"><li class="dropdown-item bg-light">Mis datos</li></a>
                            <a href="../perfil/reserva.html"><li class="dropdown-item bg-light">Mis reservas</li></a>
                            <a href="../perfil/misincidencias.html"><li class="dropdown-item bg-light">Mis incidencias</li></a>
                            <a href="../../../auth/logout.php"><li class="dropdown-item bg-light">Cerrar sesión</li></a>
                        </ul>
                    </li>
                </ul>
            </nav>
        </header>
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
                <a href="../menuadministrador.html" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
            </div>
        </main>
        <script>
            document.querySelectorAll(".estado").forEach(td => {
                const texto = td.textContent.trim().toLowerCase();

                td.classList.remove("activo", "inactivo");

                if (texto === "activo") {
                    td.classList.add("activo");
                } else {
                    td.classList.add("inactivo");
                }
            });
        </script>
        
    </body>
</html>