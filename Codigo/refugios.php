<?php
session_start();
require_once 'conexion.php';

// Obtener todos los refugios
$sql = "SELECT id, usuario_id, nombre_refugio, direccion, telefono, email, descripcion FROM refugios ORDER BY nombre_refugio ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Refugios - MHAC</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/refugios.css">
</head>
<body>
    <header>
        <h1>Refugios</h1>
    </header>

    <main>
        <?php if ($result && $result->num_rows > 0): ?>
            <ul>
                <?php while ($refugio = $result->fetch_assoc()): ?>
                    <li>
                        <h2><?= htmlspecialchars($refugio['nombre_refugio']) ?></h2>
                        <p><strong>Dirección:</strong> <?= htmlspecialchars($refugio['direccion']) ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($refugio['telefono']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($refugio['email']) ?></p>
                        <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($refugio['descripcion'])) ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No hay refugios registrados por ahora.</p>
        <?php endif; ?>
    </main>
    <a href="index.php" class="btn-volver">
            <span class="btn-volver-icon">←</span>
            Volver al inicio
        </a>
</body>
</html>