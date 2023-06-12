<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>cPanel: Pista Deportiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../ico.png">
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
    <div class="container">
            <h2>Proyecto Integrado - Adrián Fdz.</h2>
            <!--<h2 style="color: red;">PÁGINA EN CONSTRUCCIÓN</h2>-->
            <hr>
            <h1>Reservas Pista Deportiva</h1>
            <hr>
    <div><h3>Paso de confirmación del estado del Pago</h3></div>
    <form action="procesaPagos.php" method="POST" class='form'>
        <label for="fecha">Reserva seleccionada:</label>
        <?php
            $confirmarPago = $_POST['confirmarpago'];
            $estadoPagado = $_POST['opcion'];
            //var_dump($confirmarPago);
            //echo '<br>';
            //var_dump($estadoPagado);

            $fechaVisual = date('d \d\e M \d\e Y \a \l\a\s H:i', strtotime($confirmarPago));
            echo "<div><br><h3>DÍA Y HORA:</h3></div>".
                 "<div style='color: green;'><h3>".$fechaVisual."h.</h3></div>";
                 
            echo "<div><br><h3>¿Deseas cambiar el estado del pago?</h3>";
            if ($estadoPagado == 'NO') {
                echo "<div>".
                "<h3 style='color: red;'>CAMBIAR A NO PAGADO</h3></div>";
            } else {
                echo "<div>".
                "<h3 style='color: green;'>CAMBIAR A PAGADO</h3></div>";
            }
            echo "<input type='hidden' name='confirmarPago' value='".htmlentities($confirmarPago)."'>";
            echo "<input type='hidden' name='estadoPagado' value='".htmlentities($estadoPagado)."'>";

            echo "<hr><br><label>¿Quieres enviar al usuario un correo electrónico 
            con la información del pago?</label><br>";
            echo '<label style="margin: auto; width: fit-content;">
            <input type="radio" name="opcionemail" value="SI" checked> Sí</label><br>';
            echo '<label style="margin: auto; width: fit-content;">
            <input type="radio" name="opcionemail" value="NO"> No</label>';
        ?>

        <div><br><button type="submit" name="submit">Confirmar</button>
    </form>
    <div>
        <a href="gestionPagos.php" class="button-link">Cancelar y elegir otra reserva</a>
    </div>
    </div>
<footer>
    <?php require '../cookies.php'; ?>
</footer>
</body>
</html>