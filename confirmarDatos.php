<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Modificar datos: Pista Deportiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="ico.png">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Proyecto Integrado - Adrián Fdz.</h2>
        <hr>
        <h1>Reservas Pista Deportiva</h1>
        <hr>
    <div><h3>Paso de confirmación modificación de datos</h3></div>
    <form action="procesaDatos.php" method="POST" class='form'>
        <label for="fecha">¿Deseas realizar los siguientes cambios?</label>
        <?php
            // Leemos los datos antiguos
            $nombreAntiguo = $_POST['name'];
            $apellidosAntiguo = $_POST['surname'];
            $emailAntiguo = $_POST['mail'];
            // Leemos los nuevos datos
            $nuevoNombre = $_POST['nombre'];
            $nuevoApellidos = $_POST['apellidos'];
            $nuevoEmail = $_POST['email'];

            echo "<div>
                 <h3 style='color: red;'>Datos antiguos:</h3>
                 <span>Nombre: <b>$nombreAntiguo</b></span><br>
                 <span>Apellidos: <b>$apellidosAntiguo</b></span><br>
                 <span>Email: <b>$emailAntiguo</b></span><br>
                 
                 <h3 style='color: green;'>Actualizar por los nuevos datos:</h3>
                 <span>Nombre: <b>$nuevoNombre</b></span><br>
                 <span>Apellidos: <b>$nuevoApellidos</b></span><br>
                 <span>Email: <b>$nuevoEmail</b></span><br>
                 </div>";

            echo "<input type='hidden' name='nuevoNombre' value='".htmlentities($nuevoNombre)."'>";
            echo "<input type='hidden' name='nuevoApellidos' value='".htmlentities($nuevoApellidos)."'>";
            echo "<input type='hidden' name='nuevoEmail' value='".htmlentities($nuevoEmail)."'>";
            
            echo "<br><br><p><b>Pincha en confirmar para actualizar tus datos.</b></p>
                <p>(Recibirás un email indicando que se ha realizado una modificación en tus datos).</p>";
        ?>
        <div><button type="submit" name="submit">Confirmar</button>
    </form>
        <div>
            <a href="modificarDatos.php" class="button-link">Volver atrás y corregir tus datos</a>
        </div>
    </div>
    </div>
<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>