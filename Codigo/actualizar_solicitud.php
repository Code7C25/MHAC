<?php
// actualizar_solicitud.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'conexion.php';

// incluir PHPMailer (sin composer - igual que en solicitar_adopcion.php)
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- 1) Leer POST (acepta varios nombres para ser compatible con tus formularios)
$id = 0;
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
} elseif (isset($_POST['solicitud_id'])) {
    $id = intval($_POST['solicitud_id']);
}

$nuevo_estado = null;
if (isset($_POST['estado'])) {
    $nuevo_estado = $_POST['estado']; // 'aprobada' o 'rechazada'
} elseif (isset($_POST['accion'])) {
    // si venía 'accion' con 'aprobar'/'rechazar'
    $accion = $_POST['accion'];
    if ($accion === 'aprobar') $nuevo_estado = 'aprobada';
    elseif ($accion === 'rechazar') $nuevo_estado = 'rechazada';
}

// seguridad mínima
$nuevo_estado = ($nuevo_estado === 'aprobada') ? 'aprobada' : (($nuevo_estado === 'rechazada') ? 'rechazada' : null);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id <= 0 || $nuevo_estado === null) {
    $_SESSION['mensaje'] = "Datos inválidos o no se recibió la solicitud correctamente.";
    header("Location: mis_solicitudes.php");
    exit;
}

// --- 2) Detectar qué tabla existe en tu DB: 'solicitudes_adopcion' o 'adopciones'
$table = 'solicitudes_adopcion';
$res_check = $conn->query("SHOW TABLES LIKE 'solicitudes_adopcion'");
if (!$res_check || $res_check->num_rows === 0) {
    // si no existe, usamos 'adopciones' (según tu DB actual)
    $table = 'adopciones';
}

// --- 3) Actualizar estado en la BD
$sql_up = "UPDATE {$table} SET estado = ? WHERE id = ?";
$stmt_up = $conn->prepare($sql_up);
if (!$stmt_up) {
    $_SESSION['mensaje'] = "Error en la base (prepare update): " . $conn->error;
    header("Location: mis_solicitudes.php");
    exit;
}
$stmt_up->bind_param("si", $nuevo_estado, $id);
if (!$stmt_up->execute()) {
    $_SESSION['mensaje'] = "Error al actualizar el estado: " . $stmt_up->error;
    header("Location: mis_solicitudes.php");
    exit;
}

// --- 4) Traer datos para el correo (nombre/email del solicitante y nombre de la mascota)
$sql_datos = "SELECT sa.nombre AS adoptante, sa.email, sa.telefono, m.nombre AS mascota
              FROM {$table} sa
              JOIN mascotas m ON sa.mascota_id = m.id
              WHERE sa.id = ?";
$stmt_datos = $conn->prepare($sql_datos);
if (!$stmt_datos) {
    $_SESSION['mensaje'] = "Solicitud actualizada, pero error en prepare datos: " . $conn->error;
    header("Location: mis_solicitudes.php");
    exit;
}
$stmt_datos->bind_param("i", $id);
$stmt_datos->execute();
$result = $stmt_datos->get_result();
$datos = $result->fetch_assoc();

if (!$datos) {
    $_SESSION['mensaje'] = "Solicitud actualizada, pero no se encontraron datos para enviar el correo.";
    header("Location: mis_solicitudes.php");
    exit;
}

// --- 5) Armar y enviar correo (igual que en solicitar_adopcion.php)
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'equipo.mhac@gmail.com';            // tu correo
    $mail->Password   = 'eyon yplb kism xolj';             // tu app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('equipo.mhac@gmail.com', 'MHAC');
    $mail->addAddress($datos['email'], $datos['adoptante']);

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = "Estado de tu solicitud de adopción";

    $color = $nuevo_estado === 'aprobada' ? 'green' : 'red';
    $mensajeHTML = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color:#4CAF50;'>🐾 ¡Hola " . htmlspecialchars($datos['adoptante']) . "!</h2>
            <p>Tu solicitud para adoptar a <strong>" . htmlspecialchars($datos['mascota']) . "</strong> fue 
            <span style='font-weight:bold; color:{$color};'>" . ucfirst($nuevo_estado) . "</span>.</p>
            " . (
                $nuevo_estado === 'aprobada'
                ? "<p>Pronto nos pondremos en contacto para coordinar la entrega. 😊</p>"
                : "<p>Lamentablemente tu solicitud fue rechazada 💔. Pero en MHAC hay muchas mascotas que esperan familia 💕.</p>"
            ) . "
            <hr>
            <p style='font-size:12px; color:#555;'>Equipo MHAC</p>
        </body>
        </html>
    ";

    $mail->Body = $mensajeHTML;
    $mail->AltBody = "Hola {$datos['adoptante']},\nTu solicitud para adoptar a {$datos['mascota']} fue {$nuevo_estado}.\n\nEquipo MHAC";

    $mail->send();
    $_SESSION['mensaje'] = "Solicitud actualizada y notificación enviada correctamente.";
} catch (Exception $e) {
    // si falla el mail, igual la solicitud ya está actualizada
    $_SESSION['mensaje'] = "Solicitud actualizada, pero no se pudo enviar el correo. Error: " . $mail->ErrorInfo;
    // opcional: registrar el error al log
    error_log("PHPMailer error: " . $mail->ErrorInfo);
}

// --- 6) Redirigir de vuelta
header("Location: solicitudes_adopcion_refugio_dador.php");
exit;
