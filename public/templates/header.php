<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <?php
            echo '<link rel="shortcut icon" href="'.$directorio.'../assets/imagenes/favicon.png">';
            echo '<link rel="stylesheet" href="'.$directorio.'../assets/css/style.css">';
            echo $style;
            echo '<title>'.$title.'</title>';
        ?>
    </head>
    <body class="container-fluid menu">
        <header>
            <?php
                if($ruta=='login'){
                    echo '<nav class="row d-none d-lg-grid">';
                        echo '<ul class="col-7 d-none d-lg-grid text-center fs-5 pt-3 pe-1">';
                            echo '<li class="col-12 ps-2 pe-5"><a href="#"><img src="'.$directorio.'../assets/imagenes/ieslogo.png" alt="Logo"></a></li>';
                            echo '<li></li>';
                        echo '</ul>';
                    echo '</nav>';
                    echo '<nav class="row mt-0 d-lg-none" id="menumovillogin">';
                        echo '<ul class="col-7 d-grid d-lg-none text-center fs-5 pt-2">';
                            echo '<li class="col-12 ps-2 pe-5"><a href="#"><img src="'.$directorio.'../assets/imagenes/ieslogo.png" alt="Logo"></a></li>';
                            echo '<li></li>';
                        echo '</ul>';
                    echo '</nav>';
                }else{
                    include $directorio.'../templates/menu.php';
                }
            ?>
        </header>