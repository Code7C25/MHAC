<?php
// panel_voluntario.php
session_start();
require_once 'verificar_sesion.php';
require_once 'conexion.php';

// Solo para usuarios con rol 'voluntario'
if ($_SESSION['usuario_rol'] !== 'voluntario') {
    header("Location: login.php?error=Acceso denegado.");
    exit;
}

$nombre = htmlspecialchars($_SESSION['usuario_nombre']);

// Obtener campañas activas
$hoy = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT id, titulo, descripcion, fecha_inicio, fecha_fin, lugar
    FROM campañas
    WHERE fecha_inicio <= ? AND fecha_fin >= ?
    ORDER BY fecha_inicio ASC
");
$stmt->bind_param("ss", $hoy, $hoy);
$stmt->execute();
$campañas = $stmt->get_result();

// (Opcional) Obtener tus postulaciones  
// — Necesitarías una tabla 'postulaciones' que relacione usuario y campaña.
// Aquí dejamos un placeholder.
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Voluntario - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/panel.css">
</head>
<body>
  <header>
    <h1>Bienvenido, <?= $nombre ?> (Voluntario)</h1>
    <a href="logout.php" class="boton">Cerrar Sesión</a>
  </header>

  <nav class="menu-panel">
    <a href="panel_voluntario.php" class="boton">Inicio</a>
    <a href="#campañas" class="boton">Campañas Activas</a>
    <a href="#mis-postulaciones" class="boton">Mis Postulaciones</a>
  </nav>

  <main>
    <section id="campañas">
      <h2>Campañas Activas</h2>
      <?php if ($campañas->num_rows): ?>
        <ul class="lista-campañas">
          <?php while ($c = $campañas->fetch_assoc()): ?>
            <li class="card">
              <h3><?= htmlspecialchars($c['titulo']) ?></h3>
              <p><?= nl2br(htmlspecialchars($c['descripcion'])) ?></p>
              <p><strong>Fechas:</strong>
                <?= htmlspecialchars($c['fecha_inicio']) ?> 
                a <?= htmlspecialchars($c['fecha_fin']) ?>
              </p>
              <p><strong>Lugar:</strong> <?= htmlspecialchars($c['lugar']) ?></p>
              <form action="postular_voluntario.php" method="post" style="display:inline;">
                <input type="hidden" name="campaña_id" value="<?= intval($c['id']) ?>">
                <button type="submit" class="cta">Postularme</button>
              </form>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p>No hay campañas activas en este momento.</p>
      <?php endif; ?>
    </section>

    <section id="mis-postulaciones">
      <h2>Mis Postulaciones</h2>
      <p>(Próximamente: aquí verás las campañas a las que te postulaste.)</p>
    </section>
  </main>
</body>
</html>
