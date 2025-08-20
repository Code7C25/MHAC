<?php
session_start();
require_once 'conexion.php';


$usuario_id = $_SESSION['usuario_id'];

// Consulta: solicitudes del usuario logueado
$sql = "SELECT a.id AS adopcion_id, a.fecha_adopcion, a.estado, 
               m.nombre AS nombre_mascota, m.especie, m.raza, m.edad, m.foto
        FROM adopciones a
        JOIN mascotas m ON a.mascota_id = m.id
        WHERE a.usuario_id = ?
        ORDER BY a.fecha_adopcion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Solicitudes de Adopción - MHAC</title>
    <link rel="stylesheet" href="css/adopcion.css">
</head>
<body>
<header>
    <h1>🐾 Mis Solicitudes de Adopción</h1>
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
                    <p><strong>Estado:</strong> <?= htmlspecialchars($adopcion['estado']) ?></p>
                    <p><strong>Fecha de solicitud:</strong> <?= date('d/m/Y', strtotime($adopcion['fecha_adopcion'])) ?></p>

                    <?php if ($adopcion['foto']): ?>
                        <img src="<?= 'uploads/mascotas/' . htmlspecialchars($adopcion['foto']) ?>" 
                             alt="Foto de <?= htmlspecialchars($adopcion['nombre_mascota']) ?>">
                    <?php endif; ?>

                    <a href="detalle_adopcion.php?id=<?= $adopcion['adopcion_id'] ?>">
                       Ver detalles
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No tenés solicitudes de adopción todavía.  
           <a href="mascotas.php">¡Mirá las mascotas disponibles!</a></p>
    <?php endif; ?>
</main>
</body>
</html>
