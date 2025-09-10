<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'conexion.php';

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id = 0;
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
} elseif (isset($_POST['solicitud_id'])) {
    $id = intval($_POST['solicitud_id']);
}

$nuevo_estado = null;
if (isset($_POST['estado'])) {
    $nuevo_estado = $_POST['estado'];
} elseif (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    if ($accion === 'aprobar') $nuevo_estado = 'aprobada';
    elseif ($accion === 'rechazar') $nuevo_estado = 'rechazada';
}

$nuevo_estado = ($nuevo_estado === 'aprobada') ? 'aprobada' : (($nuevo_estado === 'rechazada') ? 'rechazada' : null);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id <= 0 || $nuevo_estado === null) {
    $_SESSION['mensaje'] = "Datos inválidos o no se recibió la solicitud correctamente.";
    header("Location: solicitudes_adopcion_refugio_dador.php");
    exit;
}

// Verificar tabla
$table = 'solicitudes_adopcion';
$res_check = $conn->query("SHOW TABLES LIKE 'solicitudes_adopcion'");
if (!$res_check || $res_check->num_rows === 0) {
    $table = 'adopciones';
}

// Actualizar estado de la solicitud
$sql_up = "UPDATE {$table} SET estado = ? WHERE id = ?";
$stmt_up = $conn->prepare($sql_up);
$stmt_up->bind_param("si", $nuevo_estado, $id);
$stmt_up->execute();

// Actualizar estado de la mascota según el resultado
if ($nuevo_estado === 'aprobada') {
    // Mascota adoptada
    $sql_get_mascota = "SELECT mascota_id FROM {$table} WHERE id = ?";
    $stmt_get = $conn->prepare($sql_get_mascota);
    $stmt_get->bind_param("i", $id);
    $stmt_get->execute();
    $res_get = $stmt_get->get_result();
    $row = $res_get->fetch_assoc();
    $mascota_id = $row['mascota_id'] ?? null;

    if ($mascota_id) {
        $nuevo_estado_mascota = $nuevo_estado === 'aprobada' ? 'adoptado' : 'en_adopcion';

        $sql_update_mascota = "UPDATE mascotas SET estado = ? WHERE id = ?";
        $stmt_mascota = $conn->prepare($sql_update_mascota);
        $stmt_mascota->bind_param("si", $nuevo_estado_mascota, $mascota_id);
        $stmt_mascota->execute();
    }

} elseif ($nuevo_estado === 'rechazada') {
    // Mantenerla en adopción
    $sql_mascota = "UPDATE mascotas 
                    SET estado = 'en_adopcion' 
                    WHERE id = (SELECT mascota_id FROM {$table} WHERE id = ?)";
    $stmt_mascota = $conn->prepare($sql_mascota);
    $stmt_mascota->bind_param("i", $id);
    $stmt_mascota->execute();
}

$sql_datos = "SELECT 
                    CONCAT(a.nombre, ' ', a.apellido) AS adoptante,
                    a.email AS email,
                    a.telefono AS telefono,
                    m.nombre AS mascota,
                    m.foto,
                    m.especie,
                    m.raza,
                    m.edad_categoria,
                    m.tamano,
                    m.pelaje,
                    m.color,
                    m.comportamiento,
                    m.descripcion,
                    u.nombre AS usuario_nombre,
                    u.apellido AS usuario_apellido,
                    u.email AS usuario_email,
                    u.telefono AS usuario_telefono
              FROM {$table} sa
              JOIN mascotas m ON sa.mascota_id = m.id
              JOIN usuarios a ON sa.usuario_id = a.id
              LEFT JOIN usuarios u ON m.usuario_id = u.id
              WHERE sa.id = ?";

$stmt_datos = $conn->prepare($sql_datos);
if (!$stmt_datos) {
    die("Error al preparar SELECT de solicitud: " . $conn->error);
}
$stmt_datos->bind_param("i", $id);
$stmt_datos->execute();
$result = $stmt_datos->get_result();
$datos = $result->fetch_assoc();

