<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $mail = $_POST["mail"];
    $contraseña = password_hash($_POST["contraseña"], PASSWORD_DEFAULT);
    $rol = $_POST["rol"]; // "refugio" o "adoptante"

    $sql = "INSERT INTO usuarios (nombre, apellido, mail, contraseña, rol)
            VALUES ('$nombre', '$apellido', '$mail', '$contraseña', '$rol')";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.php?registro=ok");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="POST" action="">
    <input type="text" name="nombre" placeholder="Nombre" required><br>
    <input type="text" name="apellido" placeholder="Apellido" required><br>
    <input type="email" name="mail" placeholder="Correo" required><br>
    <input type="password" name="contraseña" placeholder="Contraseña" required><br>
    <select name="rol" required>
        <option value="adoptante">Adoptante</option>
        <option value="refugio">Refugio</option>
    </select><br>
    <button type="submit">Registrarse</button>
</form>
