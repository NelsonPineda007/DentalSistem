document.addEventListener('DOMContentLoaded', () => {
    let fechaActual = new Date();
    let mesActual = fechaActual.getMonth();
    let anioActual = fechaActual.getFullYear();
    
    let miniMesActual = mesActual;
    let miniAnioActual = anioActual;
    
    let idContador = 1;
    const notas = {}; 
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    const grid = document.getElementById('calendario-grid');
    const miniGrid = document.getElementById('mini-calendario-grid');
    const listaNotasMes = document.getElementById('lista-notas-mes');
    
    const modalEvento = document.getElementById('modal-evento');
    const modalContent = document.getElementById('modal-content');
    const formEvento = document.getElementById('form-evento');

    const drawerRecordatorios = document.getElementById('drawer-recordatorios');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const formRecordatorio = document.getElementById('form-recordatorio');

    const modalAgendaLibro = document.getElementById('modal-agenda-libro');
    const agendaLibroContent = document.getElementById('agenda-libro-content');
    const agendaLibroLista = document.getElementById('agenda-libro-lista');
    const formAgendaInline = document.getElementById('form-agenda-inline');

    const colorClasses = {
        'blue': 'bg-blue-500 hover:bg-blue-600',
        'emerald': 'bg-emerald-500 hover:bg-emerald-600',
        'rose': 'bg-rose-500 hover:bg-rose-600',
        'purple': 'bg-purple-500 hover:bg-purple-600',
        'slate': 'bg-slate-600 hover:bg-slate-700'
    };
    const colorTextClasses = { 'blue': 'text-blue-900', 'emerald': 'text-emerald-900', 'rose': 'text-rose-900', 'purple': 'text-purple-900', 'slate': 'text-slate-900' };
    const colorBorderClasses = { 'blue': 'border-blue-200 bg-blue-50/80', 'emerald': 'border-emerald-200 bg-emerald-50/80', 'rose': 'border-rose-200 bg-rose-50/80', 'purple': 'border-purple-200 bg-purple-50/80', 'slate': 'border-slate-200 bg-slate-50/80' };
    const colorPillClasses = { 'blue': 'bg-blue-500', 'emerald': 'bg-emerald-500', 'rose': 'bg-rose-500', 'purple': 'bg-purple-500', 'slate': 'bg-slate-600' };

    if (document.getElementById('rec-fecha')) document.getElementById('rec-fecha').valueAsDate = new Date();
    if (document.getElementById('agenda-inline-fecha')) document.getElementById('agenda-inline-fecha').valueAsDate = new Date();

    configurarSelectoresColor();

    // ==========================================
    // 0. CARGAR DESDE LA BASE DE DATOS
    // ==========================================
    async function cargarEventosDesdeBD() {
        try {
            if(!window.API) {
                console.error("No se encontró window.API. Asegúrate de incluir api.js en el HTML.");
                return;
            }
            
            const datos = await window.API.get('/api/calendario');
            
            for (let prop in notas) delete notas[prop]; // Limpiar local

            datos.forEach(ev => {
                if (!notas[ev.fecha]) notas[ev.fecha] = [];
                notas[ev.fecha].push({
                    id: ev.id,
                    titulo: ev.titulo,
                    hora: ev.hora ? ev.hora.substring(0, 5) : '', 
                    detalles: ev.detalles,
                    color: ev.color,
                    tipo: ev.tipo
                });
            });

            actualizarVistas();
        } catch (error) {
            console.error("Error al cargar eventos:", error);
            if(window.Alerta) window.Alerta.error("Error de conexión", "No se pudieron cargar los datos del calendario");
        }
    }

    function actualizarVistas() {
        if(grid) renderizarCalendario();
        if(miniGrid) renderizarMiniCalendario();
        if(listaNotasMes) renderizarNotasDelMesOriginal();
        if(modalAgendaLibro && !modalAgendaLibro.classList.contains('hidden')) renderizarListaAgendaLibro();
    }

    // ==========================================
    // 1. SELECTORES DE COLOR
    // ==========================================
    function configurarSelectoresColor() {
        const config = [
            { containerId: 'color-selector-modal', inputId: 'evento-color' },
            { containerId: 'color-selector-agenda', inputId: 'agenda-inline-color' }
        ];

        config.forEach(c => {
            const container = document.getElementById(c.containerId);
            const input = document.getElementById(c.inputId);
            if(!container || !input) return;

            const btns = container.querySelectorAll('button');
            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    btns.forEach(b => { b.classList.remove('ring-2'); b.classList.add('ring-0'); });
                    btn.classList.remove('ring-0');
                    btn.classList.add('ring-2');
                    input.value = btn.dataset.color;
                });
            });
        });
    }

    function aplicarColorAlSelector(containerId, colorName) {
        const container = document.getElementById(containerId);
        if(!container) return;
        const colorCorrecto = colorName || 'blue';
        const btns = container.querySelectorAll('button');
        btns.forEach(btn => {
            if(btn.dataset.color === colorCorrecto) {
                btn.classList.remove('ring-0'); btn.classList.add('ring-2');
            } else {
                btn.classList.remove('ring-2'); btn.classList.add('ring-0');
            }
        });
    }

    // ==========================================
    // 2. EDICIÓN GLOBAL (Doble Clic)
    // ==========================================
    function abrirEdicion(idBuscado) {
        let notaEncontrada = null;
        let fechaNota = null;

        for (let fecha in notas) {
            const n = notas[fecha].find(x => x.id === idBuscado);
            if (n) { notaEncontrada = n; fechaNota = fecha; break; }
        }
        if (!notaEncontrada) return;

        if (notaEncontrada.tipo === 'Cita') {
            if(window.Alerta) window.Alerta.info("Cita Clínica", "Las citas deben modificarse desde el módulo de Citas.");
            return;
        }

        if (notaEncontrada.tipo === 'Recordatorio') {
            document.getElementById('rec-id').value = notaEncontrada.id;
            document.getElementById('rec-fecha').value = fechaNota;
            document.getElementById('rec-hora').value = notaEncontrada.hora || '';
            document.getElementById('rec-titulo').value = notaEncontrada.titulo;
            document.getElementById('rec-detalles').value = notaEncontrada.detalles || '';
            document.getElementById('drawer-rec-titulo').textContent = 'Editar Recordatorio';
            
            cerrarModalAgendaLibro();
            cerrarModalCentral();
            
            drawerOverlay.classList.remove('hidden');
            setTimeout(() => drawerOverlay.classList.remove('opacity-0'), 10);
            drawerRecordatorios.classList.remove('translate-x-full');
        } else {
            abrirModalAgendaLibro();
            llenarFormularioAgendaInline(notaEncontrada, fechaNota);
        }
    }

    // ==========================================
    // 3. CALENDARIO PRINCIPAL
    // ==========================================
    function renderizarCalendario() {
        grid.innerHTML = '';
        
        const titulos = [document.getElementById('sidebar-mes-anio'), document.getElementById('mobile-mes-anio')];
        titulos.forEach(t => { if(t) t.textContent = `${meses[mesActual]} ${anioActual}`; });

        const primerDia = new Date(anioActual, mesActual, 1).getDay();
        const diasMes = new Date(anioActual, mesActual + 1, 0).getDate();
        const diasMesAnt = new Date(anioActual, mesActual, 0).getDate();

        for (let i = primerDia; i > 0; i--) { crearCeldaDia(diasMesAnt - i + 1, mesActual - 1, anioActual, true); }
        for (let i = 1; i <= diasMes; i++) { crearCeldaDia(i, mesActual, anioActual, false); }
        const faltantes = 42 - grid.children.length;
        for (let i = 1; i <= faltantes; i++) { crearCeldaDia(i, mesActual + 1, anioActual, true); }
    }

    function crearCeldaDia(dia, mes, anio, esMesDiferente) {
        let m = mes; let a = anio;
        if (m < 0) { m = 11; a--; }
        if (m > 11) { m = 0; a++; }

        const fechaFormateada = `${a}-${String(m + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        const hoy = new Date();
        const esHoy = dia === hoy.getDate() && m === hoy.getMonth() && a === hoy.getFullYear();

        const celda = document.createElement('div');
        celda.className = `bg-white p-0.5 md:p-1.5 flex flex-col transition-colors h-full min-h-0 overflow-hidden relative ${esMesDiferente ? 'text-slate-400 bg-slate-50/50' : 'text-slate-700'}`;
        
        celda.addEventListener('dragover', (e) => { e.preventDefault(); celda.classList.add('bg-blue-50'); });
        celda.addEventListener('dragleave', () => { celda.classList.remove('bg-blue-50'); });
        
        // DRAG AND DROP
        celda.addEventListener('drop', async (e) => {
            e.preventDefault(); celda.classList.remove('bg-blue-50');
            const dataStr = e.dataTransfer.getData('text/plain');
            if(!dataStr) return;
            const data = JSON.parse(dataStr);
            if (data.fecha !== fechaFormateada) {
                const notaMovida = notas[data.fecha].find(n => n.id === data.id);
                if (notaMovida) {
                    if (notaMovida.tipo === 'Cita') {
                        if(window.Alerta) window.Alerta.info("Acción no permitida", "Cambia la fecha desde el módulo de Citas.");
                        return;
                    }
                    try {
                        notaMovida.fecha = fechaFormateada; 
                        await window.API.put('/api/calendario/' + data.id, notaMovida);
                        cargarEventosDesdeBD(); 
                    } catch (error) { 
                        console.error(error); 
                        if(window.Alerta) window.Alerta.error("Error", "No se pudo mover el evento");
                    }
                }
            }
        });

        celda.onclick = (e) => {
            if(e.target === celda || e.target.parentElement === celda || e.target.tagName === 'SPAN') {
                abrirModalNotaRapida(fechaFormateada, `${dia} de ${meses[m]} del ${a}`);
            }
        };

        const divHeader = document.createElement('div');
        divHeader.className = "flex justify-center md:justify-end mb-0.5 md:mb-1 shrink-0 pointer-events-none"; 
        
        const numeroDia = document.createElement('span');
        numeroDia.className = `text-[10px] md:text-xs font-black w-4 h-4 md:w-6 md:h-6 flex items-center justify-center rounded-full ${esHoy ? 'bg-blue-800 text-white shadow-md' : ''}`;
        numeroDia.textContent = dia;
        divHeader.appendChild(numeroDia);
        celda.appendChild(divHeader);

        const contenedorNotas = document.createElement('div');
        contenedorNotas.className = 'flex flex-col gap-[2px] md:gap-1 overflow-hidden md:overflow-y-auto custom-scrollbar md:pr-0.5 min-h-0 flex-1 content-start';
        
        if (notas[fechaFormateada]) {
            notas[fechaFormateada].forEach(nota => {
                const badge = document.createElement('div');
                let bgClass = nota.tipo === 'Recordatorio' ? 'bg-[#eab308] hover:bg-[#ca8a04]' : (colorClasses[nota.color] || colorClasses['blue']);
                
                badge.className = `${bgClass} w-full px-1 py-0.5 md:p-1.5 rounded-[4px] md:rounded shadow-sm cursor-pointer transition-all flex flex-col shrink-0 text-white overflow-hidden`;
                
                badge.innerHTML = `
                    <div class="flex flex-col w-full pointer-events-none">
                        <span class="text-[8px] md:text-[10px] font-bold leading-tight truncate">${nota.titulo}</span>
                        ${nota.hora ? `<span class="hidden md:block mt-0.5 text-[8px] bg-black/10 px-1 py-0.5 rounded truncate w-full font-bold">${nota.hora}</span>` : ''}
                    </div>
                `;
                
                badge.title = `${nota.titulo}\n${nota.hora || 'Todo el día'} (Doble clic para editar)`;
                badge.draggable = true;
                badge.addEventListener('dragstart', (e) => { e.dataTransfer.setData('text/plain', JSON.stringify({ fecha: fechaFormateada, id: nota.id })); });

                badge.addEventListener('dblclick', (e) => {
                    e.stopPropagation();
                    abrirEdicion(nota.id);
                });

                contenedorNotas.appendChild(badge);
            });
        }
        celda.appendChild(contenedorNotas);
        grid.appendChild(celda);
    }

    // ==========================================
    // 4. BARRA LATERAL IZQUIERDA
    // ==========================================
    function renderizarMiniCalendario() {
        if(!miniGrid) return;
        miniGrid.innerHTML = '';
        
        const miniTitulo = document.getElementById('mini-mes-anio');
        if(miniTitulo) miniTitulo.textContent = `${meses[miniMesActual]} ${miniAnioActual}`;
        
        const primerDia = new Date(miniAnioActual, miniMesActual, 1).getDay();
        const diasMes = new Date(miniAnioActual, miniMesActual + 1, 0).getDate();
        const diasMesAnt = new Date(miniAnioActual, miniMesActual, 0).getDate();

        for (let i = primerDia; i > 0; i--) { crearCeldaMiniDia(diasMesAnt - i + 1, miniMesActual - 1, miniAnioActual, true); }
        for (let i = 1; i <= diasMes; i++) { crearCeldaMiniDia(i, miniMesActual, miniAnioActual, false); }
        const faltantes = 42 - miniGrid.children.length;
        for (let i = 1; i <= faltantes; i++) { crearCeldaMiniDia(i, miniMesActual + 1, miniAnioActual, true); }
    }

    function crearCeldaMiniDia(dia, mes, anio, esMesDif) {
        let m = mes; let a = anio;
        if (m < 0) { m = 11; a--; }
        if (m > 11) { m = 0; a++; }

        const hoy = new Date();
        const esHoy = dia === hoy.getDate() && m === hoy.getMonth() && a === hoy.getFullYear();

        const celda = document.createElement('div');
        celda.className = `p-1 flex items-center justify-center cursor-pointer rounded-full transition-colors w-6 h-6 md:w-7 md:h-7 mx-auto
                           ${esMesDif ? 'text-slate-300 font-medium' : 'text-slate-700 hover:bg-slate-100 font-bold'} 
                           ${esHoy && !esMesDif ? 'bg-blue-800 text-white hover:bg-blue-900 shadow-md' : ''}`;
        celda.textContent = dia;
        celda.onclick = () => { mesActual = m; anioActual = a; actualizarVistas(); };
        miniGrid.appendChild(celda);
    }

    function renderizarNotasDelMesOriginal() {
        if (!listaNotasMes) return;
        listaNotasMes.innerHTML = '';
        let notasDelMes = [];
        const prefijoMes = `${anioActual}-${String(mesActual + 1).padStart(2, '0')}`;
        for (let fecha in notas) { if (fecha.startsWith(prefijoMes)) { notas[fecha].forEach(n => notasDelMes.push({ fecha, ...n })); } }
        
        const cont = document.getElementById('contador-notas-mes');
        if(cont) cont.textContent = notasDelMes.length;
        
        if (notasDelMes.length === 0) { 
            listaNotasMes.innerHTML = `<div class="text-slate-400 mt-6 text-center px-4 text-xs font-medium italic">Sin apuntes este mes</div>`; 
            return; 
        }

        notasDelMes.sort((a, b) => { if (a.fecha !== b.fecha) return a.fecha > b.fecha ? 1 : -1; return (a.hora || '24:00') > (b.hora || '24:00') ? 1 : -1; });
        notasDelMes.forEach(nota => {
            const card = document.createElement('div');
            
            let pColor = '';
            if(nota.tipo === 'Recordatorio') { pColor = 'bg-[#eab308]'; } 
            else { pColor = colorPillClasses[nota.color] || 'bg-blue-500'; }

            card.className = "bg-slate-50 border border-slate-100 rounded-xl p-3 relative overflow-hidden group hover:bg-slate-100 cursor-pointer transition-colors";
            card.innerHTML = `
                <div class="absolute left-0 top-0 bottom-0 w-1.5 ${pColor}"></div>
                <div class="pl-2">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-bold text-slate-800 text-[10px] uppercase tracking-wider">${nota.fecha.split('-')[2]} ${meses[mesActual].substring(0,3)}</span>
                        <span class="text-[9px] font-bold text-slate-500 bg-white border border-slate-200 px-1.5 py-0.5 rounded shadow-sm">${nota.hora || 'Todo el día'}</span>
                    </div>
                    <p class="font-bold text-slate-700 text-xs truncate">${nota.titulo}</p>
                </div>
            `;
            card.addEventListener('click', () => abrirEdicion(nota.id));
            listaNotasMes.appendChild(card);
        });
    }

    // ==========================================
    // 5. AGENDA LIBRO (Modal Central Ancho)
    // ==========================================
    const btnAgenda = document.getElementById('btn-agenda');
    if (btnAgenda) {
        btnAgenda.addEventListener('click', () => {
            abrirModalAgendaLibro();
            limpiarFormularioAgendaInline();
        });
    }

    function abrirModalAgendaLibro() {
        cerrarDrawerRecordatorio();
        cerrarModalCentral();
        modalAgendaLibro.classList.remove('hidden');
        modalAgendaLibro.classList.add('flex');
        setTimeout(() => { 
            modalAgendaLibro.classList.remove('opacity-0'); 
            agendaLibroContent.classList.remove('scale-95'); 
        }, 10);
        renderizarListaAgendaLibro();
    }

    function cerrarModalAgendaLibro() {
        modalAgendaLibro.classList.add('opacity-0'); 
        agendaLibroContent.classList.add('scale-95');
        setTimeout(() => { 
            modalAgendaLibro.classList.add('hidden'); 
            modalAgendaLibro.classList.remove('flex'); 
        }, 300);
    }

    document.getElementById('btn-cerrar-agenda-libro').addEventListener('click', cerrarModalAgendaLibro);

    const agPrev = document.getElementById('agenda-libro-prev');
    const agNext = document.getElementById('agenda-libro-next');
    if(agPrev) agPrev.addEventListener('click', () => { mesActual--; if(mesActual < 0){mesActual=11; anioActual--;} actualizarVistas(); });
    if(agNext) agNext.addEventListener('click', () => { mesActual++; if(mesActual > 11){mesActual=0; anioActual++;} actualizarVistas(); });

    function renderizarListaAgendaLibro() {
        if(!agendaLibroLista) return;
        agendaLibroLista.innerHTML = '';
        const agMes = document.getElementById('agenda-libro-mes');
        if(agMes) agMes.textContent = `${meses[mesActual]} ${anioActual}`;

        let todas = [];
        const prefijoMes = `${anioActual}-${String(mesActual + 1).padStart(2, '0')}`;
        
        for (let fecha in notas) {
            if (fecha.startsWith(prefijoMes)) {
                notas[fecha].forEach(n => todas.push({ fecha, ...n }));
            }
        }

        if (todas.length === 0) {
            agendaLibroLista.innerHTML = `
                <div class="flex flex-col items-center justify-center mt-10 md:mt-20 text-slate-400 opacity-70 px-4 text-center">
                    <svg class="w-12 h-12 md:w-16 md:h-16 mb-3 md:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <p class="text-base md:text-lg font-bold">Tu libreta está vacía</p>
                    <p class="text-xs md:text-sm mt-1">Añade notas para verlas aquí ordenadas.</p>
                </div>`;
            return;
        }

        const agrupado = {};
        todas.sort((a, b) => {
            if (a.fecha !== b.fecha) return a.fecha > b.fecha ? 1 : -1;
            return (a.hora || '24:00') > (b.hora || '24:00') ? 1 : -1;
        });

        todas.forEach(n => { if(!agrupado[n.fecha]) agrupado[n.fecha] = []; agrupado[n.fecha].push(n); });

        for (let fecha in agrupado) {
            const dateParts = fecha.split('-');
            const headerText = `${dateParts[2]} de ${meses[parseInt(dateParts[1])-1]}`;

            const dateSection = document.createElement('div');
            dateSection.innerHTML = `
                <div class="sticky top-0 bg-slate-50/95 backdrop-blur pb-2 z-10 pt-2">
                    <h3 class="text-xs md:text-sm font-black text-slate-800 flex items-center gap-2 md:gap-3">
                        <span class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-slate-400"></span>${headerText}
                    </h3>
                </div>
                <div class="ml-[3px] md:ml-[5px] border-l-[3px] border-slate-200/80 pl-3 md:pl-5 py-2 space-y-3 md:space-y-4"></div>
            `;

            const cardsContainer = dateSection.querySelector('div:last-child');

            agrupado[fecha].forEach(nota => {
                const card = document.createElement('div');
                let isRec = nota.tipo === 'Recordatorio';
                
                let cBorder = isRec ? 'border-amber-200 bg-amber-50/50' : (colorBorderClasses[nota.color] || colorBorderClasses['blue']);
                let cText = isRec ? 'text-amber-900' : (colorTextClasses[nota.color] || colorTextClasses['blue']);
                let cPill = isRec ? 'bg-[#eab308]' : (colorPillClasses[nota.color] || colorPillClasses['blue']);

                card.className = `${cBorder} border rounded-xl p-3 md:p-4 shadow-sm relative group hover:shadow-md transition-all cursor-pointer`;
                
                card.innerHTML = `
                    <div class="absolute left-0 top-0 bottom-0 w-2 rounded-l-xl ${cPill}"></div>
                    <div class="pl-2">
                        <div class="flex justify-between items-start gap-2">
                            <h4 class="font-black ${cText} text-[13px] md:text-[15px] leading-tight">${nota.titulo}</h4>
                            <button class="p-1 md:p-1.5 text-slate-400 hover:text-red-600 transition-colors btn-delete-agenda opacity-0 group-hover:opacity-100" title="Eliminar">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 mt-1.5 md:mt-2 mb-1.5 md:mb-2">
                            <span class="text-[10px] md:text-xs font-bold px-2 py-0.5 rounded bg-white text-slate-600 border border-slate-200 shadow-sm">${nota.hora || 'Todo el día'}</span>
                            <span class="text-[8px] md:text-[9px] font-black uppercase tracking-wider text-slate-400 bg-white/50 px-2 py-0.5 rounded">${nota.tipo}</span>
                        </div>
                        ${nota.detalles ? `<p class="text-[11px] md:text-xs font-medium text-slate-600 mt-1 line-clamp-2">${nota.detalles}</p>` : ''}
                    </div>
                `;

                card.addEventListener('click', (e) => {
                    if(!e.target.closest('.btn-delete-agenda')) {
                        abrirEdicion(nota.id); 
                    }
                });

                // ELIMINAR DESDE LA LIBRETA (Con SweetAlert nativo)
                card.querySelector('.btn-delete-agenda').addEventListener('click', async (e) => {
                    e.stopPropagation();
                    if(nota.tipo === 'Cita') {
                        if(window.Alerta) window.Alerta.info("Citas", "Elimina la Cita desde el módulo de Citas.");
                        return;
                    }
                    
                    const confirmado = await window.Alerta.eliminar('¿Eliminar este apunte?', `Se borrará permanentemente "${nota.titulo}".`);
                    if(confirmado) {
                        try {
                            await window.API.delete('/api/calendario/' + nota.id);
                            window.Alerta.exito("Eliminado", "El evento fue borrado correctamente.");
                            await cargarEventosDesdeBD();
                            limpiarFormularioAgendaInline();
                        } catch(err) { 
                            console.error(err);
                            window.Alerta.error("Error", "No se pudo eliminar el evento.");
                        }
                    }
                });

                cardsContainer.appendChild(card);
            });
            agendaLibroLista.appendChild(dateSection);
        }
    }

    function llenarFormularioAgendaInline(nota, fecha) {
        document.getElementById('agenda-form-titulo').textContent = 'Editar Nota';
        document.getElementById('agenda-inline-id').value = nota.id;
        document.getElementById('agenda-inline-fecha').value = fecha;
        document.getElementById('agenda-inline-hora').value = nota.hora || '';
        document.getElementById('agenda-inline-titulo').value = nota.titulo;
        document.getElementById('agenda-inline-detalles').value = nota.detalles || '';
        document.getElementById('agenda-inline-color').value = nota.color || 'blue';
        aplicarColorAlSelector('color-selector-agenda', nota.color || 'blue');
    }

    function limpiarFormularioAgendaInline() {
        document.getElementById('agenda-form-titulo').textContent = 'Nueva Nota';
        document.getElementById('agenda-inline-id').value = '';
        formAgendaInline.reset();
        document.getElementById('agenda-inline-fecha').valueAsDate = new Date();
        document.getElementById('agenda-inline-color').value = 'blue';
        aplicarColorAlSelector('color-selector-agenda', 'blue');
    }

    const btnLimp = document.getElementById('btn-limpiar-agenda');
    if(btnLimp) btnLimp.addEventListener('click', limpiarFormularioAgendaInline);

    // GUARDAR NOTA DESDE AGENDA (CON FETCH Y ALERTAS)
    if(formAgendaInline) {
        formAgendaInline.addEventListener('submit', async (e) => {
            e.preventDefault();
            const idEdit = document.getElementById('agenda-inline-id').value;
            const data = {
                titulo: document.getElementById('agenda-inline-titulo').value,
                fecha: document.getElementById('agenda-inline-fecha').value,
                hora: document.getElementById('agenda-inline-hora').value,
                color: document.getElementById('agenda-inline-color').value,
                tipo: 'Nota', 
                detalles: document.getElementById('agenda-inline-detalles').value
            };

            try {
                if(idEdit) {
                    await window.API.put('/api/calendario/' + idEdit, data);
                    if(window.Alerta) window.Alerta.exito("Actualizado", "La nota se ha modificado correctamente.");
                } else {
                    await window.API.post('/api/calendario', data);
                    if(window.Alerta) window.Alerta.exito("Guardado", "La nota se ha añadido a la agenda.");
                }
                
                limpiarFormularioAgendaInline();
                await cargarEventosDesdeBD(); 
            } catch(err) { 
                console.error(err); 
                if(window.Alerta) window.Alerta.error("Error", "Ocurrió un problema al guardar.");
            }
        });
    }

    // ==========================================
    // 6. CAJÓN DE RECORDATORIOS (DRAWER)
    // ==========================================
    const btnAbrirRecordatorios = document.getElementById('btn-abrir-recordatorios');
    if (btnAbrirRecordatorios) {
        btnAbrirRecordatorios.addEventListener('click', () => {
            document.getElementById('rec-id').value = '';
            formRecordatorio.reset();
            document.getElementById('rec-fecha').valueAsDate = new Date();
            document.getElementById('drawer-rec-titulo').textContent = 'Nuevo Recordatorio';
            
            cerrarModalAgendaLibro();
            cerrarModalCentral();

            drawerOverlay.classList.remove('hidden');
            setTimeout(() => drawerOverlay.classList.remove('opacity-0'), 10);
            drawerRecordatorios.classList.remove('translate-x-full');
        });
    }

    function cerrarDrawerRecordatorio() {
        drawerRecordatorios.classList.add('translate-x-full');
        if(modalAgendaLibro.classList.contains('hidden') && modalEvento.classList.contains('hidden')) {
            drawerOverlay.classList.add('opacity-0');
            setTimeout(() => drawerOverlay.classList.add('hidden'), 300);
        }
    }

    document.getElementById('btn-cerrar-drawer').addEventListener('click', cerrarDrawerRecordatorio);
    document.querySelector('.btn-cancelar-drawer').addEventListener('click', cerrarDrawerRecordatorio);
    
    // GUARDAR RECORDATORIO (CON FETCH Y ALERTAS)
    if(formRecordatorio) {
        formRecordatorio.addEventListener('submit', async (e) => {
            e.preventDefault();
            const idEdit = document.getElementById('rec-id').value;
            const data = {
                titulo: document.getElementById('rec-titulo').value,
                fecha: document.getElementById('rec-fecha').value,
                hora: document.getElementById('rec-hora').value,
                tipo: 'Recordatorio', 
                detalles: document.getElementById('rec-detalles').value
            };

            try {
                if(idEdit) {
                    await window.API.put('/api/calendario/' + idEdit, data);
                    if(window.Alerta) window.Alerta.exito("Actualizado", "El recordatorio se ha modificado.");
                } else {
                    await window.API.post('/api/calendario', data);
                    if(window.Alerta) window.Alerta.exito("Guardado", "El aviso se ha programado en el calendario.");
                }

                cerrarDrawerRecordatorio();
                await cargarEventosDesdeBD();
            } catch(err) { 
                console.error(err); 
                if(window.Alerta) window.Alerta.error("Error", "Hubo un fallo al guardar el recordatorio.");
            }
        });
    }


    // ==========================================
    // 7. MODAL NOTA RÁPIDA (Clic en calendario)
    // ==========================================
    function abrirModalNotaRapida(fechaValue, fechaTexto) {
        document.getElementById('evento-id').value = '';
        formEvento.reset();
        document.getElementById('evento-fecha').value = fechaValue;
        document.getElementById('evento-color').value = 'blue';
        aplicarColorAlSelector('color-selector-modal', 'blue');
        
        document.getElementById('modal-fecha-texto').textContent = fechaTexto;
        document.getElementById('modal-titulo-cabecera').textContent = 'Agregar Nota';
        
        cerrarModalAgendaLibro();
        cerrarDrawerRecordatorio();
        
        modalEvento.classList.remove('hidden'); modalEvento.classList.add('flex');
        setTimeout(() => { modalEvento.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
    }

    function cerrarModalCentral() {
        modalEvento.classList.add('opacity-0'); modalContent.classList.add('scale-95');
        setTimeout(() => { modalEvento.classList.add('hidden'); modalEvento.classList.remove('flex'); }, 300);
    }

    document.getElementById('btn-cerrar-modal').addEventListener('click', cerrarModalCentral);
    document.getElementById('btn-cancelar-modal').addEventListener('click', cerrarModalCentral);
    
    // GUARDAR NOTA RÁPIDA (CON FETCH Y ALERTAS)
    if(formEvento) {
        formEvento.addEventListener('submit', async (e) => {
            e.preventDefault();
            const idEdit = document.getElementById('evento-id').value;
            const data = {
                titulo: document.getElementById('evento-titulo').value,
                fecha: document.getElementById('evento-fecha').value,
                hora: document.getElementById('evento-hora').value,
                color: document.getElementById('evento-color').value,
                tipo: 'Nota',
                detalles: document.getElementById('evento-detalles').value
            };

            try {
                if(idEdit) {
                    await window.API.put('/api/calendario/' + idEdit, data);
                    if(window.Alerta) window.Alerta.exito("Actualizado", "Se guardaron los cambios de la nota.");
                } else {
                    await window.API.post('/api/calendario', data);
                    if(window.Alerta) window.Alerta.exito("Guardado", "Nota rápida añadida al calendario.");
                }

                cerrarModalCentral();
                await cargarEventosDesdeBD(); 
            } catch(error) { 
                console.error(error); 
                if(window.Alerta) window.Alerta.error("Error", "No se pudo guardar la nota.");
            }
        });
    }

    // Overlay global cierra todo
    drawerOverlay.addEventListener('click', () => { 
        cerrarModalCentral(); 
        cerrarDrawerRecordatorio(); 
        cerrarModalAgendaLibro(); 
    });

    // Controles de mes principales
    document.getElementById('btn-prev').addEventListener('click', () => { mesActual--; if (mesActual < 0) { mesActual = 11; anioActual--; } miniMesActual = mesActual; miniAnioActual = anioActual; actualizarVistas(); });
    document.getElementById('btn-next').addEventListener('click', () => { mesActual++; if (mesActual > 11) { mesActual = 0; anioActual++; } miniMesActual = mesActual; miniAnioActual = anioActual; actualizarVistas(); });
    document.getElementById('btn-hoy').addEventListener('click', () => { const hoy = new Date(); mesActual = hoy.getMonth(); anioActual = hoy.getFullYear(); miniMesActual = mesActual; miniAnioActual = anioActual; actualizarVistas(); });
    
    const minP = document.getElementById('mini-prev');
    const minN = document.getElementById('mini-next');
    if(minP) minP.addEventListener('click', () => { miniMesActual--; if (miniMesActual < 0) { miniMesActual = 11; miniAnioActual--; } renderizarMiniCalendario(); });
    if(minN) minN.addEventListener('click', () => { miniMesActual++; if (miniMesActual > 11) { miniMesActual = 0; miniAnioActual++; } renderizarMiniCalendario(); });

    // Iniciar pintando el calendario vacío para que no haya pantalla gris
    actualizarVistas();
    // Cargar desde la base de datos en segundo plano
    cargarEventosDesdeBD();
});