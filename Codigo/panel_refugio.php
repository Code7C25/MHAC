<?php
require_once 'verificar_sesion.php';
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];
// Obtener datos del refugio
$stmt = $conn->prepare(\"SELECT id, nombre_refugio FROM refugios WHERE usuario_id = ?\");
$stmt->bind_param(\"i\", $usuario_id);
$stmt->execute();
$ref = $stmt->get_result()->fetch_assoc();
$refugio_id = $ref['id'];
$nombre_refugio = $ref['nombre_refugio'];

// Mascotas propias
$stmt2 = $conn->prepare(\"SELECT * FROM mascotas WHERE refugio_id = ? ORDER BY fecha_alta DESC\");
$stmt2->bind_param(\"i\", $refugio_id);
$stmt2->execute();
$mascotas = $stmt2->get_result();

// Solicitudes pendientes
$sql3 = \"SELECT a.id AS sol_id, m.nombre AS mascota, u.nombre AS adoptante, a.fecha_adopcion
         FROM adopciones a
         JOIN mascotas m ON a.mascota_id = m.id
         JOIN usuarios u ON a.usuario_id = u.id
         WHERE m.refugio_id = ? AND a.estado = 'pendiente'\";
$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param(\"i\", $refugio_id);
$stmt3->execute();
$solicitudes = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Refugio - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
  <header>
    <h1>Bienvenido, <?= htmlspecialchars($nombre_refugio) ?></h1>
    <a href="logout.php" class="boton">Cerrar Sesión</a>
  </header>
  <main>
    <section>
      <a href="nuevamascota.php" class="cta">Agregar nueva mascota</a>
    </section>
    <section>
      <h2>Mis mascotas publicadas</h2>
      <?php if ($mascotas->num_rows): ?>
        <ul>
          <?php while ($m = $mascotas->fetch_assoc()): ?>
            <li>
              <?= htmlspecialchars($m['nombre']) ?>
              <a href="editar_mascota.php?id=<?= $m['id'] ?>">Editar</a> |
              <a href="eliminar_mascota.php?id=<?= $m['id'] ?>">Eliminar</a>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p>No has publicado ninguna mascota.</p>
      <?php endif; ?>
    </section>
    <section>
      <h2>Solicitudes de adopción pendientes</h2>
      <?php if ($solicitudes->num_rows): ?>
        <ul>
        <?php while ($s = $solicitudes->fetch_assoc()): ?>
          <li>
            <?= htmlspecialchars($s['adoptante']) ?> solicitó <strong><?= htmlspecialchars($s['mascota']) ?></strong>
            el <?= htmlspecialchars($s['fecha_adopcion']) ?>
            <!-- Aquí podrías agregar botones para Aprobar/Rechazar -->
          </li>
        <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p>No hay solicitudes pendientes.</p>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
