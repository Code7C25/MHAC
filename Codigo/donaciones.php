<?php
session_start();
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

  <header>
    <h1>🐾 Donaciones</h1>
    <p>Ayuda con donaciones para el cuidado de los animales</p>
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
      <h2>Contactanos para donar</h2>
      <p>Completa tus datos y te enviaremos un correo a <strong>mhac@gmail.com</strong> para coordinar tu donación.</p>
      
      <!-- Formulario tipo marketing -->
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
          <label for="tipo">Tipo de colaboración:</label><br>
          <select id="tipo" name="TipoColaboracion" required>
            <option value="">--Selecciona--</option>
            <option value="Alimentos">Alimentos</option>
            <option value="Elementos">Elementos</option>
            <option value="Voluntariado">Voluntariado</option>
          </select>
        </div><br>

        <div>
          <label for="mensaje">Mensaje:</label><br>
          <textarea id="mensaje" name="Mensaje" placeholder="Tu mensaje o consulta" rows="4"></textarea>
        </div><br>

        <button type="submit">Enviar correo 🐶💛</button>
      </form>
    </section>

    <hr>

    <section>
      <h2>Transparencia</h2>
      <p>
        Publicamos informes periódicos con el detalle de lo recibido y cómo se utilizó cada aporte.  
        Tu confianza es nuestra prioridad 💛.
      </p>
    </section>
  </main>

  <footer>
    <hr>
    <p>&copy; <?= date("Y") ?> Mis Huellitas a Casa - Todos los derechos reservados</p>
  </footer>

</body>
</html>
