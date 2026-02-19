<?php
/**
 * COMPONENTE DE TABLA UNIVERSAL
 * * Variables esperadas:
 * $tableColumns: Array con los nombres de las columnas (ej: ['Nombre', 'Edad', 'Acciones'])
 * $tableID: ID único para el tbody (ej: 'patientsTableBody')
 * $containerID: (Opcional) ID del contenedor para el cálculo responsive. Default: 'tableContainer'
 */
$containerID = isset($containerID) ? $containerID : 'tableContainer';
?>

<div id="<?php echo $containerID; ?>" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col flex-1 h-full">
    
    <div class="overflow-x-auto flex-1 relative">
        <table class="w-full text-left border-collapse absolute inset-0">
            <thead class="sticky top-0 bg-white z-10 shadow-sm">
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs uppercase text-slate-500 font-bold tracking-wider">
                    <?php foreach($tableColumns as $col): ?>
                        <th class="px-6 py-5 whitespace-nowrap"><?php echo $col; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            
            <tbody id="<?php echo $tableID; ?>" class="divide-y divide-slate-100 text-sm text-slate-600"></tbody>
        </table>
    </div>
    
    <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between bg-white flex-shrink-0">
        <span class="text-slate-500 text-sm font-medium pagination-info">Cargando...</span>
        
        <div class="flex items-center gap-3">
            <button class="btn-prev flex items-center gap-2 px-4 py-2 text-slate-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Anterior
            </button>
            
            <div class="h-4 w-px bg-slate-200"></div>
            
            <button class="btn-next flex items-center gap-2 px-4 py-2 text-slate-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
                Siguiente
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>
</div>