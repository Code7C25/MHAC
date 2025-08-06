<?php
// panel_refugio.php
session_start();
require_once 'verificar_sesion.php';
require_once 'conexion.php';

// Solo usuarios con rol 'refugio'
if ($_SESSION['usuario_rol'] !== 'refugio') {
    header("Location: index.php");
    exit;
}

// Obtener datos del refugio
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT id, nombre_refugio FROM refugios WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$refugio = $stmt->get_result()->fetch_assoc();
$refugio_id = $refugio['id'];
$nombre_refugio = $refugio['nombre_refugio'];

// Listar mascotas propias
$stmt2 = $conn->prepare("SELECT * FROM mascotas WHERE refugio_id = ? ORDER BY fecha_alta DESC");
$stmt2->bind_param("i", $refugio_id);
$stmt2->execute();
$mascotas = $stmt2->get_result();

// Listar solicitudes pendientes
$sql3 = "
  SELECT a.id AS solicitud_id,
         m.nombre AS mascota_nombre,
         u.nombre AS adoptante_nombre,
         DATE_FORMAT(a.fecha_adopcion, '%d/%m/%Y') AS fecha_solicitud
    FROM adopciones a
    JOIN mascotas m ON a.mascota_id = m.id
    JOIN usuarios u ON a.usuario_id = u.id
   WHERE m.refugio_id = ? AND a.estado = 'pendiente'
   ORDER BY a.fecha_adopcion DESC
";
$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("i", $refugio_id);
$stmt3->execute();
$solicitudes = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Refugio – <?= htmlspecialchars($nombre_refugio) ?></title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/panel.css">
</head>
<body>
  <header>
    <h1>Panel de Refugio: <?= htmlspecialchars($nombre_refugio) ?></h1>
    <a href="logout.php" class="boton">Cerrar Sesión</a>
  </header>

  <nav class="menu-panel">
    <a href="nuevamascota.php" class="cta">Agregar Mascota</a>
    <a href="#mis-mascotas" class="cta">Mis Mascotas</a>
    <a href="#solicitudes" class="cta">Solicitudes</a>
  </nav>

  <main>
    <section id="mis-mascotas">
      <h2>Mis Mascotas Publicadas</h2>
      <?php if ($mascotas->num_rows): ?>
        <ul class="lista-mascotas">
          <?php while ($m = $mascotas->fetch_assoc()): ?>
            <li class="card">
              <img src="img/<?= htmlspecialchars($m['foto'] ?? 'placeholder.png') ?>" alt="<?= htmlspecialchars($m['nombre']) ?>">
              <h3><?= htmlspecialchars($m['nombre']) ?></h3>
              <p>Especie: <?= htmlspecialchars($m['especie']) ?></p>
              <p>Edad: <?= intval($m['edad']) ?> meses</p>
              <div class="acciones">
                <a href="editar_mascota.php?id=<?= $m['id'] ?>" class="boton">Editar</a>
                <a href="eliminar_mascota.php?id=<?= $m['id'] ?>" class="boton">Eliminar</a>
              </div>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p>No has publicado mascotas aún.</p>
      <?php endif; ?>
    </section>

    <section id="solicitudes">
      <h2>Solicitudes Pendientes</h2>
      <?php if ($solicitudes->num_rows): ?>
        <ul class="lista-solicitudes">
          <?php while ($s = $solicitudes->fetch_assoc()): ?>
            <li>
              <strong><?= htmlspecialchars($s['adoptante_nombre']) ?></strong>
              solicitó <em><?= htmlspecialchars($s['mascota_nombre']) ?></em>
              el <?= htmlspecialchars($s['fecha_solicitud']) ?>
              <!-- Aquí agregar botones de aprobar/rechazar -->
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
