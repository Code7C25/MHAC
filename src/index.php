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
    <link rel="stylesheet" href="css/footer.css">
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
    <div class="hero-wag-bg" style="background-image: url('../assets/imagenes/slide1.jpg');"></div>
    <div class="hero-wag-overlay"></div>
    
    <div class="hero-wag-content">
        <div class="hero-wag-logo">
            <img src="../assets/imagenes/Logo.svg" alt="Logo MHAC">
        </div>
        <div class="hero-wag-text">
            <h1 data-es="Encuentra tu nuevo mejor amigo" data-en="Find your new best friend">Encuentra tu nuevo mejor amigo</h1>
            <p data-es="Explora mascotas de nuestra red de refugios y rescates." data-en="Explore pets from our network of over 100 shelters and rescues.">Explora mascotas de nuestra red de más de 100 refugios y rescates.</p>
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
                <img src="../assets/imagenes/icono-adopcion.png" alt="Adopciones">
            </div>
            <h3 data-es="Adopciones" data-en="Adoptions">Adopciones</h3>
        </a>
        
        <a href="refugios.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="../assets/imagenes/icono-refugios.png" alt="Refugios">
            </div>
            <h3 data-es="Refugios" data-en="Shelters">Refugios</h3>
        </a>
        
        <a href="rescates.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="../assets/imagenes/icono-rescates.png" alt="Rescates/Historias">
            </div>
            <h3 data-es="Rescates" data-en="Rescues">Rescates</h3>
        </a>
        
        <a href="comunidad.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="../assets/imagenes/icono-comunidad.png" alt="Comunidad">
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
                <img src="../assets/imagenes/icono-donaciones.png" alt="Donaciones">
            </div>
            <h3 data-es="Donaciones" data-en="Donations">Donaciones</h3>
            <p data-es="Ayuda con donaciones para el cuidado de los animales" data-en="Help with donations for animal care">Ayuda con donaciones para el cuidado de los animales</p>
        </a>

        <a href="campañas.php" class="servicio-card">
            <div class="icono">
                <img src="../assets/imagenes/icono-campañas.png" alt="Campañas">
            </div>
            <h3 data-es="Campañas" data-en="Campaigns">Campañas</h3>
            <p data-es="Participa en las campañas de concientización" data-en="Participate in our awareness campaigns">Participa en nuestras campañas de concientización</p>
        </a>
        
        <a href="voluntariado.php" class="servicio-card">
            <div class="icono">
                <img src="../assets/imagenes/icono-voluntariado.png" alt="Voluntariado">
            </div>
            <h3 data-es="Voluntariado" data-en="Volunteering">Voluntariado</h3>
            <p data-es="Únete como voluntario y marca la diferencia" data-en="Join as a volunteer and make a difference">Únete como voluntario y marca la diferencia</p>
        </a>
    </div>
</section>

<div class="servicio-card aprende-cuidarlos">
    <div>
        <h2>Aprendé a Cuidarlos ❤️</h2>
        <p>Descubrí consejos útiles...</p>
        <a href="info.php" class="btn-info">Ir a la sección</a>
    </div>
  <video controls autoplay muted loop style="width: 420px; height: 280px; border-radius: 12px;">
    <source src="../difusion/video_publicitario.mp4" type="video/mp4">
</video>

</div>

<!-- Mapa de veterinarias en Alta Gracia -->
<section class="mapa-veterinarias">
    <div class="mapa-veterinarias-header">
        <h2 data-es="Veterinarias en Alta Gracia 🐾" data-en="Veterinary Clinics in Alta Gracia 🐾">
            Veterinarias en Alta Gracia 🐾
        </h2>
        <p data-es="Encuentra las clínicas veterinarias más cercanas para el cuidado de tu mascota" 
           data-en="Find the nearest veterinary clinics for your pet's care">
            Encuentra las clínicas veterinarias más cercanas para el cuidado de tu mascota
        </p>
    </div>
    
    <div class="mapa-container">
        <iframe 
            src="https://www.google.com/maps/d/u/0/embed?mid=1jeIdRftDtBaRK4yuDoS6eGOuj6YgrC0&ehbc=2E312F&noprof=1" 
            width="640" 
            height="480"
            loading="lazy"
            title="Mapa de veterinarias en Alta Gracia">
        </iframe>
    </div>
</section>

