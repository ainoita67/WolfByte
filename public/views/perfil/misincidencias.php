<?php
    $title='Mis incidencias';
    $directorio='../';
    $ruta='misincidencias';
    $seccion='';
    $style='<link rel="stylesheet" href="'.$directorio.'../assets/css/perfil.css">';
    include '../../templates/header.php';
?>

<main class="container mt-5">
    <h2 class="text-center mb-4">Mis incidencias</h2>
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center" id="tabla-incidencias">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Espacio</th>
                    <th>Fecha</th>
                    <th class="d-none d-md-table-cell">Hora</th>
                    <th class="d-none d-md-table-cell">Profesor</th>
                    <th class="d-none d-md-table-cell">Grupo</th>
                    <th class="d-none d-md-table-cell">Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>1</td>
                    <td class="espacio">Aula 101</td>
                    <td class="fecha">12/02/2026</td>
                    <td class="hora d-none d-md-table-cell">08:30 - 09:25</td>
                    <td class="profesor d-none d-md-table-cell">Juan Pérez</td>
                    <td class="grupo d-none d-md-table-cell">2º ESO A</td>
                    <td class="estado d-none d-md-table-cell">
                        <span class="badge estadobadge">Confirmada</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-ver"
                            data-bs-toggle="modal"
                            data-bs-target="#modalReserva">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>2</td>
                    <td class="espacio">Salón de actos</td>
                    <td class="fecha">13/02/2026</td>
                    <td class="hora d-none d-md-table-cell">10:20 - 11:15</td>
                    <td class="profesor d-none d-md-table-cell">María López</td>
                    <td class="grupo d-none d-md-table-cell">1º Bachillerato</td>
                    <td class="estado d-none d-md-table-cell">
                        <span class="badge estadobadge">Pendiente</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-ver"
                            data-bs-toggle="modal"
                            data-bs-target="#modalReserva">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>3</td>
                    <td class="espacio">Salón de actos</td>
                    <td class="fecha">13/02/2026</td>
                    <td class="hora d-none d-md-table-cell">10:20 - 11:15</td>
                    <td class="profesor d-none d-md-table-cell">María López</td>
                    <td class="grupo d-none d-md-table-cell">1º Bachillerato</td>
                    <td class="estado d-none d-md-table-cell">
                        <span class="badge estadobadge">Cancelada</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-ver"
                            data-bs-toggle="modal"
                            data-bs-target="#modalReserva">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- MODAL -->
    <div class="modal fade" id="modalReserva" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de la reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Espacio:</strong> <span id="mEspacio"></span></li>
                        <li class="list-group-item"><strong>Fecha:</strong> <span id="mFecha"></span></li>
                        <li class="list-group-item"><strong>Hora:</strong> <span id="mHora"></span></li>
                        <li class="list-group-item"><strong>Profesor:</strong> <span id="mProfesor"></span></li>
                        <li class="list-group-item"><strong>Grupo:</strong> <span id="mGrupo"></span></li>
                        <li class="list-group-item"><strong>Estado:</strong> <span id="mEstado"></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5 container-fluid text-end">
        <a href="../menu.php" class="volver p-2 px-4 text-dark">Volver al menú principal</a>
    </div>
</main>

<?php
    include '../../templates/footer.php';
?>