<?php
session_start();
require_once 'conexion.php';

// Solo dadores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'dador') {
    die("❌ Debes iniciar sesión como Dador para ver tus posts.");
}

$dador_id = $_SESSION['usuario_id'];

// Traer mascotas del dador
$sql = "SELECT * FROM mascotas WHERE usuario_id = ? ORDER BY fecha_alta DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_usuario']); 
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Publicaciones</title>
    <link rel="stylesheet" href="css/adopcion.css">
</head>
<body>
<h1>🐾 Mis Publicaciones</h1>

<div class="grid">
<?php while ($m = $result->fetch_assoc()): ?>
    <div class="card">
        <?php if ($m['foto'] && file_exists(__DIR__ . "/uploads/mascotas/" . $m['foto'])): ?>
            <img src="uploads/mascotas/<?= htmlspecialchars($m['foto']) ?>" alt="<?= htmlspecialchars($m['nombre']) ?>">
        <?php else: ?>
            <img src="css/no-image.png" alt="Sin imagen">
        <?php endif; ?>

        <h3><?= htmlspecialchars($m['nombre']) ?></h3>
        <p><strong>Especie:</strong> <?= htmlspecialchars($m['especie']) ?></p>
        <p><strong>Raza:</strong> <?= htmlspecialchars($m['raza']) ?></p>
        <p><strong>Edad:</strong> <?= htmlspecialchars($m['edad_categoria']) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($m['estado']) ?></p>

        <!-- Form para actualizar estado -->
        <form action="editar_post.php" method="post">
            <input type="hidden" name="mascota_id" value="<?= $m['id'] ?>">
            <label>Estado:
                <select name="estado">
                    <option value="en_adopcion" <?= $m['estado']=='en_adopcion'?'selected':'' ?>>En adopción</option>
                    <option value="reservado" <?= $m['estado']=='reservado'?'selected':'' ?>>Reservado</option>
                    <option value="adoptado" <?= $m['estado']=='adoptado'?'selected':'' ?>>Adoptado</option>
                </select>
            </label>
            <button type="submit">Actualizar</button>
        </form>

        <!-- Botón para editar datos -->
        <form action="editar_post.php" method="get" style="margin-top:10px;">
            <input type="hidden" name="mascota_id" value="<?= $m['id'] ?>">
            <button type="submit">Editar Información</button>
        </form>
    </div>
<?php endwhile; ?>
</div>

</body>
</html>
