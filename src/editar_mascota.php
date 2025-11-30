<?php
// --- Conexión a la BD ---
include 'conexion.php';

// --- Moderación ---
require_once 'moderacion.php';

// --- Verificar ID recibido ---
if (!isset($_GET['id'])) {
    die("ID de mascota no especificado.");
}

$mascota_id = intval($_GET['id']);

// --- Obtener datos actuales ---
$stmt = $conn->prepare("SELECT * FROM mascotas WHERE id = ?");
$stmt->bind_param("i", $mascota_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Mascota no encontrada.");
}

$mascota = $result->fetch_assoc();

// --- Si enviaron el formulario ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = $_POST['nombre'];
    $especie = $_POST['especie'];
    $raza = $_POST['raza'];
    $sexo = $_POST['sexo'];
    $edad_categoria = $_POST['edad_categoria'];
    $tamano = $_POST['tamano'];
    $pelaje = $_POST['pelaje'];
    $color = $_POST['color'];
    $comportamiento = $_POST['comportamiento'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    // --- MODERAR TODOS LOS CAMPOS DE TEXTO ---
    $campos_a_moderar = [
        $nombre,
        $raza,
        $color,
        $descripcion
    ];

    foreach ($campos_a_moderar as $campo) {
        if (moderar_texto($campo)) {
            echo "<script>
                alert('Uno de los campos contiene lenguaje inapropiado. Por favor revisa el contenido.');
                history.back();
            </script>";
            exit;
        }
    }

    // --- Manejo de foto ---
    $foto_final = $mascota["foto"]; // mantener foto anterior

    if (!empty($_FILES["foto"]["name"])) {
        $nombreFoto = time() . "_" . basename($_FILES["foto"]["name"]);
        $rutaDestino = "../assets/uploads/mascotas/" . $nombreFoto;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestino)) {
            $foto_final = $nombreFoto;
        }
    }

    // --- Actualizar ---
    $update = $conn->prepare("UPDATE mascotas SET 
        nombre=?, especie=?, raza=?, sexo=?, edad_categoria=?, tamano=?, pelaje=?, color=?, 
        comportamiento=?, descripcion=?, estado=?, foto=?
        WHERE id=?");

    $update->bind_param(
        "ssssssssssssi",
        $nombre, $especie, $raza, $sexo, $edad_categoria, $tamano, $pelaje, $color,
        $comportamiento, $descripcion, $estado, $foto_final, $mascota_id
    );

    if ($update->execute()) {
        echo "<script>
        alert('Mascota actualizada correctamente');
        window.location='mis_mascotas_publicadas.php?id=$mascota_id';
        </script>";
        exit;
    } else {
        echo "Error al actualizar.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar mascota</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/editar_mascota.css">
<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>
<body>

<h2>Editar Mascota</h2>

<form action="" method="POST" enctype="multipart/form-data">

    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= $mascota['nombre'] ?>" required><br><br>

    <label>Especie:</label>
    <select name="especie" required>
        <option value="perro" <?= $mascota['especie']=='perro'?'selected':''?>>Perro</option>
        <option value="gato" <?= $mascota['especie']=='gato'?'selected':''?>>Gato</option>
        <option value="otro" <?= $mascota['especie']=='otro'?'selected':''?>>Otro</option>
    </select><br><br>

    <label>Raza:</label>
    <input type="text" name="raza" value="<?= $mascota['raza'] ?>"><br><br>

    <label>Sexo:</label>
    <select name="sexo">
        <option value="macho" <?= $mascota['sexo']=='macho'?'selected':''?>>Macho</option>
        <option value="hembra" <?= $mascota['sexo']=='hembra'?'selected':''?>>Hembra</option>
        <option value="desconocido" <?= $mascota['sexo']=='desconocido'?'selected':''?>>Desconocido</option>
    </select><br><br>

    <label>Edad:</label>
    <select name="edad_categoria">
        <option value="cachorro" <?= $mascota['edad_categoria']=='cachorro'?'selected':''?>>Cachorro</option>
        <option value="joven" <?= $mascota['edad_categoria']=='joven'?'selected':''?>>Joven</option>
        <option value="adulto" <?= $mascota['edad_categoria']=='adulto'?'selected':''?>>Adulto</option>
        <option value="mayor" <?= $mascota['edad_categoria']=='mayor'?'selected':''?>>Mayor</option>
    </select><br><br>

    <label>Tamaño:</label>
    <select name="tamano">
        <option value="pequeño" <?= $mascota['tamano']=='pequeño'?'selected':''?>>Pequeño</option>
        <option value="mededio" <?= $mascota['tamano']=='mediano'?'selected':''?>>Mediano</option>
        <option value="grande" <?= $mascota['tamano']=='grande'?'selected':''?>>Grande</option>
        <option value="desconocido" <?= $mascota['tamano']=='desconocido'?'selected':''?>>Desconocido</option>
    </select><br><br>

    <label>Pelaje:</label>
    <select name="pelaje">
        <option value="sin_pelo" <?= $mascota['pelaje']=='sin_pelo'?'selected':''?>>Sin pelo</option>
        <option value="corto" <?= $mascota['pelaje']=='corto'?'selected':''?>>Corto</option>
        <option value="medio" <?= $mascota['pelaje']=='medio'?'selected':''?>>Medio</option>
        <option value="largo" <?= $mascota['pelaje']=='largo'?'selected':''?>>Largo</option>
    </select><br><br>

    <label>Color:</label>
    <input type="text" name="color" value="<?= $mascota['color'] ?>"><br><br>

    <label>Comportamiento:</label>
    <select name="comportamiento">
        <option value="ninguno" <?= $mascota['comportamiento']=='ninguno'?'selected':''?>>Ninguno</option>
        <option value="entrenado" <?= $mascota['comportamiento']=='entrenado'?'selected':''?>>Entrenado</option>
        <option value="cuidados_especiales" <?= $mascota['comportamiento']=='cuidados_especiales'?'selected':''?>>Cuidados especiales</option>
    </select><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion" rows="6" cols="40"><?= $mascota['descripcion'] ?></textarea><br><br>

    <label>Estado:</label>
    <select name="estado">
        <option value="en_adopcion" <?= $mascota['estado']=='en_adopcion'?'selected':''?>>En adopción</option>
        <option value="adoptado" <?= $mascota['estado']=='adoptado'?'selected':''?>>Adoptado</option>
        <option value="perdido" <?= $mascota['estado']=='perdido'?'selected':''?>>Perdido</option>
        <option value="encontrado" <?= $mascota['estado']=='encontrado'?'selected':''?>>Encontrado</option>
    </select><br><br>

    <label>Foto actual:</label><br>
    <?php if ($mascota['foto']): ?>
        <img src="../assets/uploads/mascotas/<?= $mascota['foto'] ?>" width="150"><br><br>
    <?php endif; ?>

    <label>Subir nueva foto:</label>
    <input type="file" name="foto"><br><br>

    <button type="submit">Guardar cambios</button>

</form>

</body>
</html>
