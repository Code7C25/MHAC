<?php
session_start();
require_once 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';


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
        $mensaje = "Esta mascota no está disponible para adopción.";
    } else {
        // Verificar si ya existe una solicitud pendiente de este usuario
        $sql_check = "SELECT id FROM adopciones WHERE mascota_id = ? AND usuario_id = ? AND estado = 'pendiente'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $mascota_id, $usuario_id);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();

        if ($res_check->num_rows > 0) {
            $mensaje = "Ya enviaste una solicitud pendiente para esta mascota.";
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
                $mensaje = "Solicitud enviada con éxito.";
                $exito = true;

                // ---------- Envío de mail con PHPMailer ----------
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'equipo.mhac@gmail.com';
                    $mail->Password   = 'eyon yplb kism xolj';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('equipo.mhac@gmail.com', 'MHAC');
                    $mail->addAddress('equipo.mhac@gmail.com', 'Equipo MHAC');

                    $mail->isHTML(true);
                    $mail->Subject = 'Nueva solicitud - MHAC';
                    $mail->Body    = "
                        <h2>Nueva solicitud de adopción</h2>
                        <p><b>Mascota:</b> $mascota_id</p>
                        <p><b>Nombre:</b> $nombre</p>
                        <p><b>Email:</b> $email</p>
                        <p><b>Teléfono:</b> $telefono</p>
                        <p><b>Edad:</b> $edad</p>
                        <p><b>Domicilio:</b> $domicilio</p>
                        <p><b>Vivienda:</b> $vivienda</p>
                        <p><b>Experiencia:</b> $experiencia</p>";

                    $mail->send();
                } catch (Exception $e) {
                    $mensaje .= "<br>⚠️ No se pudo enviar el correo: {$mail->ErrorInfo}";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de adopción - MHAC</title>
    <link rel="stylesheet" href="css/solicitar_adopcion.css">
    <link rel="stylesheet" href="css/base.css">
<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>

<body>
    <header>
        <h1>Formulario de Solicitud de Adopción</h1>
        <p>Tu solicitud será revisada por nuestro equipo de MHAC</p>
    </header>

    <main class="contenedor-principal">
        <?php if ($mensaje): ?>
            <div class="mensaje-contenedor <?= $exito ? 'exito' : 'error' ?>">
                <div class="mensaje-icono">
                    <?= $exito ? '✅' : '⚠️' ?>
                </div>
                <div class="mensaje-texto">
                    <p><?= $mensaje ?></p>
                    <div class="mensaje-acciones">
                        <?php if ($exito): ?>
                            <a href="mis_adopciones.php" class="btn-accion primario">Ver mis solicitudes</a>
                        <?php endif; ?>
                        <a href="mascotas_en_adopcion.php" class="btn-accion secundario">Volver al listado</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php
            // Obtener mascotas en adopción
            $sql_mascotas = "SELECT id, nombre, especie, raza FROM mascotas WHERE estado = 'en_adopcion' ORDER BY nombre";
            $result_mascotas = $conn->query($sql_mascotas);
            ?>
            <div class="formulario-contenedor">
                <form method="POST" action="solicitar_adopcion.php" class="formulario-adopcion">
                    <div class="seccion-formulario">
                        <h2>Selecciona la mascota</h2>
                        <div class="campo">
                            <label for="mascota_id">Mascota que deseas adoptar:</label>
                            <select name="mascota_id" id="mascota_id" required>
                                <option value="">Selecciona una mascota</option>
                                <?php while ($m = $result_mascotas->fetch_assoc()): ?>
                                    <option value="<?= $m['id'] ?>">
                                        <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['especie']) ?>, <?= htmlspecialchars($m['raza']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="seccion-formulario">
                        <h2>Información personal</h2>
                        <div class="campos-grupo">
                            <div class="campo">
                                <label for="nombre">Nombre completo:</label>
                                <input type="text" name="nombre" id="nombre" required>
                            </div>

                            <div class="campo">
                                <label for="email">Email de contacto:</label>
                                <input type="email" name="email" id="email" required>
                            </div>

                            <div class="campo">
                                <label for="telefono">Teléfono:</label>
                                <input type="text" name="telefono" id="telefono" required>
                            </div>

                            <div class="campo">
                                <label for="edad">Edad:</label>
                                <input type="number" name="edad" id="edad" min="18" max="100" required>
                            </div>
                        </div>
                    </div>

                    <div class="seccion-formulario">
                        <h2>Información de vivienda</h2>
                        <div class="campos-grupo">
                            <div class="campo campo-completo">
                                <label for="domicilio">Domicilio completo:</label>
                                <input type="text" name="domicilio" id="domicilio" required>
                            </div>

                            <div class="campo">
                                <label for="vivienda">Tipo de vivienda:</label>
                                <select name="vivienda" id="vivienda" required>
                                    <option value="">Selecciona el tipo</option>
                                    <option value="Casa">Casa</option>
                                    <option value="Departamento">Departamento</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="seccion-formulario">
                        <h2>Experiencia con mascotas</h2>
                        <div class="campo">
                            <label for="experiencia">¿Tuviste mascotas antes? Contanos tu experiencia:</label>
                            <textarea name="experiencia" id="experiencia" rows="4" placeholder="Describe tu experiencia previa con mascotas, cuidados que les brindaste, etc."></textarea>
                        </div>
                    </div>

                    <div class="formulario-acciones">
                        <button type="submit" class="btn-enviar">Enviar solicitud</button>
                        <a href="mascotas_en_adopcion.php" class="btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>