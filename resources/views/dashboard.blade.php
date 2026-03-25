{{-- header y nav incrustados --}}
@include('header')
@include('nav')

{{-- ESTRUCTURA RESPONSIVA: Scroll vertical forzado en móvil, Estático en Desktop --}}
<main class="flex-1 p-4 md:p-6 lg:px-8 lg:pb-6 lg:pt-4 bg-[#f8fafc] h-screen overflow-y-auto lg:overflow-hidden flex flex-col">
    
    <h2 class="text-2xl md:text-3xl font-bold text-slate-800 mb-4 md:mb-6 shrink-0">Bienvenido Usuluteco</h2>

    {{-- TARJETAS SUPERIORES (shrink-0 para que no se aplasten en móvil) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6 shrink-0">
        
        <div class="bg-white p-5 md:p-6 rounded-3xl shadow-sm border border-emerald-200 flex flex-col justify-between h-36 md:h-40 relative overflow-hidden shrink-0">
            <div class="flex justify-between items-start z-10">
                <p class="text-sm md:text-base text-slate-500 font-medium">Citas realizadas</p>
                <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-end justify-between mt-2 z-10">
                <span id="numCitas" class="text-4xl md:text-5xl font-bold text-emerald-700">--</span>
                <div class="w-1/2 h-12 md:h-14 relative"> <canvas id="chartCitas"></canvas></div>
            </div>
        </div>

        <div class="bg-white p-5 md:p-6 rounded-3xl shadow-sm border border-amber-200 flex flex-col justify-between h-36 md:h-40 relative overflow-hidden shrink-0">
            <div class="flex justify-between items-start z-10">
                <p class="text-sm md:text-base text-slate-500 font-medium">Citas no asistidas</p>
                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-end justify-between mt-2 z-10">
                <span id="numNoAsistidas" class="text-4xl md:text-5xl font-bold text-amber-700">--</span>
                <div class="w-1/2 h-12 md:h-14 relative"> <canvas id="chartNoAsistidas"></canvas></div>
            </div>
        </div>

        <div class="bg-white p-5 md:p-6 rounded-3xl shadow-sm border border-rose-200 flex flex-col justify-between h-36 md:h-40 relative overflow-hidden shrink-0">
            <div class="flex justify-between items-start z-10">
                <p class="text-sm md:text-base text-slate-500 font-medium">Citas canceladas</p>
                <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-end justify-between mt-2 z-10">
                <span id="numCanceladas" class="text-4xl md:text-5xl font-bold text-rose-700">--</span>
                <div class="w-1/2 h-12 md:h-14 relative"> <canvas id="chartCanceladas"></canvas></div>
            </div>
        </div>

        <div class="bg-white p-5 md:p-6 rounded-3xl shadow-sm border border-blue-100 flex flex-col justify-between h-36 md:h-40 relative overflow-hidden shrink-0">
            <div class="flex justify-between items-start z-10">
                <p class="text-sm md:text-base text-slate-500 font-medium leading-tight">Citas Completadas <br><span class="text-[11px] md:text-xs text-slate-400 font-normal">vs Agendadas</span></p>
                <span class="bg-blue-50 text-blue-800 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-center justify-between mt-2 z-10">
                <div class="flex flex-col">
                    <span id="numCompletadas" class="text-4xl md:text-5xl font-bold text-blue-800 leading-none">--</span>
                    <span class="text-[9px] md:text-[10px] font-bold text-blue-600/60 mt-1 uppercase tracking-wide">Completadas</span>
                </div>
                <div class="w-14 h-14 md:w-16 md:h-16 relative">
                    <canvas id="chartTasa"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="percentTasa" class="text-[9px] md:text-[10px] font-bold text-blue-800">--%</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ZONA INFERIOR: shrink-0 en móvil fuerza al contenedor a crecer y activa el scroll --}}
    <div class="flex flex-col lg:flex-row gap-4 md:gap-6 flex-1 lg:min-h-0 shrink-0 lg:shrink">
        
        {{-- PANEL IZQUIERDO: Gráfica de Movimiento --}}
        <div class="w-full lg:w-2/3 bg-white p-5 md:p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col h-[300px] sm:h-[400px] lg:h-auto lg:flex-1 lg:min-h-0 shrink-0 lg:shrink">
            <div class="flex justify-between items-center mb-4 md:mb-6 shrink-0">
                <h3 class="text-base md:text-lg font-bold text-slate-800">Movimiento esta semana</h3>
                <span class="text-xs md:text-sm text-slate-400">Últimos 7 días</span>
            </div>
            
            <div class="relative w-full flex-1 min-h-0">
                <canvas id="chartMovimiento"></canvas>
            </div>
        </div>

        {{-- PANEL DERECHO: Tratamientos y Agenda --}}
        <div class="w-full lg:w-1/3 flex flex-col gap-4 md:gap-6 h-auto lg:h-full lg:min-h-0 shrink-0 lg:shrink">
            
            {{-- Tratamientos --}}
            <div class="bg-white p-4 md:p-5 rounded-3xl shadow-sm border border-slate-100 shrink-0">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-base md:text-lg font-bold text-slate-800">Tratamientos realizados</h3>
                </div>
                
                <div class="flex items-center">
                    <div class="w-1/2 h-28 md:h-32 relative">
                         <canvas id="chartTratamientos"></canvas>
                    </div>
                    <div id="leyendaTratamientos" class="w-1/2 pl-2 md:pl-4 space-y-1.5 md:space-y-2">
                        <p class="text-xs text-slate-400 italic">Cargando...</p>
                    </div>
                </div>
            </div>

            {{-- Agenda (Citas Pendientes) --}}
            <div class="bg-white p-4 md:p-5 rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col relative h-[380px] lg:h-auto lg:flex-1 shrink-0 lg:shrink">
                <div class="flex justify-between items-center mb-3 shrink-0">
                    <h3 class="text-base md:text-lg font-bold text-slate-800">Citas Pendientes</h3>
                    <button onclick="abrirModalDashboardCitas()" class="text-xs md:text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 px-2 md:px-3 py-1 rounded-lg">Ver todas</button>
                </div>

                <div id="contenedorListasCitas" class="flex-1 flex flex-col overflow-y-auto pr-1 md:pr-2 custom-scrollbar">
                    <p class="text-xs text-slate-400 italic mt-2">Cargando agenda...</p>
                </div>
            </div>

        </div>
    </div>
</main>

{{-- Modal Citas Pendientes --}}
@php ob_start(); @endphp
<div class="flex flex-col h-full">
    <div class="flex border-b border-slate-200 mb-4 gap-6 px-1 shrink-0">
        <button type="button" class="pb-2 text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors">
            Lista Completa
        </button>
    </div>
    
    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
        <div class="mb-6">
            <h4 class="text-[11px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Para Hoy</h4>
            <div id="modalListaHoy" class="space-y-2"></div>
        </div>

        <div>
            <h4 class="text-[11px] md:text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 pt-3 border-t border-slate-200">Próximos Días</h4>
            <div id="modalListaProximas" class="space-y-2"></div>
        </div>
    </div>
</div>
@php 
    $modalContent = ob_get_clean(); 
    $modalID = "modalDashboardCitas";    
    $modalTitle = "Todas las Citas Pendientes"; 
@endphp
@include('components.modal_base')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script src="{{ asset('js/utils/api.js') }}"></script>
<script src="{{ asset('js/charts.js') }}" defer></script>
<script src="{{ asset('js/horacolor.js') }}" defer></script>

</body>
</html>