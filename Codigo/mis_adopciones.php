<?php
session_start();
require_once 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Traer las adopciones con datos de la mascota
$sql = "SELECT 
            a.id AS adopcion_id, 
            a.fecha_solicitud, 
            a.estado, 
            a.nombre, 
            a.email, 
            a.telefono, 
            a.domicilio, 
            a.edad, 
            a.vivienda, 
            a.experiencia, 
            m.nombre AS nombre_mascota, 
            m.especie, 
            m.raza, 
            m.edad_categoria, 
            m.foto 
        FROM adopciones a 
        JOIN mascotas m ON a.mascota_id = m.id 
        WHERE a.usuario_id = ? 
        ORDER BY a.fecha_solicitud DESC";

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
    <a href="adopcion.php" class="">
            <span class="">←</span>
            Volver al inicio
    </a>
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
                    <p><strong>Especie:</strong> <?= ucfirst(htmlspecialchars($adopcion['especie'])) ?></p>
                    <p><strong>Raza:</strong> <?= htmlspecialchars($adopcion['raza']) ?></p>
                    <p><strong>Edad mascota:</strong> <?= ucfirst(htmlspecialchars($adopcion['edad_categoria'])) ?></p>
                    <p><strong>Estado de solicitud:</strong> <?= ucfirst(htmlspecialchars($adopcion['estado'])) ?></p>
                    <p><strong>Fecha de solicitud:</strong> <?= date('d/m/Y', strtotime($adopcion['fecha_solicitud'])) ?></p>
                    <hr>
                    <p><strong>Tu nombre:</strong> <?= htmlspecialchars($adopcion['nombre']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($adopcion['email']) ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($adopcion['telefono']) ?></p>
                    <p><strong>Domicilio:</strong> <?= htmlspecialchars($adopcion['domicilio']) ?></p>
                    <p><strong>Edad:</strong> <?= intval($adopcion['edad']) ?></p>
                    <p><strong>Tipo de vivienda:</strong> <?= htmlspecialchars($adopcion['vivienda']) ?></p>
                    <p><strong>Experiencia con mascotas:</strong> <?= htmlspecialchars($adopcion['experiencia']) ?></p>
                    
                    <?php if (!empty($adopcion['foto'])): ?>
                        <img src="<?= 'uploads/mascotas/' . htmlspecialchars($adopcion['foto']) ?>" 
                             alt="Foto de <?= htmlspecialchars($adopcion['nombre_mascota']) ?>" 
                             style="max-width:200px; border-radius:10px;">
                    <?php endif; ?>
                    
                    <br>
                    <a href="detalle_adopcion.php?id=<?= $adopcion['adopcion_id'] ?>">
                        Ver detalles
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No tenés solicitudes de adopción todavía.  
           <a href="mascotas_en_adopcion.php">¡Mirá las mascotas disponibles!</a></p>
    <?php endif; ?>
</main>
</body>
</html>
