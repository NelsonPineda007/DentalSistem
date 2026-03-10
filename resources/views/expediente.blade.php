{{-- Inclusiones de Blade --}}
@include('header')
@include('nav')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<main class="flex-1 p-4 md:p-8 bg-[#f8fafc] h-screen flex flex-col overflow-y-auto" id="main-container">

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-6 flex-shrink-0 relative overflow-hidden" id="cabecera-paciente">
        <div class="absolute top-0 left-0 w-2 h-full bg-blue-800"></div>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-5">
                <div id="exp-iniciales" class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-800 flex items-center justify-center text-2xl font-bold border border-blue-100 flex-shrink-0">--</div>
                <div>
                    <h2 id="exp-nombre" class="text-2xl font-bold text-slate-800">Cargando...</h2>
                    <div class="flex flex-wrap items-center gap-3 mt-1 text-sm font-medium text-slate-500">
                        <span class="flex items-center gap-1"><i class="fas fa-id-card text-slate-400"></i> Exp: <span id="exp-numero">--</span></span>
                        <span class="flex items-center gap-1"><i class="fas fa-birthday-cake text-slate-400"></i> <span id="exp-edad">--</span></span>
                        <span class="flex items-center gap-1"><i class="fas fa-phone text-slate-400"></i> <span id="exp-telefono">--</span></span>
                        <span id="exp-alergias" class="hidden bg-rose-100 text-rose-600 px-2 py-0.5 rounded-md text-xs font-bold items-center gap-1">
                            <i class="fas fa-exclamation-triangle"></i> Alergia: <span id="exp-alergia-texto"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto mt-2 md:mt-0" data-html2canvas-ignore="true">
                <a href="/pacientes" class="flex-1 md:flex-none text-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition-colors">Volver</a>
                <button onclick="window.imprimirFichaPDF()" class="flex-1 md:flex-none px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir PDF
                </button>
                <button id="btnGuardarFicha" onclick="window.guardarFichaClinica()" class="flex-1 md:flex-none px-5 py-2.5 bg-blue-800 hover:bg-blue-900 text-white rounded-xl font-bold shadow-lg shadow-blue-900/20 transition-all whitespace-nowrap">Guardar Ficha</button>
            </div>
        </div>
    </div>

    <div class="flex border-b border-slate-200 mb-6 gap-6 px-2 flex-shrink-0 overflow-x-auto custom-scrollbar" data-html2canvas-ignore="true">
        <button class="tab-btn pb-3 text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors whitespace-nowrap" data-target="tab-odontograma">
            <i class="fas fa-tooth mr-2"></i>Ficha Clínica Dental
        </button>
        <button class="tab-btn pb-3 text-sm font-medium text-slate-500 hover:text-blue-600 border-b-2 border-transparent transition-colors whitespace-nowrap" data-target="tab-historia">
            <i class="fas fa-notes-medical mr-2"></i>Historia de Consulta
        </button>
        <button class="tab-btn pb-3 text-sm font-medium text-slate-500 hover:text-blue-600 border-b-2 border-transparent transition-colors whitespace-nowrap" data-target="tab-finanzas">
            <i class="fas fa-file-invoice-dollar mr-2"></i>Tratamientos y Pagos
        </button>
    </div>

    <div class="flex-1 flex flex-col gap-6" id="contenedor-impresion">

        <div id="tab-odontograma" class="tab-content flex flex-col gap-8">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
                <div class="flex justify-between items-center mb-6 min-w-[700px] border-b border-slate-100 pb-4">
                    <h3 class="font-bold text-slate-800 text-lg uppercase tracking-wide">1. Diagnóstico Visual</h3>
                    
                    <div class="flex flex-wrap gap-4 text-xs font-bold text-slate-500">
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-white border border-slate-300"></div> Sano</span>
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-rose-500"></div> Caries</span>
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-blue-500"></div> Restaurado</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-slate-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="4" y1="4" x2="20" y2="20"></line><line x1="20" y1="4" x2="4" y2="20"></line></svg> Ausente
                        </span>
                        <span class="flex items-center gap-1 text-rose-500">
                            <svg class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="4" y1="4" x2="20" y2="20"></line><line x1="20" y1="4" x2="4" y2="20"></line></svg> Extr. Indicada
                        </span>
                    </div>
                </div>

                <div class="min-w-[700px] flex flex-col gap-8 py-2 mb-6">
                    <div class="flex justify-center gap-6"><div id="diag-c1" class="flex gap-1"></div><div class="w-px bg-slate-300"></div><div id="diag-c2" class="flex gap-1"></div></div>
                    <div class="flex justify-center gap-6"><div id="diag-c4" class="flex gap-1"></div><div class="w-px bg-slate-300"></div><div id="diag-c3" class="flex gap-1"></div></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 min-w-[700px]">
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Dientes Ausentes</label><input type="text" id="diag_ausentes" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 bg-slate-50 text-sm font-medium text-slate-700"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Extracción Indicada</label><input type="text" id="diag_extraccion" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 bg-slate-50 text-sm font-medium text-rose-600"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Diagnóstico Final</label><input type="text" id="diag_final" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 bg-slate-50 text-sm font-medium text-slate-700"></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
                <div class="flex justify-between items-center mb-6 min-w-[700px] border-b border-slate-100 pb-4">
                    <h3 class="font-bold text-slate-800 text-lg uppercase tracking-wide">2. Operatoria - Prótesis</h3>
                    <div class="flex gap-4 text-xs font-bold text-slate-500">
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-white border border-slate-300 rounded-full"></div> Sano</span>
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-rose-500 rounded-full"></div> Caries</span>
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-blue-500 rounded-full"></div> Restaurado</span>
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-slate-800 rounded-full"></div> Extraído / A extraer</span>
                    </div>
                </div>
                <div class="min-w-[700px] flex flex-col gap-8 py-2 mb-8">
                    <div class="flex justify-center gap-6"><div id="oper-c1" class="flex gap-1"></div><div class="w-px bg-slate-300"></div><div id="oper-c2" class="flex gap-1"></div></div>
                    <div class="flex justify-center gap-6"><div id="oper-c4" class="flex gap-1"></div><div class="w-px bg-slate-300"></div><div id="oper-c3" class="flex gap-1"></div></div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 min-w-[700px] items-end">
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Dientes Color</label><input type="text" id="prot_color" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-sm"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Guía</label><input type="text" id="prot_guia" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-sm"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Molde</label><input type="text" id="prot_molde" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-sm"></div>
                    <div class="flex items-center gap-3 h-10 px-4"><input type="checkbox" id="prot_acrilico" class="w-4 h-4 text-blue-800 rounded border-slate-300"><span class="text-sm font-bold text-slate-600 uppercase">Acrílico</span></div>
                    <div class="flex items-center gap-3 h-10 px-4"><input type="checkbox" id="prot_porcelana" class="w-4 h-4 text-blue-800 rounded border-slate-300"><span class="text-sm font-bold text-slate-600 uppercase">Porcelana</span></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-800 text-lg uppercase tracking-wide mb-6 border-b border-slate-100 pb-4">3. Endodoncia - Cirugía</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Diente</label><input type="text" id="endo_diente" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-sm"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Vitalidad</label><input type="text" id="endo_vitalidad" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-sm"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Med. Provisional</label><input type="text" id="endo_provisional" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-sm"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Med. de Trabajo</label><input type="text" id="endo_trabajo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 text-sm"></div>
                </div>
            </div>
        </div>

