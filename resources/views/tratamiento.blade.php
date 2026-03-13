{{-- Inclusiones de Blade --}}
@include('header')
@include('nav')

{{-- Chart.js para los Sparklines de las tarjetas de estadísticas --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 p-8 bg-[#f8fafc] h-screen flex flex-col overflow-y-auto">
            
    {{-- Header de la Sección --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 flex-shrink-0">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Tratamientos</h2>
            <p class="text-slate-500 mt-1 font-medium">Catálogo de servicios y procedimientos dentales</p>
        </div>
        
        <button onclick="window.openModal('modalTratamientos', 'add')" 
            class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-3 rounded-xl shadow-lg shadow-blue-900/20 font-semibold flex items-center gap-2 transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Tratamiento
        </button>
    </div>

    {{-- Tarjetas de Estadísticas (Stats) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 flex-shrink-0">
        
        {{-- Total Tratamientos --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-blue-200 flex flex-col justify-between h-40 relative overflow-hidden">
            <div class="flex justify-between items-start z-10">
                <p class="text-slate-500 font-medium">Total Tratamientos</p>
                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tooth"></i> 
                </div>
            </div>
            <div class="flex items-end justify-between mt-2 z-10">
                <span id="statTotal" class="text-4xl font-bold text-blue-700">--</span>
                <div class="w-1/2 h-16 relative"><canvas id="sparkTotal"></canvas></div>
            </div>
        </div>

        {{-- Tratamientos Activos --}}
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

        {{-- Categorías --}}
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

        {{-- Costo Promedio --}}
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

    {{-- Filtros y Búsqueda (CORREGIDO EL DISEÑO) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="md:col-span-3 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" id="searchInput" placeholder="Buscar por código o nombre del tratamiento..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 transition-all font-medium">
        </div>
        
        <div class="md:col-span-1">
            <select id="filterCategoria" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 bg-white font-medium text-slate-600">
                <option value="">Todas las categorías</option>
            </select>
        </div>
    </div>

    {{-- Tabla de Tratamientos --}}
    @php
        $tableColumns = ['Código', 'Tratamiento', 'Categoría', 'Costo Base', 'Estado', 'Acciones'];
        $tableID = 'tratamientosTableBody'; 
    @endphp
    @include('components.tabla_base')

</main>

{{-- Sección del Modal (Formulario de Tratamiento) --}}
@section('modal_tratamiento_content')
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
            <select name="categoria_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500">
                <option value="">Seleccione una categoría</option>
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
@endsection

{{-- Renderizado del Modal Base --}}
@include('components.modal_base', [
    'modalID' => 'modalTratamientos',    
    'modalTitle' => 'Nuevo Tratamiento', 
    'modalContent' => View::yieldContent('modal_tratamiento_content')
])

{{-- Scripts con Helper Asset de Laravel --}}
<script src="{{ asset('js/utils/paginadorTabla.js') }}"></script>
<script src="{{ asset('js/utils/api.js') }}"></script> {{-- Cliente API Global --}}
<script src="{{ asset('js/charts.js') }}"></script> 
<script src="{{ asset('js/tratamientosControlador.js') }}"></script>
{{-- CDN de SweetAlert2 y Alertas --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>
</body>
</html>