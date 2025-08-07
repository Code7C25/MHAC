<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Buscar usuario por email
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verifica estado y contraseña
        if ($usuario['estado'] !== 'activo') {
            header("Location: login.php?error=Tu cuenta no está activa.");
            exit;
        }

        if (password_verify($password, $usuario['password_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            header("Location: index.php");
            exit;

        } else {
            header("Location: login.php?error=Contraseña incorrecta.");
            exit;
        }
    } else {
        header("Location: login.php?error=Usuario no encontrado.");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
