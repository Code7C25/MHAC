<?php 
session_start();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MHAC - Mis Huellitas a Casa</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/consejo_module.css">
</head>

<body>
<!-- Header con sesión de usuario -->
<div class="user-session">
    <button class="btn-idioma" id="btnIdioma">🌐 English</button>
    
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
    <a href="adopcion.php" data-es="ADOPTAR O INVOLUCRARSE" data-en="ADOPT OR GET INVOLVED">ADOPTAR O INVOLUCRARSE</a>
    <a href="perros.php" data-es="PERROS Y CACHORROS" data-en="DOGS AND PUPPIES">PERROS Y CACHORROS</a>
    <a href="gatos.php" data-es="GATOS Y GATITOS" data-en="CATS AND KITTENS">GATOS Y GATITOS</a>
    <a href="otros.php" data-es="OTROS TIPOS DE MASCOTAS" data-en="OTHER TYPES OF PETS">OTROS TIPOS DE MASCOTAS</a>
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
            <h1 data-es="Encuentra tu nuevo mejor amigo" data-en="Find your new best friend">Encuentra tu nuevo mejor amigo</h1>
            <p data-es="Explora mascotas de nuestra red de más de 100 refugios y rescates." data-en="Explore pets from our network of over 100 shelters and rescues.">Explora mascotas de nuestra red de más de 100 refugios y rescates.</p>
        </div>
    </div>

    <!-- Barra de búsqueda principal -->
    <div class="search-container">
        <form class="search-form" action="buscar.php" method="GET">
            <div class="search-inputs">
                <input type="text" name="tipo" placeholder="Buscar Perro, Gato, etc." class="search-input" data-es-placeholder="Buscar Perro, Gato, etc." data-en-placeholder="Search Dog, Cat, etc.">
                <input type="text" name="ubicacion" placeholder="Ingresa raza" class="search-input" data-es-placeholder="Ingresa raza" data-en-placeholder="Enter breed">
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
            <h3 data-es="Adopciones" data-en="Adoptions">Adopciones</h3>
        </a>
        
        <a href="refugios.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="imagenes/icono-refugios.png" alt="Refugios">
            </div>
            <h3 data-es="Refugios" data-en="Shelters">Refugios</h3>
        </a>
        
        <a href="rescates.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="imagenes/icono-rescates.png" alt="Rescates/Historias">
            </div>
            <h3 data-es="Rescates" data-en="Rescues">Rescates</h3>
        </a>
        
        <a href="comunidad.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="imagenes/icono-comunidad.png" alt="Comunidad">
            </div>
            <h3 data-es="Comunidad" data-en="Community">Comunidad</h3>
        </a>
    </div>
</section>

<?php if (!isset($_SESSION['usuario_id'])): ?>
    <section class="alerta-inicio">
        <h2 data-es="¿Todavía no iniciaste sesión?" data-en="Haven't logged in yet?">¿Todavía no iniciaste sesión?</h2>
        <p data-es="Únete a nuestra comunidad y ayuda a conectar corazones con patitas" data-en="Join our community and help connect hearts with paws">Únete a nuestra comunidad y ayuda a conectar corazones con patitas</p>
        <a href="login.php" class="cta" data-es="Iniciar sesión" data-en="Log in">Iniciar sesión</a>
    </section>
<?php endif; ?>

<!-- Sección de servicios adicionales -->
<section class="servicios-adicionales">
    <h2 data-es="¿Cómo más podés ayudar?" data-en="How else can you help?">¿Cómo más podés ayudar?</h2>
    <div class="servicios-grid">
        <a href="donaciones.php" class="servicio-card">
            <div class="icono">
                <img src="imagenes/icono-donaciones.png" alt="Donaciones">
            </div>
            <h3 data-es="Donaciones" data-en="Donations">Donaciones</h3>
            <p data-es="Ayuda con donaciones para el cuidado de los animales" data-en="Help with donations for animal care">Ayuda con donaciones para el cuidado de los animales</p>
        </a>
        
        <a href="campañas.php" class="servicio-card">
            <div class="icono">
                <img src="imagenes/icono-campañas.png" alt="Campañas">
            </div>
            <h3 data-es="Campañas" data-en="Campaigns">Campañas</h3>
            <p data-es="Participa en las campañas de concientización" data-en="Participate in our awareness campaigns">Participa en nuestras campañas de concientización</p>
        </a>
        
        <a href="voluntariado.php" class="servicio-card">
            <div class="icono">
                <img src="imagenes/icono-voluntariado.png" alt="Voluntariado">
            </div>
            <h3 data-es="Voluntariado" data-en="Volunteering">Voluntariado</h3>
            <p data-es="Únete como voluntario y marca la diferencia" data-en="Join as a volunteer and make a difference">Únete como voluntario y marca la diferencia</p>
        </a>
    </div>
</section>

<div class="servicio-card">
    <h2 data-es="Aprendé a Cuidarlos ❤️" data-en="Learn to Care for Them ❤️">Aprendé a Cuidarlos ❤️</h2>
    <p data-es="Descubrí consejos útiles sobre alimentación, salud y bienestar de tus mascotas. Solo los refugios y veterinarios pueden agregar contenido confiable." data-en="Discover useful tips about feeding, health and wellness for your pets. Only shelters and vets can add trusted content.">Descubrí consejos útiles sobre alimentación, salud y bienestar de tus mascotas. Solo los refugios y veterinarios pueden agregar contenido confiable.</p>
    <a href="info.php" class="btn-info" data-es="Ir a la sección de información 🐾" data-en="Go to information section 🐾">Ir a la sección de información 🐾</a>
