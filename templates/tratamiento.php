<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 p-8 bg-[#f8fafc] h-screen flex flex-col overflow-y-auto">
            
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 flex-shrink-0">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Tratamientos</h2>
            <p class="text-slate-500 mt-1 font-medium">Catálogo de servicios y procedimientos dentales</p>
        </div>
        
        <button onclick="window.openModal('modalTratamientos', 'add')" 
            class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-3 rounded-xl shadow-lg shadow-blue-900/20 font-semibold flex items-center gap-2 transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nuevo Tratamiento
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 flex-shrink-0">
        
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-blue-200 flex flex-col justify-between h-40 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Total Tratamientos</p>
                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tooth"></i> </div>
            </div>
            <div class="flex items-end justify-between mt-2 z-10">
                <span id="statTotal" class="text-4xl font-bold text-blue-700">--</span>
                <div class="w-1/2 h-16 relative"><canvas id="sparkTotal"></canvas></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-emerald-200 flex flex-col justify-between h-40 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Tratamientos Activos</p>
                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="flex items-end justify-between mt-2 z-10">
                <span id="statActivos" class="text-4xl font-bold text-emerald-700">--</span>
                <div class="w-1/2 h-16 relative"><canvas id="sparkActivos"></canvas></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-purple-200 flex flex-col justify-between h-40 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Categorías</p>
                <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <div class="flex items-end justify-between mt-2 z-10">
                <span id="statCategorias" class="text-4xl font-bold text-purple-700">--</span>
                <div class="w-1/2 h-16 relative"><canvas id="sparkCategorias"></canvas></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-yellow-200 flex flex-col justify-between h-40 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Costo Promedio</p>
                <div class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="flex items-end justify-between mt-2 z-10">
                <span id="statCosto" class="text-3xl font-bold text-yellow-600">$--</span>
                <div class="w-1/2 h-16 relative"><canvas id="sparkCosto"></canvas></div>
            </div>
        </div>

    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row gap-4 flex-shrink-0">
        <div class="relative flex-1">
            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="searchInput" placeholder="Buscar por código o nombre del tratamiento..." 
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-blue-800/20 outline-none font-medium">
        </div>
        <select id="filterCategoria" class="px-4 py-3 bg-slate-50 rounded-xl text-slate-600 font-medium focus:ring-2 focus:ring-blue-800/20 outline-none cursor-pointer border-none min-w-[180px]">
            <option value="">Todas las categorías</option>
            <option value="Preventivo">Preventivo</option>
            <option value="Restaurador">Restaurador</option>
            <option value="Endodoncia">Endodoncia</option>
            <option value="Ortodoncia">Ortodoncia</option>
            <option value="Cirugía">Cirugía</option>
            <option value="Estética">Estética</option>
        </select>
    </div>

    <?php 
        $tableColumns = ['Código', 'Tratamiento', 'Categoría', 'Costo Base', 'Estado', 'Acciones'];
        $tableID = 'tratamientosTableBody'; 
        include 'components/tabla_base.php'; 
    ?>

</main>

<?php ob_start(); ?>
<form id="formTratamiento" class="flex flex-col h-full space-y-5">
    <input type="hidden" name="id">

    <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
        <input type="checkbox" name="activo" class="w-5 h-5 rounded border-slate-300 text-blue-800 focus:ring-blue-800">
        <span class="font-bold text-slate-700">Tratamiento Activo (Visible en el sistema)</span>
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Código *</label>
            <input type="text" name="codigo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 font-bold text-blue-800" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Requiere Cita</label>
            <select name="requiere_cita" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500">
                <option value="true">Sí</option>
                <option value="false">No</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombre del Tratamiento *</label>
            <input type="text" name="nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Categoría</label>
            <select name="categoria" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500">
                <option value="Preventivo">Preventivo</option>
                <option value="Restaurador">Restaurador</option>
                <option value="Endodoncia">Endodoncia</option>
                <option value="Ortodoncia">Ortodoncia</option>
                <option value="Cirugía">Cirugía</option>
                <option value="Estética">Estética</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Costo Base ($) *</label>
            <input type="number" step="0.01" name="costo_base" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Duración Estimada (Minutos)</label>
            <input type="number" name="duracion_estimada" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500"></textarea>
        </div>
    </div>
</form>
<?php 
    $modalContent = ob_get_clean(); 
    $modalID = "modalTratamientos";    
    $modalTitle = "Nuevo Tratamiento"; 
    include 'components/modal_base.php'; 
?>

<script src="../static/js/utils/paginadorTabla.js"></script>
<script src="../static/js/charts.js"></script> <script src="../static/js/tratamientosControlador.js"></script>

</body>
</html>