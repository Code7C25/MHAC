<?php
var_dump($_GET);
exit;

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

// Insertar la solicitud
$sql_insert = "INSERT INTO adopciones (mascota_id, usuario_id, estado) VALUES (?, ?, 'pendiente')";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ii", $mascota_id, $usuario_id);

if ($stmt_insert->execute()) {
    echo "✅ Solicitud enviada con éxito. Esperá la aprobación del refugio.";
    echo "<br><a href='mis_adopciones.php'>Ver mis solicitudes</a>";
} else {
    echo "❌ Error al enviar la solicitud: " . $conn->error;
}
