<?php 
session_start();
include("conexion.php");

// Obtener parámetros de búsqueda
$especie = isset($_GET['tipo']) ? trim($_GET['tipo']) : ''; // usamos 'tipo' del form pero mapeamos a especie
$raza = isset($_GET['ubicacion']) ? trim($_GET['ubicacion']) : ''; // usamos 'ubicacion' del form pero mapeamos a raza

// Preparar consulta
$sql = "SELECT * FROM mascotas WHERE 1=1";
$params = [];
$types = "";

if (!empty($especie)) {
    $sql .= " AND especie LIKE ?";
    $params[] = "%$especie%";
    $types .= "s";
}

if (!empty($raza)) {
    $sql .= " AND raza LIKE ?";
    $params[] = "%$raza%";
    $types .= "s";
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda - MHAC</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
<main class="contenido-principal">
    <div class="resultados-busqueda">
        <h1>Resultados de búsqueda</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="grid">
                <?php while($m = $result->fetch_assoc()): ?>
                    <div class="card">
                        <img src="imagenes/<?= htmlspecialchars($m['foto']) ?>" alt="<?= htmlspecialchars($m['nombre']) ?>">
                        <h3><?= htmlspecialchars($m['nombre']) ?></h3>
                        <p><strong>Especie:</strong> <?= htmlspecialchars($m['especie']) ?></p>
                        <p><strong>Raza:</strong> <?= htmlspecialchars($m['raza']) ?></p>
                        <p><strong>Edad:</strong> <?= htmlspecialchars($m['edad']) ?> meses</p>
                        <p><?= htmlspecialchars($m['descripcion']) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No se encontraron mascotas con esos criterios.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
