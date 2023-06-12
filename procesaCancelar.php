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
    <div><h3>Información sobre el proceso de cancelación</h3></div>
    <form action="index.php" method="POST" class='form'>
        <label for="fecha">Mensaje:</label>
    <?php
        $cancelarReserva = $_POST['confirmarCancelar']; // Salida: 2023-06-04 09:00:00
        //var_dump($cancelarReserva);

        $fechaVisual = date('d \d\e M \d\e Y', strtotime($cancelarReserva)); // Salida: 04 de Jun de 2023
        //var_dump($fechaVisual); echo '<br>';
        
        $horaVisual = date('H', strtotime($cancelarReserva)); // Salida: 09
        //var_dump($horaVisual); echo '<br>';

        $opcionEmail = $_POST['opcion'];

        // Preparamos la conexión con la base de datos
        include 'funcionConectarDelete.php';
        $conexion = conectar();
    
        // Obtener el valor de $_SESSION
        $usuario = $_SESSION["u"];
        $email = $_SESSION["e"];
        $nombre = $_SESSION["n"];
        $apellidos = $_SESSION["a"];
        
        // Consultas SQL

        //Consultamos primero el estado del pago
        try {
            $consulta1 = "SELECT fechainicio, fechafin, pagado FROM RESERVAS
                        WHERE telefono = :usuario AND fechainicio = :cancelarReserva;";
            $stmt1 = $conexion->prepare($consulta1);
            $stmt1->bindParam(':usuario', $usuario);
            $stmt1->bindParam(':cancelarReserva', $cancelarReserva);
            $stmt1->execute();
            $resultado1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);  
            //var_dump($resultado1);

        } catch (PDOException $e) {
            //echo "<h3 style='color: red;'>
            //Error en la ejecución de la consulta: " . $e->getMessage() . "</h3>";
            echo "<h3 style='color: red;'>Ha ocurrido un error, 
            vuelve a la página principal.</h3>";
        }

        // Hacemos un INSERT en la tabla CANCELADO para guardar la cancelación
        try {
            // Preparamos la consulta para la base de datos
            $consulta2 = "INSERT INTO CANCELADO (fechainicio, fechafin, pagado, telefono, fechacancelacion)
                            VALUES (:inicio, :fin, :pagado, :telefono, :cancelacion);";
            $stmt2 = $conexion->prepare($consulta2);

            // Consultamos la hora actual para reflejar en la cancelación
            date_default_timezone_set('Europe/Madrid');
            $fechaHoraActual = date('Y-m-d H:i:s');

            // Vinculamos los valores a los marcadores de posición
            $stmt2->bindParam(':inicio', $resultado1[0]['fechainicio']);
            $stmt2->bindParam(':fin', $resultado1[0]['fechafin']);
            $stmt2->bindParam(':pagado', $resultado1[0]['pagado']);
            $stmt2->bindParam(':telefono', $usuario);
            $stmt2->bindParam(':cancelacion', $fechaHoraActual);        

            // Ejecutamos la consulta
            $stmt2->execute();

            //$conexion->close();
        } catch (PDOException $e) {
            //echo "<h3 style='color: red;'>
            //Error en la ejecución de la consulta: " . $e->getMessage() . "</h3>";
            echo "<h3 style='color: red;'>Ha ocurrido un error, 
            vuelve a la página principal.</h3>";
        }

        if ($stmt2->rowCount() > 0) {
            // Si se hace lo anterior correctamente, eliminamos la reserva de la tabla RESERVAS
            try {
                $consulta3 = "DELETE FROM RESERVAS WHERE
                            fechainicio = :cancelarReserva AND telefono = :usuario;";
                $stmt3 = $conexion->prepare($consulta3);
                $stmt3->bindParam(':cancelarReserva', $cancelarReserva);
                $stmt3->bindParam(':usuario', $usuario);
                $stmt3->execute();
                
                if ($stmt3->rowCount() > 0) {
                    echo '<div style="color: green;"><h3>¡RESERVA CANCELADA CON ÉXITO!</h3></div>
                    <h4>'.$nombre.', podrás realizar otras reservas en el apartado
                        "Realizar Reserva" desde la página principal.</h4>
                    <div style="color: red;"><br><h3>NOTA IMPORTANTE:</h3></div>
                    <div><h4>Si la reserva ya ha sido pagada,
                        hay que solicitar el reembolso en la oficina de deportes,
                        indicando tus datos personales, fecha y hora de la reserva.
                    <br><br>
                    Gracias por utilizar este servicio.</h4></div>';
                    
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
                            $mail->Subject = "Reserva cancelada en pistadeportiva.es";
                            $mail->CharSet = 'UTF-8';
                            $mail->Body    = "¡Hola $nombre! <br>
                                    Acabas de cancelar una reserva en pistadeportiva.es. 
                                    <br><br>
                                    Esta es la información de la cancelación: <br>
                                    <b>RESERVA CANCELADA DEL DÍA ".$fechaVisual.", A LAS ".$horaVisual.":00h.</b>
                                    <br><br>
                                    Podrás realizar otras reservas en el apartado
                                    <b>Realizar Reserva</b> desde la página principal al iniciar sesión.
                                    <br><br>
                                    <b>NOTA IMPORTANTE:</b><br>
                                    Si la reserva ya ha sido pagada,
                                    hay que solicitar el reembolso en la oficina de deportes,
                                    indicando tus datos personales, fecha y hora de la reserva.
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
                            
                            echo '<div><p>En breve recibirás un correo electrónico a <b>'.$hiddenEmail.'</b> indicando 
                            la cancelación de la reserva.</div></p>';
                        } catch (Exception $e) {
                            //echo "Error al enviar el correo: {$mail->ErrorInfo}";
                            echo "<div><p>Error al enviar el correo.</div></p>";
                        }
                    }

                } else {
                    echo "<h3 style='color: red;'>Ha ocurrido un error, vuelve a la página principal.</h3>";
                }
            } catch (PDOException $e) {
                //echo "<h3 style='color: red;'>
                //Error en la ejecución de la consulta: " . $e->getMessage() . "</h3>";
                echo "<h3 style='color: red;'>Ha ocurrido un error, 
                vuelve a la página principal.</h3>";
            }
        }
        /*
        if ($stmt->execute()) {
            $numFilasAfectadas = $stmt->rowCount();
            //echo "Se eliminaron $numFilasAfectadas registros correctamente.";

        } else {
            $errorInfo = $stmt->errorInfo();
            //echo "Error al cancelar la reserva: " . $errorInfo[2];
            echo "<h3 style='color: red;'>Ha ocurrido un error, vuelve a la página principal.</h3>";
        }
        */
    ?>

        <div>
            <br><button type="submit" name="submit">Volver a la página principal</button>
        </div>
        </div>
    </form>
    </div>

<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>