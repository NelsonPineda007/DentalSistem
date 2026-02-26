document.addEventListener('DOMContentLoaded', () => {
    // Variables de fecha
    let fechaActual = new Date();
    let mesActual = fechaActual.getMonth();
    let anioActual = fechaActual.getFullYear();
    
    let miniMesActual = mesActual;
    let miniAnioActual = anioActual;
    
    let idContador = 1;
    const notas = {}; 
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // Elementos del DOM
    const grid = document.getElementById('calendario-grid');
    const titulo = document.getElementById('mes-anio-titulo');
    const miniGrid = document.getElementById('mini-calendario-grid');
    const miniTitulo = document.getElementById('mini-mes-anio');
    const listaNotasMes = document.getElementById('lista-notas-mes');
    const contadorNotasMes = document.getElementById('contador-notas-mes');
    
    // Modal Principal
    const modal = document.getElementById('modal-evento');
    const modalContent = document.getElementById('modal-content');
    const form = document.getElementById('form-evento');

    // Elementos del Drawer
    const drawer = document.getElementById('drawer-recordatorios');
    const drawerOverlay = document.getElementById('drawer-overlay');
    
    const tabRecordatorio = document.getElementById('tab-recordatorio');
    const tabNota = document.getElementById('tab-nota');
    const tabAgenda = document.getElementById('tab-agenda');
    
    const contentRecordatorio = document.getElementById('content-recordatorio');
    const contentNota = document.getElementById('content-nota');
    const contentAgenda = document.getElementById('content-agenda');
    
    const listaAgendaRecordatorios = document.getElementById('lista-agenda-recordatorios');
    const listaAgendaNotas = document.getElementById('lista-agenda-notas');
    const agendaMesAnio = document.getElementById('agenda-mes-anio');

    // Fechas por defecto
    const recFecha = document.getElementById('rec-fecha');
    const notaFecha = document.getElementById('nota-fecha');
    if (recFecha) recFecha.valueAsDate = new Date();
    if (notaFecha) notaFecha.valueAsDate = new Date();

    function actualizarVistas() {
        if(grid) renderizarCalendario();
        if(miniGrid) renderizarMiniCalendario();
        if(listaNotasMes) renderizarNotasDelMes();
        if(listaAgendaRecordatorios && listaAgendaNotas) renderizarResumenAgenda(); 
    }

    // ==========================================
    // 1. CALENDARIO PRINCIPAL
    // ==========================================
    function renderizarCalendario() {
        grid.innerHTML = '';
        titulo.textContent = `${meses[mesActual]} ${anioActual}`;

        const primerDiaDelMes = new Date(anioActual, mesActual, 1).getDay();
        const diasDelMes = new Date(anioActual, mesActual + 1, 0).getDate();
        const diasMesAnterior = new Date(anioActual, mesActual, 0).getDate();

        for (let i = primerDiaDelMes; i > 0; i--) { crearCeldaDia(diasMesAnterior - i + 1, mesActual - 1, anioActual, true); }
        for (let i = 1; i <= diasDelMes; i++) { crearCeldaDia(i, mesActual, anioActual, false); }
        const diasRestantes = 42 - grid.children.length;
        for (let i = 1; i <= diasRestantes; i++) { crearCeldaDia(i, mesActual + 1, anioActual, true); }
    }

    function crearCeldaDia(dia, mes, anio, esMesDiferente) {
        let m = mes; let a = anio;
        if (m < 0) { m = 11; a--; }
        if (m > 11) { m = 0; a++; }

        const fechaFormateada = `${a}-${String(m + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        const hoy = new Date();
        const esHoy = dia === hoy.getDate() && m === hoy.getMonth() && a === hoy.getFullYear();

        const celda = document.createElement('div');
        celda.className = `bg-white p-1 md:p-2 flex flex-col transition-colors min-h-[100px] md:min-h-[130px] ${esMesDiferente ? 'text-slate-400 bg-slate-50/50' : 'text-slate-700'}`;
        
        celda.addEventListener('dragover', (e) => { e.preventDefault(); celda.classList.add('bg-blue-50'); });
        celda.addEventListener('dragleave', () => { celda.classList.remove('bg-blue-50'); });
        celda.addEventListener('drop', (e) => {
            e.preventDefault();
            celda.classList.remove('bg-blue-50');
            const dataStr = e.dataTransfer.getData('text/plain');
            if(!dataStr) return;
            
            const data = JSON.parse(dataStr);
            if (data.fecha !== fechaFormateada) {
                const index = notas[data.fecha].findIndex(n => n.id === data.id);
                if (index > -1) {
                    const citaMovida = notas[data.fecha].splice(index, 1)[0];
                    if (!notas[fechaFormateada]) notas[fechaFormateada] = [];
                    notas[fechaFormateada].push(citaMovida);
                    actualizarVistas(); 
                }
            }
        });

        celda.onclick = (e) => {
            if(e.target === celda || e.target.parentElement === celda) {
                abrirModal(fechaFormateada, `${dia} de ${meses[m]} del ${a}`);
            }
        };

        const divHeader = document.createElement('div');
        divHeader.className = "flex justify-end mb-1 md:mb-2 pointer-events-none"; 
        
        const numeroDia = document.createElement('span');
        numeroDia.className = `text-xs md:text-sm font-semibold w-6 h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full ${esHoy ? 'bg-blue-800 text-white shadow-md' : ''}`;
        numeroDia.textContent = dia;
        
        divHeader.appendChild(numeroDia);
        celda.appendChild(divHeader);

        const contenedorNotas = document.createElement('div');
        contenedorNotas.className = 'flex flex-col gap-1.5 md:gap-2 overflow-y-auto max-h-full custom-scrollbar pr-0.5 md:pr-1';
        
        if (notas[fechaFormateada]) {
            notas[fechaFormateada].forEach(nota => {
                const badge = document.createElement('div');
                
                let colorFondo = '';
                if(nota.tipo === 'Recordatorio') { colorFondo = 'bg-amber-500 hover:bg-amber-600'; }
                else if(nota.tipo === 'Nota') { colorFondo = 'bg-slate-500 hover:bg-slate-600'; }
                else {
                    switch(nota.estado) {
                        case 'confirmada': colorFondo = 'bg-emerald-600 hover:bg-emerald-700'; break;
                        case 'pendiente':  colorFondo = 'bg-blue-500 hover:bg-blue-600'; break;
                        case 'cancelada':  colorFondo = 'bg-rose-600 hover:bg-rose-700'; break;
                        default:           colorFondo = 'bg-slate-600 hover:bg-slate-700'; break;
                    }
                }

                // Ajuste visual clave: Padding "p-2", texto más grande "text-xs md:text-sm" y "line-clamp-2"
                badge.className = `${colorFondo} text-white p-2 rounded-md shadow-sm cursor-grab active:cursor-grabbing transition-all flex flex-col group relative overflow-hidden`;
                
                badge.innerHTML = `
                    <div class="flex justify-between items-start gap-1">
                        <span class="text-[11px] md:text-sm font-bold line-clamp-2 leading-tight">${nota.titulo}</span>
                        <div class="flex items-center gap-1 shrink-0 mt-0.5">
                            <span class="text-[10px] md:text-xs font-semibold opacity-90 whitespace-nowrap group-hover:hidden">${nota.hora || '--:--'}</span>
                            <button class="btn-eliminar-calendario hidden group-hover:block text-white hover:text-red-200 transition-colors" title="Borrar">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                    ${nota.detalles ? `<span class="text-[10px] md:text-xs line-clamp-2 opacity-90 mt-1 leading-tight">${nota.detalles}</span>` : ''}
                `;
                
                badge.title = `Título: ${nota.titulo}\nHora: ${nota.hora || 'Sin asignar'}\nTipo: ${nota.tipo}`;
                
                badge.draggable = true;
                badge.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/plain', JSON.stringify({ fecha: fechaFormateada, id: nota.id }));
                });

                // Funcionalidad de borrar desde el calendario
                const btnBorrar = badge.querySelector('.btn-eliminar-calendario');
                if(btnBorrar) {
                    btnBorrar.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if(confirm(`¿Deseas eliminar permanentemente "${nota.titulo}"?`)) {
                            const idx = notas[fechaFormateada].findIndex(n => n.id === nota.id);
                            if(idx > -1) {
                                notas[fechaFormateada].splice(idx, 1);
                                actualizarVistas();
                            }
                        }
                    });
                }

                contenedorNotas.appendChild(badge);
            });
        }
        celda.appendChild(contenedorNotas);
        grid.appendChild(celda);
    }

    // ==========================================
    // 2. PANEL IZQUIERDO: NOTAS DEL MES
    // ==========================================
    function renderizarNotasDelMes() {
        if (!listaNotasMes) return;
        listaNotasMes.innerHTML = '';
        let notasDelMes = [];
        
        const prefijoMes = `${anioActual}-${String(mesActual + 1).padStart(2, '0')}`;
        
        for (let fecha in notas) {
            if (fecha.startsWith(prefijoMes)) {
                notas[fecha].forEach(nota => {
                    notasDelMes.push({ fecha, ...nota });
                });
            }
        }
        
        contadorNotasMes.textContent = notasDelMes.length;
        
        if (notasDelMes.length === 0) {
            listaNotasMes.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-slate-400 mt-10"><p class="text-sm font-medium text-center px-4">Sin registros este mes</p></div>`;
            return;
        }

        notasDelMes.sort((a, b) => {
            if (a.fecha !== b.fecha) return a.fecha > b.fecha ? 1 : -1;
            return (a.hora || '24:00') > (b.hora || '24:00') ? 1 : -1;
        });

        notasDelMes.forEach(nota => {
            const parts = nota.fecha.split('-');
            const dia = parts[2];
            
            const card = document.createElement('div');
            card.className = "bg-white border border-slate-200 rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden mb-3 group";
            
            let colorPunto = '';
            if(nota.tipo === 'Recordatorio') { colorPunto = 'bg-amber-500'; }
            else if(nota.tipo === 'Nota') { colorPunto = 'bg-slate-500'; }
            else {
                switch(nota.estado) {
                    case 'confirmada': colorPunto = 'bg-emerald-500'; break;
                    case 'pendiente':  colorPunto = 'bg-blue-500'; break;
                    case 'cancelada':  colorPunto = 'bg-rose-500'; break;
                    default:           colorPunto = 'bg-slate-500'; break;
                }
            }

            card.innerHTML = `
                <div class="absolute left-0 top-0 bottom-0 w-1.5 ${colorPunto}"></div>
                <div class="pl-2">
                    <div class="flex justify-between items-start mb-1.5 gap-2">
                        <span class="font-bold text-slate-800 text-xs shrink-0">${dia} ${meses[mesActual].substring(0,3)}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">${nota.hora || 'Todo el día'}</span>
                            <button class="btn-eliminar-lateral text-slate-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100" title="Eliminar registro">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                    <p class="font-bold text-slate-700 text-sm leading-tight pr-4">${nota.titulo}</p>
                    ${nota.detalles ? `<p class="text-xs text-slate-500 mt-1 line-clamp-2">${nota.detalles}</p>` : ''}
                    <div class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex justify-between gap-2">
                        <span class="truncate">${nota.tipo}</span>
                        <span class="truncate text-right">${nota.estado || ''}</span>
                    </div>
                </div>
            `;
            
            // Lógica para borrar desde el panel izquierdo
            const btnBorrarLat = card.querySelector('.btn-eliminar-lateral');
            if(btnBorrarLat) {
                btnBorrarLat.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if(confirm(`¿Deseas eliminar permanentemente "${nota.titulo}"?`)) {
                        const idx = notas[nota.fecha].findIndex(n => n.id === nota.id);
                        if(idx > -1) {
                            notas[nota.fecha].splice(idx, 1);
                            actualizarVistas();
                        }
                    }
                });
            }

            listaNotasMes.appendChild(card);
        });
    }

    // ==========================================
    // 3. MINI CALENDARIO
    // ==========================================
    function renderizarMiniCalendario() {
        if(!miniGrid) return;
        miniGrid.innerHTML = '';
        miniTitulo.textContent = `${meses[miniMesActual]} ${miniAnioActual}`;

        const primerDiaDelMes = new Date(miniAnioActual, miniMesActual, 1).getDay();
        const diasDelMes = new Date(miniAnioActual, miniMesActual + 1, 0).getDate();
        const diasMesAnterior = new Date(miniAnioActual, miniMesActual, 0).getDate();

        for (let i = primerDiaDelMes; i > 0; i--) { crearCeldaMiniDia(diasMesAnterior - i + 1, miniMesActual - 1, miniAnioActual, true); }
        for (let i = 1; i <= diasDelMes; i++) { crearCeldaMiniDia(i, miniMesActual, miniAnioActual, false); }
        const diasRestantes = 42 - miniGrid.children.length;
        for (let i = 1; i <= diasRestantes; i++) { crearCeldaMiniDia(i, miniMesActual + 1, miniAnioActual, true); }
    }

    function crearCeldaMiniDia(dia, mes, anio, esMesDiferente) {
        let m = mes; let a = anio;
        if (m < 0) { m = 11; a--; }
        if (m > 11) { m = 0; a++; }

        const hoy = new Date();
        const esHoy = dia === hoy.getDate() && m === hoy.getMonth() && a === hoy.getFullYear();

        const celda = document.createElement('div');
        celda.className = `p-1.5 flex items-center justify-center cursor-pointer rounded-full transition-colors 
                           ${esMesDiferente ? 'text-slate-300' : 'text-slate-600 hover:bg-slate-100 font-medium'} 
                           ${esHoy && !esMesDiferente ? 'bg-blue-800 text-white hover:bg-blue-900 shadow-md font-bold' : ''}`;
        celda.textContent = dia;

        celda.onclick = () => {
            mesActual = m; anioActual = a;
            actualizarVistas();
        };
        miniGrid.appendChild(celda);
    }

    // ==========================================
    // 4. LÓGICA DEL DRAWER LATERAL (PESTAÑAS)
    // ==========================================

    const btnAbrirDrawer = document.getElementById('btn-abrir-recordatorios');
    if (btnAbrirDrawer && drawer && drawerOverlay) {
        btnAbrirDrawer.addEventListener('click', () => {
            drawerOverlay.classList.remove('hidden');
            setTimeout(() => drawerOverlay.classList.remove('opacity-0'), 10);
            drawer.classList.remove('translate-x-full');
            drawer.classList.add('translate-x-0');
            renderizarResumenAgenda(); 
        });
    }

    function cerrarDrawer() {
        if(drawer) {
            drawer.classList.add('translate-x-full');
            drawer.classList.remove('translate-x-0');
        }
        if(drawerOverlay) {
            drawerOverlay.classList.add('opacity-0');
            setTimeout(() => drawerOverlay.classList.add('hidden'), 300);
        }
    }

    const btnCerrarDrawer = document.getElementById('btn-cerrar-drawer');
    const botonesCancelarDrawer = document.querySelectorAll('.btn-cancelar-drawer');
    
    if (btnCerrarDrawer) btnCerrarDrawer.addEventListener('click', cerrarDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', cerrarDrawer);
    botonesCancelarDrawer.forEach(btn => btn.addEventListener('click', cerrarDrawer));

    // Sistema Robusto de Pestañas
    const defaultTabClass = "flex-1 pb-3 pt-4 text-xs md:text-sm font-bold text-slate-400 hover:text-slate-600 border-b-2 border-transparent transition-colors text-center";
    const activeTabClass = "flex-1 pb-3 pt-4 text-xs md:text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors text-center";

    function resetearPestanas() {
        const tabs = [tabRecordatorio, tabNota, tabAgenda];
        tabs.forEach(tab => {
            if(!tab) return;
            tab.className = defaultTabClass;
        });
        if(contentRecordatorio) contentRecordatorio.classList.add('hidden');
        if(contentNota) contentNota.classList.add('hidden');
        if(contentAgenda) contentAgenda.classList.add('hidden');
    }

    function activarPestana(tabBtn, contentDiv, esAgenda = false) {
        if(!tabBtn || !contentDiv) return;
        resetearPestanas();
        tabBtn.className = activeTabClass;
        contentDiv.classList.remove('hidden');
        if(esAgenda) renderizarResumenAgenda();
    }

    if(tabRecordatorio) tabRecordatorio.addEventListener('click', () => activarPestana(tabRecordatorio, contentRecordatorio));
    if(tabNota) tabNota.addEventListener('click', () => activarPestana(tabNota, contentNota));
    if(tabAgenda) tabAgenda.addEventListener('click', () => activarPestana(tabAgenda, contentAgenda, true));

    // Guardado desde Drawer
    function guardarDesdePanel(prefix) {
        const fechaInput = document.getElementById(`${prefix}-fecha`);
        const horaInput = document.getElementById(`${prefix}-hora`);
        const tituloInput = document.getElementById(`${prefix}-titulo`);
        const detallesInput = document.getElementById(`${prefix}-detalles`);
        const tipoInput = document.getElementById(`${prefix}-tipo`);

        if(!fechaInput || !tituloInput) return;

        const fecha = fechaInput.value;
        const nuevaNota = {
            id: idContador++,
            titulo: tituloInput.value,
            hora: horaInput ? horaInput.value : '',
            tipo: tipoInput ? tipoInput.value : 'Registro', 
            estado: '', 
            detalles: detallesInput ? detallesInput.value : ''
        };

        if (!notas[fecha]) notas[fecha] = [];
        notas[fecha].push(nuevaNota);
        notas[fecha].sort((a, b) => (a.hora > b.hora) ? 1 : -1);

        // Limpiar inputs
        tituloInput.value = '';
        if(horaInput) horaInput.value = '';
        if(detallesInput) detallesInput.value = '';

        actualizarVistas();
        activarPestana(tabAgenda, contentAgenda, true);
    }

    const formRecordatorio = document.getElementById('form-recordatorio');
    if(formRecordatorio) {
        formRecordatorio.addEventListener('submit', (e) => {
            e.preventDefault();
            guardarDesdePanel('rec');
        });
    }

    const formNota = document.getElementById('form-nota');
    if(formNota) {
        formNota.addEventListener('submit', (e) => {
            e.preventDefault();
            guardarDesdePanel('nota');
        });
    }

    // ==========================================
    // HISTORIAL EN AGENDA (FILTRADO POR MES CON BOTÓN DE BORRAR)
    // ==========================================
    const agendaPrev = document.getElementById('agenda-prev');
    const agendaNext = document.getElementById('agenda-next');

    if(agendaPrev) {
        agendaPrev.addEventListener('click', () => {
            mesActual--; if (mesActual < 0) { mesActual = 11; anioActual--; }
            miniMesActual = mesActual; miniAnioActual = anioActual; 
            actualizarVistas();
        });
    }
    if(agendaNext) {
        agendaNext.addEventListener('click', () => {
            mesActual++; if (mesActual > 11) { mesActual = 0; anioActual++; }
            miniMesActual = mesActual; miniAnioActual = anioActual; 
            actualizarVistas();
        });
    }

    function renderizarResumenAgenda() {
        if (!listaAgendaRecordatorios || !listaAgendaNotas) return;
        
        listaAgendaRecordatorios.innerHTML = '';
        listaAgendaNotas.innerHTML = '';
        
        // Actualizar el título del mes en la Agenda
        if(agendaMesAnio) {
            agendaMesAnio.textContent = `${meses[mesActual]} ${anioActual}`;
        }
        
        let arrRecordatorios = [];
        let arrNotas = [];
        
        // El prefijo nos ayuda a buscar solo las del mes que estamos viendo
        const prefijoMes = `${anioActual}-${String(mesActual + 1).padStart(2, '0')}`;
        
        for (let fecha in notas) {
            if (fecha.startsWith(prefijoMes)) {
                notas[fecha].forEach(nota => {
                    if(nota.tipo === 'Recordatorio') {
                        arrRecordatorios.push({fecha, ...nota});
                    } else if(nota.tipo === 'Nota') {
                        arrNotas.push({fecha, ...nota});
                    }
                });
            }
        }

        const ordenar = (a, b) => {
            if (a.fecha !== b.fecha) return a.fecha > b.fecha ? 1 : -1;
            return (a.hora || '24:00') > (b.hora || '24:00') ? 1 : -1;
        };

        arrRecordatorios.sort(ordenar);
        arrNotas.sort(ordenar);

        const dibujarTarjeta = (nota, colorBorde, colorTexto) => {
            const el = document.createElement('div');
            el.className = `bg-white p-3 md:p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col hover:border-${colorBorde} hover:shadow-md transition-all relative group`;
            
            const dPart = nota.fecha.split('-');
            const textoFecha = `${dPart[2]} de ${meses[parseInt(dPart[1])-1]}`; 

            el.innerHTML = `
                <div class="flex justify-between items-start mb-2 gap-2">
                    <span class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">${textoFecha}</span>
                    <div class="flex items-center gap-2 shrink-0">
                        ${nota.hora ? `<span class="text-[9px] md:text-[10px] font-bold bg-slate-50 text-slate-500 px-2 py-0.5 rounded border border-slate-100">${nota.hora}</span>` : ''}
                        <button class="btn-eliminar-agenda text-slate-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100" title="Eliminar registro">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
                <p class="font-bold text-slate-800 text-sm md:text-[15px] leading-tight text-${colorTexto} pr-2">${nota.titulo}</p>
                ${nota.detalles ? `<p class="text-[11px] md:text-xs text-slate-500 mt-2 line-clamp-2">${nota.detalles}</p>` : ''}
            `;

            // Lógica de borrar individualmente desde la agenda
            const btnBorrar = el.querySelector('.btn-eliminar-agenda');
            if(btnBorrar) {
                btnBorrar.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if(confirm(`¿Deseas eliminar permanentemente "${nota.titulo}"?`)) {
                        const idx = notas[nota.fecha].findIndex(n => n.id === nota.id);
                        if(idx > -1) {
                            notas[nota.fecha].splice(idx, 1);
                            actualizarVistas();
                        }
                    }
                });
            }

            return el;
        };

        if (arrRecordatorios.length === 0) {
            listaAgendaRecordatorios.innerHTML = `<p class="text-[11px] md:text-sm text-slate-400 italic bg-slate-50 p-3 rounded-lg border border-slate-100 text-center">No hay recordatorios este mes.</p>`;
        } else {
            arrRecordatorios.forEach(nota => listaAgendaRecordatorios.appendChild(dibujarTarjeta(nota, 'amber-300', 'amber-700')));
        }

        if (arrNotas.length === 0) {
            listaAgendaNotas.innerHTML = `<p class="text-[11px] md:text-sm text-slate-400 italic bg-slate-50 p-3 rounded-lg border border-slate-100 text-center">No hay notas guardadas este mes.</p>`;
        } else {
            arrNotas.forEach(nota => listaAgendaNotas.appendChild(dibujarTarjeta(nota, 'blue-300', 'blue-800')));
        }
    }

    // ==========================================
    // 5. CONTROLES GENERALES Y MODAL DE CITAS
    // ==========================================
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnHoy = document.getElementById('btn-hoy');
    const miniPrev = document.getElementById('mini-prev');
    const miniNext = document.getElementById('mini-next');

    if(btnPrev) {
        btnPrev.addEventListener('click', () => {
            mesActual--; if (mesActual < 0) { mesActual = 11; anioActual--; }
            miniMesActual = mesActual; miniAnioActual = anioActual; 
            actualizarVistas();
        });
    }

    if(btnNext) {
        btnNext.addEventListener('click', () => {
            mesActual++; if (mesActual > 11) { mesActual = 0; anioActual++; }
            miniMesActual = mesActual; miniAnioActual = anioActual; 
            actualizarVistas();
        });
    }

    if(btnHoy) {
        btnHoy.addEventListener('click', () => {
            const hoy = new Date(); 
            mesActual = hoy.getMonth(); anioActual = hoy.getFullYear(); 
            miniMesActual = mesActual; miniAnioActual = anioActual;
            actualizarVistas();
        });
    }

    if(miniPrev) {
        miniPrev.addEventListener('click', () => {
            miniMesActual--; if (miniMesActual < 0) { miniMesActual = 11; miniAnioActual--; }
            renderizarMiniCalendario();
        });
    }

    if(miniNext) {
        miniNext.addEventListener('click', () => {
            miniMesActual++; if (miniMesActual > 11) { miniMesActual = 0; miniAnioActual++; }
            renderizarMiniCalendario();
        });
    }

    function abrirModal(fechaValue, fechaTexto) {
        const fechaInput = document.getElementById('evento-fecha');
        const fechaText = document.getElementById('modal-fecha-texto');
        if(fechaInput) fechaInput.value = fechaValue;
        if(fechaText) fechaText.textContent = fechaTexto;
        
        if(modal && modalContent) {
            modal.classList.remove('hidden'); modal.classList.add('flex');
            setTimeout(() => { modal.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
        }
    }

    function cerrarModalCentral() {
        if(modal && modalContent) {
            modal.classList.add('opacity-0'); modalContent.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); if(form) form.reset(); }, 300);
        }
    }

    const btnCerrarModal = document.getElementById('btn-cerrar-modal');
    const btnCancelarModal = document.getElementById('btn-cancelar-modal');
    if(btnCerrarModal) btnCerrarModal.addEventListener('click', cerrarModalCentral);
    if(btnCancelarModal) btnCancelarModal.addEventListener('click', cerrarModalCentral);
    
    if(form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const fecha = document.getElementById('evento-fecha').value;
            const nuevaNota = {
                id: idContador++,
                titulo: document.getElementById('evento-titulo').value,
                hora: document.getElementById('evento-hora').value,
                estado: document.getElementById('evento-estado').value,
                tipo: document.getElementById('evento-tipo').value,
                detalles: document.getElementById('evento-detalles').value
            };

            if (!notas[fecha]) notas[fecha] = [];
            notas[fecha].push(nuevaNota);
            notas[fecha].sort((a, b) => (a.hora > b.hora) ? 1 : -1);

            cerrarModalCentral();
            actualizarVistas(); 
        });
    }

    // Inicializar
    actualizarVistas();
});