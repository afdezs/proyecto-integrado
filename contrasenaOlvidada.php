<!DOCTYPE html>
<html>
<head>
    <title>Restablecer Pass: Pista Deportiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="ico.png">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Proyecto Integrado - Adrián Fdz.</h2>
        <hr>
        <h1>Reservas Pista Deportiva</h1>
        <h2>Contraseña olvidada</h2>
        <h3>Introduce a continuación tus datos para restablecer tu contraseña:</h3>
        <form action="procesaOlvidada.php" method="post"
            onsubmit="return validarFormulario()">
            <label for="telefono">Teléfono</label>
            <input type="tel" placeholder="Introduce tu número de teléfono"
                id="telefono" name="telefono" required><br>

            <button type="submit" name="submit">Restablecer contraseña</button>
        </form>
        <div>
            <br><p>Contacto:
            <a href="mailto:info@pistadeportiva.es">info@pistadeportiva.es</a></p>
        </div>
    </div>

<script>
function validarFormulario() {
    var telefono = document.getElementById("telefono").value;

    if (telefono.length != 9) {
        alert("Introduzca un teléfono válido de 9 dígitos.");
        return false;
    }

    return true;
}
</script>

<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>