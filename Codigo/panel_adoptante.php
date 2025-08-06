<?php
// panel_adoptante.php
require_once 'verificar_sesion.php';
require_once 'conexion.php';

// Verificar rol
if ($_SESSION['usuario_rol'] !== 'adoptante') {
    header("Location: login.php?error=Acceso denegado.");
    exit;
}

$nombre = htmlspecialchars($_SESSION['usuario_nombre']);

// Obtener mascotas en adopción
$sql = "SELECT id, nombre, especie, raza, edad, foto FROM mascotas WHERE estado = 'en_adopcion' ORDER BY fecha_alta DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Adoptante - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/panel.css">
</head>
<body>
  <header>
    <h1>Bienvenida, <?= $nombre ?></h1>
    <div class="user-session">
      <form action="logout.php" method="post">
        <button type="submit" class="boton">Cerrar Sesión</button>
      </form>
    </div>
  </header>

  <nav class="menu-panel">
    <a href="panel_adoptante.php" class="boton">Inicio</a>
    <a href="mis_solicitudes.php" class="boton">Mis Solicitudes</a>
    <a href="perfil.php" class="boton">Mi Perfil</a>
  </nav>

  <main>
    <h2>Mas mascotas disponibles</h2>

    <?php if ($result && $result->num_rows > 0): ?>
      <div class="mascotas-grid">
        <?php while ($m = $result->fetch_assoc()): ?>
          <div class="mascota-card">
            <?php if ($m['foto']): ?>
              <img src="<?= 'uploads/mascotas/' . htmlspecialchars($m['foto']) ?>" alt="<?= htmlspecialchars($m['nombre']) ?>">
            <?php else: ?>
              <div class="placeholder-foto">No Image</div>
            <?php endif; ?>
            <h3><?= htmlspecialchars($m['nombre']) ?></h3>
            <p><?= htmlspecialchars($m['especie']) ?> - <?= htmlspecialchars($m['raza']) ?></p>
            <p><?= intval($m['edad']) ?> meses</p>
            <a href="mascota_detalle.php?id=<?= intval($m['id']) ?>" class="cta">Ver detalle</a>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p>No hay mascotas en adopción por ahora.</p>
    <?php endif; ?>
  </main>

  <footer class="footer">
    <div class="footer-container">
      <div class="footer-logo">
        <h3>MHAC - Mis Huellitas a Casa</h3>
        <p>Un puente entre peluditos y hogares llenos de amor.</p>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 MHAC. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
</body>
</html>
