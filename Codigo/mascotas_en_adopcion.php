<?php
session_start();
require_once 'conexion.php';

// Filtros
$filtro_especie = $_GET['especie'] ?? '';
$buscar_nombre  = $_GET['nombre'] ?? '';
$orden          = $_GET['orden'] ?? 'recientes';
$filtro_dias    = $_GET['dias'] ?? '';

// Construcción de la consulta
$sql = "SELECT m.id, m.nombre, m.especie, m.raza, m.sexo, 
               m.edad_categoria, m.tamano, m.descripcion, 
               m.foto, m.fecha_alta, 
               DATEDIFF(NOW(), m.fecha_alta) AS dias_en_mhac, 
               r.nombre_refugio
        FROM mascotas m
        LEFT JOIN refugios r ON m.refugio_id = r.id
        WHERE m.estado = 'en_adopcion'";

// Filtro especie
if ($filtro_especie) {
    $sql .= " AND m.especie = '" . $conn->real_escape_string($filtro_especie) . "'";
}

// Filtro nombre
if ($buscar_nombre) {
    $sql .= " AND m.nombre LIKE '%" . $conn->real_escape_string($buscar_nombre) . "%'";
}

// Filtro días en MHAC
if ($filtro_dias == '7') {
    $sql .= " AND DATEDIFF(NOW(), m.fecha_alta) <= 7";
} elseif ($filtro_dias == '21') {
    $sql .= " AND DATEDIFF(NOW(), m.fecha_alta) <= 21";
} elseif ($filtro_dias == '60+') {
    $sql .= " AND DATEDIFF(NOW(), m.fecha_alta) > 60";
}

// Orden
if ($orden === 'edad_asc') {
    $sql .= " ORDER BY m.edad ASC";
} elseif ($orden === 'edad_desc') {
    $sql .= " ORDER BY m.edad DESC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mascotas en Adopción - MHAC</title>
    <link rel="stylesheet" href="css/adopcion.css">
    <a href="adopcion.php" class="">
            <span class="">←</span>
            Volver
    </a>
</head>
<body>
<header>
    <h1>🐾 Mascotas en Adopción</h1>
</header>

<main>
    <!-- Botón o mensaje de adopción -->
    <div style="margin-bottom:20px;">
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'adoptante'): ?>
            <a href="solicitar_adopcion.php" class="btn-adoptar">🐾 Solicitar adopción</a>
        <?php else: ?>
            <em>Iniciá sesión como adoptante para solicitar la adopción</em>
        <?php endif; ?>
    </div>

    <!-- Formulario de filtros -->
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
            Días en MHAC
            <select name="dias">
                <option value="">Todos</option>
                <option value="7" <?= $filtro_dias=='7'?'selected':'' ?>>Últimos 7 días</option>
                <option value="21" <?= $filtro_dias=='21'?'selected':'' ?>>Últimos 21 días</option>
                <option value="60+" <?= $filtro_dias=='60+'?'selected':'' ?>>Más de 60 días</option>
            </select>
        </label>

        <label>
            Ordenar por
            <select name="orden">
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
                    <p><strong>Especie:</strong> <?= ucfirst(htmlspecialchars($m['especie'])) ?></p>
                    <p><strong>Raza:</strong> <?= ucfirst(htmlspecialchars($m['raza'])) ?></p>
                    <p><strong>Sexo:</strong> <?= ucfirst(htmlspecialchars($m['sexo'])) ?></p>
                    <p><strong>Edad:</strong> <?= ucfirst(htmlspecialchars($m['edad_categoria'])) ?></p>
                    <p><strong>Tamaño:</strong> <?= ucfirst(htmlspecialchars($m['tamano'])) ?></p>
                    <p><strong>Refugio:</strong> <?= $m['nombre_refugio'] ? ucfirst(htmlspecialchars($m['nombre_refugio'])) : "Publicado por usuario" ?></p>
                    <p><?= nl2br(htmlspecialchars($m['descripcion'])) ?></p>

                    <?php if ($m['foto']): ?>
                        <img src="<?= 'uploads/mascotas/' . htmlspecialchars($m['foto']) ?>" 
                             alt="Foto de <?= htmlspecialchars($m['nombre']) ?>" width="200">
                    <?php endif; ?>
                    <p><strong>Publicado:</strong> <?= date("d/m/Y", strtotime($m['fecha_alta'])) ?></p>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay mascotas disponibles en adopción en este momento.</p>
    <?php endif; ?>
</main>
</body>
</html>
