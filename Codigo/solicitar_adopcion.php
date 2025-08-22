<?php
session_start();
require_once 'conexion.php';

// Verificar login y rol
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 'adoptante') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Mascota no especificada.";
    exit();
}

$mascota_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Verificar que la mascota exista y esté en adopción
$sql = "SELECT id, nombre, estado FROM mascotas WHERE id = ? AND estado = 'en_adopcion'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $mascota_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Esta mascota no está disponible para adopción.";
    exit();
}
$mascota = $result->fetch_assoc();

// Verificar si el usuario ya tiene una solicitud pendiente
$sql_check = "SELECT id FROM adopciones WHERE mascota_id = ? AND usuario_id = ? AND estado = 'pendiente'";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $mascota_id, $usuario_id);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    echo "⚠️ Ya enviaste una solicitud pendiente para esta mascota.";
    echo "<br><a href='mis_adopciones.php'>Ver mis solicitudes</a>";
    exit();
}

// Insertar la solicitud
$sql_insert = "INSERT INTO adopciones (mascota_id, usuario_id, estado, fecha_solicitud) 
               VALUES (?, ?, 'pendiente', NOW())";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ii", $mascota_id, $usuario_id);

if ($stmt_insert->execute()) {
    echo "✅ Solicitud enviada con éxito para <strong>" . htmlspecialchars($mascota['nombre']) . "</strong>.";
    echo "<br><a href='mis_adopciones.php'>Ver mis solicitudes</a>";
} else {
    echo "❌ Error al enviar la solicitud: " . $conn->error;
}
