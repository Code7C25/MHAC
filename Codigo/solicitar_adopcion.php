<?php
session_start();
require_once 'conexion.php';

$mensaje = '';
$exito = false;

// Solo usuarios adoptantes pueden ver el formulario
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'adoptante') {
    header("Location: login.php");
    exit();
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mascota_id  = intval($_POST['mascota_id']);
    $nombre      = trim($_POST['nombre']);
    $email       = trim($_POST['email']);
    $telefono    = trim($_POST['telefono']);
    $domicilio   = trim($_POST['domicilio']);
    $edad        = intval($_POST['edad']);
    $vivienda    = trim($_POST['vivienda']);
    $experiencia = trim($_POST['experiencia']);
    $usuario_id  = $_SESSION['usuario_id'];

    // Verificar que la mascota exista y esté en adopción
    $sql = "SELECT id, nombre FROM mascotas WHERE id = ? AND estado = 'en_adopcion'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $mascota_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $mensaje = "❌ Esta mascota no está disponible para adopción.";
    } else {
        // Verificar si ya existe una solicitud pendiente de este usuario
        $sql_check = "SELECT id FROM adopciones WHERE mascota_id = ? AND usuario_id = ? AND estado = 'pendiente'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $mascota_id, $usuario_id);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();

        if ($res_check->num_rows > 0) {
            $mensaje = "⚠️ Ya enviaste una solicitud pendiente para esta mascota.<br><a href='mis_adopciones.php'>Ver mis solicitudes</a>";
        } else {
            // Insertar solicitud
            $sql_insert = "INSERT INTO adopciones 
                (mascota_id, usuario_id, estado, fecha_solicitud, nombre, email, telefono, domicilio, edad, vivienda, experiencia) 
                VALUES (?, ?, 'pendiente', NOW(), ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_insert = $conn->prepare($sql_insert);
            if (!$stmt_insert) {
                die("Error en prepare: " . $conn->error);
            }
            $stmt_insert->bind_param(
                "iissssiss", 
                $mascota_id, $usuario_id, 
                $nombre, $email, $telefono, $domicilio, 
                $edad, $vivienda, $experiencia
            );

            if ($stmt_insert->execute()) {
                $mensaje = "✅ Solicitud enviada con éxito.<br><a href='mis_adopciones.php'>Ver mis solicitudes</a>";
                $exito = true;
            } else {
                $mensaje = "❌ Error al enviar la solicitud: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de adopción</title>
    <a href="index.php" class="">
        <span class="">←</span>
        Volver al inicio
    </a>
</head>
<body>
    <div style="max-width:600px;margin:40px auto;padding:20px;">
        <h2>🐾 Formulario de Solicitud de Adopción</h2>
        
        <?php if ($mensaje): ?>
            <div><?= $mensaje ?></div>
            <br>
            <a href="mascotas_en_adopcion.php">Volver al listado de mascotas</a>
        <?php else: ?>
            <?php
            // Obtener mascotas en adopción
            $sql_mascotas = "SELECT id, nombre, especie, raza FROM mascotas WHERE estado = 'en_adopcion'";
            $result_mascotas = $conn->query($sql_mascotas);
            ?>
            <form method="POST" action="solicitar_adopcion.php">
                <label for="mascota_id">Mascota en adopción:</label><br>
                <select name="mascota_id" id="mascota_id" required>
                    <option value="">Selecciona una mascota</option>
                    <?php while ($m = $result_mascotas->fetch_assoc()): ?>
                        <option value="<?= $m['id'] ?>">
                            <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['especie']) ?>, <?= htmlspecialchars($m['raza']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <br><br>

                <label for="nombre">Nombre completo:</label><br>
                <input type="text" name="nombre" id="nombre" required><br><br>

                <label for="email">Email de contacto:</label><br>
                <input type="email" name="email" id="email" required><br><br>

                <label for="telefono">Teléfono:</label><br>
                <input type="text" name="telefono" id="telefono" required><br><br>

                <label for="direccion">Domicilio:</label><br>
                <input type="text" name="domicilio" id="domicilio" required><br><br>

                <label for="edad">Edad:</label><br>
                <input type="number" name="edad" id="edad" min="18" required><br><br>

                <label for="vivienda">Tipo de vivienda:</label><br>
                <input list="tiposVivienda" name="vivienda" id="vivienda" required>
                <datalist id="tiposVivienda">
                <option value="Casa">
                <option value="Departamento">
                </datalist>
                <br><br>

                <label for="experiencia">¿Tuviste mascotas antes?</label><br>
                <textarea name="experiencia" id="experiencia" rows="3"></textarea>
                <br><br>

                <button type="submit">Enviar solicitud</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
