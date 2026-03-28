{{-- Barra superior móvil (SOLO visible en pantallas pequeñas) --}}
<div class="md:hidden fixed top-0 left-0 w-full bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100 z-40 px-6 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
        {{-- Imagen corregida: sin 'static/' --}}
        <img src="{{ asset('imgs/logo-diente.png') }}" alt="Logo" class="w-8 h-auto object-contain">
        <h1 class="text-xl font-bold text-blue-800 tracking-wide">DENTISTA</h1>
    </div>
    <button id="openMobileMenu" class="text-slate-500 hover:text-blue-800 focus:outline-none p-2 -mr-2 transition-colors rounded-lg hover:bg-blue-50">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>

{{-- Overlay oscuro para móvil (Fondo desenfocado) --}}
<div id="mobileOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden opacity-0 transition-opacity duration-300 md:hidden"></div>

{{-- NAVBAR LATERAL (Retráctil en móvil, Fijo en PC) --}}
<aside id="mainSidebar" class="fixed md:relative top-0 left-0 h-full w-72 bg-white flex flex-col justify-between shadow-2xl md:shadow-xl shadow-blue-900/10 border-r border-gray-100 flex-shrink-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

    {{-- Logo y titulo de la pagina --}}
    <div class="p-8 flex items-center justify-between">
        <div class="flex items-center gap-1">
            {{-- Imagen corregida y limpia (sin atributos duplicados) --}}
            <img 
                src="{{ asset('imgs/logo-diente.png') }}" 
                alt="Logo Dentista" 
                class="w-14 h-auto object-contain">

            <h1 class="text-2xl font-bold text-blue-800 tracking-wide">DENTISTA</h1>
        </div>
        
        {{-- Botón cerrar SOLO en mobile --}}
        <button id="closeMobileMenu" class="md:hidden text-slate-400 hover:text-rose-500 transition-colors p-2 -mr-4 bg-slate-50 hover:bg-rose-50 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- LÓGICA BACKEND DE SESIÓN --}}
    @php
        $userRol = \Illuminate\Support\Facades\DB::table('roles')->where('id', Auth::user()->rol_id)->value('nombre') ?? 'Usuario';
        
        $nombre = Auth::user()->nombre;
        $apellido = Auth::user()->apellido;
        $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
        
        // Colores aleatorios predefinidos
        $colores = ['bg-rose-500', 'bg-blue-600', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-600', 'bg-cyan-600', 'bg-indigo-500', 'bg-pink-500'];
        $colorIndex = (ord($iniciales[0]) + (isset($iniciales[1]) ? ord($iniciales[1]) : 0)) % count($colores);
        $avatarColor = $colores[$colorIndex];
    @endphp

    {{-- Comienzo del navbar --}}
    <nav class="flex-1 px-6 space-y-2 overflow-y-auto custom-scrollbar">
        <ul class="space-y-4">

            {{-- SOLO LOS ADMINS Y DENTISTAS PUEDEN VER EL DASHBOARD --}}
            @if($userRol === 'Admin' || $userRol === 'Dentista')
            <li>
                <a href="{{ url('/dashboard') }}"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    Dashboard
                </a>
            </li>
            @endif

            <li>
                <a href="{{ url('/citas') }}"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                        </path>
                    </svg>
                    Citas
                </a>
            </li>

            <li>
                <a href="{{ url('/calendar') }}"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5h18M15 14.25h.008v.008H15v-.008z">
                        </path>
                    </svg>
                    Calendario
                </a>
            </li>

            <li>
                <a href="{{ url('/pacientes') }}"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                        </path>
                    </svg>
                    Pacientes
                </a>
            </li>

            <li>
                <a href="{{ url('/tratamiento') }}"
                   class="nav-link flex items-center gap-4 px-4 py-3 text-blue-400 font-semibold hover:text-blue-200 hover:bg-blue-900/40 hover:shadow-lg hover:shadow-blue-900/20 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Tratamientos
                </a>
            </li>

        </ul>
    </nav>

    {{-- Usuario y cerrar sesion --}}
    <div class="p-6 border-t border-gray-100 bg-white">
        
        <button onclick="window.location.href='{{ url('/perfil') }}'" 
                class="w-full flex items-center gap-3 mb-6 p-2 rounded-xl hover:bg-blue-50 transition-colors duration-200 group text-left">
            
            <div class="w-10 h-10 flex-shrink-0 rounded-full {{ $avatarColor }} shadow-lg shadow-blue-900/30 flex items-center justify-center text-white text-sm font-bold group-hover:scale-105 transition-transform">
                {{ $iniciales }}
            </div>

            <div>
                <p class="text-sm font-bold text-blue-800">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
                <p class="text-xs text-blue-400">{{ $userRol }}</p>
            </div>
        </button>

        {{-- CONTENEDOR NUEVO: Botón de Salir (Izquierda) + Botón de Notificaciones (Derecha) --}}
        <div class="flex items-center gap-2 w-full">
            
            {{-- Formulario de Logout (Toma el espacio principal) --}}
            <form action="{{ route('logout') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full h-11 flex items-center justify-center gap-2 bg-blue-800 hover:bg-blue-900 text-blue-100 rounded-xl shadow-xl shadow-blue-900/30 transition font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Salir
                </button>
            </form>

            {{-- Botón de Notificaciones (Campanita a la derecha) --}}
            <a href="{{ url('/notificaciones') }}" 
               class="flex-shrink-0 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 w-11 h-11 rounded-xl transition-colors relative" 
               title="Ver Notificaciones">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
                {{-- Puntito de alerta (Indicador visual) --}}
                <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border border-white"></span>
            </a>

        </div>
        
    </div>

</aside>

{{-- Script Automático para controlar la lógica Mobile --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const openBtn = document.getElementById('openMobileMenu');
        const closeBtn = document.getElementById('closeMobileMenu');
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.getElementById('mobileOverlay');
        const mainContent = document.querySelector('main'); 

        function adjustMobilePadding() {
            if (window.innerWidth < 768) {
                if (mainContent) mainContent.classList.add('pt-24'); 
            } else {
                if (mainContent) mainContent.classList.remove('pt-24'); 
            }
        }

        adjustMobilePadding();
        window.addEventListener('resize', adjustMobilePadding);

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
            }, 10);
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }

        if(openBtn) openBtn.addEventListener('click', openSidebar);
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);
    });
</script>