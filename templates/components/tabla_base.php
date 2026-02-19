<?php
/**
 * COMPONENTE DE TABLA UNIVERSAL
 */
$containerID = isset($containerID) ? $containerID : 'tableContainer';
?>

<div id="<?php echo $containerID; ?>" class="bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col flex-1 min-h-[400px] overflow-hidden">
        
    <div class="flex-1 overflow-x-auto overflow-y-auto bg-white flex flex-col">
        <table class="w-full text-left border-collapse flex-1">
            <thead class="sticky top-0 bg-white/95 backdrop-blur-sm z-10 shadow-sm">
                <tr class="border-b border-slate-200 text-xs uppercase text-slate-500 font-bold tracking-wider">
                    <?php foreach($tableColumns as $col): ?>
                        <th class="px-6 py-5 whitespace-nowrap"><?php echo $col; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            
            <tbody id="<?php echo $tableID; ?>" class="divide-y divide-slate-100 text-sm text-slate-600">
                </tbody>
        </table>
    </div>
    
    <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between bg-gray-50/50 flex-shrink-0">
        <span class="text-slate-500 text-sm font-medium pagination-info">Cargando...</span>
        
        <div class="flex items-center gap-3">
            <button class="btn-prev flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-blue-800 hover:bg-blue-50 hover:border-blue-200 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group shadow-sm">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Anterior
            </button>
            
            <button class="btn-next flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-blue-800 hover:bg-blue-50 hover:border-blue-200 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group shadow-sm">
                Siguiente
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>
</div>