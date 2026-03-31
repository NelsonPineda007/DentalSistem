{{-- header y nav incrustados --}}
@include('header')
@include('nav')

{{-- ======================================================= --}}
{{-- PANTALLA DE BIENVENIDA MINIMALISTA ESTILO iOS --}}
{{-- ======================================================= --}}

{{-- LA BOMBA NUCLEAR ANTI-CACHÉ (Usando el ID de Sesión de Laravel) --}}
<script>
    // Laravel nos da un ID único que CAMBIA cada vez que haces Login/Logout.
    const llaveUnica = 'bienvenida_{{ session()->getId() }}';
    
    // Si ya vio la animación en ESTA sesión específica, la bloqueamos.
    if (sessionStorage.getItem(llaveUnica) === 'true') {
        document.documentElement.classList.add('skip-ios-welcome');
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700;800;900&display=swap');

    html.skip-ios-welcome #ios-welcome-screen {
        display: none !important;
    }

    #ios-welcome-screen {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: rgba(255, 255, 255, 0.9);
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .welcome-container {
        text-align: center;
        padding: 20px;
    }

    .ios-title {
        font-size: 1.4rem;
        font-weight: 500;
        color: #64748b;
        letter-spacing: 0.3em;
        text-transform: uppercase;
        margin-bottom: 15px;
        opacity: 0;
        animation: iosTextUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        animation-delay: 0.2s;
    }

    .ios-name {
        font-size: 5.5rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.04em;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 2px;
    }

    .ios-letter {
        display: inline-block;
        opacity: 0;
        transform: translateY(-40px);
        filter: blur(8px);
        animation: iosDropLetter 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    @keyframes iosTextUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes iosDropLetter {
        to { 
            opacity: 1; 
            transform: translateY(0); 
            filter: blur(0px);
        }
    }

    .screen-exit {
        animation: iosExit 0.8s ease-in forwards !important;
    }

    @keyframes iosExit {
        from { opacity: 1; backdrop-filter: blur(20px); }
        to { opacity: 0; backdrop-filter: blur(0px); visibility: hidden; }
    }

    @media (max-width: 768px) {
        .ios-name { font-size: 3.5rem; }
        .ios-title { font-size: 1rem; }
    }
</style>

<div id="ios-welcome-screen">
    <div class="welcome-container">
        <p class="ios-title">Buen día</p>
        <h1 class="ios-name" id="ios-dynamic-name">
            {{-- MAGIA DE LARAVEL: Cortamos la palabra desde el servidor --}}
            @php
                $nombreUsuario = auth()->user()->nombre ?? auth()->user()->name ?? 'Usuario';
                $letras = preg_split('//u', $nombreUsuario, -1, PREG_SPLIT_NO_EMPTY);
            @endphp
            @foreach($letras as $index => $letra)
                <span class="ios-letter" style="animation-delay: {{ 0.4 + ($index * 0.05) }}s;">{!! $letra === ' ' ? '&nbsp;' : $letra !!}</span>
            @endforeach
        </h1>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const welcomeScreen = document.getElementById('ios-welcome-screen');
        const llaveUnica = 'bienvenida_{{ session()->getId() }}';
        
        // Si no existe el elemento o ya tiene la clase skip, abortamos la animación
        if (!welcomeScreen || document.documentElement.classList.contains('skip-ios-welcome')) {
            if(welcomeScreen) welcomeScreen.remove();
            return;
        }

        // Guardamos en la memoria usando el ID ÚNICO de esta sesión de Laravel
        sessionStorage.setItem(llaveUnica, 'true');
        
        // 3 segundos y se va la animación
        setTimeout(() => {
            welcomeScreen.classList.add('screen-exit');
            setTimeout(() => {
                welcomeScreen.remove();
            }, 800); 
        }, 3000);
    });
</script>
{{-- ======================================================= --}}


<main class="flex-1 p-4 md:p-6 lg:px-8 lg:pb-6 lg:pt-4 bg-[#f8fafc] h-screen overflow-y-auto lg:overflow-hidden flex flex-col">
    
    <h2 class="text-2xl md:text-3xl font-bold text-slate-800 mb-4 md:mb-6 shrink-0">Bienvenido, {{ auth()->user()->nombre ?? auth()->user()->name ?? 'Usuario' }}</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6 shrink-0">
        <div class="bg-white p-5 md:p-6 rounded-3xl shadow-sm border border-emerald-200 flex flex-col justify-between h-36 md:h-40 relative overflow-hidden shrink-0">
            <div class="flex justify-between items-start z-10">
                <p class="text-sm md:text-base text-slate-500 font-medium">Citas pendientes</p>
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

    <div class="flex flex-col lg:flex-row gap-4 md:gap-6 flex-1 lg:min-h-0 shrink-0 lg:shrink">
        <div class="w-full lg:w-2/3 bg-white p-5 md:p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col h-[300px] sm:h-[400px] lg:h-auto lg:flex-1 lg:min-h-0 shrink-0 lg:shrink">
            <div class="flex justify-between items-center mb-4 md:mb-6 shrink-0">
                <h3 class="text-base md:text-lg font-bold text-slate-800">Movimiento esta semana</h3>
                <span class="text-xs md:text-sm text-slate-400">Últimos 7 días</span>
            </div>
            <div class="relative w-full flex-1 min-h-0">
                <canvas id="chartMovimiento"></canvas>
            </div>
        </div>

        <div class="w-full lg:w-1/3 flex flex-col gap-4 md:gap-6 h-auto lg:h-full lg:min-h-0 shrink-0 lg:shrink">
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

