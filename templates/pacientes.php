<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<main class="flex-1 p-8 bg-[#f8fafc] overflow-y-auto h-screen">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Pacientes</h2>
            <p class="text-slate-500 mt-1 font-medium">Gestión y control de expedientes clínicos</p>
        </div>
        
        <button onclick="window.openModal('modalPacientes', 'add')" 
            class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-3 rounded-xl shadow-lg shadow-blue-900/20 font-semibold flex items-center gap-2 transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nuevo Paciente
        </button>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="searchInput" placeholder="Buscar por nombre, expediente o teléfono..." 
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-blue-800/20 focus:bg-white transition-all outline-none placeholder:text-slate-400 font-medium">
        </div>
        <select class="px-4 py-3 bg-slate-50 rounded-xl text-slate-600 font-medium focus:ring-2 focus:ring-blue-800/20 outline-none cursor-pointer border-none min-w-[180px]">
            <option>Todos los estados</option>
            <option>Activos</option>
            <option>Inactivos</option>
        </select>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-[600px]">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-white z-10">
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs uppercase text-slate-500 font-bold tracking-wider">
                        <th class="px-6 py-5">Expediente</th>
                        <th class="px-6 py-5">Paciente</th>
                        <th class="px-6 py-5">Contacto</th>
                        <th class="px-6 py-5">Edad</th>
                        <th class="px-6 py-5">Estado</th>
                        <th class="px-6 py-5">Acciones</th>
                    </tr>
                </thead>
                <tbody id="patientsTableBody" class="divide-y divide-slate-100 text-sm text-slate-600">
                    </tbody>
            </table>
        </div>
        
        <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between bg-white" id="paginationControls">
            <span class="text-slate-500 text-sm font-medium" id="paginationInfo">Cargando...</span>
            
            <div class="flex items-center gap-3">
                <button id="btnPrev" class="flex items-center gap-2 px-4 py-2 text-slate-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Anterior
                </button>
                
                <div class="h-4 w-px bg-slate-200"></div>
                
                <button id="btnNext" class="flex items-center gap-2 px-4 py-2 text-slate-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
                    Siguiente
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </div>
</main>

<?php ob_start(); ?>
<form id="formPaciente" class="flex flex-col h-full">
    <input type="hidden" name="id">

    <div class="flex border-b border-slate-200 mb-6 gap-6 px-1">
        <button type="button" class="tab-btn pb-2 text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors" data-target="tab-personal">
            Información Personal
        </button>
        <button type="button" class="tab-btn pb-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors border-b-2 border-transparent" data-target="tab-contacto">
            Contacto
        </button>
        <button type="button" class="tab-btn pb-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors border-b-2 border-transparent" data-target="tab-medica">
            Ficha Médica
        </button>
    </div>

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
        
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
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombres *</label>
                <input type="text" name="nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Apellidos *</label>
                <input type="text" name="apellido" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fecha Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Género</label>
                <select name="genero" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500">
                    <option>Masculino</option>
                    <option>Femenino</option>
                    <option>Otro</option>
                </select>
            </div>
            <div class="md:col-span-2">
                 <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Dirección Completa</label>
                 <textarea name="direccion" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ciudad</label>
                <input type="text" name="ciudad" value="San Salvador" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500">
            </div>
             <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Código Postal</label>
                <input type="text" name="codigo_postal" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500">
            </div>
        </div>

        <div id="tab-contacto" class="tab-content hidden grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Teléfono Móvil *</label>
                <input type="tel" name="telefono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500">
            </div>
            
            <div class="md:col-span-2 pt-4 pb-2 border-t border-slate-100 mt-2">
                <h4 class="text-sm font-bold text-blue-800">Contacto de Emergencia</h4>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombre Contacto</label>
                <input type="text" name="contacto_emergencia_nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Teléfono Emergencia</label>
                <input type="tel" name="contacto_emergencia_telefono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500">
            </div>
        </div>

        <div id="tab-medica" class="tab-content hidden space-y-4">
             <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Seguro Médico</label>
                <input type="text" name="seguro_medico" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" placeholder="Nombre de la aseguradora">
            </div>
            <div>
                 <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alergias Conocidas</label>
                 <textarea name="alergias" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300" placeholder="Ej: Penicilina, Látex..."></textarea>
            </div>
            <div>
                 <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Enfermedades Crónicas</label>
                 <textarea name="enfermedades_cronicas" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300" placeholder="Ej: Diabetes, Hipertensión..."></textarea>
            </div>
            <div>
                 <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Medicamentos Actuales</label>
                 <textarea name="medicamentos_actuales" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300" placeholder="Ej: Metformina 500mg..."></textarea>
            </div>
             <div>
                 <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Notas Médicas Adicionales</label>
                 <textarea name="notas_medicas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 bg-yellow-50/50"></textarea>
            </div>
        </div>
    </div>
</form>
<?php 
    $modalContent = ob_get_clean(); 
    $modalID = "modalPacientes";    
    $modalTitle = "Nuevo Paciente"; 
    
    // Incluir componente modal
    include 'components/modal_base.php'; 
?>

<script src="../static/js/utils/paginadorTabla.js"></script>
<script src="../static/js/utils/reportes.js"></script>
<script src="../static/js/PacientesControlador.js"></script>
<script src="../static/js/paginacion.js" defer></script>

</body>
</html>