<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['dador','refugio'])) {
    header("Location: login.php");
    exit;
}

$id_solicitud = (int)$_GET['id'];
$accion = $_GET['accion'] === 'aprobar' ? 'aprobada' : 'rechazada';

// Verificamos que la solicitud pertenezca a una mascota del usuario
$sql = "SELECT s.id, u.email, u.nombre AS nombre_adoptante,
               m.nombre AS nombre_mascota, m.usuario_id
        FROM solicitudes_adopcion s
        JOIN usuarios u ON s.adoptante_id = u.id
        JOIN mascotas m ON s.mascota_id = m.id
        WHERE s.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_solicitud);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data || $data['usuario_id'] != $_SESSION['usuario_id']) {
    die("No tenés permiso para gestionar esta solicitud");
}

// Actualizamos estado
$up = $conn->prepare("UPDATE solicitudes_adopcion SET estado=? WHERE id=?");
$up->bind_param("si", $accion, $id_solicitud);
$up->execute();

// Armamos email
$to = $data['email'];
$subject = "Solicitud de adopción {$accion}";
if ($accion === 'aprobada') {
    $message = "¡Hola {$data['nombre_adoptante']}!\n\n".
               "Tu solicitud para adoptar a {$data['nombre_mascota']} fue APROBADA.\n".
               "El refugio/dador se pondrá en contacto para coordinar la entrega.";
} else {
    $message = "Hola {$data['nombre_adoptante']},\n\n".
               "Tu solicitud para adoptar a {$data['nombre_mascota']} fue RECHAZADA.\n".
               "Gracias por tu interés.";
}

$headers = "From: adopciones@mhac.org\r\n";
mail($to, $subject, $message, $headers);

header("Location: mis_solicitudes.php?msg=ok");
exit;
