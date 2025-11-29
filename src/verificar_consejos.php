<?php
session_start();
require_once 'conexion.php';

$mensaje = "";

// 1. VERIFICACIÓN DE ROL: Debe ser 'veterinario' O 'refugio'.
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['veterinario', 'refugio'])) {
    header("Location: info.php"); // Redirigir a 'info.php' (donde están los consejos) es más lógico.
    exit;
}

// 2. LÓGICA DE ACCIÓN (Aprobar o Rechazar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Usamos 'accion' para simplificar la lógica del formulario, como hicimos antes.
    $accion = $_POST['accion'] ?? '';
    $consejo_id = (int)($_POST['consejo_id'] ?? 0); 

    if ($consejo_id > 0) {
        
        if ($accion === 'aprobar') {
            // Usando prepared statement para SEGURIDAD
            $stmt = $conn->prepare("UPDATE consejos_comunidad SET verificado = TRUE WHERE id = ?");
            $stmt->bind_param("i", $consejo_id);
            if ($stmt->execute()) {
                $mensaje = "✅ Consejo ID $consejo_id verificado y publicado con éxito.";
            } else {
                $mensaje = "Error al aprobar: " . $stmt->error;
            }
        
        } elseif ($accion === 'rechazar') {
            // Usando prepared statement para SEGURIDAD
            $stmt = $conn->prepare("DELETE FROM consejos_comunidad WHERE id = ?");
            $stmt->bind_param("i", $consejo_id);
            if ($stmt->execute()) {
                $mensaje = "❌ Consejo ID $consejo_id eliminado correctamente.";
            } else {
                 $mensaje = "Error al rechazar: " . $stmt->error;
            }
        }
    }
}

// 3. OBTENER CONSEJOS PENDIENTES
$sql = "SELECT id, contenido, tipo, autor_nombre, rol_autor, fecha_creacion FROM consejos_comunidad WHERE verificado = FALSE ORDER BY fecha_creacion ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Moderación de Consejos - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/info.css"> 
</head>
<body>
<header>
  <h1>Moderación de Consejos de la Comunidad</h1>
</header>
<main>
  <a href="info.php" class="volver-inicio"><span>←</span> Volver a Consejos</a>
  
  <?php if ($mensaje): ?><p class="mensaje-alerta"><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>

  <h2>Consejos Pendientes de Revisión (<?= $result ? $result->num_rows : 0 ?>)</h2>

  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($c = $result->fetch_assoc()): ?>
      <div class="consejo-revision">
        <p>
            <span class="etiqueta-tipo-consejo"><?= htmlspecialchars($c['tipo']) ?>:</span>
            "<?= htmlspecialchars($c['contenido']) ?>"
        </p>
        <small>
          Sugerido por: <?= htmlspecialchars($c['autor_nombre']) ?> (<?= ucfirst($c['rol_autor']) ?>) 
          el <?= date("d/m/Y H:i", strtotime($c['fecha_creacion'])) ?>
        </small>
        
        <form method="post" style="display:inline-block;">
          <input type="hidden" name="consejo_id" value="<?= $c['id'] ?>">
          <button type="submit" name="accion" value="aprobar" class="btn-verificar">Aprobar</button>
        </form>
        
        <form method="post" style="display:inline-block;">
          <input type="hidden" name="consejo_id" value="<?= $c['id'] ?>">
          <button type="submit" name="accion" value="rechazar" class="btn-eliminar">Rechazar</button>
        </form>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>¡No hay consejos pendientes de moderación!</p>
  <?php endif; ?>
</main>
</body>
</html>