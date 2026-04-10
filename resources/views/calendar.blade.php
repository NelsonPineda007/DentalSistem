{{-- header y nav incrustados --}}
@include('header')
@include('nav')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<main class="flex-1 p-3 md:p-6 bg-[#f8fafc] h-screen flex flex-col overflow-y-auto lg:overflow-hidden">
    
    {{-- TÍTULO MÓVIL --}}
    <div class="lg:hidden flex items-center justify-center mb-3 shrink-0">
        <h2 class="text-2xl font-black text-[#1e293b] capitalize" id="mobile-mes-anio">Cargando...</h2>
    </div>

    {{-- BARRA SUPERIOR DE BOTONES --}}
    <div class="flex justify-center md:justify-end mb-4 md:mb-6 shrink-0 w-full">
        <div class="grid grid-cols-2 md:flex md:flex-row items-center gap-2 md:gap-3 w-full md:w-auto">
            <button id="btn-abrir-recordatorios" class="col-span-1 bg-[#eab308] hover:bg-[#ca8a04] text-white px-3 md:px-5 py-2 md:py-2.5 text-[11px] md:text-sm rounded-xl shadow-sm font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="truncate">Recordatorios</span>
            </button>

            <button id="btn-agenda" class="col-span-1 bg-blue-800 hover:bg-blue-900 text-white px-3 md:px-5 py-2 md:py-2.5 text-[11px] md:text-sm rounded-xl shadow-sm font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="truncate">Agenda Libreta</span>
            </button>

            <button id="btn-hoy" class="col-span-1 px-4 py-2 md:py-2.5 bg-white border border-slate-200 text-slate-700 font-extrabold rounded-xl shadow-sm text-[11px] md:text-sm flex justify-center items-center hover:bg-slate-50 transition-colors">
                Hoy
            </button>
            <div class="col-span-1 flex bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden h-full">
                <button id="btn-prev" class="flex-1 flex justify-center items-center px-2 py-2 text-slate-600 border-r border-slate-200 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="btn-next" class="flex-1 flex justify-center items-center px-2 py-2 text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- CUERPO PRINCIPAL --}}
    <div class="flex flex-col lg:flex-row flex-1 gap-4 lg:gap-6 min-h-0 overflow-y-auto lg:overflow-hidden pb-10 lg:pb-0">
        
        {{-- BARRA LATERAL IZQUIERDA --}}
        <div class="w-full lg:w-[280px] xl:w-[320px] flex flex-col gap-4 lg:gap-6 shrink-0 order-2 lg:order-1 lg:h-full lg:overflow-hidden">
            
            <div class="hidden lg:block shrink-0">
                <h2 class="text-3xl font-black text-[#1e293b] tracking-tight capitalize" id="sidebar-mes-anio">Cargando...</h2>
            </div>
            
            <div class="bg-white rounded-xl md:rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5 shrink-0 hidden lg:block">
                <div class="flex justify-between items-center mb-4">
                    <span id="mini-mes-anio" class="font-extrabold text-slate-800 text-xs md:text-sm capitalize"></span>
                    <div class="flex gap-1.5">
                        <button id="mini-prev" class="p-1 text-slate-400 hover:text-blue-600 bg-slate-50 hover:bg-blue-50 rounded transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                        <button id="mini-next" class="p-1 text-slate-400 hover:text-blue-600 bg-slate-50 hover:bg-blue-50 rounded transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                    </div>
                </div>
                <div class="grid grid-cols-7 text-center text-[10px] font-black text-blue-900/60 uppercase mb-2">
                    <div>DO</div><div>LU</div><div>MA</div><div>MI</div><div>JU</div><div>VI</div><div>SA</div>
                </div>
                <div id="mini-calendario-grid" class="grid grid-cols-7 gap-y-2 text-center text-xs font-bold text-slate-600"></div>
            </div>

            <div class="bg-white rounded-xl md:rounded-2xl border border-slate-200 shadow-sm flex flex-col flex-1 min-h-[250px] lg:min-h-0 overflow-hidden">
                <div class="p-4 flex justify-between items-center shrink-0 border-b border-slate-100">
                    <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Notas del Mes</h3>
                    <span id="contador-notas-mes" class="bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-black px-2 py-0.5 rounded-full">0</span>
                </div>
                <div id="lista-notas-mes" class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-slate-50/30"></div>
            </div>
        </div>

        {{-- CALENDARIO PRINCIPAL --}}
        <div class="flex-1 bg-white rounded-xl md:rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden relative order-1 lg:order-2 min-h-[550px] lg:min-h-0 lg:h-full shrink-0">
            <div class="w-full flex-1 flex flex-col h-full overflow-hidden">
                <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50/80 shrink-0">
                    <div class="py-2 text-center text-[9px] md:text-xs font-black text-slate-400 uppercase">Dom</div>
                    <div class="py-2 text-center text-[9px] md:text-xs font-black text-slate-400 uppercase">Lun</div>
                    <div class="py-2 text-center text-[9px] md:text-xs font-black text-slate-400 uppercase">Mar</div>
                    <div class="py-2 text-center text-[9px] md:text-xs font-black text-slate-400 uppercase">Mié</div>
                    <div class="py-2 text-center text-[9px] md:text-xs font-black text-slate-400 uppercase">Jue</div>
                    <div class="py-2 text-center text-[9px] md:text-xs font-black text-slate-400 uppercase">Vie</div>
                    <div class="py-2 text-center text-[9px] md:text-xs font-black text-slate-400 uppercase">Sáb</div>
                </div>
                
                <div id="calendario-grid" class="flex-1 grid grid-cols-7 grid-rows-6 bg-slate-200 gap-px border-b border-slate-200 overflow-hidden"></div>
            </div>
        </div>
    </div>
