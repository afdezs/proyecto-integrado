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

    <?php

    include './funcionConectar.php';

    //var_dump ($_POST);

    $conexion = conectar();

    $dia = $_POST['dia'];
    //cargamos las reservas de ese día, nos interesa saber la hora de inicio 
    //y si está confirmada (pagado)
    $consulta = "SELECT time(fechainicio), pagado FROM RESERVAS WHERE date(fechainicio) = :dia;";

    //guardamos en un array todo
    //$reservas = $conexion->query($consulta)->fetchAll(PDO::FETCH_NUM);

    $stmt = $conexion->prepare($consulta);
    $stmt->bindParam(':dia', $dia);
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_NUM);

    //echo "Este es el resultado de la consulta: <br>";
    //var_dump($reservas);
    echo "<hr>";
    setlocale(LC_TIME, 'es_ES'); // Establecemos la configuración regional a español
    //Formateamos la fecha para mostrarla con otro formato en el formulario
    $fechaFormateada = date('d \d\e M \d\e Y', strtotime($dia));
    echo "<h3>Fecha seleccionada: " . $fechaFormateada . "</h3>";

    echo "<div class='container'>";
    echo "<form action='confirmarReserva.php' method='POST' class='form'>";
    echo "<input type='hidden' name='dia' value='" . htmlentities($dia) . "'>";
    echo "<label for='reserva' class='label'>Horas disponibles para reservar:</label><br>";

    //con este array se puede generar la lista desplegable, o lo que quieras....
    //vamos a hacer una una lista desplegable....
    $listaDesplegable = "<br><label for='reserva' class='label'>
    Selecciona una hora para reservar:</label><br><select name='reserva' class='select'>";
    //para cada hora posible
    for ($hora = 9; $hora < 23; $hora++) {
        $horaEnFormatoReserva = str_pad($hora, 2, "0", STR_PAD_LEFT) . ":00:00";
        $estado = compruebaReserva($horaEnFormatoReserva, $reservas);
        $horaVisual = substr($horaEnFormatoReserva, 0, -3);
        if ($estado == "DISPONIBLE") {
            echo "<span style='color: green;'>" .
            $horaVisual . " - " . $estado . "</span>";
        } else {
            echo "<span style='color: red;'>" . 
            $horaVisual . " - " . $estado . "</span>";
        }
        echo "<br>";

    //si no está en las reservas la pongo en el select
        if (compruebaReserva($horaEnFormatoReserva, $reservas) == "DISPONIBLE") {
            $listaDesplegable .= "<option value= $hora>$hora:00</option>";
        }
    }
    $listaDesplegable .= "</select>";
    //echo '<div class="hr"></div>';
    echo $listaDesplegable;
    echo '<div><br>';
    echo '<button type="submit" class="submit">Reservar</button>';
    echo '</form>';
    echo '<br>';
    echo '<div>';
    echo '<a href="reservas.php" class="button-link">Volver atrás y seleccionar otro día</a>';
    echo '</div>';
        //echo '<form class="none" action="reservas.php" method="post">';
        //echo '<button type="submit">Volver atrás y seleccionar otro día</button>';
        //echo '</form>';
    echo '</div>';

    function compruebaReserva($hora, $reservas) {
        //suponemos que está "LIBRE"
        $resultado = "DISPONIBLE";
        //recorremos el array para buscar si está reservada
        foreach ($reservas as $reserva) {
            //comparamos con el elemento [0] del array
            if ($hora == $reserva[0]) {
                //devolvemos el elemento [1], que será "SI" o "NO"
                //$resultado = $reserva[1];
                $resultado = "RESERVADO";
            break;
            }
        }        
        /*if ($resultado == "DISPONIBLE") {
            return "<span style='color: green;'>$resultado</span>";
        } else {
            return "<span style='color: red;'>$resultado</span>";
        }*/
        return $resultado;
    }
    ?>
    </div>
<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>