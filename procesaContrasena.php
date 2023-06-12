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
    <title>Modificar pass: Pista Deportiva</title>
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
    <div><h3>Información proceso modificar contraseña</h3></div>
    <form class='form'>
        <label for="pass">Mensaje:</label>
    <?php
        $contrasenaActual = $_POST['contrasena'];
        $newContrasena = $_POST['newcontrasena'];
        $repeatContrasena = $_POST['repeatcontrasena'];

        //Procesamos contraseña con un hash para obtener contraseña encriptada
        $hashContrasena = password_hash($repeatContrasena, PASSWORD_DEFAULT);

        $usuario = $_SESSION["u"];
        /*
        var_dump($contrasenaActual);
        echo "<br>";
        var_dump($newContrasena);
        echo "<br>";
        var_dump($repeatContrasena);
        echo "<br>";
        var_dump($hashContrasena);
        echo "<br>";
        */
        // Preparamos la conexión con la base de datos
        include 'funcionConectar.php';
        $conexion = conectar();
        
        // Consulta SQL para contar las ocurrencias en la columna telefono
        $consulta = "SELECT * FROM USUARIOS WHERE telefono = :usuario;";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->execute();
        $resultado = $stmt->fetch();
        //var_dump($resultado);

        $nombre = $resultado['nombre'];
        $apellidos = $resultado['apellidos'];
        $email = $resultado['email'];
        $leerTelefono = $resultado['telefono'];
        $leerHashContrasena = $resultado['password'];
        
        // Verificamos el total de ocurrencias
        if (($usuario == $leerTelefono) && (password_verify($contrasenaActual, $leerHashContrasena))) {
            // Preparamos la consulta para la base de datos
            $consulta = "UPDATE USUARIOS 
                        SET password = :hashContrasena WHERE telefono = :telefono;";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':hashContrasena', $hashContrasena);
            $stmt->bindParam(':telefono', $usuario);
            $stmt->execute();
                
            if ($stmt->rowCount() > 0) {
                echo "<div style='color: green;'><h3>¡CONTRASEÑA ACTUALIZADA CON ÉXITO!</h3></div>
                <div><h4>Hola ".$nombre.", acabas de modificar tu contraseña satisfactoriamente. <br><br>
                Ahora puedes iniciar sesión con tu nueva contraseña.</h4></div>";
                session_destroy();

                // ENVIAMOS EMAIL DE CUENTA CREADA:
                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);

                try {
                    // Llamamos a la configuración del correo 
                    require 'configEmail.php';
                    
                    //Recipients
                    $mail->setFrom('info@pistadeportiva.es', 'PistaDeportiva.es');
                    $mail->addAddress("$email", "$nombre $apellidos"); 

                    //Content
                    $mail->isHTML(true);    //Set email format to HTML
                    $mail->Subject = "¡Modificar contraseña en pistadeportiva.es!";
                    $mail->CharSet = 'UTF-8';
                    $mail->Body    = "¡Hola $nombre! <br>
                        Tu contraseña se ha modificado correctamente en pistadeportiva.es.<br><br>
                        Utiliza tu teléfono y la nueva contraseña para iniciar sesión en nuestra página web.
                        <br><br>
                        Si no has sido quien ha cambiado la contraseña, inicia sesión 
                        en pistadeportiva.es y accede al apartado <b>Modificar mis datos</b> donde 
                        por seguridad te recomendamos cambiar la contraseña. 
                        <br><br>
                        Si tienes cualquier consulta, ¡no dudes en contactarnos! <br>
                        Saludos del equipo de pistadeportiva.es! <br><br>
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
                    echo "En breve recibirás un email con la confirmación de la modificación de tu
                        contraseña a <b>$hiddenEmail</b>.";
                } catch (Exception $e) {
                    //echo "Error al enviar el correo: {$mail->ErrorInfo}";
                    echo "Error al enviar el correo.";
                }

            } else {
                echo "<div style='color: red;'><h3>¡CONTRASEÑA NO ACTUALIZADA!</h3></div>
                <div><h4>Lo lamentamos $nombre, 
                pero ha ocurrido un error al modificar la contraseña. 
                <br><br>Inténtalo de nuevo.
                </h4></div><br>";
            }
        } else {
            echo "<div style='color: red;'><h3>¡Ha ocurrido un error!</h3></div>
            <div><h4>Lo lamentamos $nombre, 
            pero has introducido de forma errónea la contraseña actual.
            <br><br>
            Vuelve a intentarlo.</h4></div>";
        }

        ?>
        <div>
            <br><a href="index.php" class="button-link">Volver a la página principal</a>
        </div>
        </div>
    </form>
    </div>

<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>