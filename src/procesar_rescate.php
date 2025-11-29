<?php
session_start();
require_once 'conexion.php'; 

// Función para redirigir con mensaje y ancla
function redirigir_con_feedback($mensaje, $exito) {
    $_SESSION['rescate_mensaje'] = $mensaje;
    $_SESSION['rescate_exito'] = $exito;
    header("Location: rescates.php#compartir");
    exit;
}

// Redirigir si no hay sesión iniciada o no es POST
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: rescates.php");
    exit;
}

// ----------------------------------------------------
// 1. CAPTURAR Y SANEAR DATOS DE TEXTO
// ----------------------------------------------------
$titulo = trim($_POST['titulo_historia'] ?? '');
$mascota_nombre = trim($_POST['mascota_nombre'] ?? '');
$rescatista = trim($_POST['rescatista'] ?? '');
$email_contacto = filter_input(INPUT_POST, 'email_contacto', FILTER_VALIDATE_EMAIL);
$historia = $_POST['historia'] ?? ''; 

// ----------------------------------------------------
// 2. VALIDACIÓN DE DATOS
// ----------------------------------------------------
if (empty($titulo) || empty($mascota_nombre) || empty($rescatista) || !$email_contacto || empty($historia)) {
    redirigir_con_feedback("⚠️ Error: Todos los campos de texto son obligatorios y el email debe ser válido.", false);
}


// ----------------------------------------------------
// 3. MANEJO Y SUBIDA DE LA IMAGEN (versión robusta para XAMPP)
// ----------------------------------------------------
$foto_url = null;
$directorio_subida = __DIR__ . "/uploads/rescates/";

// Crear carpeta si no existe
if (!is_dir($directorio_subida)) {
    mkdir($directorio_subida, 0777, true);
}

if (isset($_FILES['foto_rescate']) && $_FILES['foto_rescate']['error'] === UPLOAD_ERR_OK) {
    $archivo_temp = $_FILES['foto_rescate']['tmp_name'];
    $nombre_original = $_FILES['foto_rescate']['name'];
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

    // Extensiones permitidas
    $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($extension, $ext_permitidas)) {
        redirigir_con_feedback("❌ Solo se permiten imágenes JPG, PNG o GIF.", false);
    }

    // Validar tipo MIME manualmente (sin getimagesize)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_real = finfo_file($finfo, $archivo_temp);
    finfo_close($finfo);

    $mime_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($tipo_real, $mime_permitidos)) {
        redirigir_con_feedback("❌ El archivo no parece una imagen válida. Tipo detectado: " . htmlspecialchars($tipo_real), false);
    }

    // Generar nombre único y mover archivo
    $nombre_archivo = uniqid('rescate_', true) . '.' . $extension;
    $ruta_destino = $directorio_subida . $nombre_archivo;

    if (move_uploaded_file($archivo_temp, $ruta_destino)) {
        $foto_url = "uploads/rescates/" . $nombre_archivo;
    } else {
        redirigir_con_feedback("❌ Error al guardar la imagen. Revisá los permisos de la carpeta /uploads/rescates/.", false);
    }
} else {
    redirigir_con_feedback("⚠️ Debes subir una imagen válida (máx. 2 MB).", false);
}


// ----------------------------------------------------
// 4. GUARDAR EN LA BASE DE DATOS
// ----------------------------------------------------
$sql = "INSERT INTO rescates (titulo_historia, mascota_nombre, rescatista, historia, foto_url, email_contacto, estado) VALUES (?, ?, ?, ?, ?, ?, 'Pendiente')";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    redirigir_con_feedback("❌ Error en la base de datos: Fallo al preparar la consulta SQL. Verifica si la tabla 'rescates' existe y la conexión es correcta.", false);
}

$stmt->bind_param("ssssss", $titulo, $mascota_nombre, $rescatista, $historia, $foto_url, $email_contacto);

if ($stmt->execute()) {
    // Éxito: Redirige con mensaje de éxito
    redirigir_con_feedback("✅ ¡Historia de rescate enviada con éxito! Ahora te explicamos por qué no la ves.", true);
} else {
    redirigir_con_feedback("❌ Error al guardar la historia en la BD: " . $stmt->error, false);
}