document.addEventListener("DOMContentLoaded", function() {

    // --- 1. CONFIGURACIÓN UNIVERSAL PARA SPARKLINES ---
    const commonSparklineOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false }, 
            tooltip: { enabled: false } 
        },
        scales: { 
            x: { display: false }, 
            y: { display: false } 
        },
        elements: { 
            point: { radius: 0 },
            line: { tension: 0.4, borderWidth: 2 } 
        },
        layout: { padding: 0 }
    };

    // Función interna para crear los degradados
    function getGradient(ctx, color) {
        if (!ctx) return color; 
        const gradient = ctx.createLinearGradient(0, 0, 0, 60);
        gradient.addColorStop(0, color);
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
        return gradient;
    }

    // FUNCIÓN GLOBAL PARA DIBUJAR CUALQUIER SPARKLINE
    // Así la reutilizamos en Dashboard, Pacientes, Tratamientos, etc.
    window.drawSparkline = function(canvasId, dataArray, hexColor, rgbaGradientColor) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        // Si ya hay una gráfica en este canvas, la destruimos para redibujar
        if (window[canvasId + 'Instance']) {
            window[canvasId + 'Instance'].destroy();
        }

        const ctx = canvas.getContext('2d');
        window[canvasId + 'Instance'] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataArray.map((_, index) => index), // Labels invisibles
                datasets: [{
                    data: dataArray,
                    borderColor: hexColor,
                    fill: true,
                    backgroundColor: getGradient(ctx, rgbaGradientColor)
                }]
            },
            options: commonSparklineOptions
        });
    };

    // --- 2. INICIALIZACIÓN ESPECÍFICA PARA EL DASHBOARD ---
    // (Solo se ejecuta si estamos en la vista del Dashboard)
    
    if (document.getElementById('chartMovimiento')) {
        
        const dataValues = {
            citas: 20, noAsistidas: 1, canceladas: 3,
            tasa: 10, tasaPorcentaje: 100
        };

        // Inyección de números
        if(document.getElementById('numCitas')) document.getElementById('numCitas').textContent = dataValues.citas;
        if(document.getElementById('numNoAsistidas')) document.getElementById('numNoAsistidas').textContent = dataValues.noAsistidas;
        if(document.getElementById('numCanceladas')) document.getElementById('numCanceladas').textContent = dataValues.canceladas;
        if(document.getElementById('numTasa')) document.getElementById('numTasa').textContent = dataValues.tasa;
        if(document.getElementById('percentTasa')) document.getElementById('percentTasa').textContent = dataValues.tasaPorcentaje + '%';

        // Dibujar Sparklines del Dashboard usando nuestra función universal
        drawSparkline('chartCitas', [5, 12, 8, 15, 10, dataValues.citas], '#047857', 'rgba(4, 120, 87, 0.2)');
        drawSparkline('chartNoAsistidas', [2, 8, 4, 10, 5, dataValues.noAsistidas], '#b45309', 'rgba(180, 83, 9, 0.2)');
        drawSparkline('chartCanceladas', [4, 6, 12, 8, 10, dataValues.canceladas], '#be123c', 'rgba(190, 18, 60, 0.2)');

        // Dona Tasa (Azul) - Específica del Dashboard
        const canvasTasa = document.getElementById('chartTasa');
        if (canvasTasa) {
            new Chart(canvasTasa, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [dataValues.tasaPorcentaje, 100 - dataValues.tasaPorcentaje],
                        backgroundColor: ['#1e40af', '#f1f5f9'],
                        borderWidth: 0,
                    }]
                },
                options: { cutout: '80%', maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        // Gráfica Grande: Movimiento
        const canvasMovimiento = document.getElementById('chartMovimiento');
        if (canvasMovimiento) {
            const ctxMov = canvasMovimiento.getContext('2d');
            const gradientBig = ctxMov.createLinearGradient(0, 0, 0, 300);
            gradientBig.addColorStop(0, 'rgba(30, 64, 175, 0.4)');
            gradientBig.addColorStop(1, 'rgba(255, 255, 255, 0.0)');

            new Chart(ctxMov, {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                    datasets: [{ label: 'Citas realizadas', data: [6, 9, 7, 5, 8, 7, 4], borderColor: '#1e40af', backgroundColor: gradientBig, borderWidth: 3, fill: true, tension: 0.4 }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }

        // Gráfica Dona: Tratamientos (La que está en el Dashboard)
        const canvasTratamientosDash = document.getElementById('chartTratamientos');
        if (canvasTratamientosDash) {
            new Chart(canvasTratamientosDash, {
                type: 'doughnut',
                data: {
                    labels: ['Extracción', 'Limpieza', 'Caries', 'Muelas'],
                    datasets: [{ data: [35, 20, 15, 30], backgroundColor: ['#2d9596', '#3b82f6', '#facc15', '#fb923c'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
            });
        }
    }
});