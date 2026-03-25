{{-- header y nav incrustados --}}
@include('header')
@include('nav')

<main class="flex-1 p-8 bg-[#f8fafc] overflow-y-auto h-screen">
    
    {{-- Encabezado --}}
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
        </div>
    </div>

    {{-- Tarjetas de Estadísticas --}}
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

    {{-- Filtros --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="searchInput" placeholder="Buscar por paciente, motivo o doctor..." 
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-blue-800/20 focus:bg-white transition-all outline-none placeholder:text-slate-400 font-medium">
        </div>
        <select id="filtroEstado" class="px-4 py-3 bg-slate-50 rounded-xl text-slate-600 font-medium focus:ring-2 focus:ring-blue-800/20 outline-none cursor-pointer border-none min-w-[180px]">
            <option value="">Todos los estados</option>
            <option value="Programada">Programada</option>
            <option value="Confirmada">Confirmada</option>
            <option value="En progreso">En progreso</option>
            <option value="Completada">Completada</option>
            <option value="No presentado">No presentado</option>
            <option value="Cancelada">Cancelada</option>
        </select>
    </div>

    {{-- Tabla (Componente Laravel) --}}
    @php 
        $tableColumns = ['Fecha / Hora Rango', 'Paciente', 'Motivo', 'Doctor', 'Estado', 'Acciones'];
        $tableID = 'citasTableBody'; 
    @endphp
    @include('components.tabla_base')

</main>

{{-- Modal Formulario --}}
@php ob_start(); @endphp
<form id="formCita" class="flex flex-col h-full">
    <input type="hidden" name="id">

    <div class="flex border-b border-slate-200 mb-6 gap-6 px-1">
        <button type="button" class="pb-2 text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors">
            Detalles de la Cita
        </button>
    </div>

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <div class="md:col-span-2 relative">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Paciente *</label>
            <input type="hidden" name="paciente_id" id="paciente_id" required>
            <input type="text" id="buscador_paciente" placeholder="Escribe el nombre del paciente..." autocomplete="off" 
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 bg-white transition-colors">
            <div id="dropdown_pacientes" class="absolute z-20 w-full bg-white border border-slate-200 rounded-xl shadow-lg mt-1 hidden max-h-48 overflow-y-auto">
            </div>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fecha de la cita *</label>
            <input type="date" name="fecha" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hora Inicio *</label>
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" id="hora_input_inicio" placeholder="2:30" maxlength="5"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-center font-bold text-slate-700 tracking-wider bg-white transition-colors" 
                        oninput="window.formatearHora(this)" onblur="window.validarHora(this)" required>
                </div>
                <div class="flex bg-slate-100 p-1 rounded-xl shrink-0">
                    <button type="button" id="btn_am_inicio" onclick="window.setAMPM('inicio', 'AM')" class="px-2 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all">AM</button>
                    <button type="button" id="btn_pm_inicio" onclick="window.setAMPM('inicio', 'PM')" class="px-2 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all">PM</button>
                </div>
            </div>
            <input type="hidden" id="hora_ampm_inicio" value="AM">
            <input type="hidden" name="hora" id="hora_oculta_inicio">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hora Fin *</label>
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" id="hora_input_fin" placeholder="2:30" maxlength="5"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-center font-bold text-slate-700 tracking-wider bg-white transition-colors" 
                        oninput="window.formatearHora(this)" onblur="window.validarHora(this)" required>
                </div>
                <div class="flex bg-slate-100 p-1 rounded-xl shrink-0">
                    <button type="button" id="btn_am_fin" onclick="window.setAMPM('fin', 'AM')" class="px-2 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all">AM</button>
                    <button type="button" id="btn_pm_fin" onclick="window.setAMPM('fin', 'PM')" class="px-2 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all">PM</button>
                </div>
            </div>
            <input type="hidden" id="hora_ampm_fin" value="AM">
            <input type="hidden" name="hora_fin" id="hora_oculta_fin">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Motivo de consulta *</label>
            <input type="text" name="motivo" placeholder="Ej: Dolor de muela, Limpieza..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Doctor Asignado *</label>
            <select name="empleado_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white outline-none focus:border-blue-500" required>
                <option value="">Cargando doctores...</option>
            </select>
        </div>

        <div>
             <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estado</label>
             <select name="estado" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500">
                <option value="Programada">Programada</option>
                <option value="Confirmada">Confirmada</option>
                <option value="En progreso">En progreso</option>
                <option value="Completada">Completada</option>
                <option value="No presentado">No presentado</option>
                <option value="Cancelada">Cancelada</option>
            </select>
        </div>

        <div class="md:col-span-2">
             <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Notas Adicionales</label>
             <textarea name="notas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 bg-yellow-50/50"></textarea>
        </div>
        
        <button type="submit" id="btnSubmitOculto" class="hidden"></button>
    </div>
</form>

@php 
    $modalContent = ob_get_clean(); 
    $modalID = "modalCitas";    
    $modalTitle = "Nueva Cita"; 
@endphp
@include('components.modal_base')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>
<script src="{{ asset('js/utils/api.js') }}"></script>
<script src="{{ asset('js/utils/paginadorTabla.js') }}"></script>
<script src="{{ asset('js/utils/reportes.js') }}"></script>
<script src="{{ asset('js/CitasControlador.js') }}"></script>

</body>
</html>