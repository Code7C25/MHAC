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

            // Redirige según el rol
            switch ($usuario['rol']) {
                case 'adoptante':
                    header("Location: panel_adoptante.php");
                    break;
                case 'refugio':
                    header("Location: panel_refugio.php");
                    break;
                case 'voluntario':
                    header("Location: panel_voluntario.php");
                    break;
                case 'veterinaria':
                    header("Location: panel_veterinaria.php");
                    break;
                case 'donante':
                    header("Location: panel_donante.php");
                    break;
                case 'hogar_transito':
                    header("Location: panel_hogar_transito.php");
                    break;
                default:
                    header("Location: index.php"); // por si acaso
            }
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
