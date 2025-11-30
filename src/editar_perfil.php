<?php
session_start();

// 1. Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

$id_usuario = $_SESSION['usuario_id'];

// 2. Obtener datos actuales del usuario
$sql = "SELECT nombre, apellido, email, telefono, rol, fecha_registro, foto_perfil 
        FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// 3. Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    
    // Por defecto, mantenemos la foto que ya tenía
    $foto_final = $usuario['foto_perfil'];

    // --- LÓGICA PARA ELIMINAR FOTO ---
    if (isset($_POST['eliminar_foto'])) {
        // 1. Si tiene foto física, la borramos
        if (!empty($usuario['foto_perfil'])) {
            $ruta_a_borrar = "../assets/uploads/usuarios/" . $usuario['foto_perfil'];
            if (file_exists($ruta_a_borrar)) {
                unlink($ruta_a_borrar);
            }
        }

        // 2. Limpiamos el campo en la Base de Datos (ponemos NULL o vacío)
        $sql_delete = "UPDATE usuarios SET foto_perfil = NULL WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id_usuario);
        
        if ($stmt_delete->execute()) {
            header("Location: editar_perfil.php"); // Recargamos para ver el cambio
            exit;
        }
    }

    // 4. Lógica de Subida de Imagen
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        
        // A. Definir rutas
        $carpeta_destino = "../assets/uploads/usuarios/";
        $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']); // Usamos time() para evitar duplicados
        $rutaCompleta = $carpeta_destino . $nombreArchivo;

        // B. ELIMINAR FOTO ANTERIOR (Limpieza)
        // Si el usuario ya tenía foto y el archivo existe, lo borramos antes de subir el nuevo
        if (!empty($usuario['foto_perfil'])) {
            $ruta_foto_vieja = $carpeta_destino . $usuario['foto_perfil'];
            if (file_exists($ruta_foto_vieja)) {
                unlink($ruta_foto_vieja); // Esto borra el archivo físico
            }
        }

        // C. Subir la nueva foto
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
            // Guardamos SOLO el nombre del archivo en la BD, no la ruta completa
            $foto_final = $nombreArchivo; 
        } else {
            echo "<script>alert('Error al subir la imagen a la carpeta.');</script>";
        }
    }

    // 5. Actualizar base de datos
    $sql = "UPDATE usuarios 
            SET nombre = ?, apellido = ?, email = ?, telefono = ?, foto_perfil = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $apellido, $email, $telefono, $foto_final, $id_usuario);

    if ($stmt->execute()) {
        // Actualizamos la variable de sesión por si se usa el nombre en el header
        $_SESSION['usuario_nombre'] = $nombre; 
        header("Location: perfil.php"); // Redirigir al perfil para ver cambios
        exit;
    } else {
        echo "Error al actualizar perfil en la base de datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/editar_perfil.css">
    <a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>
<body>

<div class="contenedor-form">
    <h2>Editar Perfil</h2>

    <?php 
    // --- LÓGICA VISUAL: FOTO O AVATAR ---
    
    // Rutas base
    $ruta_uploads = "../assets/uploads/usuarios/";
    $ruta_avatars = "../assets/imagenes/avatars/";

    // Determinamos qué mostrar
    if (!empty($usuario['foto_perfil']) && file_exists($ruta_uploads . $usuario['foto_perfil'])) {
        // Si tiene foto real subida
        $avatar_mostrar = $ruta_uploads . htmlspecialchars($usuario['foto_perfil']);
    } else {
        // Si no tiene foto, elegimos avatar random (1 al 5)
        $numero_random = rand(1, 5); 
        $avatar_mostrar = $ruta_avatars . "avatar" . $numero_random . ".png";
    }
    ?>

    <div style="text-align: center; margin-bottom: 20px;">
        <img src="<?php echo $avatar_mostrar; ?>" alt="Foto actual" 
             style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #ddd;">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

        <label for="telefono">Teléfono:</label>
        <input type="tel" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">

        <label for="foto">Cambiar Foto de Perfil:</label>
        <input type="file" name="foto" accept="image/*">
        <?php if (!empty($usuario['foto_perfil'])): ?>
            <div style="margin-top: 5px;">
                <button type="submit" name="eliminar_foto" value="1" 
                        style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;"
                        onclick="return confirm('¿Seguro que querés eliminar tu foto de perfil?');">
                    Eliminar foto actual
                </button>
            </div>
        <?php endif; ?>

        <div class="acciones">
            <button type="submit" class="boton boton-guardar">Guardar Cambios</button>
            <a href="perfil.php" class="boton boton-cancelar">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>