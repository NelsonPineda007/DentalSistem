// static/js/utils/api.js

/**
 * Cliente HTTP Global para comunicarse con Laravel
 * Maneja automáticamente los tokens CSRF, headers y errores.
 */
const API = {
    // Busca el token de seguridad en la cabecera
    getToken: () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    },

    // Motor principal de peticiones
    request: async (url, options = {}) => {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': API.getToken(), // Se inyecta automáticamente
            ...options.headers
        };

        const config = { ...options, headers };

        try {
            const respuesta = await fetch(url, config);
            
            // Si el servidor devuelve un error, lo atrapamos aquí globalmente
            if (!respuesta.ok) {
                // Error 419 es específico de Laravel cuando el Token CSRF expira
                if (respuesta.status === 419) {
                    alert("Tu sesión ha expirado por inactividad. La página se recargará.");
                    window.location.reload();
                    return;
                }
                
                const errorData = await respuesta.json().catch(() => ({}));
                throw { status: respuesta.status, data: errorData };
            }

            return await respuesta.json();

        } catch (error) {
            // Se propaga el error para que el controlador lo maneje si quiere
            throw error; 
        }
    },

    // Métodos limpios para usar en el resto del sistema
    get: (url) => API.request(url, { method: 'GET' }),
    
    post: (url, body) => API.request(url, { method: 'POST', body: JSON.stringify(body) }),
    
    put: (url, body) => API.request(url, { method: 'PUT', body: JSON.stringify(body) }),
    
    delete: (url) => API.request(url, { method: 'DELETE' })
};

// Lo hacemos global para todo el sistema
window.API = API;