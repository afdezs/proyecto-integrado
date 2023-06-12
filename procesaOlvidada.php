<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Restablecer Pass: Pista Deportiva</title>
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
    <div><h3>Información restablecer contraseña</h3></div>
    <form class='form'>
        <label for="cuenta">Mensaje:</label>
    <?php
        $telefono = $_POST['telefono'];
    
if ($telefono != "999999999") {
        // Preparamos la conexión con la base de datos
        include 'funcionConectar.php';
        $conexion = conectar();
    try {
        // Consulta SQL para contar las ocurrencias en la columna telefono
        $consulta = "SELECT telefono, email, nombre, apellidos
                    FROM USUARIOS WHERE telefono = :telefono;";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        //var_dump($resultado);

        $email = $resultado['email'];
        $nombre = $resultado['nombre'];
        $apellidos = $resultado['apellidos'];
        //if (count($resultado) > 0) {

        //echo '<h2 style="color: red;">-- PROCEDIMIENTO EN CONSTRUCCIÓN --</h2>';

        if ($resultado > 0 || $resultado !== false) {

            /*
            function generarContrasenaAleatoria($longitud = 10) {
                $bytes = random_bytes($longitud);
                return base64_encode($bytes);
            }
            // Generamos contraseña:
            $contrasena = generarContrasenaAleatoria(12);
            echo "Contraseña generada: " . $contrasena;
            */

            // Creamos una función que genera una nueva contraseña
            function generarContrasenaSimple($longitud = 8) {
                $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $contrasena = '';
                for ($i = 0; $i < $longitud; $i++) {
                    $indice = mt_rand(0, strlen($caracteres) - 1);
                    $contrasena .= $caracteres[$indice];
                }
                return $contrasena;
            }
            // Generamos contraseña:
            $contrasena = generarContrasenaSimple(10);
            //echo "Pass: " . $contrasena;

            //Procesamos contraseña con un hash para obtener contraseña encriptada
            $hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);

            // UPDATE EN DB CON NUEVA CONTRASEÑA
            try {
                // Preparamos Consulta SQL
                $consulta = "UPDATE USUARIOS 
                            SET password = :hashContrasena WHERE telefono = :telefono;";
                $stmt = $conexion->prepare($consulta);
                $stmt->bindParam(':hashContrasena', $hashContrasena);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // Verificar si la inserción fue exitosa
                    echo '<div style="color: green;">
                    <h3>¡RESTABLECIMIENTO DE TU CONTRASEÑA EN CURSO!</h3></div>
                    <div><h4>Hola '.$nombre.', has comenzado el proceso para 
                    restablecer tu contraseña.</h4></div>';

                    // ENVIAMOS EMAIL CON NUEVA CONTRASEÑA:
                    $mail = new PHPMailer(true);

                    try {
                        // Llamamos a la configuración del correo 
                        require 'configEmail.php';

                        //Recipients
                        $mail->setFrom('info@pistadeportiva.es', 'PistaDeportiva.es');
                        $mail->addAddress("$email", "$nombre $apellidos"); 

                        //Content
                        $mail->isHTML(true);    //Set email format to HTML
                        $mail->Subject = "Restablecer contraseña en pistadeportiva.es";
                        $mail->CharSet = 'UTF-8';
                        $mail->Body    = "¡Hola $nombre! <br>
                                Has solicitado restablecer tu contraseña en pistadeportiva.es.<br><br>
                                Utiliza el teléfono que has usado para restablecer contraseña y 
                                esta nueva contraseña para iniciar sesión en nuestra página web: <br>
                                Nueva Contraseña: <b>$contrasena</b><br><br>
                                Para establecer una contraseña diferente a tu gusto tienes que iniciar sesión 
                                en pistadeportiva.es y <br>acceder al apartado <b>Modificar mis datos</b> donde 
                                podrás cambiar la contraseña. <br><br>
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

                        echo '<div><p>En breve recibirás un correo electrónico a <b>'.$hiddenEmail.'</b> donde 
                        se indican las instrucciones a seguir para 
                        completar el proceso.</div></p>';
                    } catch (Exception $e) {
                        //echo "Error al enviar el correo: {$mail->ErrorInfo}";
                        echo "Error al enviar el correo.";
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

        } else {
            echo "<div style='color: red;'><h3>¡RESTABLECIMIENTO DE CONTRASEÑA CANCELADO!</h3></div>
                <div><h4>Lo lamentamos, pero has introducido un teléfono 
                que no tiene cuenta de usuario. 
                <br><br>Inserta un teléfono válido.
                </h4></div><br>";
        }
    } catch (PDOException $e) {
        //echo "<h3 style='color: red;'>
        //Error en la ejecución de la consulta: " . $e->getMessage() . "</h3>";
        echo "<h3 style='color: red;'>Ha ocurrido un error, 
        vuelve a la página principal.</h3>";
    }
} else {
    echo "<h3 style='color: red;'>Ha ocurrido un error, 
    vuelve a la página principal.</h3>";
}
    ?>
        <div>
            <br><a href="index.php" class="button-link">Volver a la página principal</a>
        </div>
    </form>
    </div>

<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>