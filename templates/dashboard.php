<?php // rutas php  ?>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<?php // aca ira todo el contenido del main o lo que va dentro de la pagina  ?>

<?php // titulo  ?>
<main class="flex-1 p-8 bg-[#f8fafc] overflow-y-auto">
    <h2 class="text-3xl font-bold text-slate-800 mb-8">Bienvenido Usuluteco</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-emerald-200 flex flex-col justify-between h-48 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Números de citas</p>
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

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-blue-100 flex flex-col justify-between h-48 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium leading-tight">Tasa de <br>re-agendamiento</p>
                <span class="bg-blue-50 text-blue-800 text-[10px] font-bold px-2 py-1 rounded-md">Hoy</span>
            </div>
            <div class="flex items-center justify-between mt-4 z-10">
                <span id="numTasa" class="text-5xl font-bold text-blue-800">--</span>
                <div class="w-20 h-20 relative">
                    <canvas id="chartTasa"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="percentTasa" class="text-[10px] font-bold text-blue-800">--%</span>
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
                    
                    <div class="w-1/2 pl-4 space-y-2">
                        <div class="flex items-center text-xs text-slate-600">
                            <span class="w-2 h-2 rounded-full bg-[#2d9596] mr-2"></span> Extracción
                        </div>
                        <div class="flex items-center text-xs text-slate-600">
                            <span class="w-2 h-2 rounded-full bg-[#3b82f6] mr-2"></span> Limpieza Dental
                        </div>
                        <div class="flex items-center text-xs text-slate-600">
                            <span class="w-2 h-2 rounded-full bg-[#facc15] mr-2"></span> Reparar Caries
                        </div>
                        <div class="flex items-center text-xs text-slate-600">
                            <span class="w-2 h-2 rounded-full bg-[#fb923c] mr-2"></span> Muelas de Juicio
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Notificaciones Citas</h3>
                    <span class="text-sm font-bold text-blue-800 bg-blue-50 px-2 py-1 rounded">Hoy</span>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3 p-3 hover:bg-blue-50 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-blue-100">
                        <div class="bg-blue-800 text-white p-2 rounded-lg flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Tienes una cita con <span class="font-bold text-slate-800">Jaime Nelsen</span> a las 8:00 AM</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 hover:bg-blue-50 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-blue-100">
                        <div class="bg-blue-800 text-white p-2 rounded-lg flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Tienes una cita con <span class="font-bold text-slate-800">Marta Stewart</span> a las 10:30 AM</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script src="../static/js/charts.js" defer></script>
<script src="../static/js/paginacion.js" defer></script>
<script src="../static/js/horacolor.js" defer></script>

</body>
</html>