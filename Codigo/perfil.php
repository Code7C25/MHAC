<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

$id_usuario = $_SESSION['usuario_id'];
$sql = "SELECT nombre, email, rol, fecha_registro, foto_perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// Definir avatar por defecto si no hay foto
if (!empty($usuario['foto_perfil'])) {
    $avatar = htmlspecialchars($usuario['foto_perfil']);
} else {
    // Lista de avatares
    $avatares = [
        "imagenes/avatars/avatar1.png",
        "imagenes/avatars/avatar2.png",
        "imagenes/avatars/avatar3.png",
        "imagenes/avatars/avatar4.png",
        "imagenes/avatars/avatar5.png",
        "imagenes/avatars/avatar6.png",
        "imagenes/avatars/avatar7.png",
        "imagenes/avatars/avatar8.png",
        "imagenes/avatars/avatar9.png",
        "imagenes/avatars/avatar10.png",
        "imagenes/avatars/avatar11.png",
        "imagenes/avatars/avatar12.png",
        "imagenes/avatars/avatar13.png",
        "imagenes/avatars/avatar14.png",
        "imagenes/avatars/avatar15.png",
        "imagenes/avatars/avatar16.png",
        "imagenes/avatars/avatar17.png",
        "imagenes/avatars/avatar18.png",
        "imagenes/avatars/avatar19.png",
        "imagenes/avatars/avatar20.png",
    ];  
    // Elegir uno al azar
    $avatar = $avatares[array_rand($avatares)];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - MHAC</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/perfil.css">
    <a href="index.php" class="">
            <span class="">←</span>
            Volver al inicio
    </a>
</head>
<body>
    <div class="perfil-container">
        <h1>Mi Perfil</h1>
        
        <div class="avatar-container">
            <?php if (!empty($usuario['foto_perfil'])): ?>
                <img src="<?php echo $avatar; ?>" alt="Foto de perfil" class="foto-perfil">
            <?php else: ?>
                <img src="<?php echo $avatar; ?>" alt="Avatar por defecto" class="avatar-default">
            <?php endif; ?>
        </div>


        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
        <p><strong>Rol:</strong> <?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?></p>
        <p><strong>Miembro desde:</strong> <?php echo date("d/m/Y", strtotime($usuario['fecha_registro'])); ?></p>

        <div class="acciones">
            <a href="editar_perfil.php" class="boton">Editar perfil</a>
            <form action="logout.php" method="post" style="display:inline;">
                <button type="submit" class="boton">Cerrar sesión</button>
            </form>
        </div>
    </div>

</body>
</html>
