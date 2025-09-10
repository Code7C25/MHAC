<?php
session_start();
require_once 'conexion.php';

// --- FILTROS ---
$filtro_especie = $_GET['especie'] ?? '';
$buscar_nombre = $_GET['nombre'] ?? '';
$orden = $_GET['orden'] ?? 'fecha_desc';

// Construcción de la consulta
$sql = "SELECT a.id AS adopcion_id, a.fecha_solicitud, a.estado, 
               m.id AS mascota_id, m.nombre AS nombre_mascota, m.especie, m.raza, m.edad_categoria, m.foto,
               u.nombre AS adoptante_nombre, u.apellido AS adoptante_apellido
        FROM adopciones a
        JOIN mascotas m ON a.mascota_id = m.id
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE a.estado = 'aprobada'";

if ($filtro_especie) {
    $sql .= " AND m.especie = '" . $conn->real_escape_string($filtro_especie) . "'";
}

if ($buscar_nombre) {
    $sql .= " AND m.nombre LIKE '%" . $conn->real_escape_string($buscar_nombre) . "%'";
}

// Ordenamiento
if ($orden === 'edad_asc') {
    $sql .= " ORDER BY m.edad_categoria ASC";
} elseif ($orden === 'edad_desc') {
    $sql .= " ORDER BY m.edad_categoria DESC";
} else {
    $sql .= " ORDER BY a.fecha_solicitud DESC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopciones - MHAC</title>
    <link rel="stylesheet" href="css/adopcion.css">
</head>
<body>
<a href="index.php" class="">
    <span>←</span>
    Volver al inicio
</a>

<header>
    <h1>¿Buscando un nuevo amigo?</h1>
</header>

<!-- Tarjetas de navegación -->
<div style="display: flex; gap: 20px; justify-content: center; margin-bottom: 30px;">
  <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'adoptante'): ?>
    <a href="mis_adopciones.php" class="servicio-card">
      <h3>Mis solicitudes de adopción</h3>
    </a>
  <?php endif; ?>

  <a href="" class="servicio-card">
    <h3>Adopciones aprobadas</h3>
  </a>
  <a href="mascotas_en_adopcion.php" class="servicio-card">
    <h3>Mascotas en adopción</h3>
  </a>
</div>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <nav>
            <?php if (in_array($_SESSION['rol'], ['dador', 'refugio'])): ?>
                <a href="mis_mascotas_publicadas.php">Mis mascotas publicadas</a>
                <a href="publicar_mascota.php">Publicar mascota</a>
                <a href="solicitudes_adopcion_refugio_dador.php">Ver las solicitudes de mis mascotas publicadas</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

<!-- Resultados -->
<main class="contenido-principal">
    <div class="resultados-busqueda">
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <?php if ($row['foto']): ?>
                            <div class="mascota-imagen">
                                <img src="uploads/mascotas/<?= htmlspecialchars($row['foto']) ?>" 
                                     alt="<?= htmlspecialchars($row['nombre_mascota']) ?>">
                            </div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($row['nombre_mascota']) ?></h3>
                        <p><strong>Especie:</strong> <?= htmlspecialchars($row['especie']) ?></p>
                        <p><strong>Raza:</strong> <?= htmlspecialchars($row['raza']) ?></p>
                        <p><strong>Edad:</strong> <?= htmlspecialchars($row['edad_categoria']) ?></p>
                        <p><strong>Estado:</strong> <?= htmlspecialchars($row['estado']) ?></p>
                        <p><strong>Fecha solicitud:</strong> <?= htmlspecialchars($row['fecha_solicitud']) ?></p>
                        <p><strong>Adoptante:</strong> <?= htmlspecialchars($row['adoptante_nombre'] . ' ' . $row['adoptante_apellido']) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No se encontraron adopciones aprobadas.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
