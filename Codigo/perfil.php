<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

$id_usuario = $_SESSION['usuario_id'];
$sql = "SELECT nombre, email, rol, fecha_registro FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - MHAC</title>
    <link rel="stylesheet" href="css/perfil.css">
</head>
<body>

    <div class="perfil-container">
        <h1>Mi Perfil</h1>

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
