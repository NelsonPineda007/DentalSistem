@include('header')
@include('nav')

<main class="flex-1 p-6 md:p-10 bg-[#f8fafc] overflow-y-auto h-screen w-full">
    
    {{-- ========================================== --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================== --}}
    <div class="mb-8 w-full">
        <h2 class="text-3xl font-bold text-slate-800">Mi Perfil</h2>
        <p class="text-slate-500 mt-1 font-medium">Gestiona tu información personal, seguridad y preferencias.</p>
    </div>

    {{-- ========================================== --}}
    {{-- CONTENEDOR PRINCIPAL --}}
    {{-- ========================================== --}}
    <div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        
        {{-- COLUMNA IZQUIERDA (Tarjeta de Usuario & Estadísticas) --}}
        <div class="lg:col-span-4 xl:col-span-3 flex flex-col gap-6">
            
            {{-- Tarjeta Principal de Usuario --}}
            <div class="bg-white rounded-3xl border border-slate-100 p-8 text-center shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-blue-700 to-blue-900"></div>
                
                {{-- CÍRCULO CON INICIALES --}}
                <div class="relative w-28 h-28 mx-auto mt-4 mb-4 rounded-full p-1.5 bg-white shadow-md">
                    <div class="w-full h-full bg-blue-50 text-blue-800 rounded-full flex items-center justify-center text-5xl font-bold border border-blue-100 flex-shrink-0">
                        @php
                            $user = auth()->user();
                            $inicialNombre = substr($user->nombre ?? '', 0, 1);
                            $inicialApellido = substr($user->apellido ?? '', 0, 1);
                            $iniciales = strtoupper($inicialNombre . $inicialApellido);
                        @endphp
                        {{ $iniciales ?: '--' }}
                    </div>
                </div>
                
                {{-- Nombre y Rol --}}
                <h3 class="text-xl font-bold text-slate-800 mt-2" id="ui-nombre">
                    Dr(a). {{ $user->nombre }} {{ $user->apellido }}
                </h3>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold mt-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span id="ui-rol">Administrador</span> 
                </div>
                
                {{-- Datos de contacto rápidos --}}
                <div class="border-t border-slate-100 mt-6 pt-6 flex flex-col gap-3 text-sm font-medium text-slate-500 text-left">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span id="ui-email">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span id="ui-telefono">{{ $user->telefono ?? 'Sin teléfono' }}</span>
                    </div>
                </div>
            </div>

            {{-- Tarjeta de Rendimiento --}}
            <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Citas Atendidas (Mes)</p>
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-black text-slate-800" id="ui-citas">0</h3>
                </div>
                <div class="mt-4 w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full w-[70%] rounded-full relative"></div>
                </div>
            </div>
            
        </div>

        {{-- COLUMNA DERECHA (Formularios y Pestañas) --}}
        <div class="lg:col-span-8 xl:col-span-9 flex flex-col">
            
            {{-- Navegación de Pestañas --}}
            <div class="flex border-b border-slate-200 mb-6 gap-2 overflow-x-auto custom-scrollbar">
                <button onclick="window.switchTab('info-tab', this)" class="tab-btn px-5 py-3 border-b-2 border-blue-800 text-blue-800 font-bold text-sm transition-all whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Información General
                </button>
                <button onclick="window.switchTab('security-tab', this)" class="tab-btn px-5 py-3 border-b-2 border-transparent text-slate-500 font-bold text-sm hover:text-slate-800 transition-all whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Seguridad de la Cuenta
                </button>
                <button onclick="window.switchTab('activity-tab', this)" class="tab-btn px-5 py-3 border-b-2 border-transparent text-slate-500 font-bold text-sm hover:text-slate-800 transition-all whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Actividad Reciente
                </button>
            </div>

            {{-- CONTENIDO DE PESTAÑAS --}}
            <div id="content-area" class="w-full bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
                
                {{-- 1. PESTAÑA: INFO GENERAL --}}
                <div id="info-tab" class="tab-content block animate-fadeIn">
                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800">Datos Personales</h3>
                        <p class="text-sm text-slate-500">Actualiza tu información de contacto y detalles profesionales.</p>
                    </div>

                    <form id="formPerfil" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Nombre</label>
                            <input type="text" name="nombre" id="input_nombre" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-medium focus:bg-white focus:border-blue-500 outline-none transition-all" required>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Apellido</label>
                            <input type="text" name="apellido" id="input_apellido" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-medium focus:bg-white focus:border-blue-500 outline-none transition-all" required>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Correo Electrónico</label>
                            <input type="email" name="email" id="input_email" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-medium focus:bg-white focus:border-blue-500 outline-none transition-all" required>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Teléfono</label>
                            <input type="text" name="telefono" id="input_telefono" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-medium focus:bg-white focus:border-blue-500 outline-none transition-all">
                        </div>

                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Especialidad o Cargo</label>
                            <input type="text" name="especialidad" id="input_especialidad" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-medium focus:bg-white focus:border-blue-500 outline-none transition-all">
                        </div>

                        <div class="md:col-span-2 flex justify-end mt-2 pt-4 border-t border-slate-100">
                            <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg flex items-center justify-center gap-2 w-full md:w-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 2. PESTAÑA: SEGURIDAD (Actualizada para pedir correo) --}}
                <div id="security-tab" class="tab-content hidden animate-fadeIn">
                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800">Seguridad de la Cuenta</h3>
                        <p class="text-sm text-slate-500">Por motivos de seguridad, los cambios de contraseña se realizan a través de un enlace enviado a tu correo.</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 p-6 rounded-2xl max-w-2xl flex flex-col sm:flex-row items-center gap-6">
                        <div class="w-16 h-16 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <h4 class="font-bold text-blue-900 mb-1">Restablecer Contraseña</h4>
                            <p class="text-sm text-blue-800/80 mb-4">Se enviará un enlace de seguridad al correo <b><span id="ui-email-seguridad">{{ $user->email }}</span></b>.</p>
                            
                            <button type="button" id="btn-solicitar-password" class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-md shadow-blue-900/20 text-sm w-full sm:w-auto">
                                Enviar enlace al correo
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 3. PESTAÑA: ACTIVIDAD RECIENTE --}}
                <div id="activity-tab" class="tab-content hidden animate-fadeIn">
                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800">Actividad Reciente</h3>
                        <p class="text-sm text-slate-500">Últimos movimientos registrados por el sistema.</p>
                    </div>
                    <div id="lista-actividad" class="space-y-4">
                        <p class="text-slate-400 italic p-4 text-center">Cargando actividad...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<style>
    .animate-fadeIn { animation: fadeIn 0.3s ease-in-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

{{-- Inclusión de Scripts necesarios --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>
<script src="{{ asset('js/NotificacionesControlador.js') }}"></script>
<script src="{{ asset('js/perfilControlador.js') }}"></script>

</body>
</html>