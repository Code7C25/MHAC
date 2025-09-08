<?php
session_start();
require_once 'conexion.php';

// --- FILTROS ---
$filtro_especie = $_GET['especie'] ?? '';
$buscar_nombre = $_GET['nombre'] ?? '';
$orden = $_GET['orden'] ?? 'fecha_desc';

// Construcción de la consulta
$sql = "SELECT a.id AS adopcion_id, a.fecha_solicitud, a.estado, 
               m.nombre AS nombre_mascota, m.especie, m.raza, m.edad_categoria, m.foto
        FROM adopciones a
        JOIN mascotas m ON a.mascota_id = m.id
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
    <span class="">←</span>
    Volver al inicio
</a>

<header>
    <h1>¿Buscando un nuevo amigo?</h1>
</header>

<!-- Tarjetas de navegación -->
<div style="display: flex; gap: 20px; justify-content: center; margin-bottom: 30px;">
  <a href="mis_adopciones.php" class="servicio-card">
    <h3>Solicitud de adopciones</h3>
  </a>
  <a href="adopcion.php" class="servicio-card">
    <h3>Adopciones aprobadas</h3>
  </a>
  <a href="mascotas_en_adopcion.php" class="servicio-card">
    <h3>Mascotas en adopción</h3>
  </a>

  <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'dador'): ?>
      <a href="mis_posts.php" class="servicio-card">
          <h3>Mis publicaciones</h3>
      </a>
  <?php endif; ?>
</div>


<!-- Botón publicar mascota (solo rol dador) -->
<?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'dador'): ?>
    <div style="text-align: center; margin-bottom: 30px;">
        <a href="publicar_mascota.php" class="btn-publicar">🐾 Publicar mascota</a>
    </div>
<?php endif; ?>


<!-- Resultados -->
<main class="contenido-principal">
    <div class="resultados-busqueda">
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <img src="imagenes/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nombre_mascota']) ?>">
                        <h3><?= htmlspecialchars($row['nombre_mascota']) ?></h3>
                        <p><strong>Especie:</strong> <?= htmlspecialchars($row['especie']) ?></p>
                        <p><strong>Raza:</strong> <?= htmlspecialchars($row['raza']) ?></p>
                        <p><strong>Edad:</strong> <?= htmlspecialchars($row['edad_categoria']) ?></p>
                        <p><strong>Estado:</strong> <?= htmlspecialchars($row['estado']) ?></p>
                        <p><strong>Fecha solicitud:</strong> <?= htmlspecialchars($row['fecha_solicitud']) ?></p>
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
