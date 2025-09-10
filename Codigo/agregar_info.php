<?php
session_start();
require_once 'conexion.php';

// Verificación de rol
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['veterinario','refugio'])) {
    header("Location: info.php");
    exit;
}

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $autor_id = $_SESSION['usuario_id'];
    $autor = trim($_POST['autor'] ?? '');

    if ($titulo && $contenido) {
        // ahora sí: 4 parámetros (s, s, i, s)
        $stmt = $conn->prepare("INSERT INTO cuidados (titulo, contenido, autor_id, autor) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $titulo, $contenido, $autor_id, $autor);
        
        if ($stmt->execute()) {
            header("Location: info.php");
            exit;
        } else {
            $mensaje = "Error al guardar el cuidado: " . $conn->error;
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Cuidado - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/info.css">
</head>
<body>
<header>
  <h1>Agregar cuidado o consejo</h1>
</header>
<main>
  <?php if ($mensaje): ?><p><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>
  <form method="post">
    <div class="form-group">
        <label>
        Título:<br>
        <input type="text" name="titulo" required>
        </label><br><br>

        <label>
        Contenido:<br>
        <textarea name="contenido" rows="6" required></textarea>
        </label><br><br>

        <!-- Campo para autor / créditos -->
        <label for="autor">Autor / Créditos (opcional):</label>
        <input type="text" name="autor" id="autor" 
            value="<?= isset($autor) ? htmlspecialchars($autor) : '' ?>">

        <br><br>
        <button type="submit">Guardar</button>
        <a href="info.php">Cancelar</a>
    </div>
  </form>
</main>
</body>
</html>
