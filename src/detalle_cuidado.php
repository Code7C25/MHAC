<?php
session_start();
require_once 'conexion.php';

// 1. Obtener el ID del artículo de la URL
$cuidado_id = $_GET['id'] ?? null;

// Si no hay ID, redirigir al listado
if (!$cuidado_id || !is_numeric($cuidado_id)) {
    header("Location: info.php");
    exit;
}

// 2. Consulta segura (Prepared Statement) para obtener el detalle
$stmt = $conn->prepare("
    SELECT c.*, u.nombre, u.apellido, u.rol
    FROM cuidados c
    JOIN usuarios u ON c.autor_id = u.id
    WHERE c.id = ?
");
$stmt->bind_param("i", $cuidado_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Verificar si el artículo existe
if ($result->num_rows === 0) {
    header("Location: info.php"); // Si no existe, volver al listado
    exit;
}

$cuidado_detalle = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($cuidado_detalle['titulo']) ?> - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/info.css"> 
</head>

<body>
<header>
  <h1><?= htmlspecialchars($cuidado_detalle['titulo']) ?></h1>
</header>
<main>
    <article class="cuidado-detalle-full">
        <a href="info.php" class="volver-listado">← Volver al Listado de Consejos</a>
        
        <p class="categoria-tag-full">Categoría: <?= htmlspecialchars($cuidado_detalle['categoria']) ?></p>
        
        <div class="contenido-completo">
            <?= $cuidado_detalle['contenido'] ?>
        </div>

        <div class="autor-info-full">
            <p>
                <small>
                    Publicado por 
                    <?= htmlspecialchars($cuidado_detalle['nombre'].' '.$cuidado_detalle['apellido']) ?> 
                    (<?= ucfirst($cuidado_detalle['rol']) ?>) 
                    <?php if (!empty($cuidado_detalle['autor'])): ?>
                        | Créditos: <?= htmlspecialchars($cuidado_detalle['autor']) ?>
                    <?php endif; ?>
                    <?= date("d/m/Y", strtotime($cuidado_detalle['fecha'])) ?>
                </small>
            </p>
        </div>
        
        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $cuidado_detalle['autor_id']): ?>
            <a href="editar_cuidado.php?id=<?= $cuidado_detalle['id'] ?>" class="btn-editar">Editar Artículo</a>
        <?php endif; ?>

        <a href="info.php" class="volver-listado">← Volver al Listado de Consejos</a>
    </article>
</main>
</body>
</html>