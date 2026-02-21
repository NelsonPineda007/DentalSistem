<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main class="flex-1 p-6 md:p-10 bg-[#f8fafc] overflow-y-auto h-screen">
    
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-800">Mi Perfil</h2>
        <p class="text-slate-500 text-sm">Gestiona tu información personal y seguridad.</p>
    </div>

    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center">
                <div class="w-24 h-24 bg-blue-50 text-blue-600 rounded-full mx-auto flex items-center justify-center mb-4">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 uppercase text-sm">Dr. Usuario</h3>
                <p class="text-slate-400 text-xs mt-1">Administrador</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <p class="text-slate-400 text-xs font-bold uppercase mb-2">Citas del mes</p>
                <h3 class="text-3xl font-bold text-blue-800">48</h3>
                <div class="mt-3 w-full bg-slate-100 h-1.5 rounded-full">
                    <div class="bg-blue-600 h-full w-[70%] rounded-full"></div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="flex border-b border-slate-200 mb-6">
                <button onclick="switchTab('info-tab')" id="btn-info" class="tab-btn px-6 py-3 border-b-2 border-blue-800 text-blue-800 font-bold text-sm transition-all">
                    Información General
                </button>
                <button onclick="switchTab('security-tab')" id="btn-security" class="tab-btn px-6 py-3 border-b-2 border-transparent text-slate-400 font-bold text-sm hover:text-slate-600 transition-all">
                    Seguridad
                </button>
            </div>

            <div id="content-area">
                <div id="info-tab" class="tab-content">
                    <div class="bg-white rounded-2xl border border-slate-200 p-8">
                        <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Nombre Completo</label>
                                <input type="text" value="Dr. Usuario Administrador" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 focus:border-blue-800 outline-none transition-all">
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Especialidad</label>
                                <input type="text" value="Odontología General" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 focus:border-blue-800 outline-none transition-all">
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Correo Electrónico</label>
                                <input type="email" value="admin@dentista.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 focus:border-blue-800 outline-none transition-all">
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Teléfono</label>
                                <input type="text" value="+503 2222-2222" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 focus:border-blue-800 outline-none transition-all">
                            </div>

                            <div class="md:col-span-2 flex justify-end mt-4">
                                <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-md">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="security-tab" class="tab-content hidden">
                    <div class="bg-white rounded-2xl border border-slate-200 p-8">
                        <form class="max-w-md space-y-6">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Contraseña Actual</label>
                                <input type="password" placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-blue-800 outline-none transition-all">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Nueva Contraseña</label>
                                <input type="password" placeholder="Mínimo 8 caracteres" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-blue-800 outline-none transition-all">
                            </div>
                            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-md w-full md:w-auto">
                                Actualizar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function switchTab(tabId) {
    // Ocultar contenidos
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.getElementById(tabId).classList.remove('hidden');

    // Estilos de botones
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-blue-800', 'text-blue-800');
        b.classList.add('border-transparent', 'text-slate-400');
    });

    const activeBtn = (tabId === 'info-tab') ? 'btn-info' : 'btn-security';
    document.getElementById(activeBtn).classList.add('border-blue-800', 'text-blue-800');
    document.getElementById(activeBtn).classList.remove('border-transparent', 'text-slate-400');
}
</script>