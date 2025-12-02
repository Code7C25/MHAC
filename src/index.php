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
<link rel="stylesheet" href="../manual_interactivo/introjs.min.css"> 
<script src="../manual_interactivo/intromin.js"></script>
<script src="../manual_interactivo/manual_interactivo.js"></script>
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
    <a href="adopcion.php">ADOPTAR O INVOLUCRARSE</a>
    <a href="perros.php">PERROS Y CACHORROS</a>
    <a href="gatos.php">GATOS Y GATITOS</a>
    <a href="otros.php">OTROS TIPOS DE MASCOTAS</a>
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
                <img src="../assets/imagenes/icono-adopcion.png" alt="Adopciones">
            </div>
            <h3>Adopciones</h3>
        </a>
        
        <a href="refugios.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="../assets/imagenes/icono-refugios.png" alt="Refugios">
            </div>
            <h3>Refugios</h3>
        </a>
        
        <a href="rescates.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="../assets/imagenes/icono-rescates.png" alt="Rescates/Historias">
            </div>
            <h3>Rescates</h3>
        </a>
        
        <a href="comunidad.php" class="categoria-card">
            <div class="categoria-icon">
                <img src="../assets/imagenes/icono-comunidad.png" alt="Comunidad">
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
                <img src="../assets/imagenes/icono-donaciones.png" alt="Donaciones">
            </div>
            <h3>Donaciones</h3>
            <p>Ayuda con donaciones para el cuidado de los animales</p>
        </a>

        <a href="campañas.php" class="servicio-card">
            <div class="icono">
                <img src="../assets/imagenes/icono-campañas.png" alt="Campañas">
            </div>
            <h3>Campañas</h3>
            <p>Participa en nuestras campañas de concientización</p>
        </a>
        
        <a href="voluntariado.php" class="servicio-card">
            <div class="icono">
                <img src="../assets/imagenes/icono-voluntariado.png" alt="Voluntariado">
            </div>
            <h3>Voluntariado</h3>
            <p>Únete como voluntario y marca la diferencia</p>
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
        <h2>Veterinarias en Alta Gracia 🐾</h2>
        <p>Encuentra las clínicas veterinarias más cercanas para el cuidado de tu mascota</p>
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
        <h3>MASCOTAS EN ADOPCIÓN</h3>
        <a href="mascotas_en_adopcion.php" class="btn-saber-mas">SABER MÁS</a>
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
            <h3>MHAC - Mis Huellitas a Casa</h3>
        </div>

        <div class="footer-inline-links">
            
            <span class="inline-slogan">
                Un puente entre peluditos y hogares llenos de amor.
            </span> 
            
            <span class="separador">|</span>
            
            <a href="../docs/manual_usuario_MHAC.pdf" download="Manual_Usuario_MHAC.pdf" class="footer-action-link">
                Ayuda
            </a>

            <span class="separador">|</span>
            
            <button onclick="iniciarTour()" class="footer-action-link">
                Manual Interactivo
            </button>
        </div>

        <div class="footer-bottom">
            <p>© 2025 MHAC. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<a href="denuncias.html" 
   class="btn-denuncias-flotante" 
   data-tooltip="Denunciar maltrato animal"
   aria-label="Denunciar maltrato animal">
    <img src="../assets/imagenes/amar.png" alt="Denuncias" class="icono-denuncias">
</a>

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

<!-- ⭐⭐⭐ GOOGLE TRANSLATE (GLOBAL) ⭐⭐⭐ -->
<div id="google_translate_element" style="display:none;"></div>

<script>
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'es',
    includedLanguages: 'en',
    autoDisplay: false
  }, 'google_translate_element');
}
</script>

<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<style>
.goog-te-banner-frame.skiptranslate { display: none !important; }
body { top: 0 !important; }
.goog-te-gadget-simple { display: none !important; }
.goog-te-gadget-icon { display: none !important; }
</style>

<script>
// ⭐ Cambiar idioma desde el botón
function setLanguage(lang) {
    const select = document.querySelector("select.goog-te-combo");
    if (select) {
        select.value = lang;
        select.dispatchEvent(new Event("change"));
    }
}

// ⭐ Botón 🌐 ES ↔ EN
document.getElementById("btnIdioma").addEventListener("click", function () {
    const current = localStorage.getItem("mhac_lang") || "es";
    const next = current === "es" ? "en" : "es";
    localStorage.setItem("mhac_lang", next);
    setLanguage(next);

    document.getElementById("btnIdioma").textContent =
        next === "es" ? "🌐 English" : "🌐 Español";
});

// ⭐ Aplicar idioma guardado (cuando recarga)
window.addEventListener("load", () => {
    const saved = localStorage.getItem("mhac_lang");
    if (saved) {
        setTimeout(() => setLanguage(saved), 500);
        document.getElementById("btnIdioma").textContent =
            saved === "es" ? "🌐 English" : "🌐 Español";
    }
});
</script>
<script>
// 🔧 REPARA el menú plegable (Google Translate Safe)

document.addEventListener("click", function(e) {

    // Si se clickea el botón del menú
    if (e.target.classList.contains("menu-toggle")) {
        const menu = e.target.nextElementSibling;
        
        // Alternar el menú
        menu.style.display = 
            (menu.style.display === "block") ? "none" : "block";

        return; // Importante: no dejar que Google Translate capture esto
    }

    // Cerrar el menú si se clickea afuera
    document.querySelectorAll(".menu-contenido").forEach(menu => {
        if (!menu.contains(e.target) && !e.target.classList.contains("menu-toggle")) {
            menu.style.display = "none";
        }
    });

});
</script>

</body>
</html>
