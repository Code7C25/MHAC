<?php
session_start();
require_once 'conexion.php';

// 1. Obtener el ID de la historia de la URL
$rescate_id = $_GET['id'] ?? null;

// Si no hay ID, redirigir al listado (rescates.php)
if (!$rescate_id || !is_numeric($rescate_id)) {
    header("Location: rescates.php");
    exit;
}

// 2. Consulta segura (Prepared Statement) para obtener el detalle
// NOTA: Usamos la tabla 'rescates' directamente.
$stmt = $conn->prepare("
    SELECT titulo_historia, mascota_nombre, rescatista, historia, foto_url, fecha_creacion
    FROM rescates
    WHERE id = ?
");
$stmt->bind_param("i", $rescate_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Verificar si la historia existe
if ($result->num_rows === 0) {
    header("Location: rescates.php"); // Si no existe, volver al listado
    exit;
}

$historia_detalle = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($historia_detalle['titulo_historia']) ?> - MHAC</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/rescates.css"> 
    </head>

<body>
<header>
    <h1>💖 <?= htmlspecialchars($historia_detalle['titulo_historia']) ?></h1>
</header>
<main>
    <article class="historia-detalle-full">
        <a href="rescates.php" class="volver-listado-rescate">← Volver a Historias que Inspiran</a>
        
        <?php if (!empty($historia_detalle['foto_url'])): ?>
            <img src="<?= htmlspecialchars($historia_detalle['foto_url']) ?>" 
                 alt="Foto de <?= htmlspecialchars($historia_detalle['mascota_nombre']) ?> rescatada" 
                 class="foto-principal-rescate">
        <?php else: ?>
            <div class="sin-foto-placeholder">
                [Imagen de la mascota rescatada no disponible]
            </div>
        <?php endif; ?>

        <h2>La historia de <?= htmlspecialchars($historia_detalle['mascota_nombre']) ?></h2>
        
        <div class="relato-completo">
            <p><?= nl2br(htmlspecialchars($historia_detalle['historia'])) ?></p>
        </div>

        <div class="autor-info-full">
            <p>
                <small>
                    Historia compartida por 
                    <b><?= htmlspecialchars($historia_detalle['rescatista']) ?></b>
                    el <?= date("d/m/Y", strtotime($historia_detalle['fecha_creacion'])) ?>.
                </small>
            </p>
        </div>
        
        <a href="rescates.php" class="volver-listado-rescate">← Volver a Historias que Inspiran</a>
    </article>
</main>
</body>
</html>