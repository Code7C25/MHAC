<?php
session_start();
require_once 'conexion.php';

$sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.foto_perfil,
               r.nombre_refugio, r.direccion, r.descripcion
        FROM usuarios u
        LEFT JOIN refugios r ON r.usuario_id = u.id
        WHERE u.rol = 'refugio'
        ORDER BY COALESCE(r.nombre_refugio, u.nombre) ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Voluntariado - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/refugios.css">
  <a href="index.php" class="volver-inicio">
    <span>←</span> Volver al inicio
  </a>
  <style>
    .like-btn {background:none;border:none;cursor:pointer;padding:0;}
    .like-btn img {width:34px;height:34px;vertical-align:middle;}
    .likes-count {margin-left:4px;font-size:14px;}
  </style>
</head>
<body>
<header>
<h1>Refugios</h1>
</header>
<main>
<div class="busqueda">
<input type="text" id="filtro" placeholder="Buscar refugio, responsable, ciudad...">
</div>
<?php if ($result && $result->num_rows > 0): ?>
<ul class="lista-refugios" id="lista">
<?php while ($ref = $result->fetch_assoc()): ?>
<li class="refugio-card">
  <?php if(!empty($ref['foto_perfil'])): ?>
    <img src="imagenes/<?= htmlspecialchars($ref['foto_perfil']) ?>" alt="Foto">
  <?php endif; ?>
  <h2><?= htmlspecialchars($ref['nombre_refugio'] ?: $ref['nombre']) ?></h2>
  <div class="refugio-meta"><strong>Responsable:</strong> <?= htmlspecialchars($ref['nombre'].' '.$ref['apellido']) ?></div>
  <?php if(!empty($ref['direccion'])): ?>
    <div class="refugio-meta"><strong>Dirección:</strong> <?= htmlspecialchars($ref['direccion']) ?></div>
  <?php endif; ?>
  <div class="refugio-meta"><strong>Tel:</strong> <?= htmlspecialchars($ref['telefono']) ?></div>
  <div class="refugio-meta"><strong>Email:</strong> <?= htmlspecialchars($ref['email']) ?></div>
  <?php if(!empty($ref['descripcion'])): ?>
    <p><?= nl2br(htmlspecialchars($ref['descripcion'])) ?></p>
  <?php endif; ?>
</li>
<?php endwhile; ?>
</ul>
<?php else: ?>
<p>No hay refugios registrados.</p>
<?php endif; ?>
</main>
<script>
const input = document.getElementById('filtro');
const items = document.querySelectorAll('#lista .refugio-card');
input.addEventListener('input', ()=>{
  const val = input.value.toLowerCase();
  items.forEach(li=>{
    const texto = li.textContent.toLowerCase();
    li.style.display = texto.includes(val) ? '' : 'none';
  });
});
</script>
</body>
</html>
