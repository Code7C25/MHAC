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

        <form method="GET" class="form-filtros">

            <!-- Buscar por nombre -->
            <div class="filtro-grupo">
                <label>
                    Buscar por nombre
                    <input type="text" name="nombre" 
                           value="<?= htmlspecialchars($buscar_nombre) ?>" 
                           placeholder="Nombre de la mascota...">
                </label>
            </div>

            <!-- Especie -->
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

            <!-- Raza -->
            <div class="filtro-grupo">
                <label>
                    Filtrar por raza
                    <input type="text" name="raza" 
                           value="<?= htmlspecialchars($filtro_raza) ?>"
                           placeholder="Ej: Labrador">
                </label>
            </div>

            <!-- Sexo -->
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

            <!-- Edad -->
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

            <!-- Tamaño -->
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

            <!-- Pelaje -->
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

            <!-- Color -->
            <div class="filtro-grupo">
                <label>
                    Filtrar por color
                    <select name="color">
                        <option value="">Todos</option>

                        <?php 
                        $colores = [
                            "apricot" => "Apricot / Beige",
                            "bicolor" => "Bicolor",
                            "negro" => "Negro",
                            "atigrado" => "Atigrado",
                            "marron" => "Marrón / Chocolate",
                            "dorado" => "Dorado",
                            "gris" => "Gris / Azul / Plateado",
                            "arlequin" => "Arlequín",
                            "merle_azul" => "Merle Azul",
                            "merle_rojo" => "Merle Rojo",
                            "rojo" => "Rojo / Castaño",
                            "sable" => "Sable",
                            "tricolor" => "Tricolor",
                            "blanco" => "Blanco / Crema",
                            "amarillo" => "Amarillo / Fawn",
                        ];
                        foreach ($colores as $v => $t): ?>
                            <option value="<?= $v ?>" <?= $filtro_color==$v?'selected':'' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <!-- Comportamiento -->
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

            <!-- Días en MHAC -->
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

            <!-- Orden -->
            <div class="filtro-grupo">
                <label>
                    Ordenar por
                    <select name="orden">
                        <option value="edad_asc" <?= $orden=='edad_asc'?'selected':'' ?>>Edad (menor a mayor)</option>
                        <option value="edad_desc" <?= $orden=='edad_desc'?'selected':'' ?>>Edad (mayor a menor)</option>
                    </select>
                </label>
            </div>

            <!-- Botones -->
            <div class="filtro-acciones">
                <button type="submit" class="btn-filtrar">Aplicar filtros</button>
                <a href="mascotas_en_adopcion.php" class="btn-limpiar">Limpiar filtros</a>
            </div>

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