<!-- Carrusel dinámico de mascotas en adopción -->
<section class="novedades-carrusel">
    <div class="carrusel-header">
        <h3 data-es="Mascotas en adopción" data-en="Pets for Adoption">MASCOTAS EN ADOPCIÓN</h3>
        <a href="mascotas_en_adopcion.php" class="btn-saber-mas" data-es="Saber más" data-en="Learn more">SABER MÁS</a>
    </div>

    <div class="carrusel-wrapper">
        <div class="carrusel-container">
            <?php
            require_once 'conexion.php';
            $query = $conn->query("SELECT nombre, descripcion, foto, fecha_alta FROM mascotas WHERE estado='en_adopcion' ORDER BY fecha_alta DESC");
            while($row = $query->fetch_assoc()):
            ?>
                <div class="carrusel-slide">
                    <?php if($row['foto']): ?>
                        <img src="../assets/uploads/mascotas/<?php echo htmlspecialchars($row['foto']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                    <?php endif; ?>
                    <div class="slide-info">
                        <h4><?php echo htmlspecialchars($row['nombre']); ?></h4>
                        <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        <small><?php echo date("d/m/Y", strtotime($row['fecha_alta'])); ?></small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <button class="carrusel-nav prev" aria-label="Anterior">‹</button>
        <button class="carrusel-nav next" aria-label="Siguiente">›</button>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.carrusel-container');
    const slides = document.querySelectorAll('.carrusel-slide');
    const prevBtn = document.querySelector('.carrusel-nav.prev');
    const nextBtn = document.querySelector('.carrusel-nav.next');

    let index = 0;
    let slidesPerView = getSlidesPerView();
    let autoplayInterval;

    function getSlidesPerView() {
        const width = window.innerWidth;
        if (width < 480) return 1;
        if (width < 768) return 2;
        if (width < 1024) return 3;
        if (width < 1280) return 4;
        return 5;
    }

    function getSlideWidth() {
        return slides[0].offsetWidth + parseInt(getComputedStyle(container).gap || 16);
    }

    function updateCarousel() {
        const desplazamiento = -index * getSlideWidth();
        container.style.transform = `translateX(${desplazamiento}px)`;
    }

    function nextSlide() {
        index++;
        if(index > slides.length - slidesPerView) index = 0;
        updateCarousel();
    }

    function prevSlide() {
        index--;
        if(index < 0) index = slides.length - slidesPerView;
        updateCarousel();
    }

    nextBtn.addEventListener('click', () => { nextSlide(); resetAutoplay(); });
    prevBtn.addEventListener('click', () => { prevSlide(); resetAutoplay(); });

    function startAutoplay() {
        autoplayInterval = setInterval(nextSlide, 3000);
    }

    function stopAutoplay() {
        clearInterval(autoplayInterval);
    }

    function resetAutoplay() {
        stopAutoplay();
        startAutoplay();
    }

    window.addEventListener('resize', () => {
        slidesPerView = getSlidesPerView();
        index = 0;
        updateCarousel();
    });

    // Swipe support
    let touchStartX = 0;
    container.addEventListener('touchstart', (e) => { touchStartX = e.changedTouches[0].screenX; });
    container.addEventListener('touchend', (e) => {
        let touchEndX = e.changedTouches[0].screenX;
        if(touchStartX - touchEndX > 50) nextSlide();
        if(touchEndX - touchStartX > 50) prevSlide();
    });

    startAutoplay();
});
</script>

<footer class="footer">
    <div class="footer-container">
        
        <div class="footer-logo">
            <h3 data-es="MHAC - Mis Huellitas a Casa" data-en="MHAC - My Paws at Home">MHAC - Mis Huellitas a Casa</h3>
        </div>

        <div class="footer-inline-links">
            
            <span class="inline-slogan" data-es="Un puente entre peluditos y hogares llenos de amor." data-en="A bridge between animals and homes full of love.">
                Un puente entre peluditos y hogares llenos de amor.
            </span> 
            
            <span class="separador">|</span>
            
            <a href="../docs/manual_usuario_MHAC.pdf" download="Manual_Usuario_MHAC.pdf" class="footer-action-link">
                Ayuda
            </a>

            <span class="separador">|</span>
            
            <button onclick="iniciarTour()" class="footer-action-link">
                Tour Interactivo
            </button>
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
  // Sistema de traducción con persistencia en localStorage
  const btnIdioma = document.getElementById('btnIdioma');
  let idiomaActual = 'es';

  // Función que traduce la página
  function traducirPagina(idioma) {
    // Traducir texto
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

    // Cambiar texto del botón si existe
    if (btnIdioma) btnIdioma.textContent = idioma === 'es' ? '🌐 English' : '🌐 Español';
  }

  // Leer idioma guardado en localStorage
  window.addEventListener('DOMContentLoaded', () => {
    const idiomaGuardado = localStorage.getItem('idioma') || 'es';
    idiomaActual = idiomaGuardado;
    traducirPagina(idiomaActual);
  });

  // Cambiar idioma al hacer click en el botón
  btnIdioma.addEventListener('click', () => {
    idiomaActual = idiomaActual === 'es' ? 'en' : 'es';
    localStorage.setItem('idioma', idiomaActual);
    traducirPagina(idiomaActual);
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
    '../assets/imagenes/slide1.jpg',
    '../assets/imagenes/slide2.jpg', 
    '../assets/imagenes/slide3.jpg'
  ];
  let fondoIndex = 0;
  setInterval(() => {
    fondoIndex = (fondoIndex + 1) % imagenesFondo.length;
    heroBg.style.backgroundImage = `url('${imagenesFondo[fondoIndex]}')`;
  }, 5000);

  function cerrarConsejo() {
    const popup = document.getElementById("consejoPopup");
    if (popup) popup.style.display = 'none';
  }

  window.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("consejoPopup");
    if (popup) {
      setTimeout(() => { popup.classList.add('visible'); }, 100); 
      setTimeout(() => { popup.style.display = 'none'; }, 15000); 
    }
  });
</script>
<a href="denuncias.html" 
   class="btn-denuncias-flotante" 
   data-tooltip="Denunciar maltrato animal"
   aria-label="Denunciar maltrato animal">
    <img src="../assets/imagenes/amar.png" alt="Denuncias" class="icono-denuncias">
</a>
</body>
</html>
