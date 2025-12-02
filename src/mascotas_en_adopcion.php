<?php
session_start();
require_once 'conexion.php';
include 'consejo_tip_module.php'; 

// ---------------------------
// FILTROS
// ---------------------------
$filtro_especie        = $_GET['especie'] ?? '';
$buscar_nombre         = $_GET['nombre'] ?? '';
$filtro_raza           = $_GET['raza'] ?? '';
$filtro_sexo           = $_GET['sexo'] ?? '';
$filtro_edad_categoria = $_GET['edad_categoria'] ?? '';
$filtro_tamano         = $_GET['tamano'] ?? '';
$filtro_pelaje         = $_GET['pelaje'] ?? '';
$filtro_color          = $_GET['color'] ?? '';
$filtro_comportamiento = $_GET['comportamiento'] ?? '';
$orden                 = $_GET['orden'] ?? '';
$filtro_dias           = $_GET['dias'] ?? '';

// ---------------------------
// CONSULTA BASE (con JOIN)
// ---------------------------
$sql = "SELECT 
            m.id, m.nombre, m.especie, m.raza, m.sexo,
            m.edad_categoria, m.tamano, m.descripcion, 
            m.foto, m.fecha_alta,
            DATEDIFF(NOW(), m.fecha_alta) AS dias_en_mhac,
            m.usuario_id,
            u.nombre AS user_nombre,
            u.apellido AS user_apellido,
            u.telefono AS user_telefono,
            u.email AS user_email
        FROM mascotas m
        LEFT JOIN usuarios u ON m.usuario_id = u.id
        WHERE m.estado = 'en_adopcion'";

// ---------------------------
// APLICAR FILTROS
// ---------------------------
if ($filtro_especie)        $sql .= " AND m.especie = '" . $conn->real_escape_string($filtro_especie) . "'";
if ($buscar_nombre)         $sql .= " AND m.nombre LIKE '%" . $conn->real_escape_string($buscar_nombre) . "%'";
if ($filtro_raza)           $sql .= " AND m.raza LIKE '%" . $conn->real_escape_string($filtro_raza) . "%'";
if ($filtro_sexo)           $sql .= " AND m.sexo = '" . $conn->real_escape_string($filtro_sexo) . "'";
if ($filtro_edad_categoria) $sql .= " AND m.edad_categoria = '" . $conn->real_escape_string($filtro_edad_categoria) . "'";
if ($filtro_tamano)         $sql .= " AND m.tamano = '" . $conn->real_escape_string($filtro_tamano) . "'";
if ($filtro_pelaje)         $sql .= " AND m.pelaje = '" . $conn->real_escape_string($filtro_pelaje) . "'";
if ($filtro_color)          $sql .= " AND m.color = '" . $conn->real_escape_string($filtro_color) . "'";
if ($filtro_comportamiento) $sql .= " AND m.comportamiento = '" . $conn->real_escape_string($filtro_comportamiento) . "'";

// Filtro días en MHAC
if ($filtro_dias == '7') {
    $sql .= " AND m.dias_mhac <= 7";
} elseif ($filtro_dias == '21') {
    $sql .= " AND m.dias_mhac <= 21";
} elseif ($filtro_dias == '60+') {
    $sql .= " AND m.dias_mhac > 60";
}

// ---------------------------
// ORDEN
// ---------------------------
if ($orden === 'edad_asc') {
    $sql .= " ORDER BY FIELD(m.edad_categoria, 'cachorro', 'joven', 'adulto', 'mayor')";
} elseif ($orden === 'edad_desc') {
    $sql .= " ORDER BY FIELD(m.edad_categoria, 'mayor', 'adulto', 'joven', 'cachorro')";
} else {
    $sql .= " ORDER BY m.fecha_alta DESC";
}

// ---------------------------
// EJECUTAR CONSULTA
// ---------------------------
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mascotas en Adopción - MHAC</title>
    <link rel="stylesheet" href="css/mascotas_en_adopcion.css">
    <link rel="stylesheet" href="css/consejo_module.css">

<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>
<script src="translate.js"></script>
<body>
<header>
    <h1>Mascotas en Adopción</h1>
</header>

