<?php
session_start();
require_once 'conexion.php';

// Solo permite a usuarios logueados sugerir consejos
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// --- Datos del autor (con control para refugios/usuarios) ---
$autor_id = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : 0;
$nombre = isset($_SESSION['nombre']) ? trim($_SESSION['nombre']) : '';
$apellido = isset($_SESSION['apellido']) ? trim($_SESSION['apellido']) : '';
$nickname = isset($_SESSION['nickname']) ? trim($_SESSION['nickname']) : '';
$razon_social = isset($_SESSION['razon_social']) ? trim($_SESSION['razon_social']) : ''; // para refugios
$rol_autor = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'usuario';

// Construir nombre visible según tipo de usuario
if ($nombre !== '' && $apellido !== '') {
    $autor_nombre = $nombre . ' ' . $apellido;
} elseif ($razon_social !== '') {
    $autor_nombre = $razon_social;
} elseif ($nickname !== '') {
    $autor_nombre = $nickname;
} else {
    $autor_nombre = 'Usuario #' . $autor_id;
}

// Inicializar variable para mantener valor en el formulario
$contenido_valor = '';
$tipo_valor = '';

// --- Lógica principal ---
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenido = trim($_POST['contenido'] ?? '');
    $tipo = trim($_POST['tipo'] ?? ''); // <-- CAPTURAR NUEVO CAMPO
    
    // Asignar valores para mantenerlos en el formulario si hay error
    $contenido_valor = $contenido;
    $tipo_valor = $tipo;

    // 1. Nueva validación para 'tipo'
    if (!in_array($tipo, ['Consejo', 'Dato Curioso'])) {
        $mensaje = "⚠️ Debes seleccionar si tu contribución es un Consejo o un Dato Curioso.";
    } 
    // 2. Validación de contenido
    elseif (strlen($contenido) < 20 || strlen($contenido) > 250) {
        $mensaje = "⚠️ El contenido debe tener entre 20 y 250 caracteres.";
    } 
    // 3. Inserción si todo es correcto
    elseif ($contenido) {
        // La consulta ahora incluye 'tipo' y 'verificado'
        $stmt = $conn->prepare("INSERT INTO consejos_comunidad (contenido, tipo, autor_id, autor_nombre, rol_autor, verificado) VALUES (?, ?, ?, ?, ?, FALSE)");
        // Los tipos de bind_param: (s, s, i, s, s) -> (contenido, tipo, autor_id, autor_nombre, rol_autor)
        $stmt->bind_param("ssiss", $contenido, $tipo, $autor_id, $autor_nombre, $rol_autor); 
        
        if ($stmt->execute()) {
            $mensaje = "✅ ¡Gracias! Tu consejo ha sido enviado y está pendiente de verificación por un veterinario o refugio.";
            // Limpiar valores después del éxito
            $contenido_valor = '';
            $tipo_valor = '';
        } else {
            $mensaje = "❌ Error al enviar el consejo: " . $conn->error;
        }
    } else {
        $mensaje = "⚠️ El contenido del consejo es obligatorio.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Sugerir Consejo - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/sugerir_consejo.css">
</head>

<body>
<header>
  <h1>🐾 Sugerir un Nuevo Consejo</h1>
</header>

<main>
  <div class="consejo-card">
    <?php if ($mensaje): 
        $clase_mensaje = (strpos($mensaje, '✅') !== false) ? 'exito' : 'error';
    ?>
        <p class="mensaje-alerta <?= $clase_mensaje ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        
        <label for="tipo">Tipo de Contribución:</label>
        <select name="tipo" id="tipo" required>
            <option value="">-- Seleccionar --</option>
            
            <?php 
            $tipos = ['Consejo', 'Dato Curioso'];
            foreach ($tipos as $t): 
                $selected = ($tipo_valor === $t) ? 'selected' : '';
            ?>
                <option value="<?= htmlspecialchars($t) ?>" <?= $selected ?>>
                    <?= htmlspecialchars($t) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        
        <label for="contenido">Comparte un consejo breve (20 a 250 caracteres):</label>
        <textarea name="contenido" id="contenido" rows="4" maxlength="250" required 
        placeholder="Ej: 'Vacunar anualmente a tu mascota previene enfermedades graves.'"><?= htmlspecialchars($contenido_valor) ?></textarea>

        <div class="contador"><span id="charCount"><?= strlen($contenido_valor) ?></span>/250</div>

        <p><small>💡 Tu consejo será revisado antes de mostrarse en los módulos de MHAC.</small></p>

        <button type="submit">Enviar Consejo</button>
        <br>
        <a href="info.php" class="volver">← Volver</a>
      </div>
    </form>
  </div>
</main>

<script>
// ... (El script del contador de caracteres sigue igual) ...
const textarea = document.getElementById("contenido");
const charCount = document.getElementById("charCount");

textarea.addEventListener("input", () => {
  const count = textarea.value.length;
  charCount.textContent = count;
  charCount.style.color = count < 20 ? "#b30000" : (count > 250 ? "#b30000" : "#555");
});
</script>

</body>
</html>