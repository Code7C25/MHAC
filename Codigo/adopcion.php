<?php
session_start();
require_once 'conexion.php';

// Consulta para obtener adopciones activas (por ejemplo, estado = 'activa')
$sql = "SELECT a.id AS adopcion_id, a.fecha_adopcion, a.estado, 
               m.nombre AS nombre_mascota, m.especie, m.raza, m.edad, m.foto
        FROM adopciones a
        JOIN mascotas m ON a.mascota_id = m.id
        WHERE a.estado = 'activa'
        ORDER BY a.fecha_adopcion DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Adopciones - MHAC</title>
</head>
<body>
    <header>
        <h1>Adopciones activas</h1>
    </header>

    <main>
        <?php if ($result && $result->num_rows > 0): ?>
            <ul>
                <?php while ($adopcion = $result->fetch_assoc()): ?>
                    <li>
                        <h2><?= htmlspecialchars($adopcion['nombre_mascota']) ?></h2>
                        <p><strong>Especie:</strong> <?= htmlspecialchars($adopcion['especie']) ?></p>
                        <p><strong>Raza:</strong> <?= htmlspecialchars($adopcion['raza']) ?></p>
                        <p><strong>Edad:</strong> <?= intval($adopcion['edad']) ?> meses</p>
                        <?php if ($adopcion['foto']): ?>
                            <img src="<?= 'uploads/mascotas/' . htmlspecialchars($adopcion['foto']) ?>" alt="<?= htmlspecialchars($adopcion['nombre_mascota']) ?>" style="max-width:200px;">
                        <?php endif; ?>
                        <p><strong>Fecha de adopción:</strong> <?= htmlspecialchars($adopcion['fecha_adopcion']) ?></p>
                        <p><strong>Estado:</strong> <?= htmlspecialchars($adopcion['estado']) ?></p>
                    </li>
                    <hr>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No hay adopciones activas registradas por ahora.</p>
        <?php endif; ?>
    </main>
</body>
</html>
