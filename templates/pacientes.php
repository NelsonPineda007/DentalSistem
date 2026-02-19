<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<main class="flex-1 p-8 bg-[#f8fafc] h-screen flex flex-col overflow-y-auto"> 

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 flex-shrink-0">
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

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row gap-4 flex-shrink-0">
        <div class="relative flex-1">
            <svg class="w-5 h-5 absolute left-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="searchInput" placeholder="Buscar por nombre, expediente o teléfono..." 
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl text-slate-700 focus:ring-2 focus:ring-blue-800/20 focus:bg-white transition-all outline-none placeholder:text-slate-400 font-medium">
        </div>
        <select id="filterEstado" class="px-4 py-3 bg-slate-50 rounded-xl text-slate-600 font-medium focus:ring-2 focus:ring-blue-800/20 outline-none cursor-pointer border-none min-w-[180px]">
            <option value="">Todos los estados</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
        </select>
    </div>

    <?php 
        // 1. Defines las columnas
        $tableColumns = ['Expediente', 'Paciente', 'Contacto', 'Edad', 'Estado', 'Acciones'];
        
        // 2. Defines el ID para el JS
        $tableID = 'patientsTableBody'; 
        
        // 3. Llamas al componente
        include 'components/tabla_base.php'; 
    ?>

</main>

<?php ob_start(); ?>
<form id="formPaciente" class="flex flex-col h-full">
    <input type="hidden" name="id">
    <div class="flex border-b border-slate-200 mb-6 gap-6 px-1">
        <button type="button" class="tab-btn pb-2 text-sm font-bold text-blue-800 border-b-2 border-blue-800 transition-colors" data-target="tab-personal">Información Personal</button>
        <button type="button" class="tab-btn pb-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors border-b-2 border-transparent" data-target="tab-contacto">Contacto</button>
        <button type="button" class="tab-btn pb-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors border-b-2 border-transparent" data-target="tab-medica">Ficha Médica</button>
    </div>
    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
        <div id="tab-personal" class="tab-content grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2 grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">N° Expediente</label><input type="text" name="expediente" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border-none text-slate-600 font-bold cursor-not-allowed" readonly></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estado</label><select name="activo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 bg-white outline-none focus:border-blue-500"><option value="1">Activo</option><option value="0">Inactivo</option></select></div>
            </div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombres *</label><input type="text" name="nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Apellidos *</label><input type="text" name="apellido" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fecha Nacimiento</label><input type="date" name="fecha_nacimiento" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Género</label><select name="genero" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white outline-none focus:border-blue-500"><option>Masculino</option><option>Femenino</option><option>Otro</option></select></div>
            <div class="md:col-span-2"><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Dirección Completa</label><textarea name="direccion" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500"></textarea></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ciudad</label><input type="text" name="ciudad" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
             <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Código Postal</label><input type="text" name="codigo_postal" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
        </div>
        <div id="tab-contacto" class="tab-content hidden grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Teléfono Móvil *</label><input type="tel" name="telefono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" required></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label><input type="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
            <div class="md:col-span-2 pt-4 pb-2 border-t border-slate-100 mt-2"><h4 class="text-sm font-bold text-blue-800">Contacto de Emergencia</h4></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombre Contacto</label><input type="text" name="contacto_emergencia_nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Teléfono Emergencia</label><input type="tel" name="contacto_emergencia_telefono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500"></div>
        </div>
        <div id="tab-medica" class="tab-content hidden space-y-4">
             <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Seguro Médico</label><input type="text" name="seguro_medico" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500" placeholder="Nombre de la aseguradora"></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alergias Conocidas</label><textarea name="alergias" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300"></textarea></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Enfermedades Crónicas</label><textarea name="enfermedades_cronicas" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300"></textarea></div>
            <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Medicamentos Actuales</label><textarea name="medicamentos_actuales" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 placeholder:text-slate-300"></textarea></div>
             <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Notas Médicas Adicionales</label><textarea name="notas_medicas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 outline-none resize-none focus:border-blue-500 bg-yellow-50/50"></textarea></div>
        </div>
    </div>
</form>
<?php 
    $modalContent = ob_get_clean(); 
    $modalID = "modalPacientes";    
    $modalTitle = "Nuevo Paciente"; 
    include 'components/modal_base.php'; 
?>

<script src="../static/js/utils/reportes.js"></script>
<script src="../static/js/utils/paginadorTabla.js"></script>
<script src="../static/js/PacientesControlador.js"></script>

</body>
</html>