</main>

{{-- MODAL AGREGAR NOTA (CALENDARIO) --}}
<div id="modal-evento" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[60] opacity-0 transition-opacity duration-300 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-[95%] md:max-w-md mx-auto overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]" id="modal-content">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-start shrink-0">
            <div>
                <h3 class="text-lg font-black text-slate-800" id="modal-titulo-cabecera">Agregar Nota</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5" id="modal-fecha-texto"></p>
            </div>
            <button type="button" id="btn-cerrar-modal" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full p-1.5 transition-colors shrink-0 ml-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
            <form id="form-evento" class="space-y-4">
                <input type="hidden" id="evento-id">
                <input type="hidden" id="evento-fecha">
                <input type="hidden" id="evento-color" value="blue">
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Título de la Nota *</label>
                    <input type="text" id="evento-titulo" placeholder="Ej: Llamar al proveedor..." class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 font-medium" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    {{-- HORA ACTUALIZADA A 12 HRS --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hora (Opcional)</label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="text" id="hora_input_evento" placeholder="2:30" maxlength="5"
                                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-center font-bold text-slate-700 tracking-wider bg-white transition-colors" 
                                    oninput="window.formatearHora(this)" onblur="window.validarHora(this); window.sincronizarHora('evento')">
                            </div>
                            <div class="flex bg-slate-100 p-1 rounded-xl shrink-0">
                                <button type="button" id="btn_am_evento" onclick="window.setAMPM('evento', 'AM')" class="px-2 py-1 text-xs font-bold bg-white shadow-sm text-blue-600 rounded-lg transition-all">AM</button>
                                <button type="button" id="btn_pm_evento" onclick="window.setAMPM('evento', 'PM')" class="px-2 py-1 text-xs font-bold text-slate-500 hover:text-slate-800 rounded-lg transition-all">PM</button>
                            </div>
                        </div>
                        <input type="hidden" id="hora_ampm_evento" value="AM">
                        <input type="hidden" id="evento-hora"> {{-- Input real que lee el JS original --}}
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Color</label>
                        <div class="flex gap-2 items-center h-[42px]" id="color-selector-modal">
                            <button type="button" class="w-6 h-6 rounded-full bg-blue-500 ring-2 ring-offset-2 ring-blue-500 transition-all" data-color="blue"></button>
                            <button type="button" class="w-6 h-6 rounded-full bg-emerald-500 ring-0 hover:ring-2 ring-offset-2 ring-emerald-500 transition-all" data-color="emerald"></button>
                            <button type="button" class="w-6 h-6 rounded-full bg-rose-500 ring-0 hover:ring-2 ring-offset-2 ring-rose-500 transition-all" data-color="rose"></button>
                            <button type="button" class="w-6 h-6 rounded-full bg-purple-500 ring-0 hover:ring-2 ring-offset-2 ring-purple-500 transition-all" data-color="purple"></button>
                            <button type="button" class="w-6 h-6 rounded-full bg-slate-600 ring-0 hover:ring-2 ring-offset-2 ring-slate-600 transition-all" data-color="slate"></button>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Detalles Adicionales</label>
                    <textarea id="evento-detalles" rows="3" placeholder="Añade más información si lo necesitas..." class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 outline-none focus:border-blue-500 resize-none text-slate-700"></textarea>
                </div>
                
                <div class="pt-2 flex justify-end items-center gap-3">
                    <button type="button" id="btn-cancelar-modal" class="text-sm text-slate-600 font-bold hover:text-slate-800 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm rounded-xl shadow-md font-semibold transition-all">Guardar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DRAWER LATERAL: RECORDATORIOS --}}
