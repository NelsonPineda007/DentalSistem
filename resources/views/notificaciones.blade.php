{{-- header y nav incrustados --}}
@include('header')
@include('nav')

<main class="flex-1 p-4 md:p-6 lg:p-8 bg-[#f8fafc] min-h-screen flex flex-col w-full overflow-x-hidden">
    
    {{-- ========================================== --}}
    {{-- ENCABEZADO PRINCIPAL --}}
    {{-- ========================================== --}}
    <div class="mb-8 w-full">
        <h2 class="text-3xl font-bold text-slate-800">Centro de Notificaciones</h2>
        <p class="text-slate-500 mt-1 font-medium">Gestiona tu buzón y tareas pendientes</p>
    </div>

    {{-- ========================================== --}}
    {{-- CONTENEDOR PRINCIPAL: MENÚ LATERAL + CONTENIDO --}}
    {{-- ========================================== --}}
    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 w-full flex-1 items-start">

        {{-- MENÚ DE SECCIONES (Navegación de pestañas) --}}
        <aside class="w-full lg:w-64 shrink-0 flex flex-col gap-2 bg-white p-3 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider px-3 mb-2 mt-2">Bandejas</p>
            
            <button onclick="cambiarSeccion('seccion-citas', this)" class="tab-btn active w-full flex items-center justify-between px-4 py-3 rounded-xl font-medium transition-all bg-emerald-50 text-emerald-800 border border-emerald-200">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    Citas Pendientes
                </div>
                <span id="badge-citas" class="bg-emerald-200 text-emerald-900 text-xs font-bold px-2 py-0.5 rounded-lg">0</span>
            </button>

            <button onclick="cambiarSeccion('seccion-notas', this)" class="tab-btn w-full flex items-center justify-between px-4 py-3 rounded-xl font-medium transition-all text-slate-600 hover:bg-slate-50 border border-transparent">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-700"></span>
                    Notas Hechas
                </div>
            </button>

            <button onclick="cambiarSeccion('seccion-recordatorios', this)" class="tab-btn w-full flex items-center justify-between px-4 py-3 rounded-xl font-medium transition-all text-slate-600 hover:bg-slate-50 border border-transparent">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Recordatorios
                </div>
            </button>
        </aside>

        {{-- ÁREA DE CONTENIDO DINÁMICO --}}
        <div class="flex-1 w-full bg-white rounded-3xl shadow-sm border border-slate-200 p-6 lg:p-8 min-h-[500px]">

            {{-- ---------------------------------------- --}}
            {{-- 1. SECCIÓN: CITAS PENDIENTES (VERDE) --}}
            {{-- ---------------------------------------- --}}
            <div id="seccion-citas" class="seccion-contenido block">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Citas Pendientes</h3>
                        <p class="text-sm text-slate-500 mt-1">Requieren tu atención a partir de hoy.</p>
                    </div>
                    <div class="w-full md:w-[350px]">
                        <div class="relative flex-1">
                            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" id="busquedaCitas" placeholder="Buscar citas por paciente o motivo..." 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-emerald-700/20 focus:bg-white transition-all outline-none placeholder:text-slate-400 font-medium">
                        </div>
                    </div>
                </div>
                
                {{-- Contenedor donde JS inyectará las Citas --}}
                <div id="contenedor-lista-citas" class="flex flex-col gap-4">
                    <p class="text-slate-400 text-center italic py-10">Cargando citas...</p>
                </div>
            </div>

            {{-- ---------------------------------------- --}}
            {{-- 2. SECCIÓN: NOTAS HECHAS (AZUL) --}}
            {{-- ---------------------------------------- --}}
            <div id="seccion-notas" class="seccion-contenido hidden">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Notas Registradas</h3>
                        <p class="text-sm text-slate-500 mt-1">Anotaciones rápidas del personal de la clínica.</p>
                    </div>
                    <div class="w-full md:w-[350px]">
                        <div class="relative flex-1">
                            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <input type="text" id="busquedaNotas" placeholder="Buscar en notas por autor o contenido..." 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-blue-700/20 focus:bg-white transition-all outline-none placeholder:text-slate-400 font-medium">
                        </div>
                    </div>
                </div>

                {{-- Contenedor donde JS inyectará las Notas --}}
                <div id="contenedor-lista-notas" class="flex flex-col gap-4">
                     <p class="text-slate-400 text-center italic py-10">Cargando notas...</p>
                </div>
            </div>

            {{-- ---------------------------------------- --}}
            {{-- 3. SECCIÓN: RECORDATORIOS (AMARILLO) --}}
            {{-- ---------------------------------------- --}}
            <div id="seccion-recordatorios" class="seccion-contenido hidden">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Historial de Recordatorios</h3>
                        <p class="text-sm text-slate-500 mt-1">Tareas y recordatorios registrados en el sistema.</p>
                    </div>
                    <div class="w-full md:w-[350px]">
                        <div class="relative flex-1">
                            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" id="busquedaRecordatorios" placeholder="Buscar recordatorios..." 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-amber-600/20 focus:bg-white transition-all outline-none placeholder:text-slate-400 font-medium">
                        </div>
                    </div>
                </div>

                {{-- Contenedor donde JS inyectará los Recordatorios --}}
                <div id="contenedor-lista-recordatorios" class="flex flex-col gap-4">
                     <p class="text-slate-400 text-center italic py-10">Cargando recordatorios...</p>
                </div>
            </div>

        </div>
    </div>
</main>

{{-- ========================================== --}}
{{-- LLAMADO A LOS SCRIPTS EXTERNOS           --}}
{{-- ========================================== --}}
<script src="{{ asset('js/utils/api.js') }}"></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>
<script src="{{ asset('js/NotificacionesControlador.js') }}"></script>

</body>
</html>