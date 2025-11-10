<?php
session_start();
require_once 'conexion.php'; 

// Inicialización de variables para mantener los datos en el formulario si hay un error
$titulo_valor = $_POST['titulo_historia'] ?? '';
$mascota_nombre_valor = $_POST['mascota_nombre'] ?? '';
$rescatista_valor = $_POST['rescatista'] ?? $_SESSION['nombre'] ?? ''; 
$email_contacto_valor = $_POST['email_contacto'] ?? $_SESSION['email'] ?? ''; 
$historia_valor = $_POST['historia'] ?? '';

// Si tienes mensajes de error/éxito de procesar_rescate.php, muéstralos aquí
$mensaje = $_SESSION['rescate_mensaje'] ?? '';
$exito = $_SESSION['rescate_exito'] ?? false;
unset($_SESSION['rescate_mensaje'], $_SESSION['rescate_exito']); // Limpiar mensajes

// ----------------------------------------------------
// Lógica para el LISTADO de Historias APROBADAS
// ----------------------------------------------------
$sql_listado = "SELECT id, titulo_historia, mascota_nombre, rescatista, historia, foto_url, fecha_creacion
                FROM rescates
                WHERE estado = 'Aprobado'
                ORDER BY fecha_creacion DESC";
$result_listado = $conn->query($sql_listado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historias de Rescate - MHAC</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/rescates.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <script src="https://cdn.tiny.cloud/1/lqoycqy6vgr0fym1udkfuqvgxz5nfoa5mu1v2mtjcco049yl/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <a href="index.php" class="volver-inicio">
        <span>←</span> Volver al inicio
    </a>
</head>
<body>

<header>
    <h1>💖 Historias de Rescate</h1>
    <blockquote class="refran-conmovedor">
        "Rescatar un animal no cambiará el mundo, pero cambiará el mundo de ese animal."
    </blockquote>
    <p>Inspírate con relatos de segundas oportunidades y encuentra la motivación para unirte a la causa.</p>
    <hr>
</header>

<main>
    <?php if ($mensaje): ?>
        <div class="mensaje-alerta <?= $exito ? 'exito-rescate' : 'error-rescate' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <a href="#compartir" class="btn-flotante-compartir">
            Comparte tu Historia ✨
        </a>
    <?php endif; ?>
    
    <hr>
    
    <section class="listado-historias">
        <h2>Historias que Inspiran</h2>
        
        <?php if ($result_listado && $result_listado->num_rows > 0): ?>
            <?php while ($h = $result_listado->fetch_assoc()): ?>
                <article class="historia-card">
                    <?php if (!empty($h['foto_url'])): ?>
                        <img src="<?= htmlspecialchars($h['foto_url']) ?>" alt="Foto de <?= htmlspecialchars($h['mascota_nombre']) ?> rescatada">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($h['titulo_historia']) ?></h3>
                    <p class="historia-excerpt"><?= htmlspecialchars(substr(strip_tags($h['historia']), 0, 150)) ?>... 
                        <a href="detalle_rescate.php?id=<?= $h['id'] ?>">Leer más</a>
                    </p>
                    <small>Publicado por: <?= htmlspecialchars($h['rescatista']) ?> | Mascota: <?= htmlspecialchars($h['mascota_nombre']) ?> | <?= date("d/m/Y", strtotime($h['fecha_creacion'])) ?></small>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>¡Sé el primero en compartir una historia conmovedora! (Recuerda que las historias nuevas están en revisión).</p>
        <?php endif; ?>
    </section>

    <hr>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <section class="seccion-formulario" id="compartir"> 
            <h2>Comparte tu historia</h2>
            
            <form action="procesar_rescate.php" method="POST" enctype="multipart/form-data" class="form-historia">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="titulo">Título de la Historia:</label><br>
                        <input type="text" id="titulo" name="titulo_historia" value="<?= htmlspecialchars($titulo_valor) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="mascota_nombre">Nombre de la Mascota:</label><br>
                        <input type="text" id="mascota_nombre" name="mascota_nombre" value="<?= htmlspecialchars($mascota_nombre_valor) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="rescatista">Tu Nombre o Apodo:</label><br>
                        <input type="text" id="rescatista" name="rescatista" value="<?= htmlspecialchars($rescatista_valor) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Tu Correo (No será publicado):</label><br>
                        <input type="email" id="email" name="email_contacto" value="<?= htmlspecialchars($email_contacto_valor) ?>" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="historia">Relato Completo:</label><br>
                    <textarea name="historia" rows="10">
                        <?= htmlspecialchars($historia_valor) ?>
                    </textarea>
                </div>

                <div class="form-group full-width">
                    <label for="foto">Sube una Foto:</label><br>
                    <input type="file" id="foto" name="foto_rescate" accept="image/*" required>
                </div>

                <button type="submit" class="btn-publicar">Publicar mi Historia ✨</button>
            </form>
        </section>
    <?php else: ?>
        <section class="alerta-login">
            <p>Para compartir tu historia, por favor <a href="login.php">inicia sesión</a> o <a href="registro.php">regístrate</a>.</p>
        </section>
    <?php endif; ?>

</main>

<script>
    // Inicializar TinyMCE para el campo 'historia'
    tinymce.init({
        selector: 'textarea[name="historia"]', 
        plugins: 'advlist autolink lists link charmap code', 
        toolbar: 'undo redo | formatselect | bold italic backcolor | bullist numlist outdent indent | code',
        height: 400
    });
</script>

</body>
</html>