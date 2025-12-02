<?php
session_start();
require_once 'conexion.php';

// Solo usuarios adoptantes pueden acceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'adoptante') {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// --- FILTROS opcionales ---
$filtro_estado = $_GET['estado'] ?? '';
$buscar_nombre = $_GET['nombre'] ?? '';
$orden = $_GET['orden'] ?? 'fecha_desc';

// Construcción de la consulta
$sql = "SELECT a.id AS adopcion_id, a.fecha_solicitud, a.estado, 
               m.nombre AS nombre_mascota, m.especie, m.raza, m.edad_categoria, m.foto
        FROM adopciones a
        JOIN mascotas m ON a.mascota_id = m.id
        WHERE a.usuario_id = $usuario_id";

if ($filtro_estado) {
    $sql .= " AND a.estado = '" . $conn->real_escape_string($filtro_estado) . "'";
}

if ($buscar_nombre) {
    $sql .= " AND m.nombre LIKE '%" . $conn->real_escape_string($buscar_nombre) . "%'";
}

// Ordenamiento
if ($orden === 'edad_asc') {
    $sql .= " ORDER BY m.edad_categoria ASC";
} elseif ($orden === 'edad_desc') {
    $sql .= " ORDER BY m.edad_categoria DESC";
} else {
    $sql .= " ORDER BY a.fecha_solicitud DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis solicitudes de adopción - MHAC</title>
    <link rel="stylesheet" href="css/mis_adopciones.css">
    <link rel="stylesheet" href="css/base.css">

<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>
<body>

<a href="adopcion.php" class="">
    <span>←</span>
    Volver
</a>

<header>
    <h1>Mis solicitudes de adopción</h1>
</header>

<!-- Filtros rápidos -->
<div style="margin-bottom:20px;">
    <form method="GET">
        <select name="estado">
            <option value="">Todos los estados</option>
            <option value="pendiente" <?= $filtro_estado=='pendiente'?'selected':'' ?>>Pendiente</option>
            <option value="aprobada" <?= $filtro_estado=='aprobada'?'selected':'' ?>>Aprobada</option>
            <option value="rechazada" <?= $filtro_estado=='rechazada'?'selected':'' ?>>Rechazada</option>
        </select>
        <input type="text" name="nombre" placeholder="Buscar mascota..." value="<?= htmlspecialchars($buscar_nombre) ?>">
        <select name="orden">
            <option value="fecha_desc" <?= $orden=='fecha_desc'?'selected':'' ?>>Más recientes</option>
            <option value="edad_asc" <?= $orden=='edad_asc'?'selected':'' ?>>Edad ascendente</option>
            <option value="edad_desc" <?= $orden=='edad_desc'?'selected':'' ?>>Edad descendente</option>
        </select>
        <button type="submit">Filtrar</button>
    </form>
</div>

<main class="contenido-principal">
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <?php if ($row['foto']): ?>
                        <img src="../assets/uploads/mascotas/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nombre_mascota']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h3><?= htmlspecialchars($row['nombre_mascota']) ?></h3>
                        <p><strong>Especie:</strong> <?= htmlspecialchars($row['especie']) ?></p>
                        <p><strong>Raza:</strong> <?= htmlspecialchars($row['raza']) ?></p>
                        <p><strong>Edad:</strong> <?= htmlspecialchars($row['edad_categoria']) ?></p>
                        <p><strong>Fecha solicitud:</strong> <?= htmlspecialchars($row['fecha_solicitud']) ?></p>
                        <p><strong>Estado:</strong> 
                            <span class="badge <?= htmlspecialchars($row['estado']) ?>">
                                <?= ucfirst($row['estado']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No tienes solicitudes de adopción registradas.</p>
    <?php endif; ?>
</main>
<script src="translate.js"></script>

</body>
</html>
