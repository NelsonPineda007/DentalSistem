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

window.cargarDatosDashboard = async function() {
    if (!document.getElementById('chartMovimiento')) return;
    try {
        const response = await window.API.get('/api/dashboard');
        const data = response;

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
            if(window.tasaInstance) window.tasaInstance.destroy();
            window.tasaInstance = new Chart(canvasTasa, {
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

            if(window.movimientoInstance) window.movimientoInstance.destroy();
            window.movimientoInstance = new Chart(ctxMov, {
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
                if(window.tratamientosInstance) window.tratamientosInstance.destroy();
                window.tratamientosInstance = new Chart(canvasTratamientosDash, {
                    type: 'doughnut',
                    data: { labels: labels, datasets: [{ data: dataPoints, backgroundColor: coloresTratamientos, borderWidth: 0 }] },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
                });
            }
        }

        window.citasHoyCompletas = data.notificacionesHoy || [];
        window.citasProxCompletas = data.notificacionesProximas || [];

        const contenedorListas = document.getElementById('contenedorListasCitas');
        const totalCitas = window.citasHoyCompletas.length + window.citasProxCompletas.length;

        if (totalCitas === 0) {
            contenedorListas.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full opacity-60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p class="text-sm text-slate-500 italic">No hay citas pendientes</p>
                </div>
            `;
        } else {
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
                                <div class="flex items-center justify-between p-2 hover:bg-blue-50 rounded-xl transition-colors border border-transparent">
                                    <div class="flex items-start gap-2 min-w-0">
                                        <div class="bg-blue-800 text-white p-1.5 rounded-lg shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs text-slate-700 truncate font-bold">${cita.paciente}</p>
                                            <p class="text-[10px] text-blue-800 font-semibold">${cita.hora}</p>
                                        </div>
                                    </div>
                                    <button onclick="editarCitaDesdeDashboard(${cita.id})" class="text-blue-600 hover:text-blue-800 ml-2" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
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
                                <div class="flex items-center justify-between p-2 hover:bg-slate-50 rounded-xl transition-colors border border-transparent">
                                    <div class="flex items-start gap-2 min-w-0">
                                        <div class="bg-slate-100 text-slate-400 p-1.5 rounded-lg shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs text-slate-600 truncate font-medium">${cita.paciente}</p>
                                            <p class="text-[10px] text-slate-400">${cita.fecha} | ${cita.hora}</p>
                                        </div>
                                    </div>
                                    <button onclick="editarCitaDesdeDashboard(${cita.id})" class="text-slate-400 hover:text-blue-600 ml-2" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

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
};

document.addEventListener("DOMContentLoaded", () => {
    window.cargarDatosDashboard();
});


// ==========================================
// FUNCIONES GLOBALES DE HORA (AM/PM)
// ==========================================
window.formatearHora = function(input) {
    let val = input.value.replace(/\D/g, ''); 
    if (val.length > 4) val = val.substring(0, 4);

    if (val.length === 3) {
        input.value = val.substring(0, 1) + ':' + val.substring(1, 3);
    } else if (val.length === 4) {
        input.value = val.substring(0, 2) + ':' + val.substring(2, 4);
    } else {
        input.value = val;
    }
};

window.validarHora = function(input) {
    let val = input.value.replace(/\D/g, '');
    if (!val) { input.value = ''; return; }

    let h = 0, m = 0;
    if (val.length === 1 || val.length === 2) {
        h = parseInt(val);
        m = 0;
    } else if (val.length === 3) {
        h = parseInt(val.substring(0, 1));
        m = parseInt(val.substring(1, 3));
    } else if (val.length === 4) {
        h = parseInt(val.substring(0, 2));
        m = parseInt(val.substring(2, 4));
    }

    if (h > 12) h = 12;
    if (h === 0) h = 12;
    if (m > 59) m = 59;

    input.value = `${h}:${m.toString().padStart(2, '0')}`;
};

window.setAMPMDash = function(tipo, valor, sincInicio = true) {
    document.getElementById(`dash_hora_ampm_${tipo}`).value = valor;
    const btnAM = document.getElementById(`dash_btn_am_${tipo}`);
    const btnPM = document.getElementById(`dash_btn_pm_${tipo}`);

    if(valor === 'AM') {
        btnAM.className = "px-2 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all";
        btnPM.className = "px-2 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all";
    } else {
        btnPM.className = "px-2 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all";
        btnAM.className = "px-2 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all";
    }

    if (tipo === 'inicio' && sincInicio) {
        window.setAMPMDash('fin', valor, false);
    }
};

function format24hDash(horaString, ampm) {
    let [hours, minutes] = horaString.split(':');
    let hours24 = parseInt(hours, 10);
    if (ampm === 'PM' && hours24 < 12) hours24 += 12;
    if (ampm === 'AM' && hours24 === 12) hours24 = 0;
    return `${hours24.toString().padStart(2, '0')}:${minutes}:00`;
}


// ==========================================
// LÓGICA DE MODALES
// ==========================================
window.abrirModalDashboardCitas = function() {
    const modalHoy = document.getElementById('modalListaHoy');
    const modalProx = document.getElementById('modalListaProximas');
    const modal = document.getElementById('modalDashboardCitas');

    if (window.citasHoyCompletas && window.citasHoyCompletas.length > 0) {
        modalHoy.innerHTML = window.citasHoyCompletas.map(cita => `
            <div class="flex items-center justify-between p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                <div class="flex items-start gap-3">
                    <div class="bg-blue-800 text-white p-2 rounded-lg shrink-0"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">${cita.paciente}</p>
                        <p class="text-xs font-semibold text-blue-800 mt-0.5">${cita.hora}</p>
                    </div>
                </div>
                <button onclick="editarCitaDesdeDashboard(${cita.id})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Editar Cita">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
            </div>
        `).join('');
    } else {
        modalHoy.innerHTML = `<p class="text-sm text-slate-500 italic p-2">No hay citas pendientes para hoy.</p>`;
    }

    if (window.citasProxCompletas && window.citasProxCompletas.length > 0) {
        modalProx.innerHTML = window.citasProxCompletas.map(cita => `
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                <div class="flex items-start gap-3">
                    <div class="bg-slate-200 text-slate-500 p-2 rounded-lg shrink-0"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">${cita.paciente}</p>
                        <p class="text-xs text-slate-500 mt-0.5">${cita.fecha} | ${cita.hora}</p>
                    </div>
                </div>
                <button onclick="editarCitaDesdeDashboard(${cita.id})" class="p-2 text-slate-400 hover:bg-slate-200 hover:text-blue-600 rounded-lg transition-colors" title="Editar Cita">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
            </div>
        `).join('');
    } else {
        modalProx.innerHTML = `<p class="text-sm text-slate-500 italic p-2">No hay citas agendadas.</p>`;
    }

    if(modal) {
        // Ocultar botones base del modal para evitar duplicados
        const botones = modal.querySelectorAll('button');
        botones.forEach(btn => {
            const txt = btn.textContent.trim().toLowerCase();
            if ((txt === 'guardar' || txt === 'cancelar') && !btn.closest('#formCitaDashboard')) {
                btn.parentElement.style.display = 'none'; 
            }
        });

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

window.editarCitaDesdeDashboard = async function(citaId) {
    window.closeModal('modalDashboardCitas');
    
    try {
        const citas = await window.API.get('/api/citas');
        const cita = citas.find(c => c.id == citaId);
        
        if(!cita) {
            if(window.Alerta) window.Alerta.error("No encontrada", "No se encontraron los detalles de esta cita.");
            return;
        }

        const formDatos = await window.API.get('/api/citas/datos-formulario');
        const selectDoctor = document.getElementById('dash_empleado_id');
        selectDoctor.innerHTML = formDatos.doctores.map(d => `<option value="${d.id}">${d.nombre_completo}</option>`).join('');

        document.getElementById('dash_cita_id').value = cita.id;
        document.getElementById('dash_paciente_id').value = cita.paciente_id;
        document.getElementById('dash_paciente_nombre').value = cita.paciente;
        document.getElementById('dash_empleado_id').value = cita.empleado_id;
        document.getElementById('dash_estado').value = cita.estado;
        document.getElementById('dash_fecha').value = cita.fecha;
        document.getElementById('dash_motivo').value = cita.motivo || '';
        document.getElementById('dash_notas').value = cita.notas || '';

        // Formateo exacto de la Hora Inicio
        if (cita.hora) {
            let [h, m] = cita.hora.split(':');
            let h12 = parseInt(h, 10);
            const ampm = h12 >= 12 ? 'PM' : 'AM';
            h12 = h12 % 12; h12 = h12 ? h12 : 12;
            document.getElementById('dash_hora_input_inicio').value = `${h12}:${m}`;
            window.setAMPMDash('inicio', ampm, false); 
        }
        
        // Formateo exacto de la Hora Fin
        if (cita.hora_fin) {
            let [h, m] = cita.hora_fin.split(':');
            let h12 = parseInt(h, 10);
            const ampm = h12 >= 12 ? 'PM' : 'AM';
            h12 = h12 % 12; h12 = h12 ? h12 : 12;
            document.getElementById('dash_hora_input_fin').value = `${h12}:${m}`;
            window.setAMPMDash('fin', ampm, false);
        }

        const modal = document.getElementById('modalEditarCitaDash');
        
        // Ocultar los botones genéricos del modal_base para no tener duplicados
        const botones = modal.querySelectorAll('button');
        botones.forEach(btn => {
            const txt = btn.textContent.trim().toLowerCase();
            if ((txt === 'guardar' || txt === 'cancelar') && !btn.closest('#formCitaDashboard')) {
                btn.parentElement.style.display = 'none'; 
            }
        });

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('.modal-backdrop').classList.remove('opacity-0');
            modal.querySelector('.modal-panel').classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
            modal.querySelector('.modal-panel').classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);

    } catch(error) {
        console.error("Error al preparar la edición:", error);
    }
};

window.guardarEdicionCitaDashboard = async function(event) {
    event.preventDefault(); 
    
    // Extracción de Horas estilo citas.blade.php
    const horaVisualInicio = document.getElementById('dash_hora_input_inicio').value;
    const ampmInicio = document.getElementById('dash_hora_ampm_inicio').value;
    
    const horaVisualFin = document.getElementById('dash_hora_input_fin').value;
    const ampmFin = document.getElementById('dash_hora_ampm_fin').value;
    
    if (horaVisualInicio.length < 4 || horaVisualFin.length < 4) {
        if(window.Alerta) window.Alerta.advertencia('Campos incompletos', 'Asegúrate de llenar correctamente Hora de Inicio y Fin.');
        return;
    }

    const horaFinalInicio = format24hDash(horaVisualInicio, ampmInicio);
    const horaFinalFin = format24hDash(horaVisualFin, ampmFin);
    
    document.getElementById('dash_hora_oculta_inicio').value = horaFinalInicio;
    document.getElementById('dash_hora_oculta_fin').value = horaFinalFin;

    const form = document.getElementById('formCitaDashboard');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    const btn = document.getElementById('btnGuardarDash');
    btn.disabled = true;
    btn.innerText = "Guardando...";

    try {
        const response = await window.API.put('/api/citas/' + data.id, data);
        if(response && response.success) {
            window.closeModal('modalEditarCitaDash');
            if(window.Alerta) window.Alerta.exito('¡Actualizada!', 'La cita se modificó correctamente.');
            await window.cargarDatosDashboard(); 
        }
    } catch (error) {
        console.error("Error al actualizar la cita:", error);
        if(error.status === 409 && error.data && error.data.warning) {
             if(window.Alerta) window.Alerta.error('Choque de horario', error.data.mensaje || 'El doctor ya tiene otra cita.');
        } else if (error.status === 422 && error.data && error.data.error) {
             if(window.Alerta) window.Alerta.error('Error de horario', error.data.error);
        } else {
             if(window.Alerta) window.Alerta.error('Error del servidor', 'No se pudo guardar la cita.');
        }
    } finally {
        btn.disabled = false;
        btn.innerText = "Guardar Cambios";
    }
};