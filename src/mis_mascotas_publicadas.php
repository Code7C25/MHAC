<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['dador', 'refugio'])) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol        = $_SESSION['rol'];

$sql = "SELECT m.id, m.nombre, m.especie, m.raza, m.sexo, m.edad_categoria, m.tamano, 
               m.descripcion, m.foto, m.estado, m.fecha_alta
        FROM mascotas m
        WHERE m.usuario_id = ?
        ORDER BY m.fecha_alta DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis publicaciones - MHAC</title>
    <link rel="stylesheet" href="css/mis_mascotas_publicadas.css">
    <link rel="stylesheet" href="css/base.css">

<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>
<body>
    <a href="adopcion.php" class="btn-volver">Volver</a>

    <header>
        <h1>Mis mascotas publicadas</h1>
        <p>Estas son todas las mascotas que has publicado en MHAC.</p>
        <a href="publicar_mascota.php" class="btn-publicar">Publicar nueva mascota</a>
    </header>

    <main>
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid">
                <?php while ($m = $result->fetch_assoc()): ?>
                    <div class="card">
                        <?php if ($m['foto']): ?>
                            <img src="uploads/mascotas/<?= htmlspecialchars($m['foto']) ?>" alt="<?= htmlspecialchars($m['nombre']) ?>">
                        <?php else: ?>
                            <img src="../assets/imagenes/default_pet.png" alt="Mascota sin foto">
                        <?php endif; ?>

                        <h3><?= htmlspecialchars($m['nombre']) ?></h3>
                        <p><strong>Especie:</strong> <?= htmlspecialchars($m['especie']) ?></p>
                        <p><strong>Raza:</strong> <?= htmlspecialchars($m['raza']) ?></p>
                        <p><strong>Sexo:</strong> <?= htmlspecialchars($m['sexo']) ?></p>
                        <p><strong>Edad:</strong> <?= htmlspecialchars($m['edad_categoria']) ?></p>
                        <p><strong>Tamaño:</strong> <?= htmlspecialchars($m['tamano']) ?></p>
                        <p><strong>Estado:</strong> <?= htmlspecialchars($m['estado']) ?></p>
                        <p><strong>Fecha de publicación:</strong> <?= date("d/m/Y", strtotime($m['fecha_alta'])) ?></p>
                        <p class="descripcion"><?= nl2br(htmlspecialchars($m['descripcion'])) ?></p>

                        <div class="acciones-card">
                            <a href="editar_mascota.php?id=<?= $m['id'] ?>" class="btn-editar">Editar</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No tienes mascotas publicadas todavía.</p>
        <?php endif; ?>
    </main>
</body>
</html>