// Preparar PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'equipo.mhac@gmail.com';
    $mail->Password   = 'eyon yplb kism xolj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('equipo.mhac@gmail.com', 'MHAC');
    $mail->addAddress($datos['email'], $datos['adoptante']);

    // Foto embebida si existe
    $foto_html = "";
    if (!empty($datos['foto'])) {
        $ruta_foto = __DIR__ . "/uploads/mascotas/" . $datos['foto'];
        if (file_exists($ruta_foto)) {
            $mail->addEmbeddedImage($ruta_foto, 'fotoMascota', $datos['foto']);
            $foto_html = "<div style='text-align:center; margin:20px 0;'>
                            <img src='cid:fotoMascota' alt='Foto de {$datos['mascota']}' 
                                 style='max-width:300px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.2);'>
                          </div>";
        }
    }

    // Datos contacto publicador
    $publicador = $datos['nombre_refugio'] 
        ? $datos['nombre_refugio'] 
        : trim($datos['usuario_nombre'] . ' ' . ($datos['usuario_apellido'] ?? ''));
    $contacto_html = "<p><strong>📞 Teléfono:</strong> {$datos['usuario_telefono']}<br>
                      <strong>📧 Email:</strong> {$datos['usuario_email']}</p>";

    // Características de la mascota
    $caracteristicas_html = "
        <ul style='font-size:15px; color:#555; line-height:1.5;'>
            <li><strong>Especie:</strong> {$datos['especie']}</li>
            <li><strong>Raza:</strong> {$datos['raza']}</li>
            <li><strong>Edad:</strong> {$datos['edad_categoria']}</li>
            <li><strong>Tamaño:</strong> {$datos['tamano']}</li>
            <li><strong>Pelaje:</strong> {$datos['pelaje']}</li>
            <li><strong>Color:</strong> {$datos['color']}</li>
            <li><strong>Comportamiento / Cuidados:</strong> {$datos['comportamiento']}</li>
        </ul>
        <p style='font-size:15px; color:#555;'><strong>Descripción:</strong><br>" . nl2br(htmlspecialchars($datos['descripcion'])) . "</p>
    ";

    // Contenido HTML del correo
    $mail->isHTML(true);
    $mail->Subject = "Estado de tu solicitud";
    $color = $nuevo_estado === 'aprobada' ? '#2e7d32' : '#c62828';

    $mensajeHTML = "
        <html>
        <body style='font-family: Arial, sans-serif; background-color:#f8f9fa; padding:20px;'>
            <div style='max-width:600px; margin:auto; background:white; border-radius:12px; padding:25px; box-shadow:0 4px 12px rgba(0,0,0,0.1);'>
                <h2 style='color:#4CAF50; text-align:center;'>🐾 ¡Hola " . htmlspecialchars($datos['adoptante']) . "!</h2>
                <p style='font-size:16px; color:#333;'>Tu solicitud para adoptar a <strong>" . htmlspecialchars($datos['mascota']) . "</strong> ha sido:</p>
                <h3 style='text-align:center; color:{$color}; font-size:22px;'>" . strtoupper($nuevo_estado) . "</h3>
                {$foto_html}
                " . ($nuevo_estado === 'aprobada'
                    ? "<p style='font-size:16px; color:#444;'>🎉 ¡Felicitaciones! Muy pronto coordinaremos la entrega para que <strong>{$datos['mascota']}</strong> pueda unirse a tu familia.</p>"
                    : "<p style='font-size:16px; color:#444;'>💔 Lamentamos informarte que tu solicitud fue rechazada. No te desanimes, todavía hay muchos amigos peludos que buscan hogar.</p>"
                ) . "
                <hr style='margin:20px 0;'>
                <h4 style='color:#333;'>📋 Características de la mascota:</h4>
                {$caracteristicas_html}
                <hr style='margin:20px 0;'>
                <h4 style='color:#333;'>ℹ️ Publicado por:</h4>
                <p style='font-size:15px; color:#555;'><strong>{$publicador}</strong></p>
                {$contacto_html}
                <hr style='margin:20px 0;'>
                <p style='font-size:14px; color:#777; text-align:center;'>Si querés saber más sobre por qué tu solicitud fue " . $nuevo_estado . " o necesitás más información,<br>no dudes en comunicarte con el publicador.</p>
                <p style='font-size:13px; color:#999; text-align:center;'>Equipo MHAC 🐶🐱</p>
            </div>
        </body>
        </html>
    ";

    $mail->Body = $mensajeHTML;
    $mail->AltBody = "Hola {$datos['adoptante']},\nTu solicitud para adoptar a {$datos['mascota']} fue {$nuevo_estado}.\n\nCaracterísticas:\nEspecie: {$datos['especie']}\nRaza: {$datos['raza']}\nEdad: {$datos['edad_categoria']}\nTamaño: {$datos['tamano']}\nPelaje: {$datos['pelaje']}\nColor: {$datos['color']}\nComportamiento: {$datos['comportamiento']}\nDescripción: {$datos['descripcion']}\n\nContacto publicador: {$publicador}, Tel: {$datos['usuario_telefono']}, Email: {$datos['usuario_email']}\n\nEquipo MHAC";

    $mail->send();
    $_SESSION['mensaje'] = "Solicitud actualizada y notificación enviada correctamente.";
} catch (Exception $e) {
    $_SESSION['mensaje'] = "Solicitud actualizada, pero no se pudo enviar el correo. Error: " . $mail->ErrorInfo;
    error_log("PHPMailer error: " . $mail->ErrorInfo);
}

header("Location: solicitudes_adopcion_refugio_dador.php");
exit;
