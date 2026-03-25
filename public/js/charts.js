document.addEventListener("DOMContentLoaded", async function() {

    const commonSparklineOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        scales: { x: { display: false }, y: { display: false } },
        elements: { point: { radius: 0 }, line: { tension: 0.4, borderWidth: 2 } },
        layout: { padding: 0 }
    };

    function getGradient(ctx, color) {
        if (!ctx) return color; 
        const gradient = ctx.createLinearGradient(0, 0, 0, 60);
        gradient.addColorStop(0, color);
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
        return gradient;
    }

    window.drawSparkline = function(canvasId, dataArray, hexColor, rgbaGradientColor) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        if (window[canvasId + 'Instance']) {
            window[canvasId + 'Instance'].destroy();
        }

        const ctx = canvas.getContext('2d');
        window[canvasId + 'Instance'] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataArray.map((_, index) => index), 
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

    if (document.getElementById('chartMovimiento')) {
        try {
            const response = await window.API.get('/api/dashboard');
            const data = response;

            // Inyectar Estadísticas numéricas de Hoy
            if(document.getElementById('numCitas')) document.getElementById('numCitas').textContent = data.stats.citasHoy;
            if(document.getElementById('numNoAsistidas')) document.getElementById('numNoAsistidas').textContent = data.stats.noAsistidasHoy;
            if(document.getElementById('numCanceladas')) document.getElementById('numCanceladas').textContent = data.stats.canceladasHoy;
            if(document.getElementById('numCompletadas')) document.getElementById('numCompletadas').textContent = data.stats.completadasHoy;
            if(document.getElementById('percentTasa')) document.getElementById('percentTasa').textContent = data.stats.tasaPorcentaje + '%';

            // Dibujar Sparklines
            drawSparkline('chartCitas', data.stats.sparkCitas, '#047857', 'rgba(4, 120, 87, 0.2)');
            drawSparkline('chartNoAsistidas', data.stats.sparkNoAsistidas, '#b45309', 'rgba(180, 83, 9, 0.2)');
            drawSparkline('chartCanceladas', data.stats.sparkCanceladas, '#be123c', 'rgba(190, 18, 60, 0.2)');

            // Dona de Completación
            const canvasTasa = document.getElementById('chartTasa');
            if (canvasTasa) {
                new Chart(canvasTasa, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [data.stats.tasaPorcentaje, 100 - data.stats.tasaPorcentaje],
                            backgroundColor: ['#1e40af', '#f1f5f9'],
                            borderWidth: 0,
                        }]
                    },
                    options: { cutout: '80%', maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
                });
            }

            // Gráfica Grande: Movimiento de la Semana
            const canvasMovimiento = document.getElementById('chartMovimiento');
            if (canvasMovimiento) {
                const ctxMov = canvasMovimiento.getContext('2d');
                const gradientBig = ctxMov.createLinearGradient(0, 0, 0, 300);
                gradientBig.addColorStop(0, 'rgba(30, 64, 175, 0.4)');
                gradientBig.addColorStop(1, 'rgba(255, 255, 255, 0.0)');

                new Chart(ctxMov, {
                    type: 'line',
                    data: {
                        labels: data.graficaMovimiento.labels,
                        datasets: [{ 
                            label: 'Citas realizadas', 
                            data: data.graficaMovimiento.data, 
                            borderColor: '#1e40af', 
                            backgroundColor: gradientBig, 
                            borderWidth: 3, 
                            fill: true, 
                            tension: 0.4 
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                });
            }

            // Gráfica Dona: Tratamientos Realizados
            const canvasTratamientosDash = document.getElementById('chartTratamientos');
            const leyendaContenedor = document.getElementById('leyendaTratamientos');
            
            if (canvasTratamientosDash && leyendaContenedor) {
                const coloresTratamientos = ['#2d9596', '#3b82f6', '#facc15', '#fb923c'];
                
                if (data.graficaTratamientos.length === 0) {
                    leyendaContenedor.innerHTML = '<p class="text-xs text-slate-500 italic">No hay tratamientos aplicados recientemente.</p>';
                } else {
                    const labels = [];
                    const dataPoints = [];
                    leyendaContenedor.innerHTML = ''; 

                    data.graficaTratamientos.forEach((trat, index) => {
                        labels.push(trat.nombre);
                        dataPoints.push(trat.cantidad);
                        
                        const colorIndex = index % coloresTratamientos.length;
                        
                        leyendaContenedor.innerHTML += `
                            <div class="flex items-center text-xs text-slate-600 truncate" title="${trat.nombre} (${trat.cantidad})">
                                <span class="w-2 h-2 rounded-full mr-2 shrink-0" style="background-color: ${coloresTratamientos[colorIndex]}"></span> 
                                <span class="truncate">${trat.nombre}</span>
                            </div>
                        `;
                    });

                    new Chart(canvasTratamientosDash, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{ data: dataPoints, backgroundColor: coloresTratamientos, borderWidth: 0 }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
                    });
                }
            }

            // RENDERIZAR NOTIFICACIONES DE HOY
            const notifHoyCont = document.getElementById('contenedorNotificacionesHoy');
            if (notifHoyCont) {
                if (data.notificacionesHoy.length === 0) {
                    notifHoyCont.innerHTML = `<div class="p-3 text-center rounded-xl bg-slate-50 border border-slate-100"><p class="text-xs text-slate-500">No tienes más citas para hoy.</p></div>`;
                } else {
                    notifHoyCont.innerHTML = data.notificacionesHoy.map(cita => `
                        <div class="flex items-start gap-3 p-3 hover:bg-blue-50 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-blue-100">
                            <div class="bg-blue-800 text-white p-2 rounded-lg flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm text-slate-700 truncate w-full"><span class="font-bold text-slate-900">${cita.paciente}</span></p>
                                <p class="text-xs text-blue-800 font-semibold mt-0.5">${cita.hora}</p>
                            </div>
                        </div>
                    `).join('');
                }
            }

            // RENDERIZAR NOTIFICACIONES PRÓXIMAS
            const notifProxCont = document.getElementById('contenedorNotificacionesProximas');
            if (notifProxCont) {
                if (data.notificacionesProximas.length === 0) {
                    notifProxCont.innerHTML = `<div class="p-3 text-center rounded-xl bg-slate-50 border border-slate-100"><p class="text-xs text-slate-500">No hay citas agendadas.</p></div>`;
                } else {
                    notifProxCont.innerHTML = data.notificacionesProximas.map(cita => `
                        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-slate-200">
                            <div class="bg-slate-100 text-slate-400 p-2 rounded-lg flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm text-slate-600 truncate w-full font-medium">${cita.paciente}</p>
                                <p class="text-xs text-slate-500 mt-0.5">${cita.fecha} <span class="text-slate-400">|</span> ${cita.hora}</p>
                            </div>
                        </div>
                    `).join('');
                }
            }

        } catch (error) {
            console.error("Error cargando datos del dashboard:", error);
            document.querySelectorAll('#numCitas, #numNoAsistidas, #numCanceladas, #numCompletadas, #percentTasa').forEach(el => el.textContent = 'Err');
        }
    }
});