<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Voluntariado - MHAC</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/voluntariado.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <a href="index.php" class="volver-inicio">
    <span>←</span> Volver al inicio
  </a>
  <style>
    .like-btn {background:none;border:none;cursor:pointer;padding:0;}
    .like-btn img {width:34px;height:34px;vertical-align:middle;}
    .likes-count {margin-left:4px;font-size:14px;}
  </style>
</head>
<body>

  <header>
    <h1>🌟 Voluntariado</h1>
    <p>Únete como voluntario y marca la diferencia en la vida de nuestros peludos 🐶🐱</p>
    <hr>
  </header>

  <main>
    <section>
      <h2>¿Por qué ser voluntario?</h2>
      <p>
        Tu tiempo y cariño pueden cambiar vidas. Ayuda con cuidado, paseos, socialización y apoyo en nuestros refugios. 
        Cada minuto que dedicas se convierte en amor y seguridad para los animales.
      </p>
    </section>

    <hr>

    <!-- Actividades -->
    <section>
      <h2>Actividades de voluntariado 🐾</h2>
      <ul>
        <li>🏠 Ayuda en refugios: limpieza, alimentación y cuidado diario.</li>
        <li>🐕 Paseos y socialización de perros y gatos.</li>
        <li>🎨 Talleres y eventos de concientización.</li>
        <li>📦 Recolección y organización de donaciones.</li>
        <li>📸 Fotografía y difusión de adopciones.</li>
      </ul>
    </section>

    <hr>

    <!-- Formulario de contacto -->
    <section>
      <h2>Únete como voluntario</h2>
      <p>Completa el formulario y nos pondremos en contacto para coordinar tu participación.</p>
      
      <form action="mailto:mhac@gmail.com" method="post" enctype="text/plain">
        <div>
          <label for="nombre">Nombre:</label><br>
          <input type="text" id="nombre" name="Nombre" placeholder="Tu nombre completo" required>
        </div><br>

        <div>
          <label for="email">Correo:</label><br>
          <input type="email" id="email" name="Email" placeholder="Tu correo" required>
        </div><br>

        <div>
          <label for="telefono">Teléfono:</label><br>
          <input type="tel" id="telefono" name="Telefono" placeholder="Opcional">
        </div><br>

        <div>
          <label for="intereses">¿En qué te gustaría colaborar?</label><br>
          <textarea id="intereses" name="Intereses" placeholder="Ej: paseos, alimentación, eventos..." rows="4"></textarea>
        </div><br>

        <button type="submit">Enviar 🐾</button>
      </form>
    </section>

    <hr>

    <!-- Inspiración -->
    <section>
      <h2>Historias de voluntarios 💛</h2>
      <p>
        Cada voluntario deja una huella imborrable. Conoce cómo otros cambiaron vidas y cómo vos también podés hacerlo.
      </p>
    </section>
  </main>

  <footer>
    <hr>
    <p>&copy; <?= date("Y") ?> Mis Huellitas a Casa - Todos los derechos reservados</p>
  </footer>

</body>
</html>
