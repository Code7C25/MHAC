<?php
session_start();
require_once 'conexion.php';
require_once 'moderacion.php';

// --- Definición de Rutas ---
// Ruta física para guardar el archivo (necesaria para move_uploaded_file)
// Esto navega desde /src/ hacia /assets/imagenes/posts/
$CARPETA_UPLOADS_FISICA = __DIR__ . '/../assets/uploads/posts/';

// Ruta de navegación/Browser para guardar en la DB y para mostrar
$RUTA_GUARDAR_DB = '../assets/uploads/posts/'; 

// ---------- Subir un nuevo post ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $contenido = trim($_POST['contenido']);
    $rutaImagen = null;
    $mensaje = null;

    // --- FILTRO DE MODERACIÓN ---
    //if (moderar_texto($contenido)) {
    //    $mensaje = "El contenido de tu publicación contiene palabras inapropiadas o sensibles.";
    //} else {
        // Procesar imagen si se subió
        if (!empty($_FILES['imagen']['name'])) {
            // Aseguramos que la carpeta exista antes de subir
            if (!is_dir($CARPETA_UPLOADS_FISICA)) {
                mkdir($CARPETA_UPLOADS_FISICA, 0777, true);
            }
            
            $nombreArchivo = time() . "_" . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $_FILES['imagen']['name']);
            $rutaCompleta = $CARPETA_UPLOADS_FISICA . $nombreArchivo;
            
            // Subir la imagen al servidor
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
                // GUARDAMOS EL PATH COMPLETO EN LA DB
                $rutaImagen = $RUTA_GUARDAR_DB . $nombreArchivo; 
            } else {
                $mensaje = "Error al subir la imagen al servidor. Verifique permisos (0777).";
            }
        }

        // Si hay contenido de texto O una imagen para guardar
        if (($contenido !== '' || $rutaImagen) && $mensaje === null) {
            $stmt = $conn->prepare("INSERT INTO posts (usuario_id, contenido, imagen) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $_SESSION['usuario_id'], $contenido, $rutaImagen);
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
        <span>←</span> Volver
    </a>
    <style>
        .like-btn {background:none;border:none;cursor:pointer;padding:0;}
        .like-btn img {width:34px;height:34px;vertical-align:middle;}
        .likes-count {margin-left:4px;font-size:14px;}
    </style>
</head>
<script src="translate.js"></script>

<body>

<main class="comunidad-container">
    <h1>Comunidad de Huellitas</h1>

    <?php if (isset($mensaje)): ?>
        <div class="mensaje-contenedor error">
            <div class="mensaje-icono">❌</div>
            <div class="mensaje-texto"><p><?= htmlspecialchars($mensaje) ?></p></div>
        </div>
    <?php endif; ?>

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
                <a href="perfil.php?id=<?= $post['autor'] ?>" class="enlace-perfil">
                    <strong><?= htmlspecialchars($post['nombre'].' '.$post['apellido']) ?></strong>
                </a>
                <small><?= date("d/m/Y H:i", strtotime($post['fecha'])) ?></small>
            </header>
            <p><?= nl2br(htmlspecialchars($post['contenido'])) ?></p>
            
            <?php if ($post['imagen']): 
                // La DB ya almacena la ruta completa: ../assets/uploads/posts/nombre.jpg
                $rutaImg = htmlspecialchars($post['imagen']);
            ?>
                <img class="post-img" src="<?= $rutaImg ?>" alt="imagen del post" onerror="this.style.display='none'">
            <?php endif; ?>
            
            <div class="acciones">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <button class="like-btn" data-id="<?= $post['id'] ?>">
                        <img src="../assets/imagenes/<?= $post['liked'] ? 'like.png' : 'unlike.png' ?>" alt="like">
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
                img.src='../assets/imagenes/'+(d.liked?'like.png':'unlike.png');
                document.querySelector('.likes-count[data-id="'+pid+'"]').textContent=d.total;
            }
        });
    });
});
</script>

</body>
</html>