<?php
session_start();
require_once 'conexion.php';

// Función para corregir rutas de imágenes
function corregir_ruta_imagen($ruta) {
    if (empty($ruta)) return '';
    
    // Si es una URL completa, devolverla tal cual
    if (strpos($ruta, 'http://') === 0 || strpos($ruta, 'https://') === 0) {
        return $ruta;
    }
    
    // Eliminar rutas absolutas del sistema de archivos
    $ruta = str_replace('\\', '/', $ruta);
    $ruta = preg_replace('/^[A-Z]:\//i', '', $ruta); // Eliminar C:/ D:/ etc
    $ruta = preg_replace('/^\/var\/www\/html\//', '', $ruta);
    $ruta = preg_replace('/^\/xampp\/htdocs\//', '', $ruta);
    
    // Eliminar múltiples barras
    $ruta = preg_replace('/\/+/', '/', $ruta);
    
    // Asegurarse de que no empiece con /
    $ruta = ltrim($ruta, '/');
    
    // Si la ruta no tiene carpeta, buscar en imagenes/ o uploads/mascotas/
    if (!strpos($ruta, '/')) {
        if (file_exists('imagenes/' . $ruta)) {
            return 'imagenes/' . $ruta;
        } elseif (file_exists('uploads/mascotas/' . $ruta)) {
            return 'uploads/mascotas/' . $ruta;
        }
    }
    
    return $ruta;
}

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
    $avatar = corregir_ruta_imagen($usuario['foto_perfil']);
} else {
    // Verificar si existe la carpeta de avatares
    if (is_dir("imagenes/avatars")) {
        $avatares = glob("imagenes/avatars/*.{png,jpg,jpeg}", GLOB_BRACE);
        if (count($avatares) > 0) {
            $avatar = $avatares[array_rand($avatares)];
        } else {
            $avatar = "imagenes/default-avatar.png"; // Avatar por defecto
        }
    } else {
        $avatar = "imagenes/default-avatar.png";
    }
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
    <link rel="stylesheet" href="css/perfil.css">
</head>
<body>
    <div class="perfil-container">
        <h1>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?></h1>

        <div class="avatar-container">
            <img src="<?php echo htmlspecialchars($avatar); ?>" 
                 alt="Foto de perfil" 
                 class="foto-perfil"
                 onerror="this.src='imagenes/default-avatar.png'; this.onerror=null;">
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
                    <?php if (!empty($m['foto'])): 
                        $foto_mascota = corregir_ruta_imagen($m['foto']);
                    ?>
                        <img src="<?php echo htmlspecialchars($foto_mascota); ?>" 
                             alt="Foto de <?php echo htmlspecialchars($m['nombre']); ?>" 
                             class="imagen-mascota"
                             onerror="this.style.display='none';">
                        <!-- Debug: Mostrar ruta (comentar en producción) -->
                        <!-- <small style="color: #999;">Ruta: <?php echo htmlspecialchars($foto_mascota); ?></small> -->
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
                    <?php if (!empty($p['imagen'])): 
                        $foto_post = corregir_ruta_imagen($p['imagen']);
                    ?>
                        <img src="<?php echo htmlspecialchars($foto_post); ?>" 
                             alt="Imagen del post" 
                             class="imagen-post"
                             onerror="this.style.display='none';">
                        <!-- Debug: Mostrar ruta (comentar en producción) -->
                        <!-- <small style="color: #999;">Ruta: <?php echo htmlspecialchars($foto_post); ?></small> -->
                    <?php endif; ?>
                    <p><?php echo nl2br(htmlspecialchars($p['contenido'])); ?></p>
                    <span class="fecha"><?php echo date("d/m/Y H:i", strtotime($p['fecha'])); ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Este usuario aún no tiene posts.</p>
        <?php endif; ?>
    </div>

    <script>
        // Verificar imágenes rotas y mostrar info en consola
        document.addEventListener('DOMContentLoaded', function() {
            const imagenes = document.querySelectorAll('img');
            imagenes.forEach(img => {
                img.addEventListener('error', function() {
                    console.error('Error cargando imagen:', this.src);
                });
            });
        });
    </script>
</body>
</html>