</div>

<main class="contenido-secundario">
    <!-- Slider de novedades -->
    <section class="slider-novedades">
        <h3 data-es="Novedades" data-en="News">Novedades</h3>
        <div class="slide activo" data-es="Nueva campaña de adopción este fin de semana" data-en="New adoption campaign this weekend">Nueva campaña de adopción este fin de semana</div>
        <div class="slide" data-es="Refugio 'Peluditos felices' necesita voluntarios" data-en="Shelter 'Happy Furbabies' needs volunteers">Refugio "Peluditos felices" necesita voluntarios</div>
        <div class="slide" data-es="Dona y ayuda a salvar vidas" data-en="Donate and help save lives">Dona y ayuda a salvar vidas</div>
    </section>

    <!-- Feed de publicaciones -->
    <section class="feed-publicaciones">
        <h3 data-es="Historias de éxito" data-en="Success Stories">Historias de éxito</h3>
        <article class="post">
            <h4 data-es="Max encontró un hogar" data-en="Max found a home">Max encontró un hogar</h4>
            <p data-es="Gracias a todos los que ayudaron a Max a encontrar su familia." data-en="Thanks to everyone who helped Max find his family.">Gracias a todos los que ayudaron a Max a encontrar su familia.</p>
            <small data-es="Publicado el 01/08/2025" data-en="Published on 01/08/2025">Publicado el 01/08/2025</small>
        </article>
        <article class="post">
            <h4 data-es="Nuevo voluntario destacado" data-en="Featured Volunteer">Nuevo voluntario destacado</h4>
            <p data-es="Felicitaciones a Laura por su compromiso con los peluditos." data-en="Congratulations to Laura for her commitment to the animals.">Felicitaciones a Laura por su compromiso con los peluditos.</p>
            <small data-es="Publicado el 30/07/2025" data-en="Published on 30/07/2025">Publicado el 30/07/2025</small>
        </article>
    </section>

    <section class="home-presentation">
    </section>
    
    <section class="sidebar">
        <?php 
            require_once 'conexion.php'; // Si no está incluido arriba
            include 'consejo_tip_module.php'; 
        ?>
    </section>
</main>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-logo">
            <h3 data-es="MHAC - Mis Huellitas a Casa" data-en="MHAC - My Paws at Home">MHAC - Mis Huellitas a Casa</h3>
            <p data-es="Un puente entre peluditos y hogares llenos de amor." data-en="A bridge between animals and homes full of love.">Un puente entre peluditos y hogares llenos de amor.</p>
        </div>
        <div class="footer-bottom">
            <p data-es="© 2025 MHAC. Todos los derechos reservados." data-en="© 2025 MHAC. All rights reserved.">© 2025 MHAC. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<style>
    .user-session {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .btn-idioma {
        padding: 0.5rem 1rem;
        background: #5c8b39;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-idioma:hover {
        background: #5c8b39;
        transform: translateY(-2px);
    }
</style>

<script>
  // Sistema de traducción
  const btnIdioma = document.getElementById('btnIdioma');
  let idiomaActual = 'es';

  btnIdioma.addEventListener('click', () => {
    idiomaActual = idiomaActual === 'es' ? 'en' : 'es';
    traducirPagina(idiomaActual);
    btnIdioma.textContent = idiomaActual === 'es' ? '🌐 English' : '🌐 Español';
    localStorage.setItem('idioma', idiomaActual);
  });

  function traducirPagina(idioma) {
    // Traducir elementos con data-es y data-en
    document.querySelectorAll('[data-es][data-en]').forEach(elemento => {
      const texto = idioma === 'es' ? elemento.dataset.es : elemento.dataset.en;
      if (elemento.tagName === 'INPUT' || elemento.tagName === 'TEXTAREA') {
        elemento.placeholder = texto;
      } else {
        elemento.textContent = texto;
      }
    });

    // Traducir placeholders
    document.querySelectorAll('[data-es-placeholder][data-en-placeholder]').forEach(input => {
      input.placeholder = idioma === 'es' ? input.dataset.esPlaceholder : input.dataset.enPlaceholder;
    });
  }

  // Cargar idioma guardado
  window.addEventListener('load', () => {
    const idiomaSaved = localStorage.getItem('idioma') || 'es';
    idiomaActual = idiomaSaved;
    traducirPagina(idiomaSaved);
    btnIdioma.textContent = idiomaSaved === 'es' ? '🌐 English' : '🌐 Español';
  });

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

    function cerrarConsejo() {
        const popup = document.getElementById("consejoPopup");
        if (popup) {
            popup.style.display = 'none'; // Oculta el elemento
            // Opcional: podrías usar localStorage aquí para que no aparezca de nuevo
            // localStorage.setItem('consejo_cerrado', 'true');
        }
    }

    // 2. Lógica para hacer aparecer el popup al cargar la página
    window.addEventListener("DOMContentLoaded", () => {
        const popup = document.getElementById("consejoPopup");
        // Agrega la clase 'visible' para activar la animación CSS
        if (popup) {
            // Un pequeño retraso para que la animación se vea mejor
            setTimeout(() => {
                popup.classList.add('visible');
            }, 100); 

            // Opcional: desaparecerlo automáticamente después de 15 segundos
            setTimeout(() => {
                popup.style.display = 'none';
            }, 15000); 
        }
    });
</script>
</body>
</html>