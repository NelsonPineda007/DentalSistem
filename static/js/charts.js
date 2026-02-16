document.addEventListener("DOMContentLoaded", function() {

    // --- VALORES DE DATOS (Centralizados aquí) ---
    const dataValues = {
        citas: 20,
        noAsistidas: 1,
        canceladas: 3,
        tasa: 10,
        tasaPorcentaje: 100
    };

    // --- INYECCIÓN DE NÚMEROS EN HTML ---
    if(document.getElementById('numCitas')) document.getElementById('numCitas').textContent = dataValues.citas;
    if(document.getElementById('numNoAsistidas')) document.getElementById('numNoAsistidas').textContent = dataValues.noAsistidas;
    if(document.getElementById('numCanceladas')) document.getElementById('numCanceladas').textContent = dataValues.canceladas;
    if(document.getElementById('numTasa')) document.getElementById('numTasa').textContent = dataValues.tasa;
    if(document.getElementById('percentTasa')) document.getElementById('percentTasa').textContent = dataValues.tasaPorcentaje + '%';


    // 1. Opciones comunes para las sparklines
    const commonOptions = {
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
        }
    };

    // 2. Función interna para crear los degradados
    function getGradient(ctx, color) {
        if (!ctx) return color; 
        const gradient = ctx.createLinearGradient(0, 0, 0, 60);
        gradient.addColorStop(0, color);
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
        return gradient;
    }

    // --- INICIALIZACIÓN DE GRÁFICAS ---

    // Gráfica 1: Citas (Esmeralda Oscuro)
    const canvasCitas = document.getElementById('chartCitas');
    if (canvasCitas) {
        const ctx1 = canvasCitas.getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: [1, 2, 3, 4, 5, 6],
                datasets: [{
                    data: [5, 12, 8, 15, 10, dataValues.citas], // El último dato es el valor central
                    borderColor: '#047857',
                    fill: true,
                    backgroundColor: getGradient(ctx1, 'rgba(4, 120, 87, 0.2)')
                }]
            },
            options: commonOptions
        });
    }

    // Gráfica 2: No Asistidas (Ámbar Oscuro)
    const canvasNoAsistidas = document.getElementById('chartNoAsistidas');
    if (canvasNoAsistidas) {
        const ctx2 = canvasNoAsistidas.getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: [1, 2, 3, 4, 5, 6],
                datasets: [{
                    data: [2, 8, 4, 10, 5, dataValues.noAsistidas],
                    borderColor: '#b45309',
                    fill: true,
                    backgroundColor: getGradient(ctx2, 'rgba(180, 83, 9, 0.2)')
                }]
            },
            options: commonOptions
        });
    }

    // Gráfica 3: Canceladas (Rosa/Rojo Oscuro)
    const canvasCanceladas = document.getElementById('chartCanceladas');
    if (canvasCanceladas) {
        const ctx3 = canvasCanceladas.getContext('2d');
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: [1, 2, 3, 4, 5, 6],
                datasets: [{
                    data: [4, 6, 12, 8, 10, dataValues.canceladas],
                    borderColor: '#be123c',
                    fill: true,
                    backgroundColor: getGradient(ctx3, 'rgba(190, 18, 60, 0.2)')
                }]
            },
            options: commonOptions
        });
    }

    // Gráfica 4: Dona Tasa (Azul)
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
            options: { 
                cutout: '80%',
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } } 
            }
        });
    }

    // 5. Gráfica Grande: Movimiento
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
                datasets: [{
                    label: 'Citas realizadas',
                    data: [6, 9, 7, 5, 8, 7, 4],
                    borderColor: '#1e40af',
                    backgroundColor: gradientBig,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // 6. Gráfica Dona: Tratamientos
    const canvasTratamientos = document.getElementById('chartTratamientos');
    if (canvasTratamientos) {
        new Chart(canvasTratamientos, {
            type: 'doughnut',
            data: {
                labels: ['Extracción', 'Limpieza', 'Caries', 'Muelas'],
                datasets: [{
                    data: [35, 20, 15, 30],
                    backgroundColor: ['#2d9596', '#3b82f6', '#facc15', '#fb923c'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });
    }
});