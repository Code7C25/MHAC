<?php
session_start();
require_once 'conexion.php';

// Filtros
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

$sql = "SELECT * FROM mascotas WHERE estado = 'en_adopcion'";

// Aplicar filtros
if ($filtro_especie)        $sql .= " AND especie = '" . $conn->real_escape_string($filtro_especie) . "'";
if ($buscar_nombre)         $sql .= " AND nombre LIKE '%" . $conn->real_escape_string($buscar_nombre) . "%'";
if ($filtro_raza)           $sql .= " AND raza LIKE '%" . $conn->real_escape_string($filtro_raza) . "%'";
if ($filtro_sexo)           $sql .= " AND sexo = '" . $conn->real_escape_string($filtro_sexo) . "'";
if ($filtro_edad_categoria) $sql .= " AND edad_categoria = '" . $conn->real_escape_string($filtro_edad_categoria) . "'";
if ($filtro_tamano)         $sql .= " AND tamano = '" . $conn->real_escape_string($filtro_tamano) . "'";
if ($filtro_pelaje)         $sql .= " AND pelaje = '" . $conn->real_escape_string($filtro_pelaje) . "'";
if ($filtro_color)          $sql .= " AND color = '" . $conn->real_escape_string($filtro_color) . "'";
if ($filtro_comportamiento) $sql .= " AND comportamiento = '" . $conn->real_escape_string($filtro_comportamiento) . "'";

// Filtro días en MHAC
if ($filtro_dias == '7') {
    $sql .= " AND dias_mhac <= 7";
} elseif ($filtro_dias == '21') {
    $sql .= " AND dias_mhac <= 21";
} elseif ($filtro_dias == '60+') {
    $sql .= " AND dias_mhac > 60";
}

// Orden
if ($orden === 'edad_asc') {
    $sql .= " ORDER BY edad_categoria ASC";
} elseif ($orden === 'edad_desc') {
    $sql .= " ORDER BY edad_categoria DESC";
} else {
    $sql .= " ORDER BY fecha_alta DESC";
}

// Construcción de la consulta
$sql = "SELECT m.id, m.nombre, m.especie, m.raza, m.sexo, 
               m.edad_categoria, m.tamano, m.descripcion, 
               m.foto, m.fecha_alta, 
               DATEDIFF(NOW(), m.fecha_alta) AS dias_en_mhac, 
               u.nombre AS user_nombre,
               u.apellido AS user_apellido,
               u.telefono AS user_telefono,
               u.email AS user_email
        FROM mascotas m
        LEFT JOIN usuarios u ON m.usuario_id = u.id
        WHERE m.estado = 'en_adopcion'";

