<?php
session_start();

// Asegúrate de que tu archivo de conexión esté incluido aquí
require_once 'conexion.php'; 

// 1. Obtener la lista de refugios de la BD
$refugios_stmt = $conn->prepare("
    SELECT r.id, r.nombre_refugio 
    FROM refugios r
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE u.rol = 'refugio'
    ORDER BY r.nombre_refugio ASC
");

if ($refugios_stmt) {
    $refugios_stmt->execute();
    $refugios_result = $refugios_stmt->get_result();
    $refugios_stmt->close();
} else {
    // Manejo de error si la consulta falla
    $refugios_result = null;
    error_log("Error al preparar consulta de refugios: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Donaciones - MHAC</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/donaciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <a href="index.php" class="volver-inicio">
        <span>←</span> Volver al inicio
    </a>
</head>
<body>
  <div class="registro-container">
  </div>

  <?php if (isset($_SESSION['exito'])): ?>
      <div class="mensaje-alerta exito-donacion">
          <?= $_SESSION['exito'] ?>
      </div>
      <?php unset($_SESSION['exito']); // Limpiar la variable de sesión después de mostrarla ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
      <div class="mensaje-alerta error-donacion">
          <?= $_SESSION['error'] ?>
      </div>
      <?php unset($_SESSION['error']); // Limpiar la variable de sesión después de mostrarla ?>
  <?php endif; ?>  

    <header>
        <h1>🐾 Donaciones</h1>
        <p>Ayuda con donaciones contactando directamente al refugio de tu elección.</p>
        <hr>
    </header>

    <main>
        <section>
            <h2>¿Por qué colaborar?</h2>
            <p>
                Cada aporte nos permite brindar alimento, atención veterinaria, refugio y mucho amor a los animales que lo necesitan. 
                Con tu ayuda, podemos cambiar vidas y darles una segunda oportunidad.
            </p>
        </section>

        <hr>

        <section>
            <h2>Contacta al Refugio para donar</h2>
            <p>
                Selecciona el refugio al que deseas apoyar. Una vez que completes el formulario, 
                enviaremos tu mensaje directamente al correo de contacto de ese refugio para que coordinen la donación.
            </p>
            
            <form action="procesar_donacion.php" method="POST">
                
                <div>
                    <label for="refugio">Refugio al que deseas donar:</label><br>
                    <select id="refugio" name="refugio_id" required>
                        <option value="" disabled selected>-- Selecciona un Refugio --</option>
                        
                        <?php 
                        // 2. Bucle PHP para mostrar refugios dinámicamente
                        if ($refugios_result && $refugios_result->num_rows > 0): 
                            while ($refugio = $refugios_result->fetch_assoc()):
                        ?>
                            <option value="<?= htmlspecialchars($refugio['id']) ?>">
                                <?= htmlspecialchars($refugio['nombre_refugio']) ?>
                            </option>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <option value="" disabled>No hay refugios disponibles aún.</option>
                        <?php endif; ?>
                    </select>
                </div><br>

                <div>
                    <label for="nombre">Tu Nombre:</label><br>
                    <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" required>
                </div><br>

                <div>
                    <label for="email">Tu Correo:</label><br>
                    <input type="email" id="email" name="email_usuario" placeholder="Tu correo para contacto" required>
                </div><br>

                <div>
                    <label for="tipo">Tipo de colaboración que ofreces:</label><br>
                    <select id="tipo" name="tipo_colaboracion" required>
                        <option value="">--Selecciona--</option>
                        <option value="Alimentos">Alimentos (Secos/Húmedos)</option>
                        <option value="Elementos">Elementos (Camas, correas, insumos)</option>
                        <option value="Voluntariado">Voluntariado (Paseos, limpieza)</option>
                        <option value="Efectivo">Donación monetaria</option>
                    </select>
                </div><br>

                <div>
                    <label for="mensaje">Mensaje para el refugio:</label><br>
                    <textarea id="mensaje" name="mensaje" placeholder="Describe tu donación, disponibilidad, o haz una pregunta." rows="4"></textarea>
                </div><br>

                <button type="submit">Enviar Solicitud de Donación 💛</button>
            </form>
        </section>

        <hr>

        <section>
            <h2>Transparencia</h2>
            <p>
                Al contactar directamente al refugio, asegúrate de verificar sus políticas de transparencia. 
                Tu apoyo es vital para ellos 💛.
            </p>
        </section>
    </main>

    <footer>
        <hr>
        <p>&copy; <?= date("Y") ?> Mis Huellitas a Casa - Todos los derechos reservados</p>
    </footer>

</body>
</html>