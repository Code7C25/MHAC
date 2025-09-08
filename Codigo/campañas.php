<?php
include("conexion.php");
session_start();

function obtenerRutaImagen($nombreImagen) {
  $carpeta = "uploads/campañas/";
  $ruta = $carpeta . $nombreImagen;
  return (!empty($nombreImagen) && file_exists($ruta)) ? $ruta : "imagenes/default.jpg";
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener información del usuario actual
$usuario_id = $_SESSION['usuario_id'];
$sql_usuario = "SELECT rol FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$resultado_usuario = $stmt_usuario->get_result();
$usuario = $resultado_usuario->fetch_assoc();

// Verificar si el usuario tiene rol de refugio
$es_refugio = ($usuario['rol'] == 'refugio');

// Obtener filtros
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construcción dinámica del WHERE
$where = [];
$params = [];
$types = '';

if ($q !== '') {
    $where[] = "(c.titulo LIKE ? OR c.descripcion LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $types .= 'ss';
}
if ($estado !== '') {
    $where[] = "c.estado = ?";
    $params[] = $estado;
    $types .= 's';
}

// --------------------- Campañas activas ---------------------
$where_activas = $where;
$where_activas[] = "c.estado IN ('proxima','en_curso')";
$where_sql_activas = $where_activas ? 'WHERE ' . implode(' AND ', $where_activas) : '';
$sql_activas = "SELECT c.*, u.nombre AS organizador_nombre 
                FROM `campañas` c 
                JOIN usuarios u ON c.organizador_id = u.id 
                $where_sql_activas 
                ORDER BY c.fecha_inicio ASC";

$stmt_activas = $conn->prepare($sql_activas);
if (!$stmt_activas) {
    die("Error en la query activas: " . $conn->error . " | SQL: " . $sql_activas);
}
if ($params) $stmt_activas->bind_param($types, ...$params);
$stmt_activas->execute();
$campanias_activas = $stmt_activas->get_result();

// ------------------- Campañas finalizadas -------------------
$where_finalizadas = $where;
$where_finalizadas[] = "c.estado = 'finalizada'";
$where_sql_finalizadas = $where_finalizadas ? 'WHERE ' . implode(' AND ', $where_finalizadas) : '';
$sql_finalizadas = "SELECT c.*, u.nombre AS organizador_nombre 
                    FROM `campañas` c 
                    JOIN usuarios u ON c.organizador_id = u.id 
                    $where_sql_finalizadas 
                    ORDER BY c.fecha_fin DESC";

$stmt_finalizadas = $conn->prepare($sql_finalizadas);
if (!$stmt_finalizadas) {
    die("Error en la query finalizadas: " . $conn->error . " | SQL: " . $sql_finalizadas);
}
if ($params) $stmt_finalizadas->bind_param($types, ...$params);
$stmt_finalizadas->execute();
$campanias_finalizadas = $stmt_finalizadas->get_result();

// ------------------ Crear campaña (solo refugio) ------------------
if ($es_refugio && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_campania'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $lugar = $_POST['lugar'];
    $estado_nuevo = $_POST['estado'];
    
    // Imagen
    $imagen_nombre = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorio_imagenes = 'uploads/campañas/';
        if (!file_exists($directorio_imagenes)) {
            mkdir($directorio_imagenes, 0777, true);
        }
        
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen_nombre = uniqid() . '.' . $extension;
        $ruta_imagen = $directorio_imagenes . $imagen_nombre;
        
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen);
    }

    // Insertar en BD
    $sql_insert = "INSERT INTO `campañas` 
        (titulo, descripcion, fecha_inicio, fecha_fin, lugar, organizador_id, imagen, estado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    if (!$stmt_insert) {
        die("Error en la query insert: " . $conn->error);
    }
    $stmt_insert->bind_param("ssssisss", 
        $titulo, $descripcion, $fecha_inicio, $fecha_fin, $lugar, $usuario_id, $imagen_nombre, $estado_nuevo);
    
    if ($stmt_insert->execute()) {
        $mensaje_exito = "Campaña creada exitosamente!";
        header("Location: campañas.php");
        exit();
    } else {
        $mensaje_error = "Error al crear la campaña: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Campañas MHAC</title>
<<<<<<< HEAD
  <link rel="stylesheet" href="css/campañas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
  <header class="header">
    <h1>🌟 Campañas MHAC</h1>
    <p>Unite a nuestras iniciativas para el cuidado y adopción responsable de mascotas</p>
    <?php if ($es_refugio): ?>
      <button class="btn-crear" onclick="abrirModal()"><i class="fas fa-plus"></i> Crear Nueva Campaña</button>
    <?php endif; ?>
  </header>

  <!-- Buscador y filtros -->
  <section class="buscador">
    <form method="GET" action="campañas.php">
      <input type="text" name="q" placeholder="🔍 Buscar campañas..." value="<?= htmlspecialchars($q) ?>">
      <select name="estado">
        <option value="">-- Filtrar por estado --</option>
        <option value="proxima" <?= $estado === 'proxima' ? 'selected' : '' ?>>Próximas</option>
        <option value="en_curso" <?= $estado === 'en_curso' ? 'selected' : '' ?>>En curso</option>
        <option value="finalizada" <?= $estado === 'finalizada' ? 'selected' : '' ?>>Finalizadas</option>
      </select>
      <button type="submit" class="btn"><i class="fas fa-search"></i> Buscar</button>
    </form>
  </section>

<<<<<<< HEAD
  <!-- Mensajes de éxito/error -->
  <?php if (isset($mensaje_exito)): ?>
    <div class="mensaje exito"><i class="fas fa-check-circle"></i> <?= $mensaje_exito ?></div>
  <?php endif; ?>
  <?php if (isset($mensaje_error)): ?>
    <div class="mensaje error"><i class="fas fa-times-circle"></i> <?= $mensaje_error ?></div>
  <?php endif; ?>

<!-- Campañas activas -->
<section class="campanias-activas">
  <h2>🚀 Campañas Activas</h2>
  <div class="grid">
    <?php if ($campanias_activas->num_rows > 0): ?>
      <?php while($c = $campanias_activas->fetch_assoc()): ?>
        <div class="card">
          <img src="<?= obtenerRutaImagen($c['imagen']) ?>" alt="Imagen campaña">
          <div class="card-body">
            <h3><?= htmlspecialchars($c['titulo']) ?> 
              <span class="badge <?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span>
            </h3>
            <p><?= htmlspecialchars(substr($c['descripcion'], 0, 100)) ?>...</p>
            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($c['lugar']) ?></p>
            <p><i class="fas fa-user"></i> Organiza: <?= htmlspecialchars($c['organizador_nombre']) ?></p>
            <p><strong>📅 <?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($c['fecha_fin'])) ?></strong></p>

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
              <a href="participar.php?id=<?= $c['id'] ?>" class="btn"><i class="fas fa-hand-paper"></i> Participar</a>
              <a href="detalle_campania.php?id=<?= $c['id'] ?>" class="btn secundario"><i class="fas fa-info-circle"></i> Ver más</a>
              <a href="donar.php?id=<?= $c['id'] ?>" class="btn terciario"><i class="fas fa-heart"></i> Donar</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No hay campañas activas en este momento.</p>
    <?php endif; ?>
  </div>
</section>

<<<<<<< HEAD
<!-- Campañas finalizadas -->
<section class="campanias-finalizadas">
  <h2>📜 Historial de Campañas</h2>
  <div class="grid">
    <?php if ($campanias_finalizadas->num_rows > 0): ?>
      <?php while($c = $campanias_finalizadas->fetch_assoc()): ?>
        <div class="card finalizada">
          <img src="<?= obtenerRutaImagen($c['imagen']) ?>" alt="Imagen campaña">
          <div class="card-body">
            <h3><?= htmlspecialchars($c['titulo']) ?> <span class="badge finalizada">Finalizada</span></h3>
            <p><?= htmlspecialchars($c['descripcion']) ?></p>
            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($c['lugar']) ?></p>
            <p><i class="fas fa-user"></i> Organizó: <?= htmlspecialchars($c['organizador_nombre']) ?></p>
            <p><strong>📅 <?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($c['fecha_fin'])) ?></strong></p>
