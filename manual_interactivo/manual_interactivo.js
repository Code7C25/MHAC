function iniciarTour() {
    introJs().setOptions({
        nextLabel: 'Siguiente >',
        prevLabel: '< Anterior',
        doneLabel: '¡Entendido!',
        skipLabel: 'Saltar',
        showStepNumbers: true,
        exitOnOverlayClick: false, // Evita que se cierre si dan click afuera sin querer
        steps: [
            {
                title: "👋 Bienvenid@ a MHAC",
                intro: "Te daremos un recorrido rápido para que aprendas a usar la plataforma y ayudes a más peluditos."
            },
            {
                element: document.querySelector('.user-session'),
                intro: "Aquí puedes <b>iniciar sesión</b>, registrarte o cambiar el idioma de la página. 🌐"
            },
            {
                element: document.querySelector('.search-container'),
                intro: "🔍 <b>¿Buscas un amigo específico?</b> Usa este buscador para filtrar por tipo (perro, gato) o raza."
            },
            {
                element: document.querySelector('.categorias-principales'),
                intro: "Accede rápido a las secciones principales: Adopciones, Refugios aliados, Historias de Rescate y Comunidad."
            },
            {
                element: document.querySelector('.servicios-adicionales'),
                intro: "💡 <b>¡Tu ayuda es vital!</b> Aquí puedes donar, ver campañas activas o inscribirte como voluntario."
            },
            {
                element: document.querySelector('.aprende-cuidarlos'),
                intro: "Mira nuestro video institucional y consejos sobre tenencia responsable."
            },
            {
                element: document.querySelector('.mapa-veterinarias'),
                intro: "🗺️ Encuentra las <b>veterinarias</b> más cercanas en Alta Gracia en este mapa interactivo."
            },
            {
                element: document.querySelector('.novedades-carrusel'),
                intro: "Aquí verás a las mascotas que acaban de llegar y buscan hogar urgentemente."
            },
            {
                element: document.querySelector('.btn-denuncias-flotante'),
                intro: "🚨 <b>Botón de Emergencia:</b> Si ves un caso de maltrato, usa este botón para acceder a los contactos de denuncias."
            },
            {
                element: document.querySelector('.footer-action-link'), // El botón de ayuda del footer
                intro: "Si necesitas más detalles, aquí puedes descargar el <b>Manual de Usuario en PDF</b>."
            },
            {
                title: "¡Todo listo!",
                intro: "Ya conoces lo esencial. ¡Gracias por ser parte de Mis Huellitas a Casa!"
            }
        ]
    }).start();
}