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

        body.swal2-toast-shown .swal2-container,
        .swal2-container.swal2-top-end,
        .swal2-container.swal2-bottom-end { 
            background-color: transparent !important;
            backdrop-filter: none !important;
            pointer-events: none !important;
        }

        .swal2-container.swal2-bottom-end {
            z-index: 999999 !important; 
            padding: 0 24px 24px 0 !important; 
        }

        body:not(.swal2-toast-shown) .swal2-container.swal2-backdrop-show {
            background: rgba(15, 23, 42, 0.45) !important;
            backdrop-filter: blur(4px) !important;
        }

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
            background-color: rgba(255, 255, 255, 0.98) !important; 
        }

        .da-toast.da-exito { border: 1px solid rgba(16, 185, 129, 0.3) !important; }
        .da-toast.da-error { border: 1px solid rgba(239, 68, 68, 0.3) !important; }
        .da-toast.da-advertencia { border: 1px solid rgba(217, 119, 6, 0.3) !important; }
        .da-toast.da-info { border: 1px solid rgba(59, 130, 246, 0.3) !important; }

        .da-toast .swal2-title { color: #0f172a !important; font-size: 0.9rem !important; margin: 0 0 4px 0 !important; font-weight: 700 !important; text-align: left !important; }
        .da-toast .swal2-html-container { color: #475569 !important; font-size: 0.8rem !important; margin: 0 !important; font-weight: 500 !important; text-align: left !important; }
        .da-toast .swal2-icon { width: 32px !important; height: 32px !important; margin: 0 12px 0 0 !important; border: 2px solid currentColor !important; }
        
        .da-toast.da-exito .swal2-icon { color: #10b981 !important; }
        .da-toast.da-error .swal2-icon { color: #ef4444 !important; }
        .da-toast.da-advertencia .swal2-icon { color: #d97706 !important; }
        .da-toast.da-info .swal2-icon { color: #3b82f6 !important; }

        .da-noti {
            box-sizing: border-box !important;
            font-family: var(--da-font) !important;
            pointer-events: all !important;
            width: 360px !important; 
            max-width: 100% !important;
            margin: 0 !important; 
            padding: 16px 20px !important;
            border-radius: 12px !important;
            background-color: #ffffff !important; 
            box-shadow: none !important; /* Sombra eliminada */
            border: 1px solid rgba(59, 130, 246, 0.2) !important;
            border-left: 6px solid #3b82f6 !important; 
        }
        
        .da-noti.da-noti-alerta { 
            border-left-color: #f59e0b !important; 
            border-color: rgba(245, 158, 11, 0.2) !important;
        }

        .da-noti .swal2-title { 
            color: #1e293b !important; 
            font-size: 1rem !important; 
            margin: 0 0 6px 0 !important; 
            font-weight: 800 !important; 
            text-align: left !important;
        }
        
        .da-noti .swal2-html-container { 
            color: #475569 !important; 
            font-size: 0.9rem !important; 
            margin: 0 !important; 
            font-weight: 500 !important; 
            text-align: left !important;
            line-height: 1.4 !important;
        }

        .da-noti .swal2-icon { 
            width: 36px !important; 
            height: 36px !important; 
            margin: 0 16px 0 0 !important; 
            border: none !important; 
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .da-noti:not(.da-noti-alerta) .swal2-icon { color: #3b82f6 !important; background: rgba(59, 130, 246, 0.1) !important; }
        .da-noti.da-noti-alerta .swal2-icon { color: #f59e0b !important; background: rgba(245, 158, 11, 0.1) !important; }

        .da-noti .swal2-timer-progress-bar { background: rgba(59, 130, 246, 0.3) !important; height: 3px !important; }
        .da-noti.da-noti-alerta .swal2-timer-progress-bar { background: rgba(245, 158, 11, 0.3) !important; }

        .da-modal { font-family: var(--da-font) !important; border-radius: 28px !important; overflow: hidden !important; position: relative !important; padding: 40px 0 32px 0 !important; width: 90% !important; max-width: 540px !important; border: none !important; background: white !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important; }
        .da-modal::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 6px; background: linear-gradient(90deg, #3b82f6, #8b5cf6); }
        .da-modal.da-danger::before { background: linear-gradient(90deg, #ef4444, #f59e0b); }
        .da-modal.da-info-clean::before { background: #3b82f6; } 
        .da-modal .swal2-icon { margin: 0 auto 24px !important; border-width: 3px !important; width: 84px !important; height: 84px !important; }
        .da-modal .swal2-icon.swal2-question { color: #2563eb !important; border-color: #2563eb !important; }
        .da-modal .swal2-icon.swal2-warning { color: #d97706 !important; border-color: #d97706 !important; }
        .da-modal .swal2-icon.swal2-info { color: #3ea5f6 !important; border-color: #3ea5f6 !important; }
        .da-modal .swal2-icon.swal2-success { color: #10b981 !important; border-color: #10b981 !important; }
        .da-modal .swal2-title { font-size: 1.6rem !important; color: #1e293b !important; padding: 0 32px !important; font-weight: 800 !important; }
        .da-modal .swal2-html-container { font-size: 1.05rem !important; color: #475569 !important; padding: 20px 40px 0 !important; font-weight: 500 !important; line-height: 1.6 !important; }
        .da-modal .swal2-actions { margin: 32px 0 0 0 !important; padding: 0 40px !important; gap: 16px !important; display: flex !important; width: 100% !important; box-sizing: border-box !important; }
        
        .da-btn { font-family: var(--da-font) !important; flex: 1 !important; margin: 0 !important; border-radius: 14px !important; font-weight: 700 !important; font-size: 1rem !important; padding: 14px 20px !important; border: none !important; cursor: pointer !important; transition: transform 0.1s ease, box-shadow 0.1s ease, background-color 0.2s ease !important; text-align: center !important; line-height: 1.2 !important; }
        .da-btn:hover { transform: translateY(-2px) !important; }
        .da-btn-cancel { background-color: #f1f5f9 !important; color: #475569 !important; }
        .da-btn-cancel:hover { background-color: #e2e8f0 !important; }
        .da-btn-confirm { background: linear-gradient(135deg, #2563eb, #1d4ed8) !important; color: white !important; box-shadow: 0 4px 12px rgba(37,99,235,0.3) !important; }
        .da-btn-confirm:hover { box-shadow: 0 6px 16px rgba(37,99,235,0.4) !important; }
        .da-btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626) !important; color: white !important; box-shadow: 0 4px 12px rgba(239,68,68,0.3) !important; }
        .da-btn-danger:hover { box-shadow: 0 6px 16px rgba(239,68,68,0.4) !important; }
        .da-btn-secondary { background: #64748b !important; color: white !important; box-shadow: 0 4px 12px rgba(100,116,139,0.3) !important; }
        .da-btn-secondary:hover { background: #475569 !important; box-shadow: 0 6px 16px rgba(100,116,139,0.4) !important; }

        @media (max-width: 480px) { 
            .da-modal .swal2-actions { flex-direction: column-reverse !important; padding: 0 24px !important; } 
            .da-btn { width: 100% !important; flex: none !important;} 
            .da-modal .swal2-title { font-size: 1.4rem !important; }
            .da-modal .swal2-html-container { padding: 16px 24px 0 !important; }
        }
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
        customClass: { popup: `da-toast ${cssExtra}` }
    });
}

function _modalBase(cssExtra) {
    return Swal.mixin({
        scrollbarPadding: false, 
        heightAuto: false, 
        buttonsStyling: false,
        reverseButtons: true,
        focusCancel: true,
        customClass: { 
            popup: `da-modal ${cssExtra}`, 
            confirmButton: 'da-btn da-btn-confirm', 
            cancelButton: 'da-btn da-btn-cancel' 
        }
    });
}

window.Alerta = {
    exito: (titulo, mensaje = '') => _toast('da-exito').fire({ icon: 'success', title: titulo, text: mensaje }),
    error: (titulo, mensaje = '') => _toast('da-error').fire({ icon: 'error', title: titulo, text: mensaje }),
    advertencia: (titulo, mensaje = '') => _toast('da-advertencia').fire({ icon: 'warning', title: titulo, text: mensaje }),
    info: (titulo, mensaje = '') => _toast('da-info').fire({ icon: 'info', title: titulo, text: mensaje }),
    
    modalExito: (titulo, htmlText = '') => _modalBase('').fire({ icon: 'success', title: titulo, html: htmlText, showConfirmButton: true, confirmButtonText: 'Entendido' }),
    modalError: (titulo, htmlText = '') => _modalBase('da-danger').fire({ icon: 'error', title: titulo, htmlText: htmlText, showConfirmButton: true, confirmButtonText: 'Cerrar', customClass: { confirmButton: 'da-btn da-btn-danger' } }),
    modalInfo: (titulo, htmlText = '') => _modalBase('da-info-clean').fire({ icon: 'info', title: titulo, html: htmlText, showConfirmButton: true, confirmButtonText: 'De acuerdo' }),

    confirmar: async (titulo = '¿Estás seguro?', texto = '', confirmText = 'Sí, proceder', cancelText = 'Cancelar') => {
        const result = await _modalBase('').fire({
            title: titulo, html: texto, icon: 'question', showCancelButton: true, confirmButtonText: confirmText, cancelButtonText: cancelText
        });
        return result.isConfirmed;
    },

    eliminar: async (titulo = '¿Eliminar este registro?', texto = 'Esta acción no se puede deshacer.', confirmText = 'Sí, archivar', cancelText = 'Cancelar') => {
        const result = await _modalBase('da-danger').fire({
            title: titulo, html: texto, icon: 'warning', showCancelButton: true, confirmButtonText: confirmText, cancelButtonText: cancelText,
            customClass: { popup: 'da-modal da-danger', confirmButton: 'da-btn da-btn-danger', cancelButton: 'da-btn da-btn-cancel' }
        });
        return result.isConfirmed;
    },

    eleccion: async (titulo, htmlText, btnPrimario = 'Sí', btnSecundario = 'No', icono = 'info') => {
        const result = await Swal.fire({
            title: titulo,
            html: htmlText,
            icon: icono,
            showDenyButton: true,      
            showCancelButton: false,   
            confirmButtonText: btnPrimario,
            denyButtonText: btnSecundario,
            reverseButtons: false,     
            scrollbarPadding: false,
            heightAuto: false, 
            buttonsStyling: false,
            customClass: { 
                popup: 'da-modal da-info-clean', 
                confirmButton: 'da-btn da-btn-confirm', 
                denyButton: 'da-btn da-btn-secondary' 
            }
        });
        return result.isConfirmed; 
    },

    notificarCita: (titulo, mensaje) => {
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'info',
            title: titulo,
            text: mensaje,
            showConfirmButton: false,
            timer: 5000, 
            timerProgressBar: true,
            customClass: { popup: 'da-noti' }
        });
    },

    notificarRecordatorio: (titulo, mensaje) => {
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'warning',
            title: titulo,
            text: mensaje,
            showConfirmButton: false,
            timer: 5000, 
            timerProgressBar: true,
            customClass: { popup: 'da-noti da-noti-alerta' }
        });
    }
};