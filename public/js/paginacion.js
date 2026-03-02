document.addEventListener("DOMContentLoaded", function () {
    // Obtenemos solo la ruta actual (ej: /dashboard)
    const currentPath = window.location.pathname;

    // Seleccionamos todos los enlaces con la clase 'nav-link'
    const navLinks = document.querySelectorAll(".nav-link");

    // Clases exactas
    const activeClasses = ["bg-blue-800", "text-blue-100", "shadow-xl", "shadow-blue-900/30"];
    const inactiveClasses = ["text-blue-400", "hover:text-blue-200", "hover:bg-blue-900/40", "hover:shadow-lg", "hover:shadow-blue-900/20"];

    navLinks.forEach(link => {
        // En Laravel, extraemos de forma segura el "pathname" del enlace
        // Esto convierte "http://localhost/dashboard" en simplemente "/dashboard"
        const linkPath = new URL(link.href).pathname;

        // Condición Infalible: ¿La ruta actual es exactamente igual a la ruta del enlace?
        if (link.getAttribute("href") !== "#" && currentPath === linkPath) {
            // ACTIVAR
            link.classList.add(...activeClasses);
            link.classList.remove(...inactiveClasses);
            link.style.color = "#dbeafe";
        } else {
            // DESACTIVAR
            link.classList.remove(...activeClasses);
            link.classList.add(...inactiveClasses);
            link.style.color = ""; 
        }
    });
});