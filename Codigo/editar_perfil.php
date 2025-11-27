<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

$id_usuario = $_SESSION['usuario_id'];

// Obtener datos actuales del usuario
$sql = "SELECT nombre, apellido, email, telefono, rol, fecha_registro, foto_perfil 
        FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $foto_nueva = $usuario['foto_perfil'];

    // Subida de imagen
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['foto']['name']);
        $rutaDestino = "imagenes/" . uniqid() . "_" . $nombreArchivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
            $foto_nueva = $rutaDestino;
        }
    }

    // Actualizar datos
    $sql = "UPDATE usuarios 
            SET nombre = ?, apellido = ?, email = ?, telefono = ?, foto_perfil = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $apellido, $email, $telefono, $foto_nueva, $id_usuario);

    if ($stmt->execute()) {
        header("Location: perfil.php");
        exit;
    } else {
        echo "Error al actualizar perfil.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <style>
        :root {
            --color-bg: #faf7f2;
            --color-primary: #f4a261;
            --color-primary-dark: #e76f51;
            --color-accent: #81b29a;
            --color-text: #3e3e3e;
        }

        body {
            font-family: 'Baloo 2', cursive;
            background-color: var(--color-bg);
            margin: 0;
            padding: 0;
            color: var(--color-text);
        }

        .contenedor-form {
            max-width: 520px;
            margin: 60px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border-top: 6px solid var(--color-primary);
        }

        h2 {
            text-align: center;
            color: var(--color-primary-dark);
            font-size: 2rem;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: var(--color-text);
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid var(--color-accent);
            background-color: #fdfaf7;
            color: var(--color-text);
            margin-top: 5px;
            font-size: 15px;
        }

        .acciones {
            margin-top: 25px;
            text-align: center;
        }

        .boton {
            display: inline-block;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin: 5px;
        }

        .boton-guardar {
            background-color: var(--color-primary);
            color: white;
        }

        .boton-guardar:hover {
            background-color: var(--color-primary-dark);
            transform: scale(1.05);
        }

        .boton-cancelar {
            background-color: var(--color-accent);
            color: white;
        }

        .boton-cancelar:hover {
            background-color: #6a9b85;
            transform: scale(1.05);
        }
        

    </style>
</head>
<body>

<div class="contenedor-form">
    <h2>Editar Perfil</h2>

    <?php 
    $avatar = !empty($usuario['foto_perfil']) ? htmlspecialchars($usuario['foto_perfil']) : "imagenes/avatar_mascota.png";
    ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

        <label for="telefono">Teléfono:</label>
        <input type="tel" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">

        <div class="acciones">
            <button type="submit" class="boton boton-guardar">Guardar Cambios</button>
            <a href="perfil.php" class="boton boton-cancelar">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>
