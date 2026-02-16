document.addEventListener("DOMContentLoaded", function () {
    // Obtenemos la ruta completa de la URL actual (ej: /proyecto/templates/citas.php)
    const currentPath = window.location.pathname;

    // Seleccionamos todos los enlaces que tienen la clase 'nav-link'
    const navLinks = document.querySelectorAll(".nav-link");

    // Definimos las clases exactas de Tailwind
    const activeClasses = ["bg-blue-800", "text-blue-100", "rounded-xl", "shadow-xl", "shadow-blue-900/30", "font-semibold"];
    const inactiveClasses = ["text-blue-400", "font-semibold", "hover:text-blue-200", "hover:bg-blue-900/40", "hover:shadow-lg", "hover:shadow-blue-900/20"];

    navLinks.forEach(link => {
        const linkHref = link.getAttribute("href");

        // Lógica de detección:
        // 1. Si la URL termina exactamente en el href (ej: termina en 'templates/citas.php')
        // 2. Si estamos en la raíz y el link es index.php
        const isActive = currentPath.endsWith(linkHref) || 
                         (currentPath.endsWith("/") && linkHref === "index.php");

        if (isActive) {
            // Aplicamos diseño ACTIVO
            link.classList.add(...activeClasses);
            link.classList.remove(...inactiveClasses);
        } else {
            // Aplicamos diseño INACTIVO
            link.classList.remove(...activeClasses);
            link.classList.add(...inactiveClasses);
        }
    });
});