<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<main class="flex-1 p-8 bg-[#f8fafc] overflow-y-auto h-screen">
    
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Citas Médicas</h2>
            <p class="text-slate-500 mt-1 font-medium">Programación y seguimiento de agenda</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <button onclick="window.openModal('modalCitas', 'add')" 
                class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-3 rounded-xl shadow-lg shadow-blue-900/20 font-semibold flex items-center gap-2 transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nueva Cita
            </button>

            <button class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-3 rounded-xl shadow-lg shadow-blue-900/20 font-semibold flex items-center gap-2 transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                Recordatorios
            </button>

            <button class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-3 rounded-xl shadow-lg shadow-blue-900/20 font-semibold flex items-center gap-2 transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Calendario
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Para Hoy</p>
                <h3 class="text-2xl font-bold text-slate-800" id="statHoy">0</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Esta Semana</p>
                <h3 class="text-2xl font-bold text-slate-800" id="statSemana">0</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pendientes</p>
                <h3 class="text-2xl font-bold text-slate-800" id="statPendientes">0</h3>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="searchInput" placeholder="Buscar por paciente, motivo o doctor..." 
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-blue-800/20 focus:bg-white transition-all outline-none placeholder:text-slate-400 font-medium">
        </div>
        <select id="filtroEstado" class="px-4 py-3 bg-slate-50 rounded-xl text-slate-600 font-medium focus:ring-2 focus:ring-blue-800/20 outline-none cursor-pointer border-none min-w-[180px]">
            <option value="">Todos los estados</option>
            <option value="Confirmada">Confirmada</option>
            <option value="Pendiente">Pendiente</option>
            <option value="Cancelada">Cancelada</option>
            <option value="Completada">Completada</option>
        </select>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-[600px]">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-white z-10">
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs uppercase text-slate-500 font-bold tracking-wider">
                        <th class="px-6 py-5">Fecha / Hora</th>
                        <th class="px-6 py-5">Paciente</th>
                        <th class="px-6 py-5">Motivo</th>
                        <th class="px-6 py-5">Doctor</th>
                        <th class="px-6 py-5">Estado</th>
                        <th class="px-6 py-5">Acciones</th>
                    </tr>
                </thead>
                <tbody id="citasTableBody" class="divide-y divide-slate-100 text-sm text-slate-600">
                    </tbody>
            </table>
        </div>
        
        <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between bg-white" id="paginationControls">
            <span class="text-slate-500 text-sm font-medium" id="paginationInfo">Cargando...</span>
            
            <div class="flex items-center gap-3">
                <button id="btnPrev" class="flex items-center gap-2 px-4 py-2 text-slate-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Anterior
                </button>
                
                <div class="h-4 w-px bg-slate-200"></div>
                
                <button id="btnNext" class="flex items-center gap-2 px-4 py-2 text-slate-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
                    Siguiente
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </div>
</main>

<?php ob_start(); ?>
<form id="formCita" class="flex flex-col h-full">
    <input type="hidden" name="id">

    <div class="flex border-b border-slate-200 mb-6 gap-6 px-1">
        <button type="button" class="pb-2 text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors">
            Detalles de la Cita
        </button>
    </div>

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Paciente *</label>
            <div class="relative">
                <select name="paciente_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 bg-white" required>
                    <option value="">Seleccione un paciente...</option>
                    <option value="1">María González</option>
                    <option value="2">Carlos Martínez</option>
                    <option value="3">Ana Rodríguez</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fecha *</label>
            <input type="date" name="fecha" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hora *</label>
            <input type="time" name="hora" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Motivo de consulta *</label>
            <input type="text" name="motivo" placeholder="Ej: Dolor de muela, Limpieza..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Doctor Asignado</label>
            <select name="doctor" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white outline-none focus:border-blue-500">
                <option>Dr. General</option>
                <option>Dra. Ortodoncista</option>
            </select>
        </div>
        <div>
             <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estado</label>
             <select name="estado" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500">
                <option value="Pendiente">Pendiente</option>
                <option value="Confirmada">Confirmada</option>
                <option value="Completada">Completada</option>
                <option value="Cancelada">Cancelada</option>
            </select>
        </div>

        <div class="md:col-span-2">
             <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Notas Adicionales</label>
             <textarea name="notas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 bg-yellow-50/50"></textarea>
        </div>
    </div>
</form>

<?php 
    $modalContent = ob_get_clean(); 
    $modalID = "modalCitas";    
    $modalTitle = "Nueva Cita"; 
    include 'components/modal_base.php'; 
?>

<script src="../static/js/utils/paginadorTabla.js"></script>
<script src="../static/js/utils/reportes.js"></script>
<script src="../static/js/CitasControlador.js"></script>

</body>
</html>