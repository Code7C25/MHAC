<?php
session_start();
require_once 'conexion.php';

// Verificación de rol (sin cambios)
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['veterinario','refugio'])) {
    header("Location: info.php");
    exit;
}

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $categoria = trim($_POST['categoria']); // <-- ¡NUEVO!
    $autor_id = $_SESSION['usuario_id'];
    $autor = trim($_POST['autor'] ?? '');

    // Se verifica que la categoría también esté presente.
    if ($titulo && $contenido && $categoria) { 
        
        // La consulta ahora incluye la columna 'categoria'
        // ahora sí: 5 parámetros (s, s, s, i, s)
        $stmt = $conn->prepare("INSERT INTO cuidados (titulo, categoria, contenido, autor_id, autor) VALUES (?, ?, ?, ?, ?)");
        // El tipo de parámetro debe cambiar a "sssis" (string, string, string, int, string)
        $stmt->bind_param("sssis", $titulo, $categoria, $contenido, $autor_id, $autor);
        
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

        <label for="categoria">Categoría:</label>
        <select name="categoria" id="categoria" required>
            <option value="">-- Seleccionar Categoría --</option>
            <option value="Perros: Salud">Perros: Salud</option>
            <option value="Perros: Comportamiento">Perros: Comportamiento</option>
            <option value="Gatos: Salud">Gatos: Salud</option>
            <option value="Gatos: Comportamiento">Gatos: Comportamiento</option>
            <option value="Adopción Responsable">Adopción Responsable</option>
            <option value="Primeros Auxilios">Primeros Auxilios</option>
            <option value="Varios/Legal">Varios/Legal</option>
        </select>
        <br><br>
        <label>
        Contenido:<br>
        <textarea name="contenido" rows="6" required></textarea>
        </label><br><br>

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