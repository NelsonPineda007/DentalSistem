// public/js/NotificacionesControlador.js

let datosOriginales = { citas: [], notas: [], recordatorios: [] };

document.addEventListener('DOMContentLoaded', () => {
    // 1. Arranca el reloj invisible al instante
    chequearNotificacionesFlotantes();
    
    // 2. Revisa la base de datos CADA 15 SEGUNDOS (Súper rápido)
    setInterval(chequearNotificacionesFlotantes, 15000); 

    const contenedorPrincipal = document.getElementById('contenedor-lista-citas');
    if (contenedorPrincipal) {
        cargarNotificaciones();
        configurarBuscadores();

        // 3. NUEVO: Leer la URL para ver si venimos de un botón "Ver más detalles"
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        
        if (tabParam) {
            let seccionId = 'seccion-citas'; 
            if (tabParam === 'notas') seccionId = 'seccion-notas';
            if (tabParam === 'recordatorios') seccionId = 'seccion-recordatorios';
            
            // Buscamos el botón en el menú lateral y le damos clic automático por el usuario
            const botonAside = document.querySelector(`button[onclick*="${seccionId}"]`);
            if (botonAside) {
                cambiarSeccion(seccionId, botonAside);
            }
        }
    }
});

// ==========================================
// 1. CAMBIAR DE PESTAÑAS
// ==========================================
window.cambiarSeccion = function(seccionId, botonSeleccionado) {
    document.querySelectorAll('.seccion-contenido').forEach(sec => {
        sec.classList.add('hidden');
        sec.classList.remove('block');
    });

    const seccionActiva = document.getElementById(seccionId);
    if(seccionActiva) {
        seccionActiva.classList.remove('hidden');
        seccionActiva.classList.add('block');
    }

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'bg-blue-50', 'text-blue-800', 'border-blue-200', 'bg-amber-50', 'text-amber-800', 'border-amber-200');
        btn.classList.add('text-slate-600', 'border-transparent');
    });

    botonSeleccionado.classList.remove('text-slate-600', 'border-transparent');
    if (seccionId === 'seccion-citas') botonSeleccionado.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
    else if (seccionId === 'seccion-notas') botonSeleccionado.classList.add('bg-blue-50', 'text-blue-800', 'border-blue-200');
    else if (seccionId === 'seccion-recordatorios') botonSeleccionado.classList.add('bg-amber-50', 'text-amber-800', 'border-amber-200');
};

// ==========================================
// 2. FETCH PARA LA VISTA
// ==========================================
async function cargarNotificaciones() {
    try {
        const respuesta = await fetch('/api/notificaciones/datos?t=' + new Date().getTime(), { cache: 'no-store' });
        if (!respuesta.ok) throw new Error('El servidor respondió con código ' + respuesta.status);
        const data = await respuesta.json();
        if(data.status === 'error') throw new Error(data.message);

        datosOriginales.citas = data.citas || [];
        datosOriginales.notas = data.notas || [];
        datosOriginales.recordatorios = data.recordatorios || [];

        const badge = document.getElementById('badge-citas');
        if (badge) badge.innerText = datosOriginales.citas.length;

        renderizarCitas(datosOriginales.citas);
        renderizarNotas(datosOriginales.notas);
        renderizarRecordatorios(datosOriginales.recordatorios);
        
    } catch (error) {
        console.error("Error cargando notificaciones:", error);
        const mensajeError = `<div class="p-4 bg-red-50 text-red-600 border border-red-200 rounded-xl text-center font-medium shadow-sm"><p class="font-bold mb-1">Algo salió mal 🚨</p><p class="text-sm">${error.message}</p></div>`;
        
        const c1 = document.getElementById('contenedor-lista-citas');
        const c2 = document.getElementById('contenedor-lista-notas');
        const c3 = document.getElementById('contenedor-lista-recordatorios');
        if (c1) c1.innerHTML = mensajeError;
        if (c2) c2.innerHTML = mensajeError;
        if (c3) c3.innerHTML = mensajeError;
    }
}

