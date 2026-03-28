{{-- Inclusiones de Blade --}}
@include('header')
@include('nav')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<main class="flex-1 p-8 bg-[#f8fafc] h-screen flex flex-col overflow-y-auto"> 

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 flex-shrink-0">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Pacientes</h2>
            <p class="text-slate-500 mt-1 font-medium">Gestión y control de expedientes clínicos</p>
        </div>
        
        <button onclick="window.openModal('modalPacientes', 'add')" 
            class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-3 rounded-xl shadow-lg shadow-blue-900/20 font-semibold flex items-center gap-2 transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Paciente
        </button>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row gap-4 flex-shrink-0">
        <div class="relative flex-1">
            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" id="searchInput" placeholder="Buscar por nombre, expediente o teléfono..." 
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-blue-800/20 focus:bg-white transition-all outline-none placeholder:text-slate-400 font-medium">
        </div>
        <select id="filterEstado" class="px-4 py-3 bg-slate-50 rounded-xl text-slate-600 font-medium focus:ring-2 focus:ring-blue-800/20 outline-none cursor-pointer border-none min-w-[180px]">
            <option value="1" selected>Activos</option> <option value="">Todos los estados</option>
            <option value="0">Inactivos</option>
        </select>
    </div>

    {{-- Tabla de Pacientes --}}
    @php
        $tableColumns = ['Expediente', 'Paciente', 'Contacto', 'Edad', 'Estado', 'Acciones'];
        $tableID = 'patientsTableBody'; 
    @endphp
    @include('components.tabla_base')

</main>

{{-- Sección del Modal (Contenido del formulario) --}}
@section('modal_content')
<form id="formPaciente" class="flex flex-col h-full">
    <input type="hidden" name="id">
    
    {{-- Tabs del Modal --}}
    <div class="flex border-b border-slate-200 mb-6 gap-6 px-1">
        <button type="button" class="tab-btn pb-2 text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors" data-target="tab-personal">Información Personal</button>
        <button type="button" class="tab-btn pb-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors border-b-2 border-transparent" data-target="tab-contacto">Contacto</button>
        <button type="button" class="tab-btn pb-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors border-b-2 border-transparent" data-target="tab-medica">Ficha Médica</button>
    </div>

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
        
        {{-- Tab: Información Personal --}}
        <div id="tab-personal" class="tab-content grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">N° Expediente</label>
                    <input type="text" name="expediente" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border-none text-slate-600 font-bold cursor-not-allowed" readonly>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estado</label>
                    <select name="activo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>

            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombres *</label><input type="text" name="nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Apellidos *</label><input type="text" name="apellido" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required></div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">DUI</label>
                <input type="text" name="DUI" placeholder="00000000-0" maxlength="10" 
                    oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.length > 8) { this.value = v.slice(0,8) + '-' + v.slice(8,9); } else { this.value = v; }"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500">
            </div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fecha Nacimiento</label><input type="date" name="fecha_nacimiento" max="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 hover:border-blue-400 transition-colors cursor-pointer"></div>
            
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Género</label><select name="genero" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white outline-none focus:border-blue-500"><option value="">Seleccionar...</option><option>Masculino</option><option>Femenino</option><option>Otro</option></select></div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Grupo Sanguíneo</label>
                <select name="grupo_sanguineo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white outline-none focus:border-blue-500">
                    <option value="">Desconocido</option><option>O+</option><option>O-</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option>
                </select>
            </div>

            <div class="md:col-span-2 flex flex-col md:flex-row gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200 items-center">
                <div class="flex items-center gap-3 w-full md:w-1/3">
                    <input type="checkbox" id="es_menor_check" name="es_menor_check" class="w-5 h-5 text-blue-800 rounded border-slate-300 focus:ring-blue-800 cursor-pointer" onchange="document.querySelector('[name=responsable_legal]').disabled = !this.checked; if(!this.checked) document.querySelector('[name=responsable_legal]').value = '';">
                    <label for="es_menor_check" class="text-sm font-bold text-slate-700 cursor-pointer">Es menor de edad</label>
                </div>
                <div class="w-full md:w-2/3">
                    <input type="text" name="responsable_legal" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 bg-white disabled:bg-slate-100 disabled:text-slate-400 transition-colors" placeholder="Nombre del responsable legal (Padre/Madre/Tutor)" disabled>
                </div>
            </div>

            <div class="md:col-span-2"><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Dirección Completa</label><textarea name="direccion" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500"></textarea></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ciudad</label><input type="text" name="ciudad" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Código Postal</label><input type="text" name="codigo_postal" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
        </div>

        {{-- Tab: Contacto --}}
        <div id="tab-contacto" class="tab-content hidden grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Teléfono Móvil *</label>
                {{-- NUEVA MÁSCARA TELEFÓNICA (Conecta con la función JS del final) --}}
                <input type="tel" name="telefono" placeholder="0000-0000 o +1 000-000-0000" required
                    oninput="maskPhone(this)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 placeholder:text-slate-300">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                <input type="email" name="email" maxlength="100" placeholder="ejemplo@correo.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 placeholder:text-slate-300">
            </div>
            
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Empresa / Institución</label><input type="text" name="empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" placeholder="Ej: Universidad Francisco Gavidia"></div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Preferencia de Contacto</label>
                <select name="preferencia_contacto" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white outline-none focus:border-blue-500">
                    <option value="">Cualquiera</option>
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="Llamada">Llamada telefónica</option>
                    <option value="Correo">Correo Electrónico</option>
                </select>
            </div>

            <div class="md:col-span-2 pt-4 pb-2 border-t border-slate-100 mt-2"><h4 class="text-sm font-bold text-blue-800">Contacto de Emergencia</h4></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombre Contacto</label><input type="text" name="contacto_emergencia_nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Teléfono Emergencia</label>
                {{-- NUEVA MÁSCARA TELEFÓNICA PARA EMERGENCIA --}}
                <input type="tel" name="contacto_emergencia_telefono" placeholder="0000-0000 o +1 000-000-0000"
                    oninput="maskPhone(this)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 placeholder:text-slate-300">
            </div>
        </div>

        {{-- Tab: Ficha Médica --}}
        <div id="tab-medica" class="tab-content hidden space-y-4">
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Seguro Médico</label><input type="text" name="seguro_medico" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" placeholder="Nombre de la aseguradora o póliza"></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alergias Conocidas</label><textarea name="alergias" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300"></textarea></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Enfermedades Crónicas</label><textarea name="enfermedades_cronicas" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300"></textarea></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Medicamentos Actuales</label><textarea name="medicamentos_actuales" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300"></textarea></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Notas Médicas Adicionales</label><textarea name="notas_medicas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 bg-yellow-50/50"></textarea></div>
        </div>
    </div>
