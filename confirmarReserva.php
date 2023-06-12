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
    <div><h3>Paso de confirmación de reserva</h3></div>
    <form action="procesaReserva.php" method="POST" class='form'>
        <label for="fecha">¿Deseas realizar la siguiente reserva?</label>
        <?php
            $confirmarDia = $_POST['dia'];
            $confirmarHora = $_POST['reserva'];
            //$horaVisual = $confirmarHora.":00";
            $fechaVisual = date('d \d\e M \d\e Y', strtotime($confirmarDia));
            echo "<div style='color: green;'><br><h3>RESERVA DE PISTA:</h3>".
                 "<h3>Día ".$fechaVisual."</h3><h3>A las ".$confirmarHora.":00h.</h3></div>";
            echo "<input type='hidden' name='confirmarDia' value='".htmlentities($confirmarDia)."'>";
            echo "<input type='hidden' name='confirmarHora' value='".htmlentities($confirmarHora)."'>";
            
            echo "<hr><br><label>¿Quieres recibir un correo electrónico 
                con la información de tu reserva?</label><br>";
            echo '<label style="margin: auto; width: fit-content;">
                <input type="radio" name="opcion" value="SI" checked> Sí</label><br>';
            echo '<label style="margin: auto; width: fit-content;">
                <input type="radio" name="opcion" value="NO"> No</label>';
        ?>
        <!--<input type="date" name ="dia" required>-->
        <div><br><button type="submit" name="submit">Confirmar</button>
    </form>
    <div>
        <a href="reservas.php" class="button-link">Cancelar y elegir otra reserva</a>
    </div>
    <!--<form class="none" action="reservas.php" method="post">
        <button type="submit">Cancelar y elegir otra reserva</button>
    </form>-->
    </div>
    </div>
<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>