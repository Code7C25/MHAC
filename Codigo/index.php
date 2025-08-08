<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MHAC - Mis Huellitas a Casa</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <h1 class="titulo-animado">MHAC - Mis Huellitas a Casa</h1>
    <p class="subtitulo">Conectamos corazones con patitas 🐾</p>

    <div class="user-session">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <span class="bienvenida">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>

            <div class="menu-usuario">
                <button class="menu-toggle">☰</button>
                <div class="menu-contenido">
                    <a href="perfil.php">Mi perfil</a>
                    <form action="logout.php" method="post">
                        <button type="submit" class="logout-btn">Cerrar sesión</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <a href="login.php" class="boton">Iniciar sesión</a>
            <a href="registro.php" class="boton">Registrarse</a>
        <?php endif; ?>
    </div>

    <nav class="menu-principal">
        <a class="boton" href="#">Adopción</a>
        <a class="boton" href="#">Refugios</a>
        <a class="boton" href="#">Campañas</a>
    </nav>

    <?php if (!isset($_SESSION['usuario_id'])): ?>
        <section class="alerta-inicio">
            <h2>¿Todavía no iniciaste sesión?</h2>
            <a href="login.php" class="cta">Iniciar sesión</a>
        </section>
    <?php endif; ?>

    <main>
        <section class="destacado">
            <h2>¿Buscando un nuevo amigo?</h2>
            <p>Explorá las historias de cientos de animalitos que están esperando por vos.</p>
            <a href="#" class="cta">Ver mascotas</a>
        </section>

        <!-- Slider de novedades -->
        <section class="slider-novedades">
            <div class="slide activo">Nueva campaña de adopción este fin de semana</div>
            <div class="slide">Refugio "Peluditos felices" necesita voluntarios</div>
            <div class="slide">Dona y ayuda a salvar vidas</div>
        </section>

        <!-- Tarjetas de secciones -->
        <section class="secciones-destacadas">
            <article class="tarjeta-seccion">
                <h3>Adopción</h3>
                <p>Encuentra a tu nuevo mejor amigo peludo.</p>
                <a href="adopcion.php" class="cta">Ver mascotas</a>
            </article>
            <article class="tarjeta-seccion">
                <h3>Refugios</h3>
                <p>Conoce los refugios y su labor.</p>
                <a href="refugios.php" class="cta">Ver refugios</a>
            </article>
            <article class="tarjeta-seccion">
                <h3>Campañas</h3>
                <p>Participa en nuestras campañas solidarias.</p>
                <a href="camapañas.php" class="cta">Ver campañas</a>
            </article>
        </section>

        <!-- Feed de publicaciones -->
        <section class="feed-publicaciones">
            <article class="post">
                <h4>Max encontró un hogar</h4>
                <p>Gracias a todos los que ayudaron a Max a encontrar su familia.</p>
                <small>Publicado el 01/08/2025</small>
            </article>
            <article class="post">
                <h4>Nuevo voluntario destacado</h4>
                <p>Felicitaciones a Laura por su compromiso con los peluditos.</p>
                <small>Publicado el 30/07/2025</small>
            </article>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <h3>MHAC - Mis Huellitas a Casa</h3>
                <p>Un puente entre peluditos y hogares llenos de amor.</p>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 MHAC. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

<script>
  // Toggle menú usuario
  document.querySelectorAll('.menu-toggle').forEach(btn => {
    btn.addEventListener('click', e => {
      const menu = btn.nextElementSibling;
      if (menu.style.display === 'block') {
        menu.style.display = 'none';
      } else {
        menu.style.display = 'block';
      }
    });
  });

  // Slider simple
  let slides = document.querySelectorAll('.slider-novedades .slide');
  let currentSlide = 0;
  setInterval(() => {
    slides[currentSlide].classList.remove('activo');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('activo');
  }, 4000);
</script>

</body>
</html>