{{-- MODAL 1: Lista de Citas --}}
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

{{-- MODAL 2: Editar Cita Rápida --}}
@php ob_start(); @endphp
<form id="formCitaDashboard" class="flex flex-col h-full">
    <input type="hidden" name="id" id="dash_cita_id">
    <input type="hidden" name="paciente_id" id="dash_paciente_id">

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <div class="md:col-span-2 relative">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Paciente *</label>
            <input type="text" id="dash_paciente_nombre" readonly class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 outline-none cursor-not-allowed">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fecha de la cita *</label>
            <input type="date" name="fecha" id="dash_fecha" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hora Inicio *</label>
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" id="dash_hora_input_inicio" placeholder="2:30" maxlength="5"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-center font-bold text-slate-700 tracking-wider bg-white transition-colors" 
                        oninput="window.formatearHora(this)" onblur="window.validarHora(this)" required>
                </div>
                <div class="flex bg-slate-100 p-1 rounded-xl shrink-0">
                    <button type="button" id="dash_btn_am_inicio" onclick="window.setAMPMDash('inicio', 'AM')" class="px-2 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all">AM</button>
                    <button type="button" id="dash_btn_pm_inicio" onclick="window.setAMPMDash('inicio', 'PM')" class="px-2 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all">PM</button>
                </div>
            </div>
            <input type="hidden" id="dash_hora_ampm_inicio" value="AM">
            <input type="hidden" name="hora" id="dash_hora_oculta_inicio">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hora Fin *</label>
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" id="dash_hora_input_fin" placeholder="2:30" maxlength="5"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-center font-bold text-slate-700 tracking-wider bg-white transition-colors" 
                        oninput="window.formatearHora(this)" onblur="window.validarHora(this)" required>
                </div>
                <div class="flex bg-slate-100 p-1 rounded-xl shrink-0">
                    <button type="button" id="dash_btn_am_fin" onclick="window.setAMPMDash('fin', 'AM')" class="px-2 py-1.5 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600 transition-all">AM</button>
                    <button type="button" id="dash_btn_pm_fin" onclick="window.setAMPMDash('fin', 'PM')" class="px-2 py-1.5 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-800 transition-all">PM</button>
                </div>
            </div>
            <input type="hidden" id="dash_hora_ampm_fin" value="AM">
            <input type="hidden" name="hora_fin" id="dash_hora_oculta_fin">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Motivo de consulta *</label>
            <input type="text" name="motivo" id="dash_motivo" placeholder="Ej: Dolor de muela, Limpieza..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Doctor Asignado *</label>
            <select name="empleado_id" id="dash_empleado_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white outline-none focus:border-blue-500" required>
            </select>
        </div>

        <div>
             <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estado</label>
             <select name="estado" id="dash_estado" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500">
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
             <textarea name="notas" id="dash_notas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 bg-yellow-50/50"></textarea>
        </div>
        
        <div class="md:col-span-2 flex justify-end gap-3 pt-4 mt-2 border-t border-slate-100">
            <button type="button" onclick="closeModal('modalEditarCitaDash')" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors border border-slate-200">Cancelar</button>
            <button type="button" onclick="guardarEdicionCitaDashboard(event)" id="btnGuardarDash" class="px-6 py-2.5 bg-blue-800 text-white text-sm font-bold rounded-xl hover:bg-blue-900 transition-colors shadow-lg shadow-blue-900/20">Guardar Cambios</button>
        </div>

    </div>
</form>
@php 
    $modalContent = ob_get_clean(); 
    $modalID = "modalEditarCitaDash";    
    $modalTitle = "Editar Cita Rápida"; 
@endphp
@include('components.modal_base')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script src="{{ asset('js/utils/api.js') }}"></script>
<script src="{{ asset('js/charts.js') }}" defer></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>
<script src="{{ asset('js/NotificacionesControlador.js') }}"></script>

</body>
</html>