<div id="tab-historia" class="tab-content hidden h-full">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 grid grid-cols-1 gap-6">
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Motivo de Consulta</label><textarea id="hc_motivo" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 bg-slate-50"></textarea></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Síntomas Reportados</label><textarea id="hc_sintomas" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500"></textarea></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Examen Clínico / Observaciones</label><textarea id="hc_observaciones" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500"></textarea></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Diagnóstico Presuntivo</label><textarea id="hc_diagnostico" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500"></textarea></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-2">Prescripciones / Receta</label><textarea id="hc_prescripciones" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 bg-emerald-50/30"></textarea></div>                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Próxima Cita Recomendada</label>
                    <input type="date" id="hc_proxima_cita" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-blue-500 bg-slate-50 text-sm font-medium text-slate-700">
                </div>
            </div>
        </div>

        <div id="tab-finanzas" class="tab-content hidden flex-col h-full" data-html2canvas-ignore="true">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 flex-shrink-0">
                <h3 class="font-bold text-slate-800 text-lg">Historial de Tratamientos y Pagos</h3>
                <button onclick="window.openModal('modalVisita', 'add')" class="w-full sm:w-auto px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition-all">
                    + Registrar Nueva Visita
                </button>
            </div>
            @php 
                $tableColumns = ['Fecha', 'Tratamiento(s)', 'Valor', 'Abono', 'Saldo', 'Acciones'];
                $tableID = 'pagosTableBody'; 
                $containerID = 'pagosTableContainer'; 
            @endphp
            @include('components.tabla_base')
        </div>

    </div>
</main>

{{-- MODAL PARA AGREGAR MULTIPLES TRATAMIENTOS --}}
@section('modal_content')
<form id="formVisita" class="flex flex-col gap-4">
    <input type="hidden" name="id">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fecha *</label>
            <input type="date" name="fecha" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
        </div>
        
        <div class="relative">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tratamiento(s) *</label>
            <div class="relative">
                <input type="text" id="tratamiento_search" autocomplete="off" placeholder="Buscar y agregar..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium text-slate-700">
                <svg class="absolute right-3 top-2.5 w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div id="tratamiento_dropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-52 overflow-y-auto custom-scrollbar divide-y divide-slate-100"></div>
            
            <div id="lista_tratamientos" class="flex flex-wrap gap-2 mt-3"></div>
        </div>

        <div class="col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Diente(s) Aplicado(s)</label>
            <input type="text" id="input_dientes_modal" name="diente" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 font-bold text-blue-800 bg-blue-50/50" placeholder="Ej: 16, 45">
            <p class="text-[10px] text-slate-400 mt-1">Se autocompleta basado en los dientes afectados de la Ficha Dental.</p>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Valor Total ($) *</label>
            <input type="number" step="0.01" name="valor" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 font-bold text-slate-800" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Abono Recibido ($) *</label>
            <input type="number" step="0.01" name="abono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-emerald-500 font-bold text-emerald-600" required>
        </div>
    </div>
</form>
@endsection

@include('components.modal_base', [
    'modalID' => 'modalVisita',
    'modalTitle' => 'Registrar Nueva Visita',
    'modalContent' => View::yieldContent('modal_content')
])

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>
<script src="{{ asset('js/utils/api.js') }}"></script>
<script src="{{ asset('js/utils/paginadorTabla.js') }}"></script>
<script src="{{ asset('js/expedienteControlador.js') }}"></script>


</body>
</html>