</form>
@endsection

{{-- Renderizado del Modal Base --}}
@include('components.modal_base', [
    'modalID' => 'modalPacientes',
    'modalTitle' => 'Nuevo Paciente',
    'modalContent' => View::yieldContent('modal_content')
])

{{-- MÁSCARA TELEFÓNICA UNIVERSAL (Script) --}}
<script>
    function maskPhone(input) {
        let v = input.value.replace(/[^\d+]/g, ''); // Limpia todo menos números y el signo +
        
        // Evitar múltiples signos +
        if (v.indexOf('+') > 0) {
            v = v[0] + v.substring(1).replace(/\+/g, '');
        }

        if (v.startsWith('+')) {
            // FORMATO USA: +1 000-000-0000
            let clean = v.substring(1);
            if (clean.length > 11) clean = clean.substring(0, 11); 
            
            let res = '+';
            if(clean.length > 0) res += clean.substring(0, 1);
            if(clean.length > 1) res += ' ' + clean.substring(1, 4);
            if(clean.length > 4) res += '-' + clean.substring(4, 7);
            if(clean.length > 7) res += '-' + clean.substring(7, 11);
            
            input.value = res;
        } else {
            // FORMATO EL SALVADOR: 0000-0000
            v = v.replace(/\+/g, ''); // Si borran el primer número y queda el +, lo quitamos
            if (v.length > 8) v = v.substring(0, 8);
            if (v.length > 4) {
                input.value = v.substring(0, 4) + '-' + v.substring(4, 8);
            } else {
                input.value = v;
            }
        }
    }
</script>

{{-- Scripts --}}
<script src="{{ asset('js/utils/reportes.js') }}"></script>
<script src="{{ asset('js/utils/paginadorTabla.js') }}"></script>
<script src="{{ asset('js/utils/api.js') }}"></script>
<script src="{{ asset('js/PacientesControlador.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/utils/alertas.js') }}"></script>

</body>
</html>