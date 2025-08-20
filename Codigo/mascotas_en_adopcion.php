<?php
session_start();
require_once 'conexion.php';

// Filtros
$filtro_especie = $_GET['especie'] ?? '';
$buscar_nombre = $_GET['nombre'] ?? '';
$orden = $_GET['orden'] ?? 'recientes';

// Construcción de la consulta
$sql = "SELECT m.id, m.nombre, m.especie, m.raza, m.sexo, m.edad, m.tamano, m.descripcion, m.foto, r.nombre_refugio
        FROM mascotas m
        JOIN refugios r ON m.refugio_id = r.id
        WHERE m.estado = 'en_adopcion'";

if ($filtro_especie) {
    $sql .= " AND m.especie = '" . $conn->real_escape_string($filtro_especie) . "'";
}

if ($buscar_nombre) {
    $sql .= " AND m.nombre LIKE '%" . $conn->real_escape_string($buscar_nombre) . "%'";
}

if ($orden === 'edad_asc') {
    $sql .= " ORDER BY m.edad ASC";
} elseif ($orden === 'edad_desc') {
    $sql .= " ORDER BY m.edad DESC";
} else {
    $sql .= " ORDER BY m.id DESC"; // recientes primero
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mascotas en Adopción - MHAC</title>
    <link rel="stylesheet" href="css/adopcion.css">
</head>
<body>
<header>
    <h1>🐾 Mascotas en Adopción</h1>
</header>

<main>
    <!-- Formulario de búsqueda -->
    <form method="GET">
        <label>
            Buscar por nombre
            <input type="text" name="nombre" value="<?= htmlspecialchars($buscar_nombre) ?>" placeholder="Nombre de la mascota...">
        </label>
        
        <label>
            Filtrar por especie
            <select name="especie">
                <option value="">Todas</option>
                <option value="perro" <?= $filtro_especie=='perro'?'selected':'' ?>>Perro</option>
                <option value="gato" <?= $filtro_especie=='gato'?'selected':'' ?>>Gato</option>
                <option value="otro" <?= $filtro_especie=='otro'?'selected':'' ?>>Otro</option>
            </select>
        </label>

        <label>
            Ordenar por
            <select name="orden">
                <option value="recientes" <?= $orden=='recientes'?'selected':'' ?>>Recientes primero</option>
                <option value="edad_asc" <?= $orden=='edad_asc'?'selected':'' ?>>Edad (menor a mayor)</option>
                <option value="edad_desc" <?= $orden=='edad_desc'?'selected':'' ?>>Edad (mayor a menor)</option>
            </select>
        </label>
        
        <button type="submit">Aplicar filtros</button>
        <a href="mascotas_en_adopcion.php">Limpiar filtros</a>
    </form>

    <!-- Listado -->
    <?php if ($result && $result->num_rows > 0): ?>
        <ul>
            <?php while ($m = $result->fetch_assoc()): ?>
                <li>
                    <h2><?= htmlspecialchars($m['nombre']) ?></h2>
                    <p><strong>Especie:</strong> <?= htmlspecialchars($m['especie']) ?></p>
                    <p><strong>Raza:</strong> <?= htmlspecialchars($m['raza']) ?></p>
                    <p><strong>Sexo:</strong> <?= htmlspecialchars($m['sexo']) ?></p>
                    <p><strong>Edad:</strong> <?= intval($m['edad']) ?> meses</p>
                    <p><strong>Tamaño:</strong> <?= htmlspecialchars($m['tamano']) ?></p>
                    <p><strong>Refugio:</strong> <?= htmlspecialchars($m['nombre_refugio']) ?></p>
                    <p><?= nl2br(htmlspecialchars($m['descripcion'])) ?></p>

                    <?php if ($m['foto']): ?>
                        <img src="<?= 'uploads/mascotas/' . htmlspecialchars($m['foto']) ?>" 
                             alt="Foto de <?= htmlspecialchars($m['nombre']) ?>" width="200">
                    <?php endif; ?>

                    <br>
                    <a href="solicitar_adopcion.php?id=<?= $m['id'] ?>">Solicitar adopción</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay mascotas disponibles en adopción en este momento.</p>
    <?php endif; ?>
</main>
</body>
</html>