<div id="drawer-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden opacity-0 transition-opacity duration-300"></div>

<div id="drawer-recordatorios" class="fixed inset-y-0 right-0 z-50 w-[90%] md:w-[400px] lg:w-[450px] bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <div class="px-5 md:px-8 py-5 border-b border-slate-100 flex justify-between items-start shrink-0">
        <div>
            <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight" id="drawer-rec-titulo">Nuevo Recordatorio</h2>
            <p class="text-xs md:text-sm text-slate-500 font-medium mt-1">Añadir un aviso al calendario</p>
        </div>
        <button id="btn-cerrar-drawer" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full p-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="p-5 md:p-8 flex-1 overflow-y-auto custom-scrollbar bg-white">
        <form id="form-recordatorio" class="space-y-5">
            <input type="hidden" id="rec-id">
            <input type="hidden" id="rec-tipo" value="Recordatorio">
            
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Asunto *</label>
                <input type="text" id="rec-titulo" placeholder="Ej: Cumpleaños del Doctor..." class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 font-medium text-sm md:text-base" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Fecha *</label>
                    <input type="date" id="rec-fecha" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 text-sm md:text-base" required>
                </div>
                {{-- HORA ACTUALIZADA A 12 HRS --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hora Exacta *</label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <input type="text" id="hora_input_rec" placeholder="10:00" maxlength="5"
                                class="w-full px-3 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-center font-bold text-slate-700 text-sm md:text-base tracking-wider bg-white transition-colors" 
                                oninput="window.formatearHora(this)" onblur="window.validarHora(this); window.sincronizarHora('rec')" required>
                        </div>
                        <div class="flex bg-slate-100 p-1 rounded-xl shrink-0">
                            <button type="button" id="btn_am_rec" onclick="window.setAMPM('rec', 'AM')" class="px-2 py-1.5 text-xs md:text-sm font-bold bg-white shadow-sm text-blue-600 rounded-lg transition-all">AM</button>
                            <button type="button" id="btn_pm_rec" onclick="window.setAMPM('rec', 'PM')" class="px-2 py-1.5 text-xs md:text-sm font-bold text-slate-500 hover:text-slate-800 rounded-lg transition-all">PM</button>
                        </div>
                    </div>
                    <input type="hidden" id="hora_ampm_rec" value="AM">
                    <input type="hidden" id="rec-hora" required> {{-- Input real oculto --}}
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Instrucciones Adicionales</label>
                <textarea id="rec-detalles" rows="4" placeholder="Agrega contexto para este aviso..." class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 resize-none text-slate-700 text-sm md:text-base"></textarea>
            </div>
            <div class="pt-6 flex flex-col sm:flex-row justify-end items-center gap-3 md:gap-4">
                <button type="button" class="btn-cancelar-drawer text-sm md:text-base text-slate-600 font-bold hover:text-slate-800 transition-colors w-full sm:w-auto py-2">Cancelar</button>
                <button type="submit" class="bg-[#eab308] hover:bg-[#ca8a04] text-white px-5 md:px-6 py-3 text-sm md:text-base rounded-xl shadow-md font-semibold transition-all w-full sm:w-auto">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL AGENDA "LIBRO" --}}
