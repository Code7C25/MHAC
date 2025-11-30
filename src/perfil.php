<?php 
session_start();
require_once 'conexion.php';

// 1. Ver si es perfil propio o de otro usuario
if (isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);
} else {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
    $id_usuario = $_SESSION['usuario_id'];
}

// 2. Traer datos del usuario
$sql = "SELECT id, nombre, apellido, email, telefono, rol, fecha_registro, foto_perfil 
        FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

// 3. --- LÓGICA DEL AVATAR (Igual que en editar) ---
$ruta_uploads = "../assets/uploads/usuarios/";
$ruta_avatars = "../assets/imagenes/avatars/";
$avatar_final = "";

if (!empty($usuario['foto_perfil']) && file_exists($ruta_uploads . $usuario['foto_perfil'])) {
    // Si tiene foto subida
    $avatar_final = $ruta_uploads . htmlspecialchars($usuario['foto_perfil']);
} else {
    // Si no, avatar random del 1 al 5
    // TRUCO: Usamos el ID del usuario para que el "random" sea siempre el mismo para esa persona
    // (Ej: al usuario 4 siempre le toca el avatar 4, al 6 le toca el 1, etc.)
    // Si prefieres totalmente aleatorio cada vez que refrescas, usa rand(1, 5)
    $numero_avatar = ($id_usuario % 5) + 1; 
    $avatar_final = $ruta_avatars . "avatar" . $numero_avatar . ".png";
}

// Saber si es mi perfil
$es_mi_perfil = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $usuario['id'];

// 4. Traer mascotas
$mascotas = null;
if ($usuario['rol'] === 'dador' || $usuario['rol'] === 'refugio') {
    $sql_mascotas = "SELECT id, nombre, especie, raza, descripcion, foto, estado, fecha_alta
                     FROM mascotas WHERE usuario_id = ? ORDER BY fecha_alta DESC";
    $stmt_mascotas = $conn->prepare($sql_mascotas);
    $stmt_mascotas->bind_param("i", $id_usuario);
    $stmt_mascotas->execute();
    $mascotas = $stmt_mascotas->get_result();
}

// 5. Traer posts
$sql_posts = "SELECT id, contenido, imagen, fecha FROM posts 
              WHERE usuario_id = ? ORDER BY fecha DESC";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $id_usuario);
$stmt_posts->execute();
$posts = $stmt_posts->get_result();

// Link WhatsApp
$telefono = preg_replace('/[^0-9]/', '', $usuario['telefono'] ?? '');
$whatsapp_link = !empty($telefono) ? "https://wa.me/$telefono" : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?> - MHAC</title>
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="css/base.css">

<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>

<body>
    <div class="perfil-container">
        <h1>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?></h1>

        <div class="avatar-container">
            <img src="<?php echo $avatar_final; ?>" 
                 alt="Foto de perfil" 
                 class="foto-perfil">
        </div>

        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['apellido']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
        <p><strong>Rol:</strong> <?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?></p>
        <p><strong>Miembro desde:</strong> <?php echo date("d/m/Y", strtotime($usuario['fecha_registro'])); ?></p>

        <?php if (!empty($whatsapp_link)): ?>
        <a href="<?= $whatsapp_link ?>" target="_blank" class="btn-whatsapp-grande">
            💬 Contactar por WhatsApp
        </a>
        <?php endif; ?>

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
                        <img src="../assets/uploads/mascotas/<?php echo htmlspecialchars($m['foto']); ?>" 
                             alt="<?php echo htmlspecialchars($m['nombre']); ?>" 
                             class="imagen-mascota">
                    <?php endif; ?>
                    <p><strong><?php echo htmlspecialchars($m['nombre']); ?></strong> (<?php echo htmlspecialchars($m['especie']); ?>)</p>
                    <p><?php echo nl2br(htmlspecialchars($m['descripcion'])); ?></p>
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
                        <img src="../assets/uploads/posts/<?php echo htmlspecialchars($p['imagen']); ?>" 
                             alt="Imagen del post" 
                             class="imagen-post">
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