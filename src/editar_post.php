<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'dador') {
    die("❌ Debes iniciar sesión como Dador.");
}

$dador_id = $_SESSION['usuario_id'];

// --- Actualizar datos ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mascota_id = $_POST['mascota_id'];
    $estado = $_POST['estado'];

    $sql = "UPDATE mascotas SET estado=? WHERE id=? AND dador_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $estado, $mascota_id, $dador_id);
    $stmt->execute();

    header("Location: mis_posts.php");
    exit;
}

// --- Mostrar formulario para editar info ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['mascota_id'])) {
    $mascota_id = $_GET['mascota_id'];
    $sql = "SELECT * FROM mascotas WHERE id=? AND dador_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $mascota_id, $dador_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $m = $res->fetch_assoc();

    if (!$m) die("Mascota no encontrada.");

    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Editar Mascota</title>
        <link rel="stylesheet" href="css/adopcion.css">
        <link rel="stylesheet" href="css/base.css">
        <a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
    </head>
    <body>
    <h1>Editar <?= htmlspecialchars($m['nombre']) ?></h1>
    <form action="editar_post.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="mascota_id" value="<?= $m['id'] ?>">
        <label>Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($m['nombre']) ?>"></label><br>
        <label>Descripción:<br>
            <textarea name="descripcion"><?= htmlspecialchars($m['descripcion']) ?></textarea>
        </label><br>
        <label>Foto: <input type="file" name="foto"></label><br>
        <label>Estado:
            <select name="estado">
                <option value="en_adopcion" <?= $m['estado']=='en_adopcion'?'selected':'' ?>>En adopción</option>
                <option value="reservado" <?= $m['estado']=='reservado'?'selected':'' ?>>Reservado</option>
                <option value="adoptado" <?= $m['estado']=='adoptado'?'selected':'' ?>>Adoptado</option>
            </select>
        </label><br>
        <button type="submit">Guardar Cambios</button>
    </form>
    </body>
    </html>
    <?php
    exit;
}
?>
