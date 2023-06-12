<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Reservas: Pista Deportiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="ico.png">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="container">
            <h2>Proyecto Integrado - Adrián Fdz.</h2>
            <!--<h2 style="color: red;">PÁGINA EN CONSTRUCCIÓN</h2>-->
            <hr>
            <h1>Reservas Pista Deportiva</h1>
            <hr>
    <div><h3>Paso de cancelación de reserva</h3></div>
    <form action="procesaCancelar.php" method="POST" class='form'>
        <label for="fecha">¿Deseas cancelar la siguiente reserva?</label>
        <?php
            $confirmarCancelar = $_POST['cancelareserva'];

            $fechaVisual = date('d \d\e M \d\e Y', strtotime($confirmarCancelar));
            $horaVisual = date('H', strtotime($confirmarCancelar));
            echo "<div style='color: red;'><br><h3>CANCELAR RESERVA:</h3>".
                 "<h3>Día ".$fechaVisual."</h3><h3>A las ".$horaVisual.":00h.</h3></div>";
        echo "<input type='hidden' name='confirmarCancelar' value='".htmlentities($confirmarCancelar)."'>";
        //echo "<input type='hidden' name='confirmarHora' value='".htmlentities($confirmarHora)."'>";

        echo "<hr><br><label>¿Quieres recibir un correo electrónico 
            de la cancelación de tu reserva?</label><br>";
        echo '<label style="margin: auto; width: fit-content;">
            <input type="radio" name="opcion" value="SI" checked> Sí</label><br>';
        echo '<label style="margin: auto; width: fit-content;">
            <input type="radio" name="opcion" value="NO"> No</label>';
        ?>
        <div><br><button type="submit" name="submit">Confirmar cancelación</button>
    </form>
    <form class="none" action="cancelarReserva.php" method="post">
        <button type="submit">Cancelar y elegir otra reserva</button>
    </form>
    </div>
    </div>

<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>