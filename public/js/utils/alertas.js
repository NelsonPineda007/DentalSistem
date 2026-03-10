// public/js/utils/alertas.js

(function injectAlertaStyles() {
    if (document.getElementById('dentista-alerta-styles')) return;
    const style = document.createElement('style');
    style.id = 'dentista-alerta-styles';
    style.textContent = `
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --da-font: 'Plus Jakarta Sans', -apple-system, sans-serif;
        }

        /* ══════════════════════════════════════════
           1. FIX DEL FLASH (El salto de difuminado)
        ══════════════════════════════════════════ */
        body.swal2-toast-shown .swal2-container,
        .swal2-container.swal2-top-end {
            background-color: transparent !important;
            backdrop-filter: none !important;
            pointer-events: none !important;
        }

        body:not(.swal2-toast-shown) .swal2-container.swal2-backdrop-show {
            background: rgba(15, 23, 42, 0.45) !important;
            backdrop-filter: blur(4px) !important;
        }

        /* ══════════════════════════════════════════
           2. TOAST (Diseño Blanco / Minimalista)
        ══════════════════════════════════════════ */
        .da-toast {
            font-family: var(--da-font) !important;
            pointer-events: all !important;
            width: 320px !important;
            max-width: 90vw !important;
            margin: 16px 16px 0 0 !important;
            padding: 16px 20px !important;
            border-radius: 16px !important;
            backdrop-filter: blur(10px) !important; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            /* Fondo blanco translúcido para todos */
            background-color: rgba(255, 255, 255, 0.98) !important; 
        }

        /* Solo cambiamos un borde muy sutil según el tipo de alerta */
        .da-toast.da-exito { border: 1px solid rgba(16, 185, 129, 0.3) !important; }
        .da-toast.da-error { border: 1px solid rgba(239, 68, 68, 0.3) !important; }
        .da-toast.da-advertencia { border: 1px solid rgba(217, 119, 6, 0.3) !important; }

        /* Textos oscuros y elegantes */
        .da-toast .swal2-title { 
            color: #0f172a !important; /* Gris muy oscuro */
            font-size: 0.9rem !important; 
            margin: 0 0 4px 0 !important; 
            font-weight: 700 !important; 
            text-align: left !important;
            display: block !important;
        }
        .da-toast .swal2-html-container { 
            color: #475569 !important; /* Gris pizarra para la descripción */
            font-size: 0.8rem !important; 
            margin: 0 !important; 
            font-weight: 500 !important; 
            text-align: left !important;
            display: block !important;
        }

        /* Ícono del Toast (es lo único que lleva color fuerte) */
        .da-toast .swal2-icon { 
            width: 32px !important; 
            height: 32px !important; 
            margin: 0 12px 0 0 !important; 
            border: 2px solid currentColor !important; 
        }
        .da-toast.da-exito .swal2-icon { color: #10b981 !important; }
        .da-toast.da-error .swal2-icon { color: #ef4444 !important; }
        .da-toast.da-advertencia .swal2-icon { color: #d97706 !important; }

        /* ══════════════════════════════════════════
           3. MODAL DE CONFIRMACIÓN (Intacto)
        ══════════════════════════════════════════ */
        .da-modal {
            font-family: var(--da-font) !important;
            border-radius: 24px !important;
            overflow: hidden !important;
            position: relative !important;
            padding: 24px 0 !important;
            width: 90% !important;
            max-width: 400px !important;
            border: none !important;
            background: white !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }

        .da-modal::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 6px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        }
        .da-modal.da-danger::before { background: linear-gradient(90deg, #ef4444, #f59e0b); }

        .da-modal .swal2-icon { margin: 10px auto 16px !important; border-width: 2.5px !important; width: 64px !important; height: 64px !important; }
        .da-modal .swal2-icon.swal2-question { color: #2563eb !important; border-color: #2563eb !important; }
        .da-modal .swal2-icon.swal2-warning { color: #d97706 !important; border-color: #d97706 !important; }

        .da-modal .swal2-title { font-size: 1.3rem !important; color: #0f172a !important; padding: 0 24px !important; font-weight: 800 !important; }
        .da-modal .swal2-html-container { font-size: 0.9rem !important; color: #64748b !important; padding: 10px 24px 0 !important; font-weight: 500 !important; line-height: 1.5 !important;}

        .da-modal .swal2-actions { margin: 24px 0 0 0 !important; padding: 0 24px !important; gap: 12px !important; display: flex !important; width: 100% !important; box-sizing: border-box !important;}
        
        .da-btn {
            font-family: var(--da-font) !important;
            flex: 1 !important;
            margin: 0 !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            padding: 12px 0 !important;
            border: none !important;
            cursor: pointer !important;
            transition: transform 0.1s ease, box-shadow 0.1s ease !important;
        }
        .da-btn:hover { transform: translateY(-2px) !important; }
        
        .da-btn-cancel { background-color: #f1f5f9 !important; color: #475569 !important; }
        .da-btn-cancel:hover { background-color: #e2e8f0 !important; }

        .da-btn-confirm { background: linear-gradient(135deg, #2563eb, #1d4ed8) !important; color: white !important; box-shadow: 0 4px 12px rgba(37,99,235,0.3) !important; }
        .da-btn-confirm:hover { box-shadow: 0 6px 16px rgba(37,99,235,0.4) !important; }

        .da-btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626) !important; color: white !important; box-shadow: 0 4px 12px rgba(239,68,68,0.3) !important; }
        .da-btn-danger:hover { box-shadow: 0 6px 16px rgba(239,68,68,0.4) !important; }

        @media (max-width: 380px) { .da-modal .swal2-actions { flex-direction: column-reverse !important; } }
    `;
    document.head.appendChild(style);
})();

