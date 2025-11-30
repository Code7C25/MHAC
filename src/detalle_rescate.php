<?php
session_start();
require_once 'conexion.php';

// 1. Obtener el ID
$rescate_id = $_GET['id'] ?? null;

// Validación básica
if (!$rescate_id || !is_numeric($rescate_id)) {
    header("Location: rescates.php");
    exit;
}

// 2. Consulta (Ya forzamos UTF-8 en conexion.php, así que esto traerá los datos bien)
$stmt = $conn->prepare("SELECT titulo_historia, mascota_nombre, rescatista, historia, foto_url, fecha_creacion FROM rescates WHERE id = ?");
$stmt->bind_param("i", $rescate_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Verificar existencia
if ($result->num_rows === 0) {
    header("Location: rescates.php");
    exit;
}

$historia_detalle = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($historia_detalle['titulo_historia'], ENT_QUOTES, 'UTF-8') ?> - MHAC</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/rescates.css">
    </head>

<body>

<a href="rescates.php" class="volver-inicio">
    <span>←</span> Volver al Listado
</a>

<header>
    <h1>💖 <?= htmlspecialchars($historia_detalle['titulo_historia'], ENT_QUOTES, 'UTF-8') ?></h1>
</header>

<main>
    <article class="historia-detalle-full">

        <?php if (!empty($historia_detalle['foto_url'])): ?>
            <img src="<?= htmlspecialchars($historia_detalle['foto_url'], ENT_QUOTES, 'UTF-8') ?>" 
                 alt="Foto de <?= htmlspecialchars($historia_detalle['mascota_nombre'], ENT_QUOTES, 'UTF-8') ?>" 
                 class="foto-principal-rescate">
        <?php else: ?>
            <div class="sin-foto-placeholder">
                [Imagen no disponible]
            </div>
        <?php endif; ?>

        <h2>La historia de <?= htmlspecialchars($historia_detalle['mascota_nombre'], ENT_QUOTES, 'UTF-8') ?></h2>
        
        <div class="relato-completo">
            <?php 
            // 3. CORRECCIÓN CRÍTICA PARA EL TEXTO:
            // Como usas TinyMCE, el texto ya viene con HTML (<p>, <b>). 
            // Usamos html_entity_decode para que se interpreten los acentos y las etiquetas HTML correctamente.
            // NO usamos htmlspecialchars aquí porque rompería el formato del editor de texto.
            echo html_entity_decode($historia_detalle['historia']); 
            ?>
        </div>

        <div class="autor-info-full">
            <p>
                <small>
                    Historia compartida por 
                    <b><?= htmlspecialchars($historia_detalle['rescatista'], ENT_QUOTES, 'UTF-8') ?></b>
                    el <?= date("d/m/Y", strtotime($historia_detalle['fecha_creacion'])) ?>.
                </small>
            </p>
        </div>
        
    </article>
</main>

<style>
    .goog-te-banner-frame.skiptranslate { display: none !important; } 
    body { top: 0px !important; }
    #google_translate_element { display: none; }
</style>
<div id="google_translate_element"></div>
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'es', includedLanguages: 'es,en', autoDisplay: false}, 'google_translate_element');
    }
</script>
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<script>
    // Script para mantener el idioma seleccionado
    window.addEventListener('load', function() {
        // Solo verificamos si hay cookie para ajustar visuales si fuera necesario, 
        // Google hace el resto automático al cambiar de página.
        if (document.cookie.includes('googtrans=/es/en')) {
            // Aquí podrías forzar estilos si tuvieras un botón de idioma en esta página,
            // pero como es una página interna de detalle, el usuario suele cambiar el idioma en el header principal.
        }
    });
</script>

</body>
</html>