=======
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
>>>>>>> 98bf30a4adc5b6a1348d0393f6b1af95a61b5633
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>Aún no hay campañas finalizadas registradas.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Modal para crear campaña (solo visible para refugios) -->
 <?php if ($es_refugio): ?>
  <div id="modalCrear" class="modal">
    <div class="modal-contenido">
      <span class="cerrar" onclick="cerrarModal()">&times;</span>
      <h2>Crear Nueva Campaña</h2>
      <form method="POST" action="campañas.php" enctype="multipart/form-data">
        <input type="hidden" name="crear_campania" value="1">
        
        <div class="form-group">
          <label for="titulo">Título de la campaña:</label>
          <input type="text" id="titulo" name="titulo" required>
        </div>
        
        <div class="form-group">
          <label for="descripcion">Descripción:</label>
          <textarea id="descripcion" name="descripcion" required></textarea>
        </div>
        
        <div class="form-group">
          <label for="fecha_inicio">Fecha de inicio:</label>
          <input type="date" id="fecha_inicio" name="fecha_inicio" required>
        </div>
        
        <div class="form-group">
          <label for="fecha_fin">Fecha de finalización:</label>
          <input type="date" id="fecha_fin" name="fecha_fin" required>
        </div>
        
        <div class="form-group">
          <label for="lugar">Lugar:</label>
          <input type="text" id="lugar" name="lugar" required>
        </div>
        
        <div class="form-group">
          <label for="estado">Estado:</label>
          <select id="estado" name="estado" required>
            <option value="proxima">Próxima</option>
            <option value="en_curso">En curso</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="imagen">Imagen de la campaña:</label>
          <input type="file" id="imagen" name="imagen" accept="image/*">
        </div>
        
        <button type="submit" class="btn-crear">Crear Campaña</button>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- Footer -->
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

  <!-- Botón volver arriba -->
  <button id="btnTop" onclick="window.scrollTo({top:0, behavior:'smooth'})"><i class="fas fa-arrow-up"></i></button>

  <script src="js/campanias.js"></script>
</body>
</html>
