<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<main class="flex-1 p-6 bg-[#f8fafc] overflow-y-auto h-screen flex flex-col">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="flex items-center gap-4">
            <button onclick="window.location.href='citas.php'" class="p-2 text-slate-500 hover:text-blue-800 hover:bg-blue-50 rounded-xl transition-all" title="Volver a Citas">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </button>
            <h2 class="text-3xl font-bold text-slate-800 capitalize" id="mes-anio-titulo">Cargando...</h2>
        </div>
        
        <div class="flex items-center gap-3">
            <button id="btn-abrir-recordatorios" class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-2.5 text-sm rounded-xl shadow-lg shadow-blue-900/20 font-semibold flex items-center gap-2 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                Recordatorios y Notas
            </button>

            <button id="btn-hoy" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:text-blue-800 transition-colors shadow-sm text-sm">
                Hoy
            </button>
            <div class="flex bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <button id="btn-prev" class="px-4 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-blue-800 transition-colors border-r border-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="btn-next" class="px-4 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-blue-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-1 gap-6 overflow-hidden">
        
        <div class="w-72 hidden lg:flex flex-col gap-6 flex-shrink-0 h-full">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex-shrink-0">
                <div class="flex justify-between items-center mb-4">
                    <span id="mini-mes-anio" class="font-bold text-slate-800 text-sm capitalize"></span>
                    <div class="flex gap-1">
                        <button id="mini-prev" class="p-1.5 text-slate-400 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button id="mini-next" class="p-1.5 text-slate-400 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-7 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                    <div>Do</div><div>Lu</div><div>Ma</div><div>Mi</div><div>Ju</div><div>Vi</div><div>Sa</div>
                </div>
                
                <div id="mini-calendario-grid" class="grid grid-cols-7 gap-y-1 text-center text-sm"></div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col flex-1 overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Notas del Mes</h3>
                    <span id="contador-notas-mes" class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">0</span>
                </div>
                <div id="lista-notas-mes" class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-slate-50/30"></div>
            </div>
        </div>

        <div class="flex-1 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
            <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50/80">
                <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Domingo</div>
                <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Lunes</div>
                <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Martes</div>
                <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Miércoles</div>
                <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Jueves</div>
                <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Viernes</div>
                <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Sábado</div>
            </div>
            
            <div id="calendario-grid" class="flex-1 grid grid-cols-7 auto-rows-fr bg-slate-200 gap-px border-b border-slate-200"></div>
        </div>

    </div>
</main>

<div id="modal-evento" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center z-50 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300" id="modal-content">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-start">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Agendar Cita</h3>
                <p class="text-sm text-slate-500 font-medium mt-1" id="modal-fecha-texto"></p>
            </div>
            <button type="button" id="btn-cerrar-modal" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full p-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6">
            <form id="form-evento" class="space-y-4">
                <input type="hidden" id="evento-fecha">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Paciente *</label>
                    <input type="text" id="evento-titulo" placeholder="Nombre del paciente..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 font-medium" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hora</label>
                        <input type="time" id="evento-hora" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Estado</label>
                        <select id="evento-estado" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 bg-white cursor-pointer">
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipo de Consulta</label>
                    <select id="evento-tipo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 bg-white cursor-pointer">
                        <option value="General">Odontología General</option>
                        <option value="Ortodoncia">Ortodoncia</option>
                        <option value="Limpieza">Limpieza</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Motivo / Notas</label>
                    <textarea id="evento-detalles" rows="2" placeholder="Ej: Dolor de muela..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 resize-none text-slate-700"></textarea>
                </div>
                <div class="pt-6 flex justify-end items-center gap-6">
                    <button type="button" id="btn-cancelar-modal" class="text-slate-600 font-bold hover:text-slate-800 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl shadow-md font-semibold transition-all">Guardar Cita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="drawer-overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden opacity-0 transition-opacity duration-300"></div>

<div id="drawer-recordatorios" class="fixed inset-y-0 right-0 z-50 w-full md:w-[450px] bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    
    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Centro de Notas</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Gestión de alertas y apuntes</p>
        </div>
        <button id="btn-cerrar-drawer" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full p-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="flex px-8 border-b border-slate-100">
        <button id="tab-recordatorio" class="flex-1 pb-3 pt-4 text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors">Recordatorio</button>
        <button id="tab-nota" class="flex-1 pb-3 pt-4 text-sm font-bold text-slate-400 hover:text-slate-600 border-b-2 border-transparent transition-colors">Nota Libre</button>
        <button id="tab-agenda" class="flex-1 pb-3 pt-4 text-sm font-bold text-slate-400 hover:text-slate-600 border-b-2 border-transparent transition-colors">Agenda</button>
    </div>

    <div id="content-recordatorio" class="p-8 flex-1 overflow-y-auto custom-scrollbar bg-white">
        <form id="form-recordatorio" class="space-y-5">
            <input type="hidden" id="rec-tipo" value="Recordatorio">
            
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Asunto del Recordatorio *</label>
                <input type="text" id="rec-titulo" placeholder="Ej: Llamar al proveedor..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 font-medium" required>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Fecha *</label>
                    <input type="date" id="rec-fecha" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700" required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hora Exacta *</label>
                    <input type="time" id="rec-hora" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700" required>
                </div>
            </div>
            
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Instrucciones Adicionales</label>
                <textarea id="rec-detalles" rows="4" placeholder="Agrega contexto para este aviso..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 resize-none text-slate-700"></textarea>
            </div>
            
            <div class="pt-6 flex justify-end items-center gap-6">
                <button type="button" class="btn-cancelar-drawer text-slate-600 font-bold hover:text-slate-800 transition-colors">Cancelar</button>
                <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl shadow-md font-semibold transition-all">Guardar</button>
            </div>
        </form>
    </div>

    <div id="content-nota" class="p-8 flex-1 overflow-y-auto custom-scrollbar bg-white hidden">
        <form id="form-nota" class="space-y-5">
            <input type="hidden" id="nota-tipo" value="Nota">
            
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Título de la Nota *</label>
                <input type="text" id="nota-titulo" placeholder="Ej: Ideas para el consultorio..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 font-medium" required>
            </div>
            
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Fecha de Referencia *</label>
                <input type="date" id="nota-fecha" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700" required>
            </div>
            
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Contenido de la Nota *</label>
                <textarea id="nota-detalles" rows="6" placeholder="Escribe el contenido aquí..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 resize-none text-slate-700" required></textarea>
            </div>
            
            <div class="pt-6 flex justify-end items-center gap-6">
                <button type="button" class="btn-cancelar-drawer text-slate-600 font-bold hover:text-slate-800 transition-colors">Cancelar</button>
                <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl shadow-md font-semibold transition-all">Guardar</button>
            </div>
        </form>
    </div>

    <div id="content-agenda" class="p-8 flex-1 overflow-y-auto custom-scrollbar hidden bg-slate-50/50">
        
        <div class="flex justify-between items-center mb-8 bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
            <button id="agenda-prev" type="button" class="p-2 text-slate-400 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <span id="agenda-mes-anio" class="font-bold text-slate-700 uppercase tracking-wider text-xs">Cargando...</span>
            <button id="agenda-next" type="button" class="p-2 text-slate-400 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        <div class="mb-8">
            <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2">Recordatorios del Mes</h4>
            <div id="lista-agenda-recordatorios" class="space-y-3">
                </div>
        </div>

        <div>
            <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2">Notas del Mes</h4>
            <div id="lista-agenda-notas" class="space-y-3">
                </div>
        </div>

    </div>
</div>

<script src="../static/js/calendar.js?v=7.0"></script>

</body>
</html>