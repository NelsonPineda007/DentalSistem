<div id="<?php echo $modalID; ?>" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0 modal-backdrop"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 modal-panel">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold leading-6 text-slate-800" id="modalTitle">
                        <?php echo $modalTitle; // Título dinámico ?>
                    </h3>
                    <button type="button" onclick="closeModal('<?php echo $modalID; ?>')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-4 py-6 sm:p-6 bg-slate-50/50 max-h-[70vh] overflow-y-auto">
                     <?php 
                        // Aquí se imprimirá el contenido HTML específico de cada página
                        if(isset($modalContent)) echo $modalContent; 
                     ?>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 gap-2">
                    <button type="button" id="btnGuardar" onclick="guardarDatos()" class="inline-flex w-full justify-center rounded-xl bg-blue-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-900 sm:ml-3 sm:w-auto transition-all shadow-blue-900/20">
                        Guardar
                    </button>
                    <button type="button" onclick="closeModal('<?php echo $modalID; ?>')" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all">
                        Cancelar
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>