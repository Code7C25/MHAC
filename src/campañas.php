<?php
include("conexion.php");
session_start();

// --- 1. ACTUALIZACIÓN AUTOMÁTICA DE ESTADOS BASADA EN FECHAS ---
$hoy = date('Y-m-d');

// Actualizar estados
$conn->query("UPDATE campañas SET estado = 'finalizada' WHERE fecha_fin < '$hoy' AND estado != 'finalizada'");
$conn->query("UPDATE campañas SET estado = 'en_curso' WHERE fecha_inicio <= '$hoy' AND fecha_fin >= '$hoy' AND estado != 'en_curso'");
$conn->query("UPDATE campañas SET estado = 'proxima' WHERE fecha_inicio > '$hoy' AND estado != 'proxima'");

// Función para obtener ruta de imagen
function obtenerRutaImagen($nombreImagen) {
    $carpeta = "../assets/uploads/campañas/";
    $ruta = $carpeta . $nombreImagen;
    return (!empty($nombreImagen) && file_exists($ruta)) ? $ruta : "../assets/imagenes/default_campaign.jpg";
}

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener usuario
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$es_refugio = ($usuario['rol'] == 'refugio');

// Filtros
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';

$where = [];
$params = [];
$types = '';

if ($q !== '') {
    $where[] = "(c.titulo LIKE ? OR c.descripcion LIKE ?)";
    $params[] = "%$q%"; $params[] = "%$q%";
    $types .= 'ss';
}
if ($estado_filtro !== '') {
    $where[] = "c.estado = ?";
    $params[] = $estado_filtro;
    $types .= 's';
}

// Campañas activas
$where_activas = $where;
$where_activas[] = "c.estado IN ('proxima','en_curso')";
$where_sql_activas = $where_activas ? 'WHERE ' . implode(' AND ', $where_activas) : '';
$sql_activas = "SELECT c.*, u.nombre AS organizador_nombre FROM `campañas` c JOIN usuarios u ON c.organizador_id = u.id $where_sql_activas ORDER BY c.estado ASC, c.fecha_inicio ASC";
$stmt_activas = $conn->prepare($sql_activas);
if ($params) $stmt_activas->bind_param($types, ...$params);
$stmt_activas->execute();
$campanias_activas = $stmt_activas->get_result();

// Campañas finalizadas
$where_finalizadas = $where;
$where_finalizadas[] = "c.estado = 'finalizada'";
$where_sql_finalizadas = $where_finalizadas ? 'WHERE ' . implode(' AND ', $where_finalizadas) : '';
$sql_finalizadas = "SELECT c.*, u.nombre AS organizador_nombre FROM `campañas` c JOIN usuarios u ON c.organizador_id = u.id $where_sql_finalizadas ORDER BY c.fecha_fin DESC";
$stmt_finalizadas = $conn->prepare($sql_finalizadas);
if ($params) $stmt_finalizadas->bind_param($types, ...$params);
$stmt_finalizadas->execute();
$campanias_finalizadas = $stmt_finalizadas->get_result();

