<?php
session_start();
require_once 'conexion.php';

// --- FILTROS ---
$filtro_especie = $_GET['especie'] ?? '';
$buscar_nombre = $_GET['nombre'] ?? '';
$orden = $_GET['orden'] ?? 'fecha_desc';

// Construcción de la consulta
$sql = "SELECT a.id AS adopcion_id, a.fecha_adopcion, a.estado, 
               m.nombre AS nombre_mascota, m.especie, m.raza, m.edad, m.foto
        FROM adopciones a
        JOIN mascotas m ON a.mascota_id = m.id
        WHERE a.estado = 'aprobada'";

if ($filtro_especie) {
    $sql .= " AND m.especie = '" . $conn->real_escape_string($filtro_especie) . "'";
}

if ($buscar_nombre) {
    $sql .= " AND m.nombre LIKE '%" . $conn->real_escape_string($buscar_nombre) . "%'";
}

// Ordenamiento
if ($orden === 'edad_asc') {
    $sql .= " ORDER BY m.edad ASC";
} elseif ($orden === 'edad_desc') {
    $sql .= " ORDER BY m.edad DESC";
} else {
    $sql .= " ORDER BY a.fecha_adopcion DESC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Adopciones - MHAC</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/adopcion.css">
</head>
<body>
<header>
    <h1>Adopciones aprobadas</h1>
</header>

<main>
    <!-- FORMULARIO DE FILTROS -->
    <form method="GET" style="margin-bottom: 20px; background:#f9f9f9; padding:10px; border-radius:8px;">
        <label>Buscar por nombre:
            <input type="text" name="nombre" value="<?= htmlspecialchars($buscar_nombre) ?>">
        </label>
        <label>Filtrar por especie:
            <select name="especie">
                <option value="">Todas</option>
                <option value="perro" <?= $filtro_especie=='perro'?'selected':'' ?>>Perro</option>
                <option value="gato" <?= $filtro_especie=='gato'?'selected':'' ?>>Gato</option>
                <option value="otro" <?= $filtro_especie=='otro'?'selected':'' ?>>Otro</option>
            </select>
        </label>
        <label>Ordenar por:
            <select name="orden">
                <option value="fecha_desc" <?= $orden=='fecha_desc'?'selected':'' ?>>Fecha (recientes primero)</option>
                <option value="edad_asc" <?= $orden=='edad_asc'?'selected':'' ?>>Edad (menor a mayor)</option>
                <option value="edad_desc" <?= $orden=='edad_desc'?'selected':'' ?>>Edad (mayor a menor)</option>
            </select>
        </label>
        <button type="submit">Aplicar</button>
        <a href="adopciones.php" style="margin-left:10px;">Limpiar filtros</a>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <ul>
            <?php while ($adopcion = $result->fetch_assoc()): ?>
                <li>
                    <h2><?= htmlspecialchars($adopcion['nombre_mascota']) ?></h2>
                    <p><strong>Especie:</strong> <?= htmlspecialchars($adopcion['especie']) ?></p>
                    <p><strong>Raza:</strong> <?= htmlspecialchars($adopcion['raza']) ?></p>
                    <p><strong>Edad:</strong> <?= intval($adopcion['edad']) ?> meses</p>
                    <?php if ($adopcion['foto']): ?>
                        <img src="<?= 'uploads/mascotas/' . htmlspecialchars($adopcion['foto']) ?>" 
                             alt="<?= htmlspecialchars($adopcion['nombre_mascota']) ?>" style="max-width:200px;">
                    <?php endif; ?>
                    <p><strong>Fecha de adopción:</strong> <?= htmlspecialchars($adopcion['fecha_adopcion']) ?></p>
                    <p><strong>Estado:</strong> <?= htmlspecialchars($adopcion['estado']) ?></p>

                    <!-- Botón para ver más -->
                    <a href="detalle_adopcion.php?id=<?= $adopcion['adopcion_id'] ?>" 
                       style="display:inline-block; padding:6px 12px; background:#c07436; color:white; border-radius:6px; text-decoration:none;">
                       Ver detalles
                    </a>
                </li>
                <hr>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay adopciones aprobadas registradas por ahora.</p>
    <?php endif; ?>
</main>
</body>
</html>
