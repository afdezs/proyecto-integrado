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
    <div><h3>Información proceso crear cuenta</h3></div>
    <form class='form'>
        <label for="cuenta">Mensaje:</label>
    <?php
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $contrasena = $_POST['contrasena'];

        //Procesamos contraseña con un hash para obtener contraseña encriptada
        $hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);

        /*
        var_dump($nombre);
        echo "<br>";
        var_dump($apellidos);
        echo "<br>";
        var_dump($email);
        echo "<br>";
        var_dump($telefono);
        echo "<br>";
        //var_dump($contrasena);
        echo "<br>";
        var_dump($hashContrasena);
        echo "<br>";
        */

        // Preparamos la conexión con la base de datos
        include 'funcionConectar.php';
        $conexion = conectar();
        
        // Consulta SQL para contar las ocurrencias en la columna telefono
        $consulta = "SELECT COUNT(*) AS total FROM USUARIOS WHERE telefono = :telefono;";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener el total de ocurrencias del usuario
        $totalOcurrencias = $resultado['total'];
        
        // Verificamos el total de ocurrencias
        if ($totalOcurrencias >= 1) {
            echo "<div style='color: red;'><h3>¡CUENTA NO CREADA!</h3></div>
            <div><h4>Lo lamentamos $nombre, 
            pero actualmente ya existe un usuario con los datos insertados.
            <br><br>
            Usa otro número de teléfono para poder crear una cuenta.</h4></div>";
            //$conexion->close(); // Cerrar la conexión
        } else {

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $dominio = explode("@", $email)[1];
            if (checkdnsrr($dominio)) {

                // Preparamos la consulta para la base de datos
                $consulta = "INSERT INTO USUARIOS (nombre, apellidos, telefono, email, password)
                VALUES (:nombre, :apellidos, :telefono, :email, :password);";

                // Prepararmos la consulta con prepare y execute
                $stmt = $conexion->prepare($consulta);

                // Vinculamos los valores a los marcadores de posición
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashContrasena);

                // Ejecutamos la consulta
                $stmt->execute();
                
                /*
                // Verificar si la inserción fue exitosa
                if ($stmt->rowCount() > 0) {
                    echo "Inserción exitosa";
                } else {
                    echo "Error al insertar";
                }
                */

                echo "<div style='color: green;'><h3>¡CUENTA CREADA CON ÉXITO!</h3></div>
                <div><h4>¡Genial $nombre!<br>Ahora podrás gestionar las reservas
                    desde tu perfil cuando inicies sesión en la página principal.</h4></div>";

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
                    $mail->Subject = "¡Bienvenid@ a pistadeportiva.es!";
                    $mail->CharSet = 'UTF-8';
                    $mail->Body    = "¡Hola $nombre, bienvenid@ a pistadeportiva.es! <br><br>
                            Estas son tus credenciales para iniciar sesión en nuestra página web: <br>
                            Usuario: <b>$telefono</b><br><br>
                            Tener una cuenta te permite: <br>
                            - Realizar reservas. <br>
                            - Ver tus reservas. <br>
                            - Cancelar reservas. <br><br>
                            ¡Gracias! Saludos del equipo de pistadeportiva.es! <br><br>
                            Visítanos en: https://pistadeportiva.es/";

                    $mail->send();
                    echo "En breve recibirás un email de bienvenida a <b>$email</b>.";
                } catch (Exception $e) {
                    //echo "Error al enviar el correo: {$mail->ErrorInfo}";
                    echo "Error al enviar el correo.";
                }
                    // El dominio del correo electrónico existe
            } else {
                echo "<div style='color: red;'><h3>¡CUENTA NO CREADA!</h3></div>
                <div><h4>Lo lamentamos $nombre, 
                pero has introducido un email que no existe. 
                <br><br>Inserta un email válido.
                </h4></div><br>";
                // El dominio del correo electrónico no existe
            }
        } else {
            echo "<div style='color: red;'><h3>¡CUENTA NO CREADA!</h3></div>
                div><h4>Lo lamentamos $nombre, 
                pero has introducido un email que no tiene un formato válido.
                </h4></div><br>";
            // El correo electrónico no tiene un formato válido
        }
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