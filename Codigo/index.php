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
<!-- Header con sesión de usuario -->
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

<!-- Menú principal con navegación -->
<nav class="menu-principal">
    <a href="adopcion.php">ADOPTAR O INVOLUCRARSE</a>
    <a href="perros.php">PERROS Y CACHORROS</a>
    <a href="gatos.php">GATOS Y GATITOS</a>
    <a href="otros.php">OTROS TIPOS DE MASCOTAS</a>
</nav>

<!-- Hero section con imagen de fondo -->
<section class="hero-wag">
    <div class="hero-wag-bg" style="background-image: url('imagenes/slide1.jpg');"></div>
    <div class="hero-wag-overlay"></div>
    
    <div class="hero-wag-content">
        <div class="hero-wag-logo">
            <img src="imagenes/logo.png" alt="Logo MHAC">
        </div>
        <div class="hero-wag-text">
            <h1>Encuentra tu nuevo mejor amigo</h1>
            <p>Explora mascotas de nuestra red de más de 100 refugios y rescates.</p>
        </div>
    </div>

    <!-- Barra de búsqueda principal -->
    <div class="search-container">
        <form class="search-form" action="buscar.php" method="GET">
            <div class="search-inputs">
                <input type="text" name="tipo" placeholder="Buscar Perro, Gato, etc." class="search-input">
                <input type="text" name="ubicacion" placeholder="Ingresa raza" class="search-input">
                <button type="submit" class="search-button">🔍</button>
            </div>
        </form>
    </div>
</section>

<!-- Sección de categorías principales -->
<section class="categorias-principales">
    <div class="categorias-grid">
        <a href="adopcion.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="imagenes/icono-adopcion.png" alt="Adopciones">
            </div>
            <h3>Adopciones</h3>
        </a>
        
        <a href="refugios.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="imagenes/icono-refugios.png" alt="Refugios">
            </div>
            <h3>Refugios</h3>
        </a>
        
        <a href="rescates.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="imagenes/icono-rescates.png" alt="Rescates/Historias">
            </div>
            <h3>Rescates/Historias</h3>
        </a>
        
        <a href="comunidad.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="imagenes/icono-comunidad.png" alt="Comunidad">
            </div>
            <h3>Comunidad</h3>
        </a>
    </div>
</section>

<?php if (!isset($_SESSION['usuario_id'])): ?>
    <section class="alerta-inicio">
        <h2>¿Todavía no iniciaste sesión?</h2>
        <p>Únete a nuestra comunidad y ayuda a conectar corazones con patitas</p>
        <a href="login.php" class="cta">Iniciar sesión</a>
    </section>
<?php endif; ?>

<!-- Sección de servicios adicionales -->
<section class="servicios-adicionales">
    <h2>¿Cómo más podés ayudar?</h2>
    <div class="servicios-grid">
        <a href="donaciones.php" class="servicio-card">
            <div class="icono">
                <img src="imagenes/icono-donaciones.png" alt="Donaciones">
            </div>
            <h3>Donaciones</h3>
            <p>Ayuda con donaciones para el cuidado de los animales</p>
        </a>
        
        <a href="campañas.php" class="servicio-card">
            <div class="icono">
                <img src="imagenes/icono-campañas.png" alt="Campañas">
            </div>
            <h3>Campañas</h3>
            <p>Participa en nuestras campañas de concientización</p>
        </a>
        
        <a href="voluntariado.php" class="servicio-card">
            <div class="icono">
                <img src="imagenes/icono-voluntariado.png" alt="Voluntariado">
            </div>
            <h3>Voluntariado</h3>
            <p>Únete como voluntario y marca la diferencia</p>
        </a>
    </div>
</section>

<main class="contenido-secundario">
    <!-- Slider de novedades -->
    <section class="slider-novedades">
        <h3>Novedades</h3>
        <div class="slide activo">Nueva campaña de adopción este fin de semana</div>
        <div class="slide">Refugio "Peluditos felices" necesita voluntarios</div>
        <div class="slide">Dona y ayuda a salvar vidas</div>
    </section>

    <!-- Feed de publicaciones -->
    <section class="feed-publicaciones">
        <h3>Historias de éxito</h3>
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

  // Cambio automático del fondo en hero-wag
  const heroBg = document.querySelector('.hero-wag-bg');
  const imagenesFondo = [
    'imagenes/slide1.jpg',
    'imagenes/slide2.jpg', 
    'imagenes/slide3.jpg'
  ];
  let fondoIndex = 0;

  setInterval(() => {
    fondoIndex = (fondoIndex + 1) % imagenesFondo.length;
    heroBg.style.backgroundImage = `url('${imagenesFondo[fondoIndex]}')`;
  }, 5000);
</script>
</body>
</html>