// ⭐ GOOGLE TRANSLATE — GLOBAL SYSTEM (MHAC)

// Crear elemento oculto para Google Translate
(function () {
    let div = document.createElement("div");
    div.id = "google_translate_element";
    div.style.display = "none";
    document.body.appendChild(div);
})();

// Inicializar Google Translate
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'es',
        includedLanguages: 'en',
        autoDisplay: false
    }, 'google_translate_element');
}

// Cargar script de Google
(function () {
    let gt = document.createElement("script");
    gt.src = "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    document.body.appendChild(gt);
})();

// CSS para ocultar barra fea
(function () {
    const css = `
        .goog-te-banner-frame.skiptranslate { display: none !important; }
        body { top: 0 !important; }
        .goog-te-gadget-simple { display: none !important; }
        .goog-te-gadget-icon { display: none !important; }
    `;
    const style = document.createElement("style");
    style.innerHTML = css;
    document.head.appendChild(style);
})();

// Cambiar idioma
function setLanguage(lang) {
    const select = document.querySelector("select.goog-te-combo");
    if (select) {
        select.value = lang;
        select.dispatchEvent(new Event("change"));
    }
}

// Aplicar idioma guardado al cargar
window.addEventListener("load", () => {
    const saved = localStorage.getItem("mhac_lang");
    if (saved) {
        setTimeout(() => setLanguage(saved), 600);

        const btn = document.getElementById("btnIdioma");
        if (btn) {
            btn.textContent = saved === "es" ? "🌐 English" : "🌐 Español";
        }
    }
});

// Conectar el botón automáticamente
document.addEventListener("click", (e) => {
    if (e.target.id === "btnIdioma") {

        const current = localStorage.getItem("mhac_lang") || "es";
        const next = current === "es" ? "en" : "es";

        localStorage.setItem("mhac_lang", next);
        setLanguage(next);

        e.target.textContent =
            next === "es" ? "🌐 English" : "🌐 Español";
    }
});