// ==========================================
// 3. FUNCIONES DE RENDERIZADO
// ==========================================
function renderizarCitas(citas) {
    const contenedor = document.getElementById('contenedor-lista-citas');
    if(!contenedor) return;

    if(citas.length === 0) {
        contenedor.innerHTML = '<p class="text-slate-500 italic p-4 text-center bg-slate-50 rounded-xl">No hay citas pendientes para mostrar.</p>';
        return;
    }

    contenedor.innerHTML = citas.map(cita => `
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl shadow-sm hover:shadow-md hover:border-emerald-300 transition-all gap-4 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-600"></div>
            <div class="flex items-center gap-4 flex-1">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:bg-emerald-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-800">Cita: ${cita.paciente}</h4>
                    <p class="text-sm text-slate-500 mt-0.5">${cita.motivo}</p>
                </div>
            </div>
            <div class="flex flex-col md:items-end gap-3 w-full md:w-auto shrink-0 mt-2 md:mt-0">
                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1.5 rounded-lg w-fit md:ml-auto">${cita.etiqueta_fecha}, ${cita.hora}</span>
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto mt-1">
                    <button onclick="window.location.href='/citas?id=${cita.id}'" class="text-sm font-bold text-emerald-700 hover:text-emerald-900 transition-colors bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-4 py-2 rounded-xl w-full sm:w-auto text-center">Abrir Cita</button>
                    <button onclick="window.location.href='/expediente?id=${cita.paciente_id}'" class="text-sm font-bold text-blue-700 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 border border-blue-200 px-4 py-2 rounded-xl w-full sm:w-auto text-center">Abrir Expediente</button>
                </div>
            </div>
        </div>
    `).join('');
}

function renderizarNotas(notas) {
    const contenedor = document.getElementById('contenedor-lista-notas');
    if(!contenedor) return;

    if(notas.length === 0) {
        contenedor.innerHTML = '<p class="text-slate-500 italic p-4 text-center bg-slate-50 rounded-xl">No hay notas registradas.</p>';
        return;
    }

    contenedor.innerHTML = notas.map(nota => `
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl shadow-sm hover:shadow-md hover:border-blue-300 transition-all gap-4 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-700"></div>
            <div class="flex items-center gap-4 flex-1">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-800">Nota: ${nota.autor}</h4>
                    <p class="text-sm text-slate-500 mt-0.5">"${nota.contenido}"</p>
                </div>
            </div>
            <div class="flex flex-col md:items-end gap-3 w-full md:w-auto shrink-0 mt-2 md:mt-0">
                <span class="text-sm font-semibold text-slate-400">${nota.tiempo}</span>
            </div>
        </div>
    `).join('');
}

function renderizarRecordatorios(recordatorios) {
    const contenedor = document.getElementById('contenedor-lista-recordatorios');
    if(!contenedor) return;

    if(recordatorios.length === 0) {
        contenedor.innerHTML = '<p class="text-slate-500 italic p-4 text-center bg-slate-50 rounded-xl">No hay recordatorios registrados.</p>';
        return;
    }

    contenedor.innerHTML = recordatorios.map(rec => `
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl shadow-sm hover:shadow-md hover:border-amber-300 transition-all gap-4 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
            <div class="flex items-center gap-4 flex-1">
                <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:bg-amber-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-800">${rec.titulo}</h4>
                    <p class="text-sm text-slate-500 mt-0.5">${rec.detalles || 'Sin detalles adicionales'}</p>
                </div>
            </div>
            <div class="flex flex-col md:items-end gap-3 w-full md:w-auto shrink-0 mt-2 md:mt-0">
                <span class="text-sm font-semibold text-slate-400">${rec.tiempo}</span>
            </div>
        </div>
    `).join('');
}

// ==========================================
// 4. CONFIGURACIÓN DE BUSCADORES EN TIEMPO REAL
// ==========================================
function configurarBuscadores() {
    const bCitas = document.getElementById('busquedaCitas');
    if (bCitas) bCitas.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        renderizarCitas(datosOriginales.citas.filter(c => c.paciente.toLowerCase().includes(query) || c.motivo.toLowerCase().includes(query)));
    });

    const bNotas = document.getElementById('busquedaNotas');
    if (bNotas) bNotas.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        renderizarNotas(datosOriginales.notas.filter(n => n.autor.toLowerCase().includes(query) || n.contenido.toLowerCase().includes(query)));
    });

    const bRecs = document.getElementById('busquedaRecordatorios');
    if (bRecs) bRecs.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        renderizarRecordatorios(datosOriginales.recordatorios.filter(r => r.titulo.toLowerCase().includes(query) || (r.detalles && r.detalles.toLowerCase().includes(query))));
    });
}

