document.addEventListener("DOMContentLoaded", async function() {

    const commonSparklineOptions = {
        responsive: true, maintainAspectRatio: false,
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

        if (window[canvasId + 'Instance']) { window[canvasId + 'Instance'].destroy(); }

        const ctx = canvas.getContext('2d');
        window[canvasId + 'Instance'] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataArray.map((_, index) => index), 
                datasets: [{ data: dataArray, borderColor: hexColor, fill: true, backgroundColor: getGradient(ctx, rgbaGradientColor) }]
            },
            options: commonSparklineOptions
        });
    };

    if (document.getElementById('chartMovimiento')) {
        try {
            const response = await window.API.get('/api/dashboard');
            const data = response;

            // Inyectar Estadísticas numéricas
            if(document.getElementById('numCitas')) document.getElementById('numCitas').textContent = data.stats.citasHoy;
            if(document.getElementById('numNoAsistidas')) document.getElementById('numNoAsistidas').textContent = data.stats.noAsistidasHoy;
            if(document.getElementById('numCanceladas')) document.getElementById('numCanceladas').textContent = data.stats.canceladasHoy;
            if(document.getElementById('numCompletadas')) document.getElementById('numCompletadas').textContent = data.stats.completadasHoy;
            if(document.getElementById('percentTasa')) document.getElementById('percentTasa').textContent = data.stats.tasaPorcentaje + '%';

            drawSparkline('chartCitas', data.stats.sparkCitas, '#047857', 'rgba(4, 120, 87, 0.2)');
            drawSparkline('chartNoAsistidas', data.stats.sparkNoAsistidas, '#b45309', 'rgba(180, 83, 9, 0.2)');
            drawSparkline('chartCanceladas', data.stats.sparkCanceladas, '#be123c', 'rgba(190, 18, 60, 0.2)');

            const canvasTasa = document.getElementById('chartTasa');
            if (canvasTasa) {
                new Chart(canvasTasa, {
                    type: 'doughnut',
                    data: { datasets: [{ data: [data.stats.tasaPorcentaje, 100 - data.stats.tasaPorcentaje], backgroundColor: ['#1e40af', '#f1f5f9'], borderWidth: 0 }] },
                    options: { cutout: '80%', maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
                });
            }

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
                        datasets: [{ label: 'Citas realizadas', data: data.graficaMovimiento.data, borderColor: '#1e40af', backgroundColor: gradientBig, borderWidth: 3, fill: true, tension: 0.4 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                });
            }

            const canvasTratamientosDash = document.getElementById('chartTratamientos');
            const leyendaContenedor = document.getElementById('leyendaTratamientos');
            
            if (canvasTratamientosDash && leyendaContenedor) {
                const coloresTratamientos = ['#2d9596', '#3b82f6', '#facc15', '#fb923c'];
                
                if (data.graficaTratamientos.length === 0) {
                    leyendaContenedor.innerHTML = '<p class="text-xs text-slate-500 italic">No hay tratamientos aplicados.</p>';
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
                        data: { labels: labels, datasets: [{ data: dataPoints, backgroundColor: coloresTratamientos, borderWidth: 0 }] },
                        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
                    });
                }
            }

            // GUARDAMOS DATA GLOBAL PARA EL MODAL
            window.citasHoyCompletas = data.notificacionesHoy || [];
            window.citasProxCompletas = data.notificacionesProximas || [];

            // ==========================================
            // LOGICA DINÁMICA DE CITAS PENDIENTES
            // ==========================================
            const contenedorListas = document.getElementById('contenedorListasCitas');
            const totalCitas = window.citasHoyCompletas.length + window.citasProxCompletas.length;

            if (totalCitas === 0) {
                // ESTADO VACÍO (Centrado)
                contenedorListas.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full opacity-60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <p class="text-sm text-slate-500 italic">No hay citas pendientes</p>
                    </div>
                `;
            } else {
                // LÍMITE MÁXIMO PARA NO ESTIRAR LA TARJETA (4 citas en total)
                const MAX_ITEMS = 4;
                let showHoy = Math.min(window.citasHoyCompletas.length, MAX_ITEMS);
                let showProx = Math.min(window.citasProxCompletas.length, MAX_ITEMS - showHoy);

                const previewHoy = window.citasHoyCompletas.slice(0, showHoy);
                const previewProx = window.citasProxCompletas.slice(0, showProx);
                const faltantes = totalCitas - (showHoy + showProx);

                let html = '';

                if (previewHoy.length > 0) {
                    html += `
                        <div class="mb-4">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                Para Hoy <span class="bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded text-[8px]">Prioridad</span>
                            </h4>
                            <div class="space-y-2">
                                ${previewHoy.map(cita => `
                                    <div class="flex items-start gap-2 p-2 hover:bg-blue-50 rounded-xl transition-colors border border-transparent">
                                        <div class="bg-blue-800 text-white p-1.5 rounded-lg shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs text-slate-700 truncate font-bold">${cita.paciente}</p>
                                            <p class="text-[10px] text-blue-800 font-semibold">${cita.hora}</p>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                if (previewProx.length > 0) {
                    html += `
                        <div>
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ${previewHoy.length > 0 ? 'pt-2 border-t border-slate-100' : ''}">
                                Próximos días
                            </h4>
                            <div class="space-y-2">
                                ${previewProx.map(cita => `
                                    <div class="flex items-start gap-2 p-2 hover:bg-slate-50 rounded-xl transition-colors border border-transparent">
                                        <div class="bg-slate-100 text-slate-400 p-1.5 rounded-lg shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs text-slate-600 truncate font-medium">${cita.paciente}</p>
                                            <p class="text-[10px] text-slate-400">${cita.fecha} | ${cita.hora}</p>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                // Si hay más citas que no cupieron, mostramos el indicador
                if (faltantes > 0) {
                    html += `
                        <div class="text-center mt-auto pt-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">+ ${faltantes} citas más...</span>
                        </div>
                    `;
                }

                contenedorListas.innerHTML = html;
            }

        } catch (error) {
            console.error("Error cargando datos del dashboard:", error);
        }
    }
});

// FUNCIONES PARA ABRIR EL MODAL Y RENDERIZAR LA LISTA COMPLETA
window.abrirModalDashboardCitas = function() {
    const modalHoy = document.getElementById('modalListaHoy');
    const modalProx = document.getElementById('modalListaProximas');
    const modal = document.getElementById('modalDashboardCitas');

    if (window.citasHoyCompletas && window.citasHoyCompletas.length > 0) {
        modalHoy.innerHTML = window.citasHoyCompletas.map(cita => `
            <div class="flex items-start gap-3 p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                <div class="bg-blue-800 text-white p-2 rounded-lg shrink-0"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                <div>
                    <p class="text-sm font-bold text-slate-800">${cita.paciente}</p>
                    <p class="text-xs font-semibold text-blue-800 mt-0.5">${cita.hora}</p>
                </div>
            </div>
        `).join('');
    } else {
        modalHoy.innerHTML = `<p class="text-sm text-slate-500 italic p-2">No hay citas pendientes para hoy.</p>`;
    }

    if (window.citasProxCompletas && window.citasProxCompletas.length > 0) {
        modalProx.innerHTML = window.citasProxCompletas.map(cita => `
            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                <div class="bg-slate-200 text-slate-500 p-2 rounded-lg shrink-0"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                <div>
                    <p class="text-sm font-medium text-slate-700">${cita.paciente}</p>
                    <p class="text-xs text-slate-500 mt-0.5">${cita.fecha} | ${cita.hora}</p>
                </div>
            </div>
        `).join('');
    } else {
        modalProx.innerHTML = `<p class="text-sm text-slate-500 italic p-2">No hay citas agendadas.</p>`;
    }

    if(modal) {
        // OCULTAR BOTONES DE GUARDAR/CANCELAR AUTOMÁTICAMENTE
        const botones = modal.querySelectorAll('button');
        botones.forEach(btn => {
            const txt = btn.textContent.trim().toLowerCase();
            if (txt === 'guardar' || txt === 'cancelar') {
                btn.parentElement.style.display = 'none'; 
            }
        });

        // Mostrar Modal
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('.modal-backdrop').classList.remove('opacity-0');
            modal.querySelector('.modal-panel').classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
            modal.querySelector('.modal-panel').classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }
};

window.closeModal = function(modalID) {
    const modal = document.getElementById(modalID);
    if(modal) {
        modal.querySelector('.modal-backdrop').classList.add('opacity-0');
        const panel = modal.querySelector('.modal-panel');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:scale-95');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
};