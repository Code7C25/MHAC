<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['refugio', 'dador'])) {
    header("Location: login.php");
    exit();
}

$publicador_id = intval($_SESSION['usuario_id']);
$rol = $_SESSION['rol'];

$sql = "
    SELECT 
        a.id AS solicitud_id,
        a.fecha_solicitud,
        a.estado,
        a.nombre  AS nombre_adoptante,
        a.email,
        a.telefono,
        a.domicilio,
        a.edad,
        a.vivienda,
        a.experiencia,
        m.id     AS mascota_id,
        m.nombre AS nombre_mascota,
        m.especie,
        m.raza,
        m.edad_categoria,
        m.foto
    FROM adopciones a
    INNER JOIN mascotas m ON a.mascota_id = m.id
    WHERE m.usuario_id = ?
    ORDER BY a.fecha_solicitud DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparando consulta de solicitudes: " . $conn->error);
}
$stmt->bind_param("i", $publicador_id);
$stmt->execute();
$result = $stmt->get_result();

// Flash message
$flash = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : "";
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solicitudes de Adopción - Mis Publicaciones</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/solicitudes_adopcion_refugio_dador.css">
<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>

<body>
  <header>
    <h1>🐾 Solicitudes de Adopción - Tus Publicaciones</h1>
  </header>

  <main>
    <?php if ($flash): ?>
      <div class="alert <?= stripos($flash, 'no se pudo') !== false || stripos($flash, 'Error') !== false ? 'alert-err':'alert-ok' ?>">
        <?= htmlspecialchars($flash) ?>
      </div>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($s = $result->fetch_assoc()): ?>
        <div class="card">
          <h2><?= htmlspecialchars($s['nombre_mascota']) ?></h2>
          <p>
            <strong>Especie:</strong> <?= htmlspecialchars(ucfirst($s['especie'])) ?>
            • <strong>Raza:</strong> <?= htmlspecialchars($s['raza']) ?>
            • <strong>Edad mascota:</strong> <?= htmlspecialchars(ucfirst($s['edad_categoria'] ?? '')) ?>
          </p>

          <?php if (!empty($s['foto'])): ?>
            <img class="mascota" src="<?= '../assets/uploads/mascotas/' . htmlspecialchars($s['foto']) ?>" alt="Foto de <?= htmlspecialchars($s['nombre_mascota']) ?>">
          <?php endif; ?>

          <hr>

          <p><strong>Adoptante:</strong> <?= htmlspecialchars($s['nombre_adoptante']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($s['email']) ?></p>
          <p><strong>Teléfono:</strong> <?= htmlspecialchars($s['telefono']) ?></p>
          <p><strong>Domicilio:</strong> <?= htmlspecialchars($s['domicilio']) ?></p>
          <p><strong>Edad solicitante:</strong> <?= intval($s['edad']) ?> • <strong>Vivienda:</strong> <?= htmlspecialchars($s['vivienda']) ?></p>
          <p><strong>Experiencia:</strong><br><?= nl2br(htmlspecialchars($s['experiencia'])) ?></p>

          <p>
            <strong>Fecha de solicitud:</strong> <?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?>
            • <strong>Estado actual:</strong> <?= htmlspecialchars(ucfirst($s['estado'])) ?>
          </p>

          <?php if ($s['estado'] === 'pendiente'): ?>
            <div class="acciones">
              <form action="actualizar_solicitud.php" method="POST">
                <input type="hidden" name="solicitud_id" value="<?= intval($s['solicitud_id']) ?>">
                <button type="submit" name="accion" value="aprobar">✅ Aprobar</button>
              </form>

              <form action="actualizar_solicitud.php" method="POST">
                <input type="hidden" name="solicitud_id" value="<?= intval($s['solicitud_id']) ?>">
                <button type="submit" name="accion" value="rechazar">❌ Rechazar</button>
              </form>
            </div>
          <?php else: ?>
            <p><em>Esta solicitud ya fue <strong><?= htmlspecialchars($s['estado']) ?></strong>.</em></p>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="card">
        <p>No hay solicitudes de adopción para tus publicaciones todavía.</p>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>