// ==========================================
// 5. RELOJ: NOTIFICACIONES FLOTANTES CON LOCALSTORAGE
// ==========================================
async function chequearNotificacionesFlotantes() {
    try {
        const respuesta = await fetch('/api/notificaciones/datos?t=' + new Date().getTime(), {
            method: 'GET',
            cache: 'no-store',
            headers: { 'Pragma': 'no-cache', 'Cache-Control': 'no-cache' }
        });
        if (!respuesta.ok) return;

        const data = await respuesta.json();
        if (data.status !== 'success') return;

        const ahora = new Date();
        const hoyStr = `${ahora.getFullYear()}-${String(ahora.getMonth() + 1).padStart(2, '0')}-${String(ahora.getDate()).padStart(2, '0')}`;

        function evaluarAviso(fecha_cruda, hora_cruda, idUnico, callback) {
            if (!fecha_cruda) return; 
            
            const fechaLimpia = fecha_cruda.split(' ')[0].split('T')[0];
            if (fechaLimpia !== hoyStr) return; 
            
            if (hora_cruda) {
                const [h, m] = hora_cruda.split(':');
                const horaEvento = new Date();
                horaEvento.setHours(parseInt(h, 10), parseInt(m, 10), 0, 0);

                const diffMins = Math.floor((horaEvento - ahora) / 60000);
                
                // 1. AVISO DE 1 HORA (Aplica para todo)
                if (diffMins > 15 && diffMins <= 60) {
                    const keyMemoria = `noti_60m_${idUnico}_${hora_cruda}`;
                    if (!localStorage.getItem(keyMemoria)) {
                        localStorage.setItem(keyMemoria, 'true');
                        callback(diffMins);
                    }
                }
                
                // 2. AVISO DE 15 MINUTOS INMINENTE
                if (diffMins >= 0 && diffMins <= 15) {
                    const keyMemoria = `noti_15m_${idUnico}_${hora_cruda}`;
                    if (!localStorage.getItem(keyMemoria)) {
                        localStorage.setItem(keyMemoria, 'true');
                        callback(diffMins);
                    }
                }
            } else {
                if (ahora.getHours() >= 8) {
                    const keyMemoria = `noti_allday_${idUnico}_${hoyStr}`;
                    if (!localStorage.getItem(keyMemoria)) {
                        localStorage.setItem(keyMemoria, 'true');
                        callback(null);
                    }
                }
            }
        }

        // --- CITAS (Color Verde) ---
        if (data.citas) {
            data.citas.forEach(cita => {
                evaluarAviso(cita.fecha_cruda, cita.hora_cruda, `cita_${cita.id}`, (mins) => {
                    let subtitulo = `En ${mins} minutos tienes cita con ${cita.paciente}.`;
                    if (window.Alerta) window.Alerta.notificarCitaFlotante('Cita Cercana', subtitulo);
                });
            });
        }

        // --- NOTAS (Color Azul) ---
        if (data.notas) {
            data.notas.forEach(nota => {
                evaluarAviso(nota.fecha_cruda, nota.hora_cruda, `nota_${nota.id}`, (mins) => {
                    let subtitulo = mins !== null ? `En ${mins} minutos: ${nota.contenido}` : `Para hoy: ${nota.contenido}`;
                    if (window.Alerta) window.Alerta.notificarNotaFlotante(`Nota: ${nota.autor}`, subtitulo);
                });
            });
        }

        // --- RECORDATORIOS (Color Amarillo) ---
        if (data.recordatorios) {
            data.recordatorios.forEach(rec => {
                evaluarAviso(rec.fecha_cruda, rec.hora_cruda, `rec_${rec.id}`, (mins) => {
                    let subtitulo = mins !== null ? `En ${mins} minutos: ${rec.detalles}` : `Para hoy: ${rec.detalles}`;
                    if (window.Alerta) window.Alerta.notificarRecordatorioFlotante(`Recordatorio: ${rec.titulo}`, subtitulo);
                });
            });
        }

    } catch (e) {
        console.error('Error revisando el reloj de notificaciones:', e);
    }
}