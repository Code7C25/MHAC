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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopciones - MHAC</title>
    <link rel="stylesheet" href="css/adopcion.css">
    <link rel="stylesheet" href="css/base.css">
    <a href="index.php" class="">
            <span class="">←</span>
            Volver al inicio
    </a>
</head>
<body>
<header>
    <h1>¿Buscando un nuevo amigo?</h1>
</header>

<!-- Tarjetas de navegación -->
<div style="display: flex; gap: 20px; justify-content: center; margin-bottom: 30px;">
  <a href="mis_adopciones.php" class="servicio-card">
    <h3>Solicitud de adopciones</h3>
  </a>
  <a href="adopcion.php" class="servicio-card">
    <h3>Adopciones aprobadas</h3>
  </a>
  <a href="mascotas_en_adopcion.php" class="servicio-card">
    <h3>Mascotas en adopción</h3>
  </a>
</div>

</html>
