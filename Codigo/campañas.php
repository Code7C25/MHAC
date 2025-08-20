<?php
session_start();
require_once 'conexion.php';

// Obtener campañas activas (fecha actual entre inicio y fin)
$hoy = date('Y-m-d');
$sql = "SELECT c.id, c.titulo, c.descripcion, c.fecha_inicio, c.fecha_fin, c.lugar, u.nombre AS organizador
        FROM campañas c
        LEFT JOIN usuarios u ON c.organizador_id = u.id
        WHERE c.fecha_inicio <= ? AND c.fecha_fin >= ?
        ORDER BY c.fecha_inicio DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $hoy, $hoy);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Campañas - MHAC</title>
</head>
<body>
    <h1>Campañas activas - MHAC</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <ul>
            <?php while ($campaña = $result->fetch_assoc()): ?>
                <li>
                    <strong><?= htmlspecialchars($campaña['titulo']) ?></strong><br />
                    <em>Organizador:</em> <?= htmlspecialchars($campaña['organizador']) ?><br />
                    <em>Lugar:</em> <?= htmlspecialchars($campaña['lugar']) ?><br />
                    <em>Desde:</em> <?= htmlspecialchars($campaña['fecha_inicio']) ?> <em>hasta</em> <?= htmlspecialchars($campaña['fecha_fin']) ?><br />
                    <p><?= nl2br(htmlspecialchars($campaña['descripcion'])) ?></p>
                </li>
                <hr />
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay campañas activas por ahora.</p>
    <?php endif; ?>
</body>
</html>
