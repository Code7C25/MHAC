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
               r.nombre_refugio,
               u.nombre AS user_nombre,
               u.apellido AS user_apellido,
               u.telefono AS user_telefono,
               u.email AS user_email
        FROM mascotas m
        LEFT JOIN refugios r ON m.refugio_id = r.id
        LEFT JOIN usuarios u ON m.usuario_id = u.id
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mascotas en Adopción - MHAC</title>
    <link rel="stylesheet" href="css/mascotas_en_adopcion.css">
</head>
<body>
    <a href="adopcion.php" class="btn-volver">
        <span>←</span>
        Volver
    </a>

    <header>
        <h1>Mascotas en Adopción</h1>
    </header>

    <main>
        <!-- Botón de adopción -->
        <div class="adopcion-cta">
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'adoptante'): ?>
                <a href="solicitar_adopcion.php" class="btn-adoptar">Solicitar adopción</a>
            <?php else: ?>
                <div class="mensaje-login">
                    <p>Iniciá sesión como adoptante para solicitar la adopción</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Formulario de filtros -->
        <section class="filtros">
            <h2>Filtros de búsqueda</h2>
            <form method="GET" class="form-filtros">
                <div class="filtro-grupo">
                    <label>
                        Buscar por nombre
                        <input type="text" name="nombre" value="<?= htmlspecialchars($buscar_nombre) ?>" placeholder="Nombre de la mascota...">
                    </label>
                </div>
                
                <div class="filtro-grupo">
                    <label>
                        Filtrar por especie
                        <select name="especie">
                            <option value="">Todas</option>
                            <option value="perro" <?= $filtro_especie=='perro'?'selected':'' ?>>Perro</option>
                            <option value="gato" <?= $filtro_especie=='gato'?'selected':'' ?>>Gato</option>
                            <option value="otro" <?= $filtro_especie=='otro'?'selected':'' ?>>Otro</option>
                        </select>
                    </label>
                </div>

                <div class="filtro-grupo">
                    <label>
                        Días en MHAC
                        <select name="dias">
                            <option value="">Todos</option>
                            <option value="7" <?= $filtro_dias=='7'?'selected':'' ?>>Últimos 7 días</option>
                            <option value="21" <?= $filtro_dias=='21'?'selected':'' ?>>Últimos 21 días</option>
                            <option value="60+" <?= $filtro_dias=='60+'?'selected':'' ?>>Más de 60 días</option>
                        </select>
                    </label>
                </div>

                <div class="filtro-grupo">
                    <label>
                        Ordenar por
                        <select name="orden">
                            <option value="edad_asc" <?= $orden=='edad_asc'?'selected':'' ?>>Edad (menor a mayor)</option>
                            <option value="edad_desc" <?= $orden=='edad_desc'?'selected':'' ?>>Edad (mayor a menor)</option>
                        </select>
                    </label>
                </div>
                
                <div class="filtro-acciones">
                    <button type="submit" class="btn-filtrar">Aplicar filtros</button>
                    <a href="mascotas_en_adopcion.php" class="btn-limpiar">Limpiar filtros</a>
                </div>
            </form>
        </section>

        <!-- Listado de mascotas -->
        <section class="mascotas-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="grid">
                    <?php while ($m = $result->fetch_assoc()): ?>
                        <div class="mascota-card">
                            <?php if ($m['foto']): ?>
                                <div class="mascota-imagen">
                                    <img src="uploads/mascotas/<?= htmlspecialchars($m['foto']) ?>" 
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
                                        <?php if ($m['nombre_refugio']): ?>
                                            <?= htmlspecialchars($m['nombre_refugio']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($m['user_nombre']) ?>
                                            <?php if (!empty($m['user_apellido'])): ?>
                                                <?= ' ' . htmlspecialchars($m['user_apellido']) ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </p>

                                    <p><strong>Contacto:</strong>
                                        <?php 
                                            $contactos = [];
                                            if (!empty($m['user_telefono'])) $contactos[] = "📞 " . htmlspecialchars($m['user_telefono']);
                                            if (!empty($m['user_email'])) $contactos[] = "✉ " . htmlspecialchars($m['user_email']);
                                            echo $contactos ? implode(" | ", $contactos) : "No disponible";
                                        ?>
                                    </p>

                                    <p><strong>Publicado:</strong> <?= date("d/m/Y", strtotime($m['fecha_alta'])) ?></p>
                                    <p><strong>Días en MHAC:</strong> <?= $m['dias_en_mhac'] ?> días</p>
                                </div>

                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="sin-resultados">
                    <p>No hay mascotas disponibles en adopción en este momento.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>