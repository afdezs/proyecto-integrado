<?php
session_start();
//Si queremos cerrar la sesión
if (isset($_POST["cerrar"])) {
    session_destroy();
} else {

    // Conexión a la base de datos
    require 'funcionConectar.php';

    // Procesamiento de los datos del formulario
    $usuario = $_POST['username'];
    $contrasena = $_POST['password'];
/*
    // Hash que deberíamos obtener
    $hashCorrecto = password_hash($contrasena, PASSWORD_DEFAULT);
*/
    // Consulta a la base de datos utilizando prepare() y execute()
        $conexion = conectar();
        $sql = "SELECT * FROM USUARIOS WHERE telefono = :usuario";
        $consulta = $conexion->prepare($sql);
        $consulta->bindValue(':usuario', $usuario);
        //$consulta->bindValue(':contrasena', $contrasena);
        $consulta->execute();
        //$consulta->execute(['usuario' => $usuario, 'contrasena' => $contrasena]);

        $resultado = $consulta->fetch();
        $leerTelefono = $resultado['telefono'];
        $leerHashContrasena = $resultado['password'];

    // Si los datos enviados son válidos...
    if (($usuario == $leerTelefono) && (password_verify($contrasena, $leerHashContrasena))) {
    //if (($usuario == $leerTelefono) && ($contrasena == $leerHashContrasena)){
        // Establecemos las variables de $_SESSION
        $_SESSION["u"] = $usuario;
        $_SESSION["c"] = $contrasena;
        $_SESSION["n"] = $resultado['nombre'];
        $_SESSION["a"] = $resultado['apellidos'];
        $_SESSION["e"] = $resultado['email'];
        /*// Establecemos las cookies
        setcookie("cookie1", $_SESSION["u"]);
        setcookie("cookie2", $_SESSION["c"]);
        setcookie("cookie3", date("H:i"));*/
    } else {
        $_SESSION["error"] = "error";
    }

    // Consulta SQL para contar las ocurrencias en la columna telefono en RESERVAS
    $consulta = "SELECT COUNT(*) AS total FROM RESERVAS WHERE telefono = :telefono;";
    $stmt = $conexion->prepare($consulta);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener el total de ocurrencias del usuario
    $totalOcurrencias = $resultado['total'];
}

// Volvemos al index en cualquier caso
header("Location: index.php");

?>