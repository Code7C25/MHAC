<?php
session_start();
require_once 'conexion.php';
require_once 'moderacion.php'; // ← AGREGADO

$usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;

$mensaje = '';
$exito = false;

// Solo DADOR o REFUGIO
if (!$usuario_id || !in_array($rol, ['dador', 'refugio'])) {
    header("Location: login.php");
    exit();
}

/**
 * Devuelve un nombre legible del publicador.
 */
function obtenerPublicadorNombre(mysqli $conn, int $usuario_id, string $rol): string {
    $default = ($rol === 'refugio') ? 'Refugio' : 'Dador';

    $queries = [
        "SELECT COALESCE(
            NULLIF(CONCAT(TRIM(nombre),' ',TRIM(apellido)),''), 
            email
        ) AS display
        FROM usuarios
        WHERE id = ?",

        "SELECT COALESCE(
            NULLIF(CONCAT(TRIM(nombre),' ',TRIM(apellido)),''), 
            email
        ) AS display
        FROM usuarios
        WHERE id_usuario = ?"
    ];

    foreach ($queries as $sql) {
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $usuario_id);
            if ($stmt->execute()) {
                $stmt->bind_result($display);
                if ($stmt->fetch() && $display) {
                    $stmt->close();
                    return $display;
                }
            }
            $stmt->close();
        }
    }

    return $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos del formulario
    $nombre         = trim($_POST['nombre'] ?? '');
    $especie        = $_POST['especie'] ?? '';
    $raza           = trim($_POST['raza'] ?? '');
    $sexo           = $_POST['sexo'] ?? '';
    $edad_categoria = $_POST['edad_categoria'] ?? '';
    $tamano         = $_POST['tamano'] ?? '';
    $pelaje         = $_POST['pelaje'] ?? '';
    $color          = $_POST['color'] ?? '';
    $comportamiento = $_POST['comportamiento'] ?? '';
    $descripcion    = trim($_POST['descripcion'] ?? '');
    $estado         = "en_adopcion";

    // VALIDACIÓN DE CAMPOS VACÍOS
    if ($nombre === '' || $especie === '' || $sexo === '' || $edad_categoria === '' || $tamano === '') {
        $mensaje = "Faltan campos obligatorios.";
    } 
    // FILTRO DE MODERACIÓN — PROHÍBE TEXTO SENSIBLE / INSULTOS
    elseif (moderar_texto($nombre) || moderar_texto($raza) || moderar_texto($descripcion)) {
        $mensaje = "El texto contiene palabras inapropiadas o contenido no permitido.";
    }
    else {
        // Nombre del publicador
        $publicador_nombre = obtenerPublicadorNombre($conn, $usuario_id, $rol);

        // Manejo de foto
        $foto = null;
        if (!empty($_FILES['foto']['name'])) {
            $target_dir = __DIR__ . "../assets/uploads/mascotas/";
            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0777, true);
            }
            $base = basename($_FILES["foto"]["name"]);
            $foto = time() . "_" . preg_replace('/[^A-Za-z0-9.-]/', '_', $base);
            $target_file = $target_dir . $foto;

            if (!@move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto = null;
            }
        }

        // Guardar mascota
        $sql_insert = "INSERT INTO mascotas 
            (usuario_id, publicador_nombre, nombre, especie, raza, sexo, edad_categoria, 
             tamano, pelaje, color, comportamiento, descripcion, foto, estado, fecha_alta) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql_insert);

        if (!$stmt) {
            $mensaje = "Error al preparar la inserción: " . $conn->error;
        } else {
            $stmt->bind_param(
                "isssssssssssss",
                $usuario_id,
                $publicador_nombre,
                $nombre,
                $especie,
                $raza,
                $sexo,
                $edad_categoria,
                $tamano,
                $pelaje,
                $color,
                $comportamiento,
                $descripcion,
                $foto,
                $estado
            );

            $exito = $stmt->execute();

            if ($exito) {
                $mensaje = "Mascota publicada con éxito 🎉";
            } else {
                $mensaje = "Error al guardar la mascota: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Mascota - MHAC</title>
    <link rel="stylesheet" href="css/publicar-mascota.css">
    <link rel="stylesheet" href="css/base.css">

<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>

<body>
    <header>
        <h1>Publicar Mascota en Adopción</h1>
        <p>Ayudá a esta mascota a encontrar su hogar para siempre</p>
    </header>

    <main class="contenedor-principal">
        <?php if ($mensaje): ?>
            <div class="mensaje-contenedor <?= $exito ? 'exito' : 'error' ?>">
                <div class="mensaje-icono">
                    <?= $exito ? '✅' : '❌' ?>
                </div>
                <div class="mensaje-texto">
                    <p><?= $mensaje ?></p>
                    <div class="mensaje-acciones">
                        <?php if ($exito): ?>
                            <a href="mascotas_en_adopcion.php" class="btn-accion primario">Ver mascotas en adopción</a>
                        <?php endif; ?>
                        <a href="publicar_mascota.php" class="btn-accion secundario">Publicar otra mascota</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="formulario-contenedor">
                <form method="POST" enctype="multipart/form-data" class="formulario-publicar">
                    
                    <div class="seccion-formulario">
                        <h2>Información básica</h2>
                        <div class="campos-grupo">
                            <div class="campo">
                                <label for="nombre">Nombre de la mascota:</label>
                                <input type="text" name="nombre" id="nombre" required placeholder="Ej: Max, Luna, Toby">
                            </div>

                            <div class="campo">
                                <label for="especie">Especie:</label>
                                <select name="especie" id="especie" required>
                                    <option value="">Selecciona la especie</option>
                                    <option value="perro">Perro</option>
                                    <option value="gato">Gato</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>

                            <div class="campo">
                                <label for="raza">Raza:</label>
                                <input type="text" name="raza" id="raza" placeholder="Ej: Labrador, Caniche, Mestizo">
                            </div>

                            <div class="campo">
                                <label for="sexo">Sexo:</label>
                                <select name="sexo" id="sexo" required>
                                    <option value="">Selecciona el sexo</option>
                                    <option value="macho">Macho</option>
                                    <option value="hembra">Hembra</option>
                                    <option value="desconocido">Desconocido</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="seccion-formulario">
                        <h2>Características físicas</h2>
                        <div class="campos-grupo">
                            <div class="campo">
                                <label for="edad_categoria">Categoría de edad:</label>
                                <select name="edad_categoria" id="edad_categoria" required>
                                    <option value="">Selecciona la edad</option>
                                    <option value="cachorro">Cachorro</option>
                                    <option value="joven">Joven</option>
                                    <option value="adulto">Adulto</option>
                                    <option value="mayor">Adulto Mayor</option>
                                </select>
                            </div>

                            <div class="campo">
                                <label for="tamano">Tamaño:</label>
                                <select name="tamano" id="tamano" required>
                                    <option value="">Selecciona el tamaño</option>
                                    <option value="pequeño">Pequeño (hasta 10kg)</option>
                                    <option value="mediano">Mediano (10–25kg)</option>
                                    <option value="grande">Grande (25–40kg)</option>
                                    <option value="extra_grande">Extra Grande (+40kg)</option>
                                </select>
                            </div>

                            <div class="campo">
                                <label for="pelaje">Largo del pelo:</label>
                                <select name="pelaje" id="pelaje">
                                    <option value="">Selecciona el pelaje</option>
                                    <option value="sin_pelo">Sin pelo</option>
                                    <option value="corto">Corto</option>
                                    <option value="medio">Medio</option>
                                    <option value="largo">Largo</option>
                                </select>
                            </div>

                            <div class="campo">
                                <label for="color">Color:</label>
                                <select name="color" id="color">
                                    <option value="">Selecciona el color</option>
                                    <option value="apricot">Apricot / Beige</option>
                                    <option value="bicolor">Bicolor</option>
                                    <option value="negro">Negro</option>
                                    <option value="atigrado">Atigrado</option>
                                    <option value="marron">Marrón / Chocolate</option>
                                    <option value="dorado">Dorado</option>
                                    <option value="gris">Gris / Azul / Plateado</option>
                                    <option value="arlequin">Arlequín</option>
                                    <option value="merle_azul">Merle Azul</option>
                                    <option value="merle_rojo">Merle Rojo</option>
                                    <option value="rojo">Rojo / Castaño / Naranja</option>
                                    <option value="sable">Sable</option>
                                    <option value="tricolor">Tricolor</option>
                                    <option value="blanco">Blanco / Crema</option>
                                    <option value="amarillo">Amarillo / Canela / Fawn</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="seccion-formulario">
                        <h2>Comportamiento y cuidados</h2>
                        <div class="campo campo-completo">
                            <label for="comportamiento">Cuidados & comportamiento:</label>
                            <select name="comportamiento" id="comportamiento">
                                <option value="">Selecciona el comportamiento</option>
                                <option value="entrenado">Entrenado para casa</option>
                                <option value="cuidados_especiales">Cuidados especiales</option>
                                <option value="ninguno">Ninguno</option>
                            </select>
                        </div>

                        <div class="campo campo-completo">
                            <label for="descripcion">Descripción:</label>
                            <textarea name="descripcion" id="descripcion" rows="5" placeholder="Contanos sobre la personalidad, historia, cuidados especiales o cualquier información relevante sobre esta mascota..."></textarea>
                        </div>
                    </div>

                    <div class="seccion-formulario">
                        <h2>Foto de la mascota</h2>
                        <div class="campo campo-completo">
                            <label for="foto" class="label-foto">
                                <span class="icono-foto">📷</span>
                                <span class="texto-foto">Seleccionar foto</span>
                                <input type="file" name="foto" id="foto" accept="image/*">
                            </label>
                            <p id="nombre-archivo" style="font-size:14px; color:#555; margin-top:5px;"></p>
                            <p class="ayuda-foto">Elegí una foto clara y atractiva que muestre bien a la mascota</p>
                        </div>
                    </div>

                    <div class="formulario-acciones">
                        <button type="submit" class="btn-publicar">Publicar mascota</button>
                        <a href="mascotas_en_adopcion.php" class="btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>

<script>
document.getElementById('foto').addEventListener('change', function() {
    const archivo = this.files[0];
    const nombreArchivo = archivo ? archivo.name : "Ningún archivo seleccionado";
    document.getElementById('nombre-archivo').textContent = nombreArchivo;
});
</script>

</body>
</html>