<main>

    <!-------------------------------
        BOTÓN DE ADOPCIÓN
    -------------------------------->
    <div class="adopcion-cta">
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'adoptante'): ?>
            <a href="solicitar_adopcion.php" class="btn-adoptar">Solicitar adopción</a>
        <?php else: ?>
            <div class="mensaje-login">
                <p>Iniciá sesión como adoptante para solicitar la adopción</p>
            </div>
        <?php endif; ?>
    </div>

    <!-------------------------------
        FORMULARIO DE FILTROS
    -------------------------------->
    <section class="filtros">
        <h2>Filtros de búsqueda</h2>

        <!-- (TODOS TUS FILTROS QUEDAN IGUAL, NO LOS REPITO PORQUE SON MUCHOS) -->

        <form method="GET" class="form-filtros">

            <!-- ... TODOS TUS FILTROS ... -->

        </form>
    </section>

    <!-------------------------------
        LISTADO DE MASCOTAS
    -------------------------------->
    <section class="mascotas-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid">

                <?php while ($m = $result->fetch_assoc()): ?>
                    <div class="mascota-card">

                        <?php if ($m['foto']): ?>
                            <div class="mascota-imagen">
                                <img src="../assets/uploads/mascotas/<?= htmlspecialchars($m['foto']) ?>" 
                                     alt="Foto de <?= htmlspecialchars($m['nombre']) ?>">

                                <div class="mascota-badge">
                                    <?php if ($m['dias_en_mhac'] <= 7): ?>
                                        <span class="badge nuevo">NUEVO</span>
                                    <?php elseif ($m['dias_en_mhac'] > 60): ?>
                                        <span class="badge urgente">URGENTE</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mascota-info">
                            <h3><?= htmlspecialchars($m['nombre']) ?></h3>

                            <div class="info-basica">
                                <p><strong>Especie:</strong> <?= ucfirst(htmlspecialchars($m['especie'])) ?></p>
                                <p><strong>Raza:</strong> <?= ucfirst(htmlspecialchars($m['raza'])) ?></p>
                                <p><strong>Sexo:</strong> <?= ucfirst(htmlspecialchars($m['sexo'])) ?></p>
                                <p><strong>Edad:</strong> <?= ucfirst(htmlspecialchars($m['edad_categoria'])) ?></p>
                                <p><strong>Tamaño:</strong> <?= ucfirst(htmlspecialchars($m['tamano'])) ?></p>
                            </div>

                            <div class="descripcion">
                                <p><?= nl2br(htmlspecialchars($m['descripcion'])) ?></p>
                            </div>

                            <div class="info-adicional">

                                <p><strong>Publicado por:</strong>
                                    <a href="perfil.php?id=<?= $m['usuario_id'] ?>" class="enlace-perfil">
                                        <?= htmlspecialchars($m['user_nombre']) ?>
                                        <?= $m['user_apellido'] ? " ".htmlspecialchars($m['user_apellido']) : "" ?>
                                    </a>
                                </p>

                                <p><strong>Contacto:</strong>
                                    <?php 
                                        $contactos = [];
                                        if ($m['user_telefono']) $contactos[] = "📞 " . htmlspecialchars($m['user_telefono']);
                                        if ($m['user_email'])    $contactos[] = "✉ " . htmlspecialchars($m['user_email']);
                                        echo $contactos ? implode(" | ", $contactos) : "No disponible";
                                    ?>
                                </p>

                                <p><strong>Publicado:</strong> <?= date("d/m/Y", strtotime($m['fecha_alta'])) ?></p>
                                <p><strong>Días en MHAC:</strong> <?= $m['dias_en_mhac'] ?> días</p>

                            </div>

                            <!-- --------------------------
                                BOTONES NUEVOS
                            --------------------------- -->
                            <div class="acciones-card">
                                <a href="solicitar_adopcion.php?id_mascota=<?= $m['id'] ?>" 
                                   class="btn-adoptar-card">
                                    ¡QUIERO ADOPTAR!
                                </a>

                                <a href="perfil.php?id=<?= $m['usuario_id'] ?>" 
                                   class="btn-perfil-card">
                                    Ver perfil
                                </a>
                            </div>

                        </div>
                    </div>
                <?php endwhile; ?>

            </div>

        <?php else: ?>
            <div class="sin-resultados">
                <p>No hay mascotas disponibles con los filtros seleccionados.</p>
            </div>
        <?php endif; ?>
    </section>

</main>


<!-- POPUP CONSEJO -->
<script>
function cerrarConsejo() {
    const popup = document.getElementById("consejoPopup");
    if (popup) popup.style.display = 'none';
}

window.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("consejoPopup");
    if (popup) {
        setTimeout(() => popup.classList.add('visible'), 100); 
        setTimeout(() => popup.style.display = 'none', 15000);
    }
});
</script>

</body>
</html>
