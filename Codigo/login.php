<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = $_POST["mail"];
    $contraseña = $_POST["contraseña"];

    $sql = "SELECT * FROM usuarios WHERE mail='$mail'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($contraseña, $row["contraseña"])) {
            $_SESSION["id_usuario"] = $row["id_usuario"];
            $_SESSION["nombre"] = $row["nombre"];
            $_SESSION["rol"] = $row["rol"];

            if ($row["rol"] == "refugio") {
                header("Location: panel_refugio.php");
            } else {
                header("Location: panel_adoptante.php");
            }
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
}
?>

<form method="POST" action="">
    <input type="email" name="mail" placeholder="Correo" required><br>
    <input type="password" name="contraseña" placeholder="Contraseña" required><br>
    <button type="submit">Iniciar sesión</button>
</form>
