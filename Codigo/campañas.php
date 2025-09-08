<?php
// campanias.php
include("conexion.php");

// Campañas activas (próximas o en curso)
$sql_activas = "SELECT * FROM campañas WHERE estado IN ('proxima','en_curso') ORDER BY fecha_inicio ASC";
$campanias_activas = $conn->query($sql_activas);

// Campañas finalizadas (historial)
$sql_finalizadas = "SELECT * FROM campañas WHERE estado = 'finalizada' ORDER BY fecha_fin DESC";
$campanias_finalizadas = $conn->query($sql_finalizadas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Campañas MHAC</title>
 <link rel="stylesheet" href="css/campañas.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

  <!-- Cabecera -->
  <header class="header">
    <h1>🌟 Campañas MHAC</h1>
    <p>Unite a nuestras iniciativas para el cuidado y adopción responsable de mascotas</p>
  </header>

  <!-- Buscador y filtros -->
  <section class="buscador">
    <form method="GET" action="campañas.php">
      <input type="text" name="q" placeholder="🔍 Buscar campañas...">
      <select name="estado">
        <option value="">-- Filtrar por estado --</option>
        <option value="proxima">Próximas</option>
        <option value="en_curso">En curso</option>
        <option value="finalizada">Finalizadas</option>
      </select>
      <button type="submit">Buscar</button>
    </form>
  </section>

  <!-- Campañas activas -->
  <section class="campanias-activas">
    <h2>Campañas Activas</h2>
    <div class="grid">
      <?php if ($campanias_activas->num_rows > 0): ?>
        <?php while($c = $campanias_activas->fetch_assoc()): ?>
          <div class="card">
            <img src="<?= $c['imagen'] ?: 'imagenes/default.jpg' ?>" alt="Imagen campaña">
            <h3><?= htmlspecialchars($c['titulo']) ?></h3>
            <p><?= htmlspecialchars($c['descripcion']) ?></p>
            <p><strong> <?= $c['fecha_inicio'] ?> - <?= $c['fecha_fin'] ?></strong></p>
            
            <!-- Botones -->
            <div class="acciones">
              <a href="participar.php?id=<?= $c['id'] ?>" class="btn">Participar</a>
              <a href="detalle_campania.php?id=<?= $c['id'] ?>" class="btn secundario">Ver más</a>
              <a href="donar.php?id=<?= $c['id'] ?>" class="btn terciario">Donar</a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No hay campañas activas en este momento.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Campañas finalizadas -->
  <section class="campanias-finalizadas">
    <h2> Historial de Campañas</h2>
    <div class="grid">
      <?php if ($campanias_finalizadas->num_rows > 0): ?>
        <?php while($c = $campanias_finalizadas->fetch_assoc()): ?>
          <div class="card finalizada">
            <img src="<?= $c['imagen'] ?: 'imagenes/default.jpg' ?>" alt="Imagen campaña">
            <h3><?= htmlspecialchars($c['titulo']) ?> <span class="badge">Finalizada</span></h3>
            <p><?= htmlspecialchars($c['descripcion']) ?></p>
            <p><strong> <?= $c['fecha_inicio'] ?> - <?= $c['fecha_fin'] ?></strong></p>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Aún no hay campañas finalizadas registradas.</p>
      <?php endif; ?>
    </div>
  </section>

</body>
</html>