// Crear campaña
if ($es_refugio && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_campania'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $lugar = $_POST['lugar'];
    
    $estado_nuevo = 'proxima';
    if ($fecha_inicio <= $hoy && $fecha_fin >= $hoy) $estado_nuevo = 'en_curso';
    elseif ($fecha_fin < $hoy) $estado_nuevo = 'finalizada';
    
    $imagen_nombre = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $dir = '../assets/uploads/campañas/';
        if (!file_exists($dir)) mkdir($dir, 0777, true);
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen_nombre = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $imagen_nombre);
    }

    $stmt_ins = $conn->prepare("INSERT INTO `campañas` (titulo, descripcion, fecha_inicio, fecha_fin, lugar, organizador_id, imagen, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_ins->bind_param("ssssisss", $titulo, $descripcion, $fecha_inicio, $fecha_fin, $lugar, $usuario_id, $imagen_nombre, $estado_nuevo);
    
    if ($stmt_ins->execute()) {
        header("Location: campañas.php?msg=creada");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Campañas - MHAC</title>
  <link rel="stylesheet" href="css/campañas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <a href="index.php" class="volver-inicio">
      <span>←</span> Volver al Inicio
  </a>

  <header class="header">
    <h1>Campañas MHAC</h1>
    <p>Unite a nuestras iniciativas para el cuidado y adopción responsable de mascotas</p>
    <?php if ($es_refugio): ?>
      <button class="btn-crear-header" onclick="abrirModal()">
        <i class="fas fa-plus"></i> Crear Nueva Campaña
      </button>
    <?php endif; ?>
  </header>

  <section class="buscador">
    <form method="GET" action="campañas.php">
      <input type="text" name="q" placeholder="Buscar campañas..." value="<?= htmlspecialchars($q) ?>">
      <select name="estado">
        <option value="">-- Filtrar por estado --</option>
        <option value="proxima" <?= $estado_filtro === 'proxima' ? 'selected' : '' ?>>Próximas</option>
        <option value="en_curso" <?= $estado_filtro === 'en_curso' ? 'selected' : '' ?>>En curso</option>
        <option value="finalizada" <?= $estado_filtro === 'finalizada' ? 'selected' : '' ?>>Finalizadas</option>
      </select>
      <button type="submit" class="btn-buscar"><i class="fas fa-search"></i> Buscar</button>
      <?php if($q || $estado_filtro): ?>
          <a href="campañas.php" class="btn-limpiar">Limpiar</a>
      <?php endif; ?>
    </form>
  </section>

  <?php if (isset($_GET['msg']) && $_GET['msg'] == 'creada'): ?>
    <div class="mensaje-exito">
        <i class="fas fa-check-circle"></i> ¡Campaña creada exitosamente!
    </div>
  <?php endif; ?>

  <section class="seccion-campanias">
    <h2>🚀 Campañas Activas</h2>
    <div class="grid">
      <?php if ($campanias_activas->num_rows > 0): ?>
        <?php while($c = $campanias_activas->fetch_assoc()): ?>
          <div class="card">
            <div class="card-img-container">
                <img src="<?= obtenerRutaImagen($c['imagen']) ?>" alt="Imagen campaña">
            </div>
            <div class="card-body">
              <h3>
                  <?= htmlspecialchars($c['titulo']) ?> 
                  <?php if($c['estado'] == 'en_curso'): ?>
                      <span class="badge en-curso">En Curso</span>
                  <?php else: ?>
                      <span class="badge proxima">Próxima</span>
                  <?php endif; ?>
              </h3>
              
              <p class="descripcion"><?= htmlspecialchars(substr($c['descripcion'], 0, 100)) ?>...</p>
              
              <div class="info-meta">
                  <p><i class="fas fa-map-marker-alt"></i> <strong>Lugar:</strong> <?= htmlspecialchars($c['lugar']) ?></p>
                  <p><i class="fas fa-calendar-alt"></i> <strong>Desde:</strong> <?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?></p>
                  <p><i class="fas fa-flag-checkered"></i> <strong>Hasta:</strong> <?= date('d/m/Y', strtotime($c['fecha_fin'])) ?></p>
              </div>
              
              <div class="acciones">
                <a href="donaciones.php?campana_id=<?= $c['id'] ?>" class="btn-colaborar">
                    <i class="fas fa-heart"></i> Colaborar
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="sin-resultados">No hay campañas activas en este momento.</p>
      <?php endif; ?>
    </div>
  </section>

  <section class="seccion-finalizadas">
    <h2>🏁 Historial de Campañas (Finalizadas)</h2>
    <div class="grid">
      <?php if ($campanias_finalizadas->num_rows > 0): ?>
        <?php while($c = $campanias_finalizadas->fetch_assoc()): ?>
          <div class="card finalizada">
            <div class="card-img-container">
                <img src="<?= obtenerRutaImagen($c['imagen']) ?>" alt="Imagen campaña">
            </div>
            <div class="card-body">
                <h3><?= htmlspecialchars($c['titulo']) ?> <span class="badge finalizada-badge">Finalizada</span></h3>
                <p><strong>Finalizó el:</strong> <?= date('d/m/Y', strtotime($c['fecha_fin'])) ?></p>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="sin-resultados">Aún no hay campañas finalizadas registradas.</p>
      <?php endif; ?>
    </div>
  </section>

  <?php if ($es_refugio): ?>
  <div id="modalCrear" class="modal">
    <div class="modal-contenido">
      <span class="cerrar" onclick="cerrarModal()">&times;</span>
      <h2>Crear Nueva Campaña</h2>
      <form method="POST" action="campañas.php" enctype="multipart/form-data" class="form-modal">
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
          <label for="lugar">Lugar / Dirección:</label>
          <input type="text" id="lugar" name="lugar" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
            <label for="fecha_inicio">Fecha inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="form-group">
            <label for="fecha_fin">Fecha fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>
        </div>
        
        <div class="form-group">
          <label for="imagen">Imagen de la campaña:</label>
          <input type="file" id="imagen" name="imagen" accept="image/*" required>
        </div>
        
        <button type="submit" class="btn-submit-modal">Publicar Campaña</button>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <script>
    function abrirModal() { document.getElementById("modalCrear").style.display = "block"; }
    function cerrarModal() { document.getElementById("modalCrear").style.display = "none"; }
    window.onclick = function(e) {
        if (e.target == document.getElementById("modalCrear")) cerrarModal();
    }
  </script>
</body>
</html>