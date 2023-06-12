<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
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
    <div><h3>Información sobre el proceso de reserva</h3></div>
    <form action="index.php" method="POST" class='form'>
        <label for="fecha">Mensaje:</label>
    <?php
        $reservarDia = $_POST['confirmarDia'];
        $reservarHora = $_POST['confirmarHora'];
        //$reservarDia = date('Y-m-d', strtotime($recibirDia)); // Salida: 2023-05-08

        //Damos formato a reservarDia para enviar un email
        $fechaVisual = date('d \d\e M \d\e Y', strtotime($reservarDia));

        $opcionEmail = $_POST['opcion'];
        
        // Preparamos primero la hora fin partiendo de la horaInicio = reservarHora
        $horaFin = $reservarHora + 1;
        // Preparamos la hora, YA QUE VIENE SOLO UN NUMERO para insertarla
        // en el INSERT siguiendo el formato de hora necesario 00:00:00 -> horas:minutos:segundos
        if ($reservarHora < 10) {
            $reservarHora = "0".$reservarHora.":00:00";
            $horaFin = $horaFin.":00:00";
        } else {
            $reservarHora = $reservarHora.":00:00";
            $horaFin = $horaFin.":00:00";
        }
        /*
        var_dump($reservarDia);
        echo "<br>";
        var_dump($reservarHora);
        echo "<br>";
        var_dump($horaFin);
        echo "<br>";
        */
        // Preparamos la conexión con la base de datos
        include 'funcionConectar.php';
        $conexion = conectar();
    
        // Obtener el valor de $_SESSION
        $usuario = $_SESSION["u"];
        $email = $_SESSION["e"];
        $nombre = $_SESSION["n"];
        $apellidos = $_SESSION["a"];
        
        // Consulta SQL para contar las ocurrencias del usuario en la columna telefono
        $consulta = "SELECT COUNT(*) AS total
                    FROM RESERVAS WHERE telefono = :usuario
                    AND fechainicio >= CURRENT_DATE";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener el total de ocurrencias del usuario
        $totalOcurrencias = $resultado['total'];
        
        // Verificar si el total de ocurrencias es igual o mayor a 3
        if ($totalOcurrencias >= 3) {
            echo "<div style='color: red;'><h3>¡RESERVA NO REALIZADA!</h3></div>
            <div><h4>".$_SESSION["n"].", actualmente no es posible realizar más de tres reservas.
            <br><br>
            Tiene que haber finalizado alguna de tus reservas para poder realizar otra reserva, 
            o también puedes cancelar alguna de ellas desde tu página principal de usuario.
            <br><br>
            Disculpa las molestias.
            <br><br>
            Gracias por utilizar este servicio.</h4></div>";
            //$conexion->close(); // Cerrar la conexión
        } else {
            // Ejemplo de la consulta a la base de datos:
            // $consulta = "INSERT INTO 'RESERVAS'('fechainicio', 'fechafin', 'pagado', 'telefono')
                            //VALUES ('2023-05-23 18:00:00','2023-05-23 19:00:00','NO','666666666');";
        try {
            // Preparamos la consulta para la base de datos
            $consulta = "INSERT INTO RESERVAS (fechainicio, fechafin, pagado, telefono, fechareserva)
                            VALUES (:inicio, :fin, :pagado, :telefono, :fechareserva)";

            // Prepararmos la consulta con prepare y execute
            $stmt = $conexion->prepare($consulta);

            // Consultamos la hora actual para reflejar la reserva
            date_default_timezone_set('Europe/Madrid');
            $fechaHoraActual = date('Y-m-d H:i:s');

            // Asignamos los valores a los marcadores de posición
            $inicio = $reservarDia." ".$reservarHora;
            $fin = $reservarDia." ".$horaFin;
            $pagado = "NO";
            $telefono = $_SESSION["u"];

            // Vinculamos los valores a los marcadores de posición
            $stmt->bindParam(':inicio', $inicio);
            $stmt->bindParam(':fin', $fin);
            $stmt->bindParam(':pagado', $pagado);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':fechareserva', $fechaHoraActual);

            // Ejecutamos la consulta
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Verificar si la inserción fue exitosa
                echo '<div style="color: green;"><h3>¡RESERVA REALIZADA CON ÉXITO!</h3></div>
                <h4>'.$nombre.', podrás gestionar tu reserva en el apartado
                "Ver mis Reservas" desde la página principal.</h4>
                <div style="color: red;"><br><h3>NOTA IMPORTANTE:</h3></div>
                <div><h4>Para que sea efectiva completamente la reserva,
                debes ir a la oficina de deportes y realizar el pago de la reserva.
                <br><br>
                Una vez hecho esto, te facilitaremos un código de acceso
                para la apertura de puertas de la pista deportiva.
                <br><br>
                Gracias por utilizar este servicio.</h4></div>
                ';

                // Comprobamos que el usuario quiere recibir info de su reserva
                if ($opcionEmail == 'SI') {

                    // ENVIAMOS EMAIL CON LA INFO DE LA RESERVA:
                    $mail = new PHPMailer(true);

                    try {
                        // Llamamos a la configuración del correo 
                        require 'configEmail.php';

                        //Recipients
                        $mail->setFrom('info@pistadeportiva.es', 'PistaDeportiva.es');
                        $mail->addAddress("$email", "$nombre $apellidos"); 

                        //Content
                        $mail->isHTML(true);    //Set email format to HTML
                        $mail->Subject = "Reserva realizada en pistadeportiva.es";
                        $mail->CharSet = 'UTF-8';
                        $mail->Body    = "¡Hola $nombre! <br>
                                Acabas de realizar una reserva en pistadeportiva.es. 
                                <br><br>
                                Esta es la información de tu reserva: <br>
                                <b>RESERVA EL DÍA ".$fechaVisual.", A LAS ".$_POST['confirmarHora'].":00h.</b>
                                <br><br>
                                Podrás gestionar tu reserva en el apartado <b>Ver mis Reservas</b> 
                                desde la página principal al iniciar sesión.
                                <br><br>
                                <b>NOTA IMPORTANTE:</b><br>
                                Recuerda que para que sea efectiva completamente la reserva, 
                                debes acudir a la oficina de deportes y realizar el pago de la reserva.
                                <br>
                                Una vez hecho esto, te facilitaremos un código de acceso
                                para la apertura de puertas de la pista deportiva que podrás 
                                consultar en <b>Ver mis Reservas</b>.
                                <br><br>
                                Gracias por utilizar este servicio. <br>
                                Si tienes cualquier consulta, ¡no dudes en contactarnos! <br>
                                Saludos del equipo de pistadeportiva.es!
                                <br><br>
                                Visítanos en: https://pistadeportiva.es/";

                        $mail->send();
                        
                        // Preparamos el correo para que se cifre parte de él
                        // Obtener la posición del símbolo "@"
                        $atPosition = strpos($email, "@");
                        if ($atPosition !== false) {
                            // Obtener la parte antes del símbolo "@"
                            $username = substr($email, 0, $atPosition);
                            // Obtener las dos primeras letras del nombre de usuario
                            $firstTwo = substr($username, 0, 2);
                            // Obtener las tres últimas letras del nombre de usuario
                            $lastThree = substr($username, -3);
                            // Construir la dirección de correo electrónico oculta
                            $hiddenEmail = $firstTwo . str_repeat("*", $atPosition - 5) . $lastThree . substr($email, $atPosition);
                            //echo $hiddenEmail; // Mostrar la dirección de correo electrónico oculta
                        } else {
                            // La dirección de correo electrónico no es válida
                            echo "Dirección de correo electrónico no válida";
                        }

                        echo '<div><p>En breve recibirás un correo electrónico a <b>'.$hiddenEmail.'</b> con 
                        la información de tu reserva.</div></p>';
                    } catch (Exception $e) {
                        //echo "Error al enviar el correo: {$mail->ErrorInfo}";
                        echo "<div><p>Error al enviar el correo.</div></p>";
                    }
                }

            } else {
                echo "<h3 style='color: red;'>Ha ocurrido un error, 
            vuelve a la página principal.</h3>";
            }

        } catch (PDOException $e) {
            //echo "<h3 style='color: red;'>
            //Error en la ejecución de la consulta: " . $e->getMessage() . "</h3>";
            echo "<h3 style='color: red;'>Ha ocurrido un error, 
            vuelve a la página principal.</h3>";
        }
        }
        ?>
        <div>
            <br>
            <!--<button type="submit" name="submit">Volver a la página principal</button>-->
            <a href="index.php" class="button-link">Volver a la página principal</a>
        </div>
        </div>
    </form>
    </div>

</body>
<footer>
    <?php require 'cookies.php'; ?>
</footer>
</html>