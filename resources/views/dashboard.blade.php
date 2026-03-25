{{-- header y nav incrustados --}}
@include('header')
@include('nav')

{{-- Contenido principal del dashboard: Se cambió el padding para subirlo al tope --}}
<main class="flex-1 px-8 pb-8 pt-4 bg-[#f8fafc] overflow-y-auto">
    <h2 class="text-3xl font-bold text-slate-800 mb-8">Bienvenido Usuluteco</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-emerald-200 flex flex-col justify-between h-48 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Citas realizadas</p>
                <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-end justify-between mt-4 z-10">
                <span id="numCitas" class="text-5xl font-bold text-emerald-700">--</span>
                <div class="w-1/2 h-16 relative"> <canvas id="chartCitas"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-amber-200 flex flex-col justify-between h-48 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Citas no asistidas</p>
                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-end justify-between mt-4 z-10">
                <span id="numNoAsistidas" class="text-5xl font-bold text-amber-700">--</span>
                <div class="w-1/2 h-16 relative"> <canvas id="chartNoAsistidas"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-rose-200 flex flex-col justify-between h-48 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Citas canceladas</p>
                <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-end justify-between mt-4 z-10">
                <span id="numCanceladas" class="text-5xl font-bold text-rose-700">--</span>
                <div class="w-1/2 h-16 relative"> <canvas id="chartCanceladas"></canvas>
                </div>
            </div>
        </div>

        {{-- Tarjeta rediseñada: Número + Dona --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-blue-100 flex flex-col justify-between h-48 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium leading-tight">Citas Completadas <br><span class="text-xs text-slate-400 font-normal">Citas Agendadas</span></p>
                <span class="bg-blue-50 text-blue-800 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-center justify-between mt-4 z-10">
                <div class="flex flex-col">
                    <span id="numCompletadas" class="text-5xl font-bold text-blue-800 leading-none">--</span>
                    <span class="text-xs font-bold text-blue-600/60 mt-2 uppercase tracking-wide">Completadas</span>
                </div>
                <div class="w-20 h-20 relative">
                    <canvas id="chartTasa"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="percentTasa" class="text-xs font-bold text-blue-800">--%</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-800">Movimiento esta semana</h3>
                <span class="text-sm text-slate-400">Últimos 7 días</span>
            </div>
            
            <div class="relative w-full h-80">
                <canvas id="chartMovimiento"></canvas>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Tratamientos realizados</h3>
                    <span class="text-sm text-slate-400">Últimos 7 días</span>
                </div>
                
                <div class="flex items-center">
                    <div class="w-1/2 h-40 relative">
                         <canvas id="chartTratamientos"></canvas>
                    </div>
                    
                    <div id="leyendaTratamientos" class="w-1/2 pl-4 space-y-2">
                        <p class="text-xs text-slate-400 italic">Cargando...</p>
                    </div>
                </div>
            </div>

            {{-- Tarjeta de Citas Pendientes Rediseñada (Separada en Hoy y Próximas) --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex-1 overflow-hidden flex flex-col">
                <div class="flex justify-between items-center mb-2 shrink-0">
                    <h3 class="text-lg font-bold text-slate-800">Agenda</h3>
                </div>

                <div class="overflow-y-auto pr-2 custom-scrollbar flex-1 pb-2">
                    
                    {{-- Sección HOY --}}
                    <div class="mb-5">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            Para Hoy <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[9px]">Prioridad</span>
                        </h4>
                        <div id="contenedorNotificacionesHoy" class="space-y-3">
                            <p class="text-sm text-slate-400 italic">Cargando...</p>
                        </div>
                    </div>

                    {{-- Sección PRÓXIMAS --}}
                    <div>
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 pt-3 border-t border-slate-100">
                            Próximos días
                        </h4>
                        <div id="contenedorNotificacionesProximas" class="space-y-3">
                            <p class="text-sm text-slate-400 italic">Cargando...</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script src="{{ asset('js/utils/api.js') }}"></script>
<script src="{{ asset('js/charts.js') }}" defer></script>
<script src="{{ asset('js/horacolor.js') }}" defer></script>

</body>
</html>