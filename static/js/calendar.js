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
    const titulo = document.getElementById('mes-anio-titulo');
    const miniGrid = document.getElementById('mini-calendario-grid');
    const miniTitulo = document.getElementById('mini-mes-anio');
    const listaNotasMes = document.getElementById('lista-notas-mes');
    const contadorNotasMes = document.getElementById('contador-notas-mes');
    const modal = document.getElementById('modal-evento');
    const modalContent = document.getElementById('modal-content');
    const form = document.getElementById('form-evento');

    // ==========================================
    // FUNCIÓN MAESTRA: Sincroniza todas las vistas
    // ==========================================
    function actualizarVistas() {
        renderizarCalendario();
        renderizarMiniCalendario();
        renderizarNotasDelMes();
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
        celda.className = `bg-white p-2 flex flex-col transition-colors min-h-[130px] ${esMesDiferente ? 'text-slate-400 bg-slate-50/50' : 'text-slate-700'}`;
        
        // Drag & Drop
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
        divHeader.className = "flex justify-end mb-2 pointer-events-none"; 
        
        const numeroDia = document.createElement('span');
        numeroDia.className = `text-sm font-semibold w-7 h-7 flex items-center justify-center rounded-full ${esHoy ? 'bg-blue-800 text-white shadow-md' : ''}`;
        numeroDia.textContent = dia;
        
        divHeader.appendChild(numeroDia);
        celda.appendChild(divHeader);

        const contenedorNotas = document.createElement('div');
        contenedorNotas.className = 'flex flex-col gap-1.5 overflow-y-auto max-h-full custom-scrollbar pr-1';
        
        if (notas[fechaFormateada]) {
            notas[fechaFormateada].forEach(nota => {
                const badge = document.createElement('div');
                
                // NUEVO DISEÑO: Colores Sólidos 100% (Nada transparente)
                let colorFondo = '';
                switch(nota.estado) {
                    case 'confirmada': colorFondo = 'bg-emerald-600 hover:bg-emerald-700'; break;
                    case 'pendiente':  colorFondo = 'bg-amber-500 hover:bg-amber-600'; break;
                    case 'cancelada':  colorFondo = 'bg-rose-600 hover:bg-rose-700'; break;
                    default:           colorFondo = 'bg-slate-600 hover:bg-slate-700'; break;
                }

                badge.className = `${colorFondo} text-white p-2 rounded-md shadow-sm cursor-grab active:cursor-grabbing transition-all flex flex-col`;
                
                // Ahora muestra Paciente, Hora y Nota directamente
                badge.innerHTML = `
                    <div class="flex justify-between items-start gap-1">
                        <span class="text-xs font-bold truncate">${nota.titulo}</span>
                        <span class="text-[10px] font-semibold opacity-90 whitespace-nowrap">${nota.hora || '--:--'}</span>
                    </div>
                    ${nota.detalles ? `<span class="text-[10px] truncate opacity-90 mt-0.5">${nota.detalles}</span>` : ''}
                `;
                
                badge.title = `Paciente: ${nota.titulo}\nHora: ${nota.hora || 'Sin asignar'}\nTipo: ${nota.tipo}\nEstado: ${nota.estado.toUpperCase()}\nNotas: ${nota.detalles || 'Ninguna'}`;
                
                badge.draggable = true;
                badge.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/plain', JSON.stringify({ fecha: fechaFormateada, id: nota.id }));
                });

                badge.ondblclick = (e) => {
                    e.stopPropagation();
                    if(confirm(`¿Deseas eliminar la cita de ${nota.titulo}?`)) {
                        const idx = notas[fechaFormateada].findIndex(n => n.id === nota.id);
                        notas[fechaFormateada].splice(idx, 1);
                        actualizarVistas();
                    }
                };
                contenedorNotas.appendChild(badge);
            });
        }
        celda.appendChild(contenedorNotas);
        grid.appendChild(celda);
    }

    // ==========================================
    // 2. PANEL LATERAL: NOTAS DEL MES
    // ==========================================
    function renderizarNotasDelMes() {
        if (!listaNotasMes) return;
        listaNotasMes.innerHTML = '';
        let notasDelMes = [];
        
        // Filtrar solo las notas del mes actual
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
            listaNotasMes.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-slate-400 mt-10"><svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><p class="text-sm font-medium">Sin citas este mes</p></div>`;
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
            card.className = "bg-white border border-slate-200 rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden mb-3";
            
            let colorPunto = '';
            switch(nota.estado) {
                case 'confirmada': colorPunto = 'bg-emerald-500'; break;
                case 'pendiente':  colorPunto = 'bg-amber-500'; break;
                case 'cancelada':  colorPunto = 'bg-rose-500'; break;
                default:           colorPunto = 'bg-slate-500'; break;
            }

            card.innerHTML = `
                <div class="absolute left-0 top-0 bottom-0 w-1.5 ${colorPunto}"></div>
                <div class="pl-2">
                    <div class="flex justify-between items-start mb-1.5">
                        <span class="font-bold text-slate-800 text-xs">${dia} ${meses[mesActual].substring(0,3)}</span>
                        <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">${nota.hora || 'Todo el día'}</span>
                    </div>
                    <p class="font-bold text-slate-700 text-sm leading-tight">${nota.titulo}</p>
                    ${nota.detalles ? `<p class="text-xs text-slate-500 mt-1 line-clamp-2">${nota.detalles}</p>` : ''}
                    <div class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex justify-between">
                        <span>${nota.tipo}</span>
                        <span>${nota.estado}</span>
                    </div>
                </div>
            `;
            listaNotasMes.appendChild(card);
        });
    }

    // ==========================================
    // 3. MINI CALENDARIO
    // ==========================================
    function renderizarMiniCalendario() {
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
    // 4. CONTROLES Y EVENTOS DEL MODAL
    // ==========================================
    document.getElementById('btn-prev').addEventListener('click', () => {
        mesActual--; if (mesActual < 0) { mesActual = 11; anioActual--; }
        miniMesActual = mesActual; miniAnioActual = anioActual; 
        actualizarVistas();
    });
    document.getElementById('btn-next').addEventListener('click', () => {
        mesActual++; if (mesActual > 11) { mesActual = 0; anioActual++; }
        miniMesActual = mesActual; miniAnioActual = anioActual; 
        actualizarVistas();
    });
    document.getElementById('btn-hoy').addEventListener('click', () => {
        const hoy = new Date(); 
        mesActual = hoy.getMonth(); anioActual = hoy.getFullYear(); 
        miniMesActual = mesActual; miniAnioActual = anioActual;
        actualizarVistas();
    });

    document.getElementById('mini-prev').addEventListener('click', () => {
        miniMesActual--; if (miniMesActual < 0) { miniMesActual = 11; miniAnioActual--; }
        renderizarMiniCalendario();
    });
    document.getElementById('mini-next').addEventListener('click', () => {
        miniMesActual++; if (miniMesActual > 11) { miniMesActual = 0; miniAnioActual++; }
        renderizarMiniCalendario();
    });

    function abrirModal(fechaValue, fechaTexto) {
        document.getElementById('evento-fecha').value = fechaValue;
        document.getElementById('modal-fecha-texto').textContent = fechaTexto;
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
    }

    function cerrarModal() {
        modal.classList.add('opacity-0'); modalContent.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); form.reset(); }, 300);
    }

    document.getElementById('btn-cerrar-modal').addEventListener('click', cerrarModal);
    document.getElementById('btn-cancelar-modal').addEventListener('click', cerrarModal);
    
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

        cerrarModal();
        actualizarVistas(); // Esto asegura que la nota aparezca instantáneamente en la lista lateral
    });

    // INICIAR TODO
    actualizarVistas();
});