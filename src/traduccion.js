// ------------------------
// Sistema de traducción con persistencia en localStorage
// ------------------------

const btnIdioma = document.getElementById('btnIdioma');
let idiomaActual = 'es';

// Función que traduce la página
function traducirPagina(idioma) {
  document.querySelectorAll('[data-es][data-en]').forEach(elemento => {
    const texto = idioma === 'es' ? elemento.dataset.es : elemento.dataset.en;
    if (elemento.tagName === 'INPUT' || elemento.tagName === 'TEXTAREA') {
      elemento.placeholder = texto;
    } else {
      elemento.textContent = texto;
    }
  });

  document.querySelectorAll('[data-es-placeholder][data-en-placeholder]').forEach(input => {
    input.placeholder = idioma === 'es' ? input.dataset.esPlaceholder : input.dataset.enPlaceholder;
  });

  if (btnIdioma) btnIdioma.textContent = idioma === 'es' ? '🌐 English' : '🌐 Español';
}

// Aplicar idioma al cargar la página
window.addEventListener('DOMContentLoaded', () => {
  const idiomaGuardado = localStorage.getItem('idioma') || 'es';
  idiomaActual = idiomaGuardado;
  traducirPagina(idiomaActual);
});

// Cambiar idioma al hacer click en el botón (solo index.php tiene el botón)
if (btnIdioma) {
  btnIdioma.addEventListener('click', () => {
    idiomaActual = idiomaActual === 'es' ? 'en' : 'es';
    localStorage.setItem('idioma', idiomaActual);
    traducirPagina(idiomaActual);
  });
}

// ------------------------
// Menú usuario toggle
// ------------------------
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

// ------------------------
// Slider simple
// ------------------------
let slides = document.querySelectorAll('.slider-novedades .slide');
let currentSlide = 0;
setInterval(() => {
  slides[currentSlide].classList.remove('activo');
  currentSlide = (currentSlide + 1) % slides.length;
  slides[currentSlide].classList.add('activo');
}, 4000);

// ------------------------
// Hero-wag fondo rotativo
// ------------------------
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

// ------------------------
// Popup consejo
// ------------------------
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
