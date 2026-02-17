document.addEventListener("DOMContentLoaded", function () {
    // Obtenemos la ruta actual de la URL (ej: /DentalSistem/templates/pacientes.php)
    const currentPath = window.location.pathname;

    // Seleccionamos todos los enlaces con la clase 'nav-link'
    const navLinks = document.querySelectorAll(".nav-link");

    // Clases exactas para estado ACTIVO (Azul fuerte, texto claro, sombra)
    const activeClasses = ["bg-blue-800", "text-blue-100", "shadow-xl", "shadow-blue-900/30"];

    // Clases exactas para estado INACTIVO (Azul tenue, sin fondo, con hovers)
    const inactiveClasses = ["text-blue-400", "hover:text-blue-200", "hover:bg-blue-900/40", "hover:shadow-lg", "hover:shadow-blue-900/20"];

    navLinks.forEach(link => {
        const linkHref = link.getAttribute("href");

        // Condición Infalible: ¿La ruta actual contiene el nombre del archivo del enlace?
        // Ejemplo: ¿"/templates/pacientes.php" incluye "pacientes.php"? SI.
        if (linkHref !== "#" && currentPath.includes(linkHref)) {
            // ACTIVAR
            link.classList.add(...activeClasses);
            link.classList.remove(...inactiveClasses);
            // Asegurar que el texto sea brillante
            link.style.color = "#dbeafe"; // text-blue-100 aproximado
        } else {
            // DESACTIVAR
            link.classList.remove(...activeClasses);
            link.classList.add(...inactiveClasses);
            // Restaurar color tenue si es necesario
            link.style.color = ""; 
        }
    });
});