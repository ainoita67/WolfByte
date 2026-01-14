<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
                    <li class="col-12"><a href="../menu.html"><img src="../../assets/imagenes/ieslogo.png" alt="Logo"></a></li>
                    <li></li>
                    <li class="pt-5 pb-5 d-none d-lg-block ms-5"><a href="#">Aulas</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Salón de actos</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Material</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Otros espacios</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Incidencias</a></li>
                    <li class="pt-5 pb-5 d-none d-lg-block"><a href="#">Liberar aula</a></li>
                    <li class="list-group-item pt-5 pb-5 me-5 d-none d-lg-block" id="perfil"><a href="#" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle fs-1"></i></a>
                        <ul class="dropdown-menu" data-target="#menudesplegable">
                            <a href="perfil/datos.html"><li class="dropdown-item">Mis datos</li></a>
                            <a href="perfil/reserva.html"><li class="dropdown-item">Mis reservas</li></a>
                            <a href="#"><li class="dropdown-item">Mis incidencias</li></a>
                            <a href="#"><li class="dropdown-item">Cerrar sesión</li></a>
                        </ul>
                    </li>
                </ul>
            </nav>
            <nav class="row" id="menumovil">
                <ul class="col-12 d-flex d-lg-none text-center fs-5 pt-3">
                    <li class="col-2"><a href="../menu.html"><img src="../../assets/imagenes/ieslogo.png" alt="Logo"></a></li>
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
                            <a href="perfil/datos.html"><li class="dropdown-item bg-light">Mis datos</li></a>
                            <a href="perfil/reserva.html"><li class="dropdown-item bg-light">Mis reservas</li></a>
                            <a href="#"><li class="dropdown-item bg-light">Mis incidencias</li></a>
                            <a href="#"><li class="dropdown-item bg-light">Cerrar sesión</li></a>
                        </ul>
                    </li>
                </ul>
            </nav>
        </header>

        <main class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gestión de Aulas y Espacios</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
                    <i class="bi bi-plus-circle"></i> Crear aula
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle tabla-cabecera">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Aula 101</td>
                            <td>Aula</td>
                            <td>30</td>
                            <td>Disponible</td>
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
                            <td>Salón de actos</td>
                            <td>Espacio</td>
                            <td>200</td>
                            <td>Ocupado</td>
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
                <a href="../menuadministrador.html" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
            </div>

        </main>

        <div class="modal fade" id="modalCrear" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear aula/espacio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Tipo</label>
                        <select class="form-control">
                        <option>Aula</option>
                        <option>Espacio</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Capacidad</label>
                        <input type="number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select class="form-control">
                        <option>Disponible</option>
                        <option>Ocupado</option>
                        </select>
                    </div>
                    <button class="btn btn-success w-100">Guardar</button>
                    </form>
                </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditar" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar aula/espacio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" class="form-control" id="editNombre">
                    </div>
                    <div class="mb-3">
                        <label>Tipo</label>
                        <select class="form-control" id="editTipo">
                        <option>Aula</option>
                        <option>Espacio</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Capacidad</label>
                        <input type="number" class="form-control" id="editCapacidad">
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select class="form-control" id="editEstado">
                        <option>Disponible</option>
                        <option>Ocupado</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Guardar cambios</button>
                    </form>
                </div>
                </div>
                    </div>
            </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

        <script>
            document.querySelectorAll(".btn-editar").forEach(boton => {
                boton.addEventListener("click", function () {
                    const fila = this.closest("tr");
                    const celdas = fila.querySelectorAll("td");

                    document.getElementById("editNombre").value = celdas[0].textContent.trim();
                    document.getElementById("editTipo").value = celdas[1].textContent.trim();
                    document.getElementById("editCapacidad").value = celdas[2].textContent.trim();
                    document.getElementById("editEstado").value = celdas[3].textContent.trim();
                });
            });
        </script>


    </body>
</html>