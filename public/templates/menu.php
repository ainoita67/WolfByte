<!-- MENÚ ESCRITORIO -->
<nav class="row">
    <ul class="col-12 d-none d-lg-grid text-center fs-5 pt-3">
        <!-- Logo -->
        <li class="col-12">
            <a href="<?= $ruta == 'menu' ? '#' : $directorio.'menu.php' ?>">
                <img src="<?= $directorio ?>../assets/imagenes/ieslogo.png" alt="Logo">
            </a>
        </li>
        <li></li>

        <!-- Enlaces del menú -->
        <li class="pt-5 pb-5 d-none d-lg-block ms-5">
            <a href="<?= $ruta=='aulas' || $seccion=='aulas' ? '#' : $directorio.'aulas/aulas.php' ?>" class="<?= $ruta=='aulas' || $seccion=='aulas' ? 'text-dark' : '' ?>">Aulas</a>
        </li>
        <li class="pt-5 pb-5 d-none d-lg-block ms-5">
            <a href="<?= $ruta=='salonactos' || $seccion=='salonactos' ? '#' : $directorio.'salonactos/salonactos.php' ?>" class="<?= $ruta=='salonactos' || $seccion=='salonactos' ? 'text-dark' : '' ?>">Salón de actos</a>
        </li>
        <li class="pt-5 pb-5 d-none d-lg-block ms-5">
            <a href="<?= $ruta=='material' || $seccion=='material' ? '#' : $directorio.'material/material.php' ?>" class="<?= $ruta=='material' || $seccion=='material' ? 'text-dark' : '' ?>">Material</a>
        </li>
        <li class="pt-5 pb-5 d-none d-lg-block ms-5">
            <a href="<?= $ruta=='espacios' || $seccion=='espacios' ? '#' : $directorio.'espacios/espacios.php' ?>" class="<?= $ruta=='espacios' || $seccion=='espacios' ? 'text-dark' : '' ?>">Otros espacios</a>
        </li>
        <li class="pt-5 pb-5 d-none d-lg-block ms-5">
            <a href="<?= $ruta=='incidencias' || $seccion=='incidencias' ? '#' : $directorio.'incidencias/incidencias.php' ?>" class="<?= $ruta=='incidencias' || $seccion=='incidencias' ? 'text-dark' : '' ?>">Incidencias</a>
        </li>
        <li class="pt-5 pb-5 d-none d-lg-block ms-5">
            <a href="<?= $ruta=='liberar' || $seccion=='liberar' ? '#' : $directorio.'liberar/liberar.php' ?>" class="<?= $ruta=='liberar' || $seccion=='liberar' ? 'text-dark' : '' ?>">Liberar aula</a>
        </li>

<<<<<<< HEAD
            if($ruta=='material'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="#" class="text-dark">Material</a></li>';
            }else if($seccion=='material'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'material/material.php" class="text-dark">Material</a></li>';
            }else{
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'material/material.php">Material</a></li>';
            }

            if($ruta=='espacios'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="#" class="text-dark">Otros espacios</a></li>';
            }else if($seccion=='espacios'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'espacios/espacios.php" class="text-dark">Otros espacios</a></li>';
            }else{
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'espacios/espacios.php">Otros espacios</a></li>';
            }

            if($ruta=='incidencias'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="#" class="text-dark">Incidencias</a></li>';
            }else if($seccion=='incidencias'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'incidencias/incidencias.php" class="text-dark">Incidencias</a></li>';
            }else{
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'incidencias/incidencias.php">Incidencias</a></li>';
            }

            if($ruta=='liberar'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="#" class="text-dark">Liberar aula</a></li>';
            }else if($seccion=='liberar'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'liberar/liberar.php" class="text-dark">Liberar aula</a></li>';
            }else{
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'liberar/liberar.php">Liberar aula</a></li>';
            }

            // MENÚ DESPLEGABLE PERFIL
            echo '<li class="list-group-item pt-5 pb-5 d-none d-lg-block" id="perfil"><i class="bi bi-person-circle fs-1"></i>';
                echo '<ul class="dropdown-menu" data-target="#menudesplegable">';
                    if($ruta=='misdatos'){
                        echo '<a href="#"><li class="dropdown-item bg-lightgrey">Mis datos</li></a>';
                    }else{
                        echo '<a href="'.$directorio.'perfil/datos.php"><li class="dropdown-item">Mis datos</li></a>';
                    }
                
                    if($ruta=='misreservas'){
                        echo '<a href="#"><li class="dropdown-item bg-lightgrey">Mis reservas</li></a>';
                    }else{
                        echo '<a href="'.$directorio.'perfil/reserva.php"><li class="dropdown-item">Mis reservas</li></a>';
                    }

                    if($ruta=='misincidencias'){
                        echo '<a href="#"><li class="dropdown-item bg-lightgrey">Mis incidencias</li></a>';
                    }else{
                        echo '<a href="'.$directorio.'perfil/misincidencias.php"><li class="dropdown-item">Mis incidencias</li></a>';
                    }
                    echo '<a href="'.$directorio.'../auth/logout.php"><li class="dropdown-item">Cerrar sesión</li></a>';
                echo '</ul>';
            echo '</li>';
        ?>
