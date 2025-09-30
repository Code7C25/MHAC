<?php
session_start();
require_once 'conexion.php';

// Ver si se pasa un id en la URL (perfil público)
if (isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);
} else {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
    $id_usuario = $_SESSION['usuario_id'];
}

// Traer datos del usuario
$sql = "SELECT id, nombre, apellido, email, rol, fecha_registro, foto_perfil 
        FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Avatar
if (!empty($usuario['foto_perfil'])) {
    $avatar = htmlspecialchars($usuario['foto_perfil']);
} else {
    $avatares = glob("imagenes/avatars/*.png");
    $avatar = $avatares[array_rand($avatares)];
}

// Saber si es mi perfil
$es_mi_perfil = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $usuario['id'];

// Traer mascotas publicadas (solo si es dador o refugio)
$mascotas = null;
if ($usuario['rol'] === 'dador' || $usuario['rol'] === 'refugio') {
    $sql_mascotas = "SELECT id, nombre, especie, raza, descripcion, foto, estado, fecha_alta
                     FROM mascotas
                     WHERE usuario_id = ?
                     ORDER BY fecha_alta DESC";
    $stmt_mascotas = $conn->prepare($sql_mascotas);
    $stmt_mascotas->bind_param("i", $id_usuario);
    $stmt_mascotas->execute();
    $mascotas = $stmt_mascotas->get_result();
}

// Traer posts
$sql_posts = "SELECT id, contenido, imagen, fecha 
              FROM posts 
              WHERE usuario_id = ? 
              ORDER BY fecha DESC";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $id_usuario);
$stmt_posts->execute();
$posts = $stmt_posts->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?> - MHAC</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/perfil.css">
</head>
<body>
    <div class="perfil-container">
        <h1>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?></h1>

        <div class="avatar-container">
            <img src="<?php echo $avatar; ?>" alt="Foto de perfil" class="foto-perfil">
        </div>

        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['apellido']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
        <p><strong>Rol:</strong> <?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?></p>
        <p><strong>Miembro desde:</strong> <?php echo date("d/m/Y", strtotime($usuario['fecha_registro'])); ?></p>

        <?php if ($es_mi_perfil): ?>
        <div class="acciones">
            <a href="editar_perfil.php" class="boton">Editar perfil</a>
            <form action="logout.php" method="post" style="display:inline;">
                <button type="submit" class="boton">Cerrar sesión</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <hr>

    <?php if ($mascotas !== null): ?>
    <div class="mascotas-container">
        <h2>Mascotas publicadas</h2>
        <?php if ($mascotas->num_rows > 0): ?>
            <?php while ($m = $mascotas->fetch_assoc()): ?>
                <div class="mascota">
                    <?php if (!empty($m['foto'])): ?>
                        <img src="<?php echo htmlspecialchars($m['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($m['nombre']); ?>" class="imagen-mascota">
                    <?php endif; ?>
                    <p><strong><?php echo htmlspecialchars($m['nombre']); ?></strong> (<?php echo htmlspecialchars($m['especie']); ?>)</p>
                    <p><?php echo nl2br(htmlspecialchars($m['descripcion'])); ?></p>
                    <span class="fecha">Publicado: <?php echo date("d/m/Y", strtotime($m['fecha_alta'])); ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Este usuario no ha publicado mascotas.</p>
        <?php endif; ?>
    </div>
    <hr>
    <?php endif; ?>

    <div class="posts-container">
        <h2>Posts</h2>
        <?php if ($posts->num_rows > 0): ?>
            <?php while ($p = $posts->fetch_assoc()): ?>
                <div class="publicacion">
                    <?php if (!empty($p['imagen'])): ?>
                        <img src="<?php echo htmlspecialchars($p['imagen']); ?>" alt="Imagen del post" class="imagen-post">
                    <?php endif; ?>
                    <p><?php echo nl2br(htmlspecialchars($p['contenido'])); ?></p>
                    <span class="fecha"><?php echo date("d/m/Y H:i", strtotime($p['fecha'])); ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Este usuario aún no tiene posts.</p>
        <?php endif; ?>
    </div>
</body>
</html>
