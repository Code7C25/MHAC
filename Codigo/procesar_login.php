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
            $_SESSION['rol'] = $usuario['rol'];

            // 👇 Si el usuario es un refugio, obtener su refugio_id
            if ($usuario['rol'] === 'refugio') {
                $sql_ref = "SELECT id FROM refugios WHERE usuario_id = ?";
                $stmt_ref = $conn->prepare($sql_ref);
                $stmt_ref->bind_param("i", $usuario['id']);
                $stmt_ref->execute();
                $res_ref = $stmt_ref->get_result();

                if ($fila_ref = $res_ref->fetch_assoc()) {
                    $_SESSION['refugio_id'] = $fila_ref['id']; // ✅ guardamos el refugio_id real
                }
            }

            header("Location: index.php");
            exit;
        }
    }}