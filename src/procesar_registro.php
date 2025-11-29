<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol = $_POST['rol'];  // primero definimos el rol
    $nombre = trim($_POST['nombre']);
    $apellido = ($rol === 'refugio') ? '' : trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['password'];

    $email = trim($_POST['email']);
    
    // CAMBIO CLAVE: Leer el campo oculto concatenado
    $telefono = trim($_POST['telefono_completo']); 
    
    $password = $_POST['password'];

    // ----------------------------------------------------
    // VALIDACIÓN PHP DE FORMATO (BACKEND)
    // ----------------------------------------------------
    
    // 1. Validación de Email (Refuerzo de seguridad)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['registro_error'] = "El formato del correo electrónico no es válido.";
        header("Location: registro.php");
        exit;
    }
    
    // 2. Validación de Teléfono
    if (empty($telefono) || $telefono === '+') { // Verifica que no esté vacío
        $_SESSION['registro_error'] = "Debes ingresar un número de teléfono válido, incluyendo el código de país.";
        header("Location: registro.php");
        exit;
    }

    // Validación básica
    if (strlen($password) < 6) {
        $_SESSION['registro_error'] = "La contraseña debe tener al menos 6 caracteres.";
        header("Location: registro.php");
        exit;
    }

    // Hashear contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (nombre, apellido, email, telefono, password_hash, rol) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nombre, $apellido, $email, $telefono, $password_hash, $rol);
        $stmt->execute();

        // Si es refugio, creamos entrada vacía en `refugios`
        if ($rol === 'refugio') {
            $usuario_id = $stmt->insert_id;
            $sql_ref = "INSERT INTO refugios (usuario_id, nombre_refugio) VALUES (?, ?)";
            $stmt_ref = $conn->prepare($sql_ref);
            $stmt_ref->bind_param("is", $usuario_id, $nombre);
            $stmt_ref->execute();
        }

        $_SESSION['registro_exito'] = "Registro exitoso. Ahora podés iniciar sesión.";
        header("Location: login.php");
        exit;

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() === 1062) {
            $_SESSION['registro_error'] = "El email ya está registrado.";
        } else {
            $_SESSION['registro_error'] = "Error al registrar: " . $e->getMessage();
        }
        header("Location: registro.php");
        exit;
    }
} else {
    header("Location: registro.php");
    exit;
}
