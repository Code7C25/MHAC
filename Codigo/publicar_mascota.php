<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $especie = $_POST['especie'] ?? '';
    $raza = trim($_POST['raza'] ?? '');
    $sexo = $_POST['sexo'] ?? '';
    $edad_categoria = $_POST['edad_categoria'] ?? '';
    $tamano = $_POST['tamano'] ?? '';
    $pelaje = $_POST['pelaje'] ?? '';
    $color = $_POST['color'] ?? '';
    $comportamiento = $_POST['comportamiento'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');

    // Manejo de foto
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = __DIR__ . "/uploads/mascotas/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        // sanitizar nombre de archivo básico
        $base = basename($_FILES["foto"]["name"]);
        $foto = time() . "" . preg_replace('/[^A-Za-z0-9.-]/', '_', $base);
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
    }

$refugio_id = null;
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'refugio') {
    $refugio_id = $_SESSION['refugio_id'] ?? null; // ✅ id real del refugio
}

    // Preparar INSERT dinámico: si hay refugio_id lo agregamos, si no no lo incluimos
    $columns = [
        'nombre', 'especie', 'raza', 'sexo',
        'edad_categoria', 'tamano', 'pelaje', 'color',
        'comportamiento', 'descripcion', 'foto', 'estado', 'fecha_alta'
    ];
    $placeholders = array_fill(0, count($columns), '?');
    $values = [
        $nombre, $especie, $raza, $sexo,
        $edad_categoria, $tamano, $pelaje, $color,
        $comportamiento, $descripcion, $foto, 'en_adopcion', date('Y-m-d H:i:s')
    ];

    if ($refugio_id !== null) {
        // agrego columna y valor al final
        $columns[] = 'refugio_id';
        $placeholders[] = '?';
        $values[] = $refugio_id;
    }

    $sql_insert = "INSERT INTO mascotas (" . implode(', ', $columns) . ")
                   VALUES (" . implode(', ', $placeholders) . ")";

    $stmt = $conn->prepare($sql_insert);
    if (!$stmt) {
        die("Error en prepare: " . $conn->error);
    }

    // Construir tipos dinámicamente: 'i' para enteros, 's' para strings
    $types = '';
    foreach ($values as $v) {
        $types .= is_int($v) ? 'i' : 's';
    }

    // Preparar parámetros por referencia para bind_param
    $bind_names[] = $types;
    for ($i = 0; $i < count($values); $i++) {
        // casteo explícito para evitar surprises
        if (is_int($values[$i])) {
            $bind_val = $values[$i];
        } else {
            // si es NULL lo dejamos como null (bind_param aceptará string vacía si es necesario)
            $bind_val = $values[$i];
        }
        $bind_names[] = &$values[$i];
    }

    // bind_param con call_user_func_array
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    if ($stmt->execute()) {
        $id_mascota = $conn->insert_id;
        echo "✅ Mascota publicada con éxito.";
        echo "<br><a href='mascotas_en_adopcion.php'>Ver mascotas en adopción</a>";
    } else {
        echo "❌ Error al publicar: " . $stmt->error;
    }

    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Mascota</title>
    <link rel="stylesheet" href="css/adopcion.css">
</head>
<body>
    <h1>🐾 Publicar Mascota en Adopción</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Nombre: <input type="text" name="nombre" required></label><br>

        <label>Especie:
            <select name="especie" required>
                <option value="perro">Perro</option>
                <option value="gato">Gato</option>
                <option value="otro">Otro</option>
            </select>
        </label><br>

        <label>Raza: <input type="text" name="raza" placeholder="Ej: Labrador, Caniche, Mestizo"></label><br>

        <label>Sexo:
            <select name="sexo" required>
                <option value="macho">Macho</option>
                <option value="hembra">Hembra</option>
                <option value="desconocido">Desconocido</option>
            </select>
        </label><br>

        <label>Categoría de edad:
            <select name="edad_categoria" required>
                <option value="cachorro">Cachorro</option>
                <option value="joven">Joven</option>
                <option value="adulto">Adulto</option>
                <option value="mayor">Adulto Mayor</option>
            </select>
        </label><br>

        <label>Tamaño:
            <select name="tamano" required>
                <option value="pequeño">Pequeño (hasta 10kg)</option>
                <option value="mediano">Mediano (10–25kg)</option>
                <option value="grande">Grande (25–40kg)</option>
                <option value="extra_grande">Extra Grande (+40kg)</option>
            </select>
        </label><br>

        <label>Largo del pelo:
            <select name="pelaje">
                <option value="sin_pelo">Sin pelo</option>
                <option value="corto">Corto</option>
                <option value="medio">Medio</option>
                <option value="largo">Largo</option>
            </select>
        </label><br>

        <label>Color:
            <select name="color">
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
        </label><br>

        <label>Cuidados & comportamiento:
            <select name="comportamiento">
                <option value="entrenado">Entrenado para casa</option>
                <option value="cuidados_especiales">Cuidados especiales</option>
                <option value="ninguno">Ninguno</option>
            </select>
        </label><br>

        <label>Descripción:<br>
            <textarea name="descripcion" rows="4"></textarea>
        </label><br>

        <label>Foto: <input type="file" name="foto"></label><br><br>

        <button type="submit">Publicar</button>
    </form>
</body>
</html>