<?php
session_start();
require_once 'conexion.php';

// ---------- Subir un nuevo post ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $contenido = trim($_POST['contenido']);
    $nombreArchivo = null;

    // Procesar imagen si se subió
    if (!empty($_FILES['imagen']['name'])) {
        $carpeta = __DIR__ . "/uploads/";
        if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);
        $nombreArchivo = time() . "_" . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['imagen']['name']);
        $ruta = $carpeta . $nombreArchivo;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    }

    if ($contenido !== '' || $nombreArchivo) {
        $stmt = $conn->prepare("INSERT INTO posts (usuario_id, contenido, imagen) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['usuario_id'], $contenido, $nombreArchivo);
        $stmt->execute();
        header("Location: comunidad.php");
        exit;
    }
}

// ---------- Paginación ----------
$porPagina = 10;
$pagina = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($pagina - 1) * $porPagina;

// ---------- Traer posts con likes y si ya dio like el usuario ----------
$sql = "SELECT p.id, p.contenido, p.imagen, p.fecha, u.nombre, u.apellido, u.id AS autor,
               (SELECT COUNT(*) FROM likes WHERE post_id=p.id) AS likes,
               (SELECT COUNT(*) FROM likes WHERE post_id=p.id AND usuario_id=" . ($_SESSION['usuario_id'] ?? 0) . ") AS liked
        FROM posts p
        JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.fecha DESC
        LIMIT $porPagina OFFSET $offset";
$result = $conn->query($sql);
$posts = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comunidad - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/comunidad.css">
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

<main class="comunidad-container">
  <h1>Comunidad de Huellitas</h1>

  <?php if (isset($_SESSION['usuario_id'])): ?>
  <form class="form-post" method="post" enctype="multipart/form-data">
    <textarea name="contenido" rows="3" placeholder="Comparte algo..." required></textarea>
    <input type="file" name="imagen" accept="image/*">
    <button type="submit">Publicar</button>
  </form>
  <?php else: ?>
  <p><a href="login.php">Inicia sesión</a> para compartir publicaciones.</p>
  <?php endif; ?>

  <section class="feed">
  <?php foreach ($posts as $post): ?>
    <article class="post">
      <header>
        <strong><?= htmlspecialchars($post['nombre'].' '.$post['apellido']) ?></strong>
        <small><?= date("d/m/Y H:i", strtotime($post['fecha'])) ?></small>
      </header>
      <p><?= nl2br(htmlspecialchars($post['contenido'])) ?></p>
      <?php if ($post['imagen']): ?>
        <img class="post-img" src="uploads/<?= htmlspecialchars($post['imagen']) ?>" alt="imagen del post">
      <?php endif; ?>
      <div class="acciones">
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <button class="like-btn" data-id="<?= $post['id'] ?>">
            <img src="imagenes/<?= $post['liked'] ? 'like.png' : 'unlike.png' ?>" alt="like">
          </button>
          <span class="likes-count" data-id="<?= $post['id'] ?>"><?= $post['likes'] ?></span>
        <?php else: ?>
          <span class="likes-count" data-id="<?= $post['id'] ?>"><?= $post['likes'] ?></span>
        <?php endif; ?>
      </div>
    </article>
  <?php endforeach; ?>
  </section>
</main>

<script>
document.querySelectorAll('.like-btn').forEach(btn=>{
  btn.addEventListener('click',function(e){
    e.preventDefault();
    const pid=this.dataset.id;
    fetch('like.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:'post_id='+encodeURIComponent(pid)
    })
    .then(r=>r.json())
    .then(d=>{
      if(d.ok){
        const img=this.querySelector('img');
        img.src='imagenes/'+(d.liked?'like.png':'unlike.png');
        document.querySelector('.likes-count[data-id="'+pid+'"]').textContent=d.total;
      }
    });
  });
});
</script>

</body>
</html>