// Ejecutar consulta
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
                        Filtrar por raza
                        <input type="text" name="raza" value="<?= htmlspecialchars($filtro_raza) ?>" placeholder="Ej: Labrador">
                    </label>
                </div>

                <div class="filtro-grupo">
                    <label>
                        Filtrar por sexo
                        <select name="sexo">
                            <option value="">Todos</option>
                            <option value="macho" <?= $filtro_sexo=='macho'?'selected':'' ?>>Macho</option>
                            <option value="hembra" <?= $filtro_sexo=='hembra'?'selected':'' ?>>Hembra</option>
                            <option value="desconocido" <?= $filtro_sexo=='desconocido'?'selected':'' ?>>Desconocido</option>
                        </select>
                    </label>
                </div>

                <div class="filtro-grupo">
                    <label>
                        Filtrar por edad
                        <select name="edad_categoria">
                            <option value="">Todas</option>
                            <option value="cachorro" <?= $filtro_edad_categoria=='cachorro'?'selected':'' ?>>Cachorro</option>
                            <option value="joven" <?= $filtro_edad_categoria=='joven'?'selected':'' ?>>Joven</option>
                            <option value="adulto" <?= $filtro_edad_categoria=='adulto'?'selected':'' ?>>Adulto</option>
                            <option value="mayor" <?= $filtro_edad_categoria=='mayor'?'selected':'' ?>>Adulto mayor</option>
                        </select>
                    </label>
                </div>

                <div class="filtro-grupo">
                    <label>
                        Filtrar por tamaño
                        <select name="tamano">
                            <option value="">Todos</option>
                            <option value="pequeño" <?= $filtro_tamano=='pequeño'?'selected':'' ?>>Pequeño</option>
                            <option value="mediano" <?= $filtro_tamano=='mediano'?'selected':'' ?>>Mediano</option>
                            <option value="grande" <?= $filtro_tamano=='grande'?'selected':'' ?>>Grande</option>
                            <option value="extra_grande" <?= $filtro_tamano=='extra_grande'?'selected':'' ?>>Extra Grande</option>
                        </select>
                    </label>
                </div>

                <div class="filtro-grupo">
                    <label>
                        Filtrar por pelaje
                        <select name="pelaje">
                            <option value="">Todos</option>
                            <option value="sin_pelo" <?= $filtro_pelaje=='sin_pelo'?'selected':'' ?>>Sin pelo</option>
                            <option value="corto" <?= $filtro_pelaje=='corto'?'selected':'' ?>>Corto</option>
                            <option value="medio" <?= $filtro_pelaje=='medio'?'selected':'' ?>>Medio</option>
                            <option value="largo" <?= $filtro_pelaje=='largo'?'selected':'' ?>>Largo</option>
                        </select>
                    </label>
                </div>

                <div class="filtro-grupo">
                    <label>
                        Filtrar por color
                        <select name="color">
                            <option value="">Todos</option>
                            <option value="apricot" <?= $filtro_color=='apricot'?'selected':'' ?>>Apricot / Beige</option>
                            <option value="bicolor" <?= $filtro_color=='bicolor'?'selected':'' ?>>Bicolor</option>
                            <option value="negro" <?= $filtro_color=='negro'?'selected':'' ?>>Negro</option>
                            <option value="atigrado" <?= $filtro_color=='atigrado'?'selected':'' ?>>Atigrado</option>
                            <option value="marron" <?= $filtro_color=='marron'?'selected':'' ?>>Marrón / Chocolate</option>
                            <option value="dorado" <?= $filtro_color=='dorado'?'selected':'' ?>>Dorado</option>
                            <option value="gris" <?= $filtro_color=='gris'?'selected':'' ?>>Gris / Azul / Plateado</option>
                            <option value="arlequin" <?= $filtro_color=='arlequin'?'selected':'' ?>>Arlequín</option>
                            <option value="merle_azul" <?= $filtro_color=='merle_azul'?'selected':'' ?>>Merle Azul</option>
                            <option value="merle_rojo" <?= $filtro_color=='merle_rojo'?'selected':'' ?>>Merle Rojo</option>
                            <option value="rojo" <?= $filtro_color=='rojo'?'selected':'' ?>>Rojo / Castaño / Naranja</option>
                            <option value="sable" <?= $filtro_color=='sable'?'selected':'' ?>>Sable</option>
                            <option value="tricolor" <?= $filtro_color=='tricolor'?'selected':'' ?>>Tricolor</option>
                            <option value="blanco" <?= $filtro_color=='blanco'?'selected':'' ?>>Blanco / Crema</option>
                            <option value="amarillo" <?= $filtro_color=='amarillo'?'selected':'' ?>>Amarillo / Canela / Fawn</option>
                        </select>
                    </label>
                </div>

                <div class="filtro-grupo">
                    <label>
                        Filtrar por comportamiento
                        <select name="comportamiento">
                            <option value="">Todos</option>
                            <option value="entrenado" <?= $filtro_comportamiento=='entrenado'?'selected':'' ?>>Entrenado</option>
                            <option value="cuidados_especiales" <?= $filtro_comportamiento=='cuidados_especiales'?'selected':'' ?>>Cuidados especiales</option>
                            <option value="ninguno" <?= $filtro_comportamiento=='ninguno'?'selected':'' ?>>Ninguno</option>
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
                                        <?= htmlspecialchars($m['user_nombre']) ?>
                                        <?php if (!empty($m['user_apellido'])): ?>
                                            <?= ' ' . htmlspecialchars($m['user_apellido']) ?>
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