<div id="modal-agenda-libro" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm hidden items-center justify-center z-[70] opacity-0 transition-opacity duration-300 p-2 md:p-6">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[98%] md:max-w-5xl h-[95vh] md:h-[85vh] flex flex-col md:flex-row overflow-hidden transform scale-95 transition-transform duration-300 relative" id="agenda-libro-content">
        
        <button type="button" id="btn-cerrar-agenda-libro" class="absolute top-3 right-3 md:top-4 md:right-4 p-2 text-slate-400 hover:text-slate-600 bg-white/80 backdrop-blur border border-slate-200 shadow-sm md:border-none md:bg-slate-50 md:shadow-none hover:bg-slate-100 rounded-full transition-colors z-20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="w-full md:w-1/2 bg-slate-50 border-b md:border-r md:border-b-0 border-slate-200 flex flex-col h-[50%] md:h-full shrink-0 md:shrink-1">
            <div class="p-3 md:p-5 border-b border-slate-200 bg-white flex justify-between items-center shrink-0">
                <h2 class="text-base md:text-xl font-black text-blue-900 flex items-center gap-2">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Libreta
                </h2>
                <div class="flex items-center gap-1 mr-8 md:mr-0">
                    <button id="agenda-libro-prev" class="p-1 md:p-1.5 text-slate-400 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                    <span id="agenda-libro-mes" class="text-[10px] md:text-xs font-bold text-slate-600 uppercase w-20 md:w-24 text-center truncate">...</span>
                    <button id="agenda-libro-next" class="p-1 md:p-1.5 text-slate-400 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                </div>
            </div>
            <div id="agenda-libro-lista" class="flex-1 overflow-y-auto p-4 space-y-5 md:space-y-6 custom-scrollbar relative"></div>
        </div>

        <div class="w-full md:w-1/2 bg-white flex flex-col h-[50%] md:h-full">
            <div class="p-5 md:p-8 flex-1 overflow-y-auto custom-scrollbar flex flex-col justify-start md:justify-center">
                <div class="mb-4 md:mb-6">
                    <h3 class="text-lg md:text-2xl font-extrabold text-slate-800" id="agenda-form-titulo">Nueva Nota</h3>
                    <p class="text-[10px] md:text-sm text-slate-500 font-medium mt-0.5 md:mt-1">Se guardará en tu calendario automáticamente</p>
                </div>

                <form id="form-agenda-inline" class="space-y-3 md:space-y-5">
                    <input type="hidden" id="agenda-inline-id">
                    <input type="hidden" id="agenda-inline-tipo" value="Nota">
                    <input type="hidden" id="agenda-inline-color" value="blue">

                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Título *</label>
                        <input type="text" id="agenda-inline-titulo" placeholder="Ej: Revisar insumos..." class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 font-medium text-sm md:text-base" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="block text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Fecha *</label>
                            <input type="date" id="agenda-inline-fecha" class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 text-sm md:text-base" required>
                        </div>
                        
                        {{-- HORA ACTUALIZADA A 12 HRS --}}
                        <div>
                            <label class="block text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Hora</label>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <input type="text" id="hora_input_agenda" placeholder="04:00" maxlength="5"
                                        class="w-full px-3 py-2 md:py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-center font-bold text-slate-700 text-sm md:text-base tracking-wider bg-white transition-colors" 
                                        oninput="window.formatearHora(this)" onblur="window.validarHora(this); window.sincronizarHora('agenda')">
                                </div>
                                <div class="flex bg-slate-100 p-1 rounded-xl shrink-0">
                                    <button type="button" id="btn_am_agenda" onclick="window.setAMPM('agenda', 'AM')" class="px-2 py-1 md:py-1.5 text-xs md:text-sm font-bold bg-white shadow-sm text-blue-600 rounded-lg transition-all">AM</button>
                                    <button type="button" id="btn_pm_agenda" onclick="window.setAMPM('agenda', 'PM')" class="px-2 py-1 md:py-1.5 text-xs md:text-sm font-bold text-slate-500 hover:text-slate-800 rounded-lg transition-all">PM</button>
                                </div>
                            </div>
                            <input type="hidden" id="hora_ampm_agenda" value="AM">
                            <input type="hidden" id="agenda-inline-hora"> {{-- Input real oculto --}}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Color</label>
                        <div class="flex gap-2.5 items-center h-[35px] md:h-[42px]" id="color-selector-agenda">
                            <button type="button" class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-blue-500 ring-2 ring-offset-2 ring-blue-500 transition-all" data-color="blue"></button>
                            <button type="button" class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-emerald-500 ring-0 hover:ring-2 ring-offset-2 ring-emerald-500 transition-all" data-color="emerald"></button>
                            <button type="button" class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-rose-500 ring-0 hover:ring-2 ring-offset-2 ring-rose-500 transition-all" data-color="rose"></button>
                            <button type="button" class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-purple-500 ring-0 hover:ring-2 ring-offset-2 ring-purple-500 transition-all" data-color="purple"></button>
                            <button type="button" class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-slate-600 ring-0 hover:ring-2 ring-offset-2 ring-slate-600 transition-all" data-color="slate"></button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Detalles Adicionales</label>
                        <textarea id="agenda-inline-detalles" rows="2" placeholder="Escribe más detalles..." class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 resize-none text-slate-700 text-sm md:text-base"></textarea>
                    </div>

                    <div class="pt-2 flex flex-col-reverse sm:flex-row justify-end items-center gap-3">
                        <button type="button" id="btn-limpiar-agenda" class="text-[11px] md:text-sm text-slate-500 font-bold hover:text-slate-700 transition-colors w-full sm:w-auto py-2">Limpiar Campos</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 md:px-6 py-2.5 md:py-3 rounded-xl shadow-md font-semibold transition-all w-full sm:w-auto text-[11px] md:text-base">Guardar en Agenda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>