=======
        <!-- Menú desplegable Perfil -->
        <li class="list-group-item pt-5 pb-5 d-none d-lg-block" id="perfil">
            <a href="#" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-1"></i>
            </a>
            <ul class="dropdown-menu" data-target="#menudesplegable">
                <li class="dropdown-item">
                    <a href="/perfil/datos.php">Mis datos</a>
                </li>
                <li class="dropdown-item">
                    <a href="/perfil/reserva.php"> Mis reservas</a>
                </li>
                <li class="dropdown-item">
                    <a href="/perfil/incidencias.php"> Mis incidencias</a>
                </li>
                <li class="dropdown-item">
                    <a href="/auth/logout.php"> Cerrar sesión</a>
                </li>
            </ul>
        </li>
>>>>>>> main
    </ul>
</nav>

<!-- MENÚ MÓVIL -->
<nav class="row" id="menumovil">
    <ul class="col-12 d-flex d-lg-none text-center fs-5 pt-3 ps-4">
        <!-- Logo móvil -->
        <li class="col-2">
            <a href="<?= $ruta == 'menu' ? '#' : $directorio.'menu.php' ?>">
                <img src="<?= $directorio ?>../assets/imagenes/ieslogo.png" alt="Logo">
            </a>
        </li>
        <li class="offset-6 offset-sm-7"></li>

        <!-- Menú hamburguesa -->
        <li class="mx-3 list-group-item pt-5 pb-5 d-lg-none ms-5" id="perfil">
            <a href="<?= $directorio ?>" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-list fs-1"></i>
            </a>
            <ul class="dropdown-menu" data-target="#menudesplegable">
                <a href="<?= $directorio.'aulas/aulas.php' ?>"><li class="dropdown-item">Aulas</li></a>
                <a href="<?= $directorio.'salonactos/salonactos.php' ?>"><li class="dropdown-item">Salón de actos</li></a>
                <a href="<?= $directorio.'material/material.php' ?>"><li class="dropdown-item">Material</li></a>
                <a href="<?= $directorio.'espacios/espacios.php' ?>"><li class="dropdown-item">Otros espacios</li></a>
                <a href="<?= $directorio.'incidencias/incidencias.php' ?>"><li class="dropdown-item">Incidencias</li></a>
                <a href="<?= $directorio.'liberar/liberar.php' ?>"><li class="dropdown-item">Liberar aula</li></a>
            </ul>
        </li>

<<<<<<< HEAD
                    if($ruta=='misincidencias'){
                        echo '<a href="#"><li class="dropdown-item bg-lightgrey">Mis incidencias</li></a>';
                    }else{
                        echo '<a href="'.$directorio.'perfil/misincidencias.php"><li class="dropdown-item">Mis incidencias</li></a>';
                    }
                    echo '<a href="'.$directorio.'../auth/logout.php"><li class="dropdown-item">Cerrar sesión</li></a>';
                echo '</ul>';
            echo '</li>';
        ?>
=======
        <!-- Menú desplegable Perfil móvil -->
        <li class="list-group-item pt-5 pb-5 d-lg-none" id="perfil">
            <a href="<?= $directorio ?>" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-1"></i>
            </a>
            <ul class="dropdown-menu" data-target="#menudesplegable">
                <a href="<?= $ruta=='misdatos' ? '#' : $directorio.'perfil/datos.php' ?>"><li class="dropdown-item <?= $ruta=='misdatos'?'bg-lightgrey':'' ?>">Mis datos</li></a>
                <a href="<?= $ruta=='misreservas' ? '#' : $directorio.'perfil/reserva.php' ?>"><li class="dropdown-item <?= $ruta=='misreservas'?'bg-lightgrey':'' ?>">Mis reservas</li></a>
                <a href="<?= $ruta=='misincidencias' ? '#' : $directorio.'perfil/incidencias.php' ?>"><li class="dropdown-item <?= $ruta=='misincidencias'?'bg-lightgrey':'' ?>">Mis incidencias</li></a>
                <a href="<?= $directorio ?>"><li class="dropdown-item">Cerrar sesión</li></a>
            </ul>
        </li>
>>>>>>> main
    </ul>
</nav>
