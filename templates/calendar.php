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
                
                <div id="mini-calendario-grid" class="grid grid-cols-7 gap-y-1 text-center text-sm">
                    </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col flex-1 overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Notas del Mes</h3>
                    <span id="contador-notas-mes" class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">0</span>
                </div>
                <div id="lista-notas-mes" class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-slate-50/30">
                </div>
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
            
            <div id="calendario-grid" class="flex-1 grid grid-cols-7 auto-rows-fr bg-slate-200 gap-px border-b border-slate-200">
                </div>
        </div>

    </div>
</main>

<div id="modal-evento" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300" id="modal-content">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Agendar Cita</h3>
                <p class="text-sm text-slate-500 font-medium" id="modal-fecha-texto"></p>
            </div>
            <button type="button" id="btn-cerrar-modal" class="text-slate-400 hover:text-red-500 bg-white rounded-lg p-1 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6">
            <form id="form-evento" class="space-y-4">
                <input type="hidden" id="evento-fecha">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Paciente *</label>
                    <input type="text" id="evento-titulo" placeholder="Nombre del paciente..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-800/20 text-slate-700 font-medium" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hora</label>
                        <input type="time" id="evento-hora" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-800/20 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estado</label>
                        <select id="evento-estado" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 bg-white cursor-pointer">
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tipo de Consulta</label>
                    <select id="evento-tipo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-slate-700 bg-white cursor-pointer">
                        <option value="General">Odontología General</option>
                        <option value="Ortodoncia">Ortodoncia</option>
                        <option value="Limpieza">Limpieza</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Motivo / Notas</label>
                    <textarea id="evento-detalles" rows="2" placeholder="Ej: Dolor de muela..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-800/20 resize-none text-slate-700"></textarea>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" id="btn-cancelar-modal" class="px-5 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-900/20 font-semibold transition-all active:scale-95">Guardar Cita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../static/js/paginacion.js" defer></script>
<script src="../static/js/calendar.js"></script>

</body>
</html>