function _toast(cssExtra) {
    return Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        scrollbarPadding: false,
        heightAuto: false,
        customClass: { popup: `da-toast ${cssExtra}` }
    });
}

window.Alerta = {
    exito: (titulo, mensaje = '') => _toast('da-exito').fire({ icon: 'success', title: titulo, text: mensaje }),
    error: (titulo, mensaje = '') => _toast('da-error').fire({ icon: 'error', title: titulo, text: mensaje }),
    advertencia: (titulo, mensaje = '') => _toast('da-advertencia').fire({ icon: 'warning', title: titulo, text: mensaje }),
    info: (titulo, mensaje = '') => _toast('da-info').fire({ icon: 'info', title: titulo, text: mensaje }),
    
    confirmar: async (titulo = '¿Estás seguro?', texto = '', confirmText = 'Sí, proceder', cancelText = 'Cancelar') => {
        const result = await Swal.fire({
            title: titulo, text: texto, icon: 'question', showCancelButton: true, confirmButtonText: confirmText, cancelButtonText: cancelText, reverseButtons: true, focusCancel: true,
            scrollbarPadding: false, heightAuto: false,
            customClass: { popup: 'da-modal', confirmButton: 'da-btn da-btn-confirm', cancelButton: 'da-btn da-btn-cancel' }, buttonsStyling: false
        });
        return result.isConfirmed;
    },

    eliminar: async (titulo = '¿Eliminar este registro?', texto = 'Esta acción no se puede deshacer.', confirmText = 'Sí, archivar', cancelText = 'Cancelar') => {
        const result = await Swal.fire({
            title: titulo, text: texto, icon: 'warning', showCancelButton: true, confirmButtonText: confirmText, cancelButtonText: cancelText, reverseButtons: true, focusCancel: true,
            scrollbarPadding: false, heightAuto: false,
            customClass: { popup: 'da-modal da-danger', confirmButton: 'da-btn da-btn-danger', cancelButton: 'da-btn da-btn-cancel' }, buttonsStyling: false
        });
        return result.isConfirmed;
    }
};