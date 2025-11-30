<?php
session_start();
require_once 'conexion.php';

// 1. Verificar Rol: Solo veterinario o refugio pueden acceder
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['veterinario', 'refugio'])) {
    header("Location: info.php");
    exit;
}

$mensaje = "";
$cuidado_id = $_GET['id'] ?? null;
$cuidado_data = null; // Para almacenar los datos del artículo a editar

// Redirigir si no hay ID válido
if (!$cuidado_id || !is_numeric($cuidado_id)) {
    header("Location: info.php");
    exit;
}

// ----------------------------------------------------
// 2. LÓGICA POST: Procesar la actualización
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $categoria = trim($_POST['categoria']);
    $autor = trim($_POST['autor'] ?? '');

    // Se verifica que la categoría, título y contenido estén presentes.
    if ($titulo && $contenido && $categoria) {
        
        // 2a. Verificar si el usuario es el autor antes de actualizar (SEGURIDAD EXTRA)
        $stmt_check = $conn->prepare("SELECT autor_id FROM cuidados WHERE id = ?");
        $stmt_check->bind_param("i", $cuidado_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $current_author = $result_check->fetch_assoc();

        if (!$current_author || $current_author['autor_id'] != $_SESSION['usuario_id']) {
            $mensaje = "Error: No tienes permiso para editar este artículo.";
            // Si no tiene permiso, volvemos a cargar los datos originales
            // La lógica GET se encargará de esto si no hacemos un exit.
        } else {
            // 2b. Ejecutar la actualización (UPDATE)
            $stmt = $conn->prepare("
                UPDATE cuidados 
                SET titulo = ?, categoria = ?, contenido = ?, autor = ? 
                WHERE id = ? AND autor_id = ?
            ");
            // Tipos: (s, s, s, s, i, i) -> (titulo, categoria, contenido, autor, id, autor_id)
            $stmt->bind_param("ssssii", $titulo, $categoria, $contenido, $autor, $cuidado_id, $_SESSION['usuario_id']);
            
            if ($stmt->execute()) {
                // Redirigir al detalle del cuidado para ver el cambio
                header("Location: detalle_cuidado.php?id=" . $cuidado_id);
                exit;
            } else {
                $mensaje = "Error al actualizar el cuidado: " . $conn->error;
            }
        }
    } else {
        $mensaje = "Todos los campos (Título, Categoría, Contenido) son obligatorios.";
    }
}

// ----------------------------------------------------
// 3. LÓGICA GET: Obtener los datos actuales del artículo (para prellenar el formulario)
// ----------------------------------------------------

// 3a. Usamos Prepared Statement para obtener el artículo
$stmt = $conn->prepare("SELECT titulo, contenido, categoria, autor, autor_id FROM cuidados WHERE id = ?");
$stmt->bind_param("i", $cuidado_id);
$stmt->execute();
$result = $stmt->get_result();
$cuidado_data = $result->fetch_assoc();

// 3b. Verificar permiso final (si el usuario es el autor)
if (!$cuidado_data || $cuidado_data['autor_id'] != $_SESSION['usuario_id']) {
    // Si no existe el artículo o no es el autor, redirigir
    header("Location: info.php");
    exit;
}

// Si la solicitud es POST y hubo un error, usamos los datos POST para que el usuario no pierda lo escrito
// Si la solicitud es GET, usamos $cuidado_data
$titulo_valor = $_POST['titulo'] ?? $cuidado_data['titulo'];
$contenido_valor = $_POST['contenido'] ?? $cuidado_data['contenido'];
$categoria_valor = $_POST['categoria'] ?? $cuidado_data['categoria'];
$autor_valor = $_POST['autor'] ?? $cuidado_data['autor'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Cuidado - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/info.css">
  <script src="https://cdn.tiny.cloud/1/lqoycqy6vgr0fym1udkfuqvgxz5nfoa5mu1v2mtjcco049yl/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>

<body>
<header>
  <h1>Editar cuidado o consejo</h1>
  <small>ID de Artículo: <?= htmlspecialchars($cuidado_id) ?></small>
</header>

<main>
  <?php if ($mensaje): ?><p class="mensaje-alerta error"><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>
  
  <form method="post">
    <div class="form-group">
        <label>
        Título:<br>
        <input type="text" name="titulo" value="<?= htmlspecialchars($titulo_valor) ?>" required>
        </label><br><br>

        <label for="categoria">Categoría:</label>
        <select name="categoria" id="categoria" required>
            <option value="">-- Seleccionar Categoría --</option>
            
            <?php 
            $categorias = ["Perros: Salud", "Perros: Comportamiento", "Gatos: Salud", "Gatos: Comportamiento", 
                           "Adopción Responsable", "Primeros Auxilios", "Varios/Legal"];
            foreach ($categorias as $cat): 
                $selected = ($categoria_valor === $cat) ? 'selected' : '';
            ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $selected ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        
        <label>
        Contenido:<br>
        <textarea name="contenido" rows="10">
            <?= htmlspecialchars($contenido_valor) ?>
        </textarea>
        </label><br><br>

        <label for="autor">Autor / Créditos (opcional):</label>
        <input type="text" name="autor" id="autor" 
            value="<?= htmlspecialchars($autor_valor) ?>">

        <br><br>
        <button type="submit">Guardar Cambios</button>
        <a href="detalle_cuidado.php?id=<?= htmlspecialchars($cuidado_id) ?>">Cancelar</a>
    </div>
  </form>
</main>

<script>
    tinymce.init({
        selector: 'textarea[name="contenido"]', 
        plugins: 'advlist autolink lists link charmap code', // Plugins básicos
        toolbar: 'undo redo | formatselect | bold italic backcolor | bullist numlist | code',
        height: 400
    });
</script>
</body>
</html>