<script src="{{ asset('js/NotificacionesControlador.js') }}"></script>
<script src="{{ asset('js/utils/api.js') }}"></script>
<script src="{{ asset('js/calendar.js') }}"></script>

{{-- ======================================================== --}}
{{-- SCRIPT PARA MANEJAR EL FORMATO 12 HORAS (AM/PM) EN LA UI --}}
{{-- ======================================================== --}}
{{-- SCRIPT PARA MANEJAR EL FORMATO 12 HORAS (AM/PM) EN LA UI --}}
{{-- ======================================================== --}}
{{-- ======================================================== --}}
{{-- SCRIPT PARA MANEJAR EL FORMATO 12 HORAS (AM/PM) EN LA UI --}}
{{-- ======================================================== --}}
<script>
    // Formatear input de hora visualmente mientras escribe
    window.formatearHora = function(input) {
        let val = input.value.replace(/\D/g, ''); // Quita letras y signos

        if (val.length === 0) {
            input.value = '';
            window.sincronizarHora(input.id.replace('hora_input_', ''));
            return;
        }

        // Si el usuario escribe un "0" de primero, lo ignoramos para que quede natural
        if (val[0] === '0') {
            val = val.substring(1);
        }

        if (val.length === 0) {
            input.value = '';
            window.sincronizarHora(input.id.replace('hora_input_', ''));
            return;
        }

        let primerDigito = parseInt(val[0]);

        if (primerDigito === 1) {
            // Si empieza con 1, puede ser 10, 11 o 12 (Permite hasta 4 dígitos: "1230" -> "12:30")
            if (val.length > 4) val = val.substring(0, 4);
            if (val.length > 2) {
                input.value = val.substring(0, 2) + ':' + val.substring(2);
            } else {
                input.value = val;
            }
        } else {
            // Si empieza del 2 al 9, es un solo dígito de hora (Permite hasta 3 dígitos: "740" -> "7:40")
            if (val.length > 3) val = val.substring(0, 3);
            if (val.length > 1) {
                input.value = val.substring(0, 1) + ':' + val.substring(1);
            } else {
                input.value = val;
            }
        }
        
        window.sincronizarHora(input.id.replace('hora_input_', ''));
    };

    // Validar que la hora tenga lógica al quitar el clic
    window.validarHora = function(input) {
        let val = input.value;
        if (val.length === 0) return;
        
        let parts = val.split(':');
        let h = parseInt(parts[0]) || 0;
        let m = parts[1] ? parseInt(parts[1]) || 0 : 0;
        
        if (h === 0) h = 12;
        if (h > 12) h = 12;
        if (m > 59) m = 59;
        
        // AQUÍ ESTÁ LA MAGIA: Quitamos el "0" extra de la hora (h.toString() en vez de padStart)
        input.value = h.toString() + ':' + m.toString().padStart(2, '0');
        window.sincronizarHora(input.id.replace('hora_input_', ''));
    };

    // Botones de colores para seleccionar AM/PM
    window.setAMPM = function(prefijo, valor) {
        document.getElementById('hora_ampm_' + prefijo).value = valor;
        
        const btnAM = document.getElementById('btn_am_' + prefijo);
        const btnPM = document.getElementById('btn_pm_' + prefijo);
        
        if (valor === 'AM') {
            btnAM.className = btnAM.className.replace('text-slate-500 hover:text-slate-800', 'bg-white shadow-sm text-blue-600');
            btnPM.className = btnPM.className.replace('bg-white shadow-sm text-blue-600', 'text-slate-500 hover:text-slate-800');
        } else {
            btnPM.className = btnPM.className.replace('text-slate-500 hover:text-slate-800', 'bg-white shadow-sm text-blue-600');
            btnAM.className = btnAM.className.replace('bg-white shadow-sm text-blue-600', 'text-slate-500 hover:text-slate-800');
        }
        window.sincronizarHora(prefijo);
    };

    // Guarda la hora en formato 24H "secreto" para la Base de Datos
    window.sincronizarHora = function(prefijo) {
        const inputVisible = document.getElementById('hora_input_' + prefijo);
        const ampm = document.getElementById('hora_ampm_' + prefijo).value;
        let inputOculto;
        
        // Relacionamos los prefijos con los IDs reales
        if(prefijo === 'evento') inputOculto = document.getElementById('evento-hora');
        if(prefijo === 'rec') inputOculto = document.getElementById('rec-hora');
        if(prefijo === 'agenda') inputOculto = document.getElementById('agenda-inline-hora');
        if(prefijo === 'inicio') inputOculto = document.getElementById('hora_oculta_inicio'); 
        if(prefijo === 'fin') inputOculto = document.getElementById('hora_oculta_fin');       

        if (!inputVisible || !inputOculto) return;

        if (inputVisible.value.trim() === '') {
            inputOculto.value = '';
            return;
        }

        let parts = inputVisible.value.split(':');
        let h = parseInt(parts[0]) || 0;
        let m = parts[1] || '00';

        // Convertimos a 24 horas para la BD
        if (ampm === 'PM' && h < 12) h += 12;
        if (ampm === 'AM' && h === 12) h = 0;

        inputOculto.value = h.toString().padStart(2, '0') + ':' + m + ':00';
    };
</script>

</body>
</html>