<?php
session_start();
require_once 'conexion.php';

// Obtener todos los cuidados
$sql = "SELECT c.id, c.titulo, c.contenido, c.fecha, c.autor, u.nombre, u.apellido, u.rol, c.autor_id
        FROM cuidados c
        JOIN usuarios u ON c.autor_id = u.id
        ORDER BY c.fecha DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cuidados y Consejos - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/info.css">
</head>
<body>
<a href="index.php" class="volver-inicio"><span>←</span> Volver al inicio</a>
<header>
  <h1>Cuidados y Consejos para Animales</h1>
</header>

<main>
  <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['veterinario','refugio'])): ?>
    <a href="agregar_info.php" class="btn-nuevo">➕ Agregar cuidado</a>
  <?php endif; ?>

  <section class="cuidados-lista">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($c = $result->fetch_assoc()): ?>
        <article class="cuidado-card">
          <h2><?= htmlspecialchars($c['titulo']) ?></h2>
          <p><?= nl2br(htmlspecialchars($c['contenido'])) ?></p>
          <div class="autor-info">
            <small>
              Publicado por <?= htmlspecialchars($c['nombre'].' '.$c['apellido']) ?> 
              (<?= ucfirst($c['rol']) ?>) 
              <?php if (!empty($c['autor'])): ?>
                | Créditos: <?= htmlspecialchars($c['autor']) ?>
              <?php endif; ?>
              el <?= date("d/m/Y", strtotime($c['fecha'])) ?>
            </small>
          </div>

          <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $c['autor_id']): ?>
            <a href="editar_cuidado.php?id=<?= $c['id'] ?>" class="btn-editar">✏️ Editar</a>
          <?php endif; ?>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No hay cuidados publicados todavía.</p>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
