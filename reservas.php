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
    <div><h3>Selecciona un día para ver las reservas disponibles</h3></div>
    <form action="seleccionaReserva.php" method="POST" class='form'>
        <label for="fecha">Pincha aquí para ver el calendario:</label><br>
    <?php
        // Obtenemos la fecha actual
        $fechaActual = new DateTime();
        //$fechaActual->modify('+1 day'); // Sumar un día a la fecha actual para evitar reserva el mismo día

        // Calculamos la fecha mínima permitida (hasta 30 días después de la fecha actual)
        $fechaMinima = clone $fechaActual;
        $fechaMinima->modify('+30 days');

        // Formateamos las fechas en formato YYYY-MM-DD
        $fechaActualString = $fechaActual->format('Y-m-d');
        $fechaMinimaString = $fechaMinima->format('Y-m-d');
    ?>
        <input style='border: 3.5px solid; border-radius: 4px;'
            type="date" name="dia" id="fecha" required
            min="<?php echo $fechaActualString; ?>" max="<?php echo $fechaMinimaString; ?>">
        <label class="container" for="nota"><br><br><b>NOTA: </b><br><br>
                                    Se recomienda realizar las reservas 
                                    con 24 horas de antelación.</label>
        <div><br><button type="submit" name="submit">Ver Reservas</button>
    <div>
        <a href="index.php" class="button-link">Volver atrás y seleccionar otra opción</a>
    </div>
    </form>
    <!--<form class="none" action="index.php" method="post">
        <button type="submit">Volver atrás y seleccionar otra opción</button>
    </form>-->
    </div>
    </div>

<script>
    /*
    // Obtenemos la fecha actual en formato YYYY-MM-DD
    var fechaActual = new Date();
    fechaActual.setDate(fechaActual.getDate() + 1); // Sumar un día a la fecha actual

    // Formateaamos la fecha en formato YYYY-MM-DD
    var fechaMinima = fechaActual.toISOString().split("T")[0];

    // Obtenemos la referencia al elemento input de fecha
    var inputFecha = document.getElementById("fecha");

    // Establecemos el atributo min con la fecha mínima
    inputFecha.min = fechaMinima;
    */
</script>

</body>
<footer>
    <?php require 'cookies.php'; ?>
</footer>
</html>