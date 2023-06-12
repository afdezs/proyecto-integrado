<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require '../PHPMailer/Exception.php';
    require '../PHPMailer/PHPMailer.php';
    require '../PHPMailer/SMTP.php';
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
    <div><h3>Información sobre el estado del Pago</h3></div>
    <form class='form'>
        <label for="fecha">Mensaje:</label>
    <?php
        $fechaReserva = $_POST['confirmarPago']; // Contiene: 2023-05-31 18:00:00
        $estadoFinalPago = $_POST['estadoPagado']; // Contiene: SI o NO
        
        //var_dump($fechaReserva);
        //echo "<br>";
        //var_dump($estadoFinalPago);

        //Damos formato para enviar un email
        $fechaVisual = date('d \d\e M \d\e Y', strtotime($fechaReserva));
        $horaVisual = date('H', strtotime($fechaReserva));

        $opcionEmail = $_POST['opcionemail']; // Contiene: SI o NO
        
        // Preparamos la conexión con la base de datos
        include '../funcionConectar.php';
        $conexion = conectar();
        
        // Comprobamos el estado actual del pago
        try {
            //$consulta = "SELECT pagado FROM RESERVAS WHERE fechainicio = :fechaReserva;";

            $consulta = "SELECT RESERVAS.pagado, USUARIOS.nombre, USUARIOS.apellidos, USUARIOS.email
            FROM RESERVAS INNER JOIN 
            USUARIOS ON RESERVAS.telefono = USUARIOS.telefono
            WHERE RESERVAS.fechainicio = :fechaReserva;";

            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':fechaReserva', $fechaReserva);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $nombre = $resultado[0]['nombre'];
            $apellidos = $resultado[0]['apellidos'];
            $email = $resultado[0]['email'];
            /*
            var_dump($resultado); echo '<br>';
            var_dump($nombre); echo '<br>';
            var_dump($apellidos); echo '<br>';
            var_dump($email); echo '<br>';
            var_dump($resultado[0]['pagado']); echo '<br>';
            var_dump($fechaVisual); echo '<br>';
            var_dump($horaVisual); echo '<br>';
            var_dump($opcionEmail); echo '<br>';
            var_dump($estadoFinalPago); echo '<br>';
            */
            if ($resultado[0]['pagado'] == $estadoFinalPago) {
                echo '<div style="color: red;">
                <h3>¡NO SE PUEDE ACTUALIZAR EL ESTADO DEL PAGO!</h3></div>
                <h4>No se puede establecer de nuevo el mismo estado actual del pago.</h4> 
                <h4>Si el estado ya es PAGADO, no se puede establecer de nuevo a PAGADO.</h4>';       
            } else {
                try {
                    // Preparamos Consulta SQL
                    $consulta = "UPDATE RESERVAS 
                                SET pagado = :estadoFinalPago, fechapago = :fechaPago
                                WHERE fechainicio = :fechaReserva;";
                    $stmt = $conexion->prepare($consulta);

                    // Consultamos la hora actual para reflejar el pago
                    date_default_timezone_set('Europe/Madrid');
                    $fechaHoraActual = date('Y-m-d H:i:s');

                    $stmt->bindParam(':estadoFinalPago', $estadoFinalPago);

                    if ($estadoFinalPago == 'SI') {
                        $stmt->bindParam(':fechaPago', $fechaHoraActual);
                    } else {
                        $fechaHoraActual = NULL;
                        $stmt->bindParam(':fechaPago', $fechaHoraActual, PDO::PARAM_NULL);
                    }
                    $stmt->bindParam(':fechaReserva', $fechaReserva);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        // Verificar si la inserción fue exitosa
                        echo '<div style="color: green;">
                        <h3>¡CAMBIO DEL ESTADO DEL PAGO REALIZADO CON ÉXITO!</h3></div>
                        <h4>Puedes continuar cambiando el estado del pago de otras reservas 
                        volviendo a la página de Gestión de Pagos.</h4>';        

                        // Comprobamos que el usuario quiere recibir info de su reserva
                        if ($opcionEmail == 'SI') {

                            // ENVIAMOS EMAIL CON LA INFO DE LA RESERVA:
                            $mail = new PHPMailer(true);

                            try {
                                // Llamamos a la configuración del correo 
                                require '../configEmail.php';

                                // Creamos dos tipos de correo para el estado del pago SI o NO

                                //Recipients
                                $mail->setFrom('info@pistadeportiva.es', 'PistaDeportiva.es');
                                $mail->addAddress("$email", "$nombre $apellidos"); 

                                //Content
                                $mail->isHTML(true);    //Set email format to HTML
                                
                                if ($estadoFinalPago == 'SI') {
                                    $mail->Subject = "Pago realizado en pistadeportiva.es";
                                    $mail->CharSet = 'UTF-8';
                                    $mail->Body    = "¡Hola $nombre! <br>
                                            Has realizado el pago de una reserva en pistadeportiva.es. 
                                            <br><br>
                                            Este es el estado del pago de tu reserva: <br>
                                            <b>PAGO COMPLETADO DE LA RESERVA <br>
                                            DEL DÍA ".$fechaVisual.", A LAS ".$horaVisual.":00h.</b>
                                            <br><br>
                                            Te hemos facilitado el código de acceso
                                            para la apertura de puertas de la pista deportiva que podrás
                                            visualizar en el apartado <b>Ver mis Reservas</b> 
                                            desde la página principal al iniciar sesión, 
                                            y utilizar en el día y a la hora de tu reserva.
                                            <br><br>
                                            <b>NOTA IMPORTANTE: </b><br>
                                            Ese código únicamente es válido en la hora y día de la reserva.
                                            <br><br>
                                            Gracias por utilizar este servicio. <br>
                                            Si tienes cualquier consulta, ¡no dudes en contactarnos! <br>
                                            Saludos del equipo de pistadeportiva.es!
                                            <br><br>
                                            Visítanos en: https://pistadeportiva.es/";
                                } else {
                                    $mail->Subject = "Pago no realizado en pistadeportiva.es";
                                    $mail->CharSet = 'UTF-8';
                                    $mail->Body    = "¡Hola $nombre! <br>
                                            Aún no has realizado el pago de una reserva en pistadeportiva.es. 
                                            <br><br>
                                            Este es el estado del pago de tu reserva: <br>
                                            <b>PAGO NO REALIZADO DE LA RESERVA <br>
                                            DEL DÍA ".$fechaVisual.", A LAS ".$horaVisual.":00h.</b>
                                            <br><br>
                                            Para que sea efectiva completamente la reserva,
                                            debes acudir a la oficina de deportes y realizar el pago de la reserva.
                                            <br>
                                            Una vez hecho esto, te facilitaremos un código de acceso
                                            para la apertura de puertas de la pista deportiva.                                            
                                            <br><br>
                                            Gracias por utilizar este servicio. <br>
                                            Si tienes cualquier consulta, ¡no dudes en contactarnos! <br>
                                            Saludos del equipo de pistadeportiva.es!
                                            <br><br>
                                            Visítanos en: https://pistadeportiva.es/";
                                    }
                                    $mail->send();
                                    echo '<div><p>El usuario recibirá un correo electrónico con el estado
                                    del pago de su reserva.</div></p>';
                            } catch (Exception $e) {
                                //echo "Error al enviar el correo: {$mail->ErrorInfo}";
                                echo "<div><p>Error al enviar el correo.</div></p>";
                            }
                        }
                    } else {
                        echo "<h3 style='color: red;'>Ha ocurrido un error, 
                    vuelve a la página de Gestión de Pagos.</h3>";
                    }

                } catch (PDOException $e) {
                    //echo "<h3 style='color: red;'>
                    //Error en la ejecución de la consulta: " . $e->getMessage() . "</h3>";
                    echo "<h3 style='color: red;'>Ha ocurrido un error, 
                    vuelve a la página de Gestión de Pagos.</h3>";
                }
            }
        } catch (PDOException $e) {
            //echo "<h3 style='color: red;'>
            //Error en la ejecución de la consulta: " . $e->getMessage() . "</h3>";
            echo "<h3 style='color: red;'>Ha ocurrido un error, 
            vuelve a la página de Gestión de Pagos.</h3>";
        }

        ?>
        <div>
            <br>
            <a href="gestionPagos.php" class="button-link">Volver a la Gestión de Pagos</a>
        </div>
        </div>
    </form>
    </div>

<footer>
    <?php require '../cookies.php'; ?>
</footer>
</body>
</html>