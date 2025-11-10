<?php
session_start();
require_once 'conexion.php'; 

// ----------------------------------------------------
// INCLUSIÓN Y USO DE PHPMailer
// ----------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Asegúrate que estas rutas sean correctas desde procesar_donacion.php
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';


// Función de saneamiento de datos
function sanear_input($data) {
    return htmlspecialchars(trim($data));
}

// ----------------------------------------------------
// 1. CAPTURA DE DATOS
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $refugio_id = filter_input(INPUT_POST, 'refugio_id', FILTER_VALIDATE_INT);
    $nombre_usuario = sanear_input($_POST['nombre'] ?? '');
    $email_usuario = filter_input(INPUT_POST, 'email_usuario', FILTER_VALIDATE_EMAIL);
    $tipo_colaboracion = sanear_input($_POST['tipo_colaboracion'] ?? '');
    $mensaje_usuario = sanear_input($_POST['mensaje'] ?? '');
    
    if (!$refugio_id || !$email_usuario || empty($nombre_usuario) || empty($tipo_colaboracion)) {
        $_SESSION['error'] = "⚠️ Error: Faltan datos esenciales o el email no es válido.";
        header("Location: donaciones.php");
        exit;
    }

    // ----------------------------------------------------
    // 2. BUSCAR DATOS DEL REFUGIO (Email de destino)
    // ----------------------------------------------------
    $sql_refugio = "SELECT u.email, r.nombre_refugio 
                    FROM refugios r
                    JOIN usuarios u ON r.usuario_id = u.id
                    WHERE r.id = ?";
                    
    $stmt = $conn->prepare($sql_refugio);
    
    if (!$stmt) {
        $_SESSION['error'] = "❌ Error interno al preparar la consulta de refugio.";
        header("Location: donaciones.php");
        exit;
    }
    
    $stmt->bind_param("i", $refugio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $refugio_data = $result->fetch_assoc();
    $stmt->close();
    
    if (!$refugio_data) {
        $_SESSION['error'] = "❌ Error: Refugio seleccionado no encontrado.";
        header("Location: donaciones.php");
        exit;
    }

    $email_refugio = $refugio_data['email'];
    $nombre_refugio = $refugio_data['nombre_refugio'];
    
    // ----------------------------------------------------
    // 3. REGISTRO EN LA BASE DE DATOS
    // ----------------------------------------------------
    $sql_insert = "INSERT INTO solicitudes_donacion 
                   (refugio_id, nombre_donante, email_donante, tipo_colaboracion, mensaje_donante) 
                   VALUES (?, ?, ?, ?, ?)";
                   
    $stmt_insert = $conn->prepare($sql_insert);
    
    if ($stmt_insert) {
        $stmt_insert->bind_param("issss", $refugio_id, $nombre_usuario, $email_usuario, $tipo_colaboracion, $mensaje_usuario);
        
        if (!$stmt_insert->execute()) {
            error_log("Fallo al registrar solicitud de donación: " . $stmt_insert->error);
        }
        $stmt_insert->close();
    } else {
        error_log("Fallo al preparar INSERT de solicitud de donación: " . $conn->error);
    }
    
    // ----------------------------------------------------
    // 4. ENVÍO DE EMAIL CON PHPMailer
    // ----------------------------------------------------
    $mail = new PHPMailer(true);
    
    try {
        // Configuración SMTP (Usando tus credenciales de solicitar_adopcion.php)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        // TU CUENTA REAL DE MHAC Y TU CLAVE DE APLICACIÓN
        $mail->Username   = 'equipo.mhac@gmail.com';
        $mail->Password   = 'eyon yplb kism xolj'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8'; // Para tildes y eñes

        // Remitente: El sistema MHAC
        $mail->setFrom('equipo.mhac@gmail.com', 'MHAC - Solicitudes de Donación');
        
        // Destino 1: El Refugio (Email de contacto)
        $mail->addAddress($email_refugio, $nombre_refugio);
        // Destino 2: Copia al equipo MHAC para seguimiento (Opcional, pero recomendado)
        $mail->addBCC('equipo.mhac@gmail.com', 'Equipo MHAC');
        
        // Responder al email del Donante
        $mail->addReplyTo($email_usuario, $nombre_usuario);

        $mail->isHTML(true);
        $mail->Subject = 'URGENTE: Oferta de Donación de ' . $nombre_usuario . ' (Vía MHAC)';
        
        // Cuerpo del Correo (Formato HTML)
        $mail->Body = "
            <html>
            <body>
                <h2>Nueva Oferta de Colaboración - Refugio: " . $nombre_refugio . "</h2>
                <p>El usuario <b>" . $nombre_usuario . "</b> ha ofrecido una colaboración para tu refugio a través de Mis Huellitas a Casa.</p>
                <hr>
                <p><b>Tipo de Colaboración:</b> " . $tipo_colaboracion . "</p>
                <p><b>Mensaje del Donante:</b> <i>" . nl2br($mensaje_usuario) . "</i></p>
                <hr>
                <h3>Datos de Contacto:</h3>
                <ul>
                    <li><b>Nombre:</b> " . $nombre_usuario . "</li>
                    <li><b>Email:</b> " . $email_usuario . "</li>
                </ul>
                <p style='color:red;'>¡Por favor, responde directamente al email del donante para coordinar los detalles!</p>
            </body>
            </html>
        ";
        
        $mail->send();
        
        // Éxito: El correo se envió (o al menos se intentó enviar de forma profesional)
        $_SESSION['exito'] = "✅ Solicitud de Donación Enviada a **" . htmlspecialchars($nombre_refugio) . "**."
                           . "<br>Recibirás una respuesta **directamente desde su correo electrónico** para coordinar los detalles. ¡Gracias por tu generosidad!";
        
    } catch (Exception $e) {
        // Fallo en el envío de PHPMailer (común en XAMPP si la clave falla)
        $_SESSION['error'] = "⚠️ Solicitud de donación registrada en el sistema, pero hubo un error de envío del correo. ¡El equipo MHAC está notificado y asegurará la comunicación! Error Mailer: " . $mail->ErrorInfo;
    }

    // Redirección final
    header("Location: donaciones.php");
    exit;

} else {
    // Si acceden directamente, redirigir
    header("Location: donaciones.php");
    exit;
}
?>