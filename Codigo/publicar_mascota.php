<?php
session_start();
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];

// Buscar el ID del refugio asociado a este usuario
$sql_ref = "SELECT id FROM refugios WHERE usuario_id = ?";
$stmt_ref = $conn->prepare($sql_ref);
$stmt_ref->bind_param("i", $usuario_id);
$stmt_ref->execute();
$result_ref = $stmt_ref->get_result();
$refugio = $result_ref->fetch_assoc();

if (!$refugio) {
    echo "❌ No se encontró refugio asociado a tu cuenta.";
    exit();
}
$refugio_id = $refugio['id'];

// Si enviaron el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $especie = $_POST['especie'];
    $raza = $_POST['raza'];
    $sexo = $_POST['sexo'];
    $edad = intval($_POST['edad']);
    $tamano = $_POST['tamano'];
    $descripcion = $_POST['descripcion'];

    // Manejo de foto
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/mascotas/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $foto = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
    }

    // Insertar mascota
    $sql_insert = "INSERT INTO mascotas 
        (nombre, especie, raza, sexo, edad, tamano, descripcion, foto, refugio_id, estado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_adopcion')";

    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ssssisssi", 
        $nombre, $especie, $raza, $sexo, $edad, $tamano, $descripcion, $foto, $refugio_id
    );

    if ($stmt->execute()) {
        echo "✅ Mascota publicada con éxito.";
        echo "<br><a href='adopcion.php'>Ver mascotas</a>";
    } else {
        echo "❌ Error al publicar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Mascota - Refugio</title>
    <link rel="stylesheet" href="css/forms.css">
</head>
<body>
    <h1>🐶 Publicar Mascota en Adopción</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Nombre: <input type="text" name="nombre" required></label><br>
        <label>Especie:
            <select name="especie">
                <option value="perro">Perro</option>
                <option value="gato">Gato</option>
                <option value="otro">Otro</option>
            </select>
        </label><br>
        <label>Raza: <input type="text" name="raza"></label><br>
        <label>Sexo:
            <select name="sexo">
                <option value="macho">Macho</option>
                <option value="hembra">Hembra</option>
                <option value="desconocido">Desconocido</option>
            </select>
        </label><br>
        <label>Edad (en meses): <input type="number" name="edad" min="0"></label><br>
        <label>Tamaño:
            <select name="tamano">
                <option value="pequeño">Pequeño</option>
                <option value="mediano">Mediano</option>
                <option value="grande">Grande</option>
                <option value="desconocido">Desconocido</option>
            </select>
        </label><br>
        <label>Descripción:<br>
            <textarea name="descripcion" rows="4"></textarea>
        </label><br>
        <label>Foto: <input type="file" name="foto"></label><br><br>

        <button type="submit">📌 Publicar</button>
    </form>
</body>
</html>
