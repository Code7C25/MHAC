<?php
session_start();
require_once 'conexion.php';

// Verificar que esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_evaluado_id = intval($_POST['usuario_evaluado_id']);
    $usuario_evaluador_id = $_SESSION['usuario_id'];
    $estrellas = intval($_POST['estrellas']);
    $comentario = trim($_POST['comentario'] ?? '');
    
    // Validaciones
    if ($usuario_evaluado_id == $usuario_evaluador_id) {
        die("No puedes dejarte una referencia a ti mismo.");
    }
    
    if ($estrellas < 1 || $estrellas > 5) {
        die("Calificación inválida.");
    }
    
    // Verificar que no haya dejado una referencia antes
    $sql_check = "SELECT id FROM referencias 
                  WHERE usuario_evaluado_id = ? AND usuario_evaluador_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $usuario_evaluado_id, $usuario_evaluador_id);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows > 0) {
        die("Ya dejaste una referencia para este usuario.");
    }
    
    // Insertar referencia
    $sql = "INSERT INTO referencias (usuario_evaluado_id, usuario_evaluador_id, estrellas, comentario) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $usuario_evaluado_id, $usuario_evaluador_id, $estrellas, $comentario);
    
    if ($stmt->execute()) {
        header("Location: perfil.php?id=" . $usuario_evaluado_id . "&msg=referencia_ok");
    } else {
        die("Error al guardar la referencia: " . $conn->error);
    }
} else {
    header("Location: index.php");
    exit;
}
?>