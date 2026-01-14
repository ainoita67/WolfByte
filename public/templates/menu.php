<nav class="row">
    <ul class="col-12 d-none d-lg-grid text-center fs-5 pt-3">
        <?php
            if($ruta=='menu'){
                echo '<li class="col-12"><a href="#"><img src="'.$directorio.'../assets/imagenes/ieslogo.png" alt="Logo"></a></li>';
            }else{
                echo '<li class="col-12"><a href="'.$directorio.'menu.php"><img src="'.$directorio.'../assets/imagenes/ieslogo.png" alt="Logo"></a></li>';
            }
        ?>
        <li></li>
        <?php
            if($ruta=='aulas'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block ms-5"><a href="#" class="text-dark">Aulas</a></li>';
            }else if($seccion=='aulas'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block ms-5"><a href="'.$directorio.'aulas/aulas.php" class="text-dark">Aulas</a></li>';
            }else{
                echo '<li class="pt-5 pb-5 d-none d-lg-block ms-5"><a href="'.$directorio.'aulas/aulas.php">Aulas</a></li>';
            }

            if($ruta=='salonactos'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="#" class="text-dark">Salón de actos</a></li>';
            }else if($seccion=='salonactos'){
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'salonactos/salonactos.php" class="text-dark">Salón de actos</a></li>';
            }else{
                echo '<li class="pt-5 pb-5 d-none d-lg-block"><a href="'.$directorio.'salonactos/salonactos.php">Salón de actos</a></li>';
            }

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
            echo '<li class="list-group-item pt-5 pb-5 d-none d-lg-block" id="perfil"><a href="'.$directorio.'" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle fs-1"></i></a>';
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
                        echo '<a href="'.$directorio.'perfil/incidencias.php"><li class="dropdown-item">Mis incidencias</li></a>';
                    }
                    echo '<a href="'.$directorio.'../../auth/logout.php"><li class="dropdown-item">Cerrar sesión</li></a>';
                echo '</ul>';
            echo '</li>';
        ?>
    </ul>
</nav>

<!--MENÚ MÓVIL-->
<nav class="row" id="menumovil">
    <ul class="col-12 d-flex d-lg-none text-center fs-5 pt-3 ps-4">
        <?php
            if($ruta=='menu'){
                echo '<li class="col-2"><a href="#"><img src="'.$directorio.'../assets/imagenes/ieslogo.png" alt="Logo"></a></li>';
            }else{
                echo '<li class="col-2"><a href="'.$directorio.'menu.php"><img src="'.$directorio.'../assets/imagenes/ieslogo.png" alt="Logo"></a></li>';
            }
        ?>
        <li class="offset-6 offset-sm-7"></li>
        <?php
            //MENÚ DESPLEGABLE MÓVIL
            echo '<li class="mx-3 list-group-item pt-5 pb-5 d-lg-none ms-5" id="perfil"><a href="'.$directorio.'" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-list fs-1"></i></a>';
                echo '<ul class="dropdown-menu" data-target="#menudesplegable">';
                    echo '<a href="'.$directorio.'aulas/aulas.php"><li class="dropdown-item">Aulas</li></a>';
                    echo '<a href="'.$directorio.'salonactos/salonactos.php"><li class="dropdown-item">Salón de actos</li></a>';
                    echo '<a href="'.$directorio.'material/material.php"><li class="dropdown-item">Material</li></a>';
                    echo '<a href="'.$directorio.'espacios/espacios.php"><li class="dropdown-item">Otros espacios</li></a>';
                    echo '<a href="'.$directorio.'incidencias/incidencias.php"><li class="dropdown-item">Incidencias</li></a>';
                    echo '<a href="'.$directorio.'liberar/liberar.php"><li class="dropdown-item">Liberar aula</li></a>';
                echo '</ul>';
            echo '</li>';

            //MENÚ DESPLEGABLE PERFIL
            echo '<li class="list-group-item pt-5 pb-5 d-lg-none" id="perfil"><a href="'.$directorio.'" id="menudesplegable" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle fs-1"></i></a>';
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
                        echo '<a href="'.$directorio.'perfil/incidencias.php"><li class="dropdown-item">Mis incidencias</li></a>';
                    }
                    echo '<a href="'.$directorio.'"><li class="dropdown-item">Cerrar sesión</li></a>';
                echo '</ul>';
            echo '</li>';
        ?>
    </ul>
</nav>