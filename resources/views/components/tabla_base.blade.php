<?php
/**
 * COMPONENTE DE TABLA UNIVERSAL
 * — Desktop / Tablet / Laptop : tabla clásica (sin cambios)
 * — Móvil (< md / 768 px)      : cards con accordion (progressive disclosure)
 *
 * Variables esperadas:
 *   $containerID  (string)  — ID del wrapper principal
 *   $tableID      (string)  — ID del <tbody>
 *   $tableColumns (array)   — Nombres de columnas en orden
 */
$containerID = isset($containerID) ? $containerID : 'tableContainer';
?>

{{-- ─────────────────────────────────────────────────────────────
     ESTILOS DEL COMPONENTE  (sólo mobile cards)
───────────────────────────────────────────────────────────────── --}}
<style>
/* ── Accordion body ── */
.tbl-card-body {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows .22s ease;
}
.tbl-card-body > .tbl-card-inner {
    overflow: hidden;
}
.tbl-card-body.tbl-card-open {
    grid-template-rows: 1fr;
}

/* ── Chevron rotation ── */
.tbl-chevron {
    transition: transform .22s ease;
    flex-shrink: 0;
}
.tbl-card-open-chevron {
    transform: rotate(180deg);
}

/* ── Card entrada ── */
@keyframes tblFadeUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.tbl-mobile-card {
    animation: tblFadeUp .2s ease both;
}
</style>

{{-- ─────────────────────────────────────────────────────────────
     CONTENEDOR PRINCIPAL
───────────────────────────────────────────────────────────────── --}}
<div id="<?php echo $containerID; ?>" class="bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-1 md:min-h-[400px] md:overflow-hidden">

    {{-- ════════════════════════════════════════════
         VISTA ESCRITORIO / TABLET / LAPTOP  (md+)
    ═══════════════════════════════════════════════ --}}
    <div class="hidden md:flex flex-1 overflow-x-auto overflow-y-auto bg-white flex-col">
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

    {{-- ════════════════════════════════════════════
         VISTA MÓVIL  (< md / 768 px)
         Cards con accordion – progressive disclosure
    ═══════════════════════════════════════════════ --}}
    <div
        id="<?php echo $tableID; ?>_mobile"
        class="flex md:hidden flex-col overflow-y-auto"
    >
        {{-- Estado vacío --}}
        <div
            id="<?php echo $tableID; ?>_mobile_empty"
            class="hidden flex-1 flex flex-col items-center justify-center gap-3 py-20 text-slate-400"
        >
            <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 17v-2m3 2v-4m3 4v-6M5 21h14a2 2 0 002-2V7a2 2 0 00-.586-1.414l-4-4A2 2 0 0014 1H5a2 2 0 00-2 2v16a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-medium">Sin registros disponibles</p>
        </div>

        {{-- Estado cargando --}}
        <div
            id="<?php echo $tableID; ?>_mobile_loading"
            class="flex flex-1 items-center justify-center py-20 text-slate-400"
        >
            <p class="text-sm">Cargando...</p>
        </div>

        {{-- Lista de cards (se llena por JS) --}}
        <div
            id="<?php echo $tableID; ?>_mobile_list"
            class="flex flex-col gap-3 p-4 pb-2"
        ></div>
    </div>

    {{-- ════════════════════════════════════════════
         PAGINACIÓN  (compartida, sin cambios)
    ═══════════════════════════════════════════════ --}}
    <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between bg-gray-50/50 flex-shrink-0">
        <span class="text-slate-500 text-sm font-medium pagination-info">Cargando...</span>

        <div class="flex items-center gap-3">
            <button class="btn-prev flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-blue-800 hover:bg-blue-50 hover:border-blue-200 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group shadow-sm">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Anterior
            </button>

            <button class="btn-next flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-blue-800 hover:bg-blue-50 hover:border-blue-200 rounded-lg text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group shadow-sm">
                Siguiente
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────────
     LÓGICA MOBILE CARDS
     • Observa el <tbody> con MutationObserver
     • Cuando cambian las filas, reconstruye las cards
     • Scoped por tableID → funciona para TODAS las tablas del proyecto
───────────────────────────────────────────────────────────────── --}}
<script>
(function () {
    /* ── Referencias ─────────────────────────────────────────── */
    const COLUMNS     = <?php echo json_encode(array_values($tableColumns)); ?>;
    const tbodyId     = '<?php echo $tableID; ?>';
    const mobileId    = tbodyId + '_mobile';
    const listId      = tbodyId + '_mobile_list';
    const emptyId     = tbodyId + '_mobile_empty';
    const loadingId   = tbodyId + '_mobile_loading';

    /* ── Utilidades ──────────────────────────────────────────── */
    function initial(text) {
        return (text || '?').trim().charAt(0).toUpperCase();
    }

    function isActionCell(td) {
        return !!(td && td.querySelector('button, a[href], [onclick], [data-action]'));
    }

    /* ── Construir una card a partir de un <tr> ──────────────── */
    function buildCard(row, index) {
        const cells = row.querySelectorAll('td');
        if (!cells.length) return null;

        /* ── Valores de cabecera (siempre visibles) */
        const titleText    = (cells[0]?.textContent || '').trim() || '—';
        const subtitleText = COLUMNS.length > 1
            ? (cells[1]?.textContent || '').trim()
            : '';

        /* ── Card wrapper */
        const card = document.createElement('div');
        card.className = 'tbl-mobile-card rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden';
        card.style.animationDelay = (index * 35) + 'ms';

        /* ── ID único para aria */
        const bodyId = tbodyId + '_cb_' + index;

        /* ── Botón cabecera */
        const header = document.createElement('button');
        header.type = 'button';
        header.setAttribute('aria-expanded', 'false');
        header.setAttribute('aria-controls', bodyId);
        header.className = 'w-full flex items-center gap-3 px-4 py-3.5 text-left hover:bg-slate-50/80 active:bg-slate-100 transition-colors';

        /* Avatar / inicial */
        header.innerHTML = `
            <span class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center flex-shrink-0 select-none">
                <span class="text-[#1a6fc4] font-bold text-sm">${initial(titleText)}</span>
            </span>
            <span class="flex-1 min-w-0">
                <span class="block font-semibold text-slate-800 text-sm truncate">${titleText}</span>
                ${subtitleText
                    ? `<span class="block text-xs text-slate-400 mt-0.5 truncate">${subtitleText}</span>`
                    : ''}
            </span>
            <svg class="tbl-chevron w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        `;

        /* ── Cuerpo colapsable (muestra todas las columnas) */
        const body = document.createElement('div');
        body.id        = bodyId;
        body.className = 'tbl-card-body border-t border-slate-100';

        const inner = document.createElement('div');
        inner.className = 'tbl-card-inner';

        /* Filas de detalle: una por columna */
        const detailWrap = document.createElement('div');
        detailWrap.className = 'px-4 pb-3 pt-2 space-y-0';

        COLUMNS.forEach(function (colName, i) {
            const td = cells[i];
            if (!td) return;

            /* Si es celda de acciones (botones) la ponemos en fila especial */
            const hasAction = isActionCell(td);
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2 py-2 '
                + (i < COLUMNS.length - 1 ? 'border-b border-slate-50' : '');

            const label = document.createElement('span');
            label.className = 'text-[11px] font-semibold text-slate-400 uppercase tracking-wide flex-shrink-0';
            label.textContent = colName;

            const value = document.createElement('div');
            value.className = hasAction
                ? 'flex items-center gap-1.5 flex-wrap justify-end'
                : 'text-xs text-slate-700 text-right';

            if (hasAction) {
                /* Clonar nodo para no desconectarlo del tbody real */
                const clone = td.cloneNode(true);
                clone.querySelectorAll('*').forEach(function(el) {
                    el.className = el.className; /* preservar clases */
                });
                value.innerHTML = td.innerHTML;
            } else {
                value.textContent = td.textContent.trim() || '—';
            }

            row.appendChild(label);
            row.appendChild(value);
            detailWrap.appendChild(row);
        });

        inner.appendChild(detailWrap);
        body.appendChild(inner);

        /* ── Toggle accordion ──────────────────────────────── */
        header.addEventListener('click', function () {
            const isOpen = body.classList.contains('tbl-card-open');
            const chevron = header.querySelector('.tbl-chevron');

            body.classList.toggle('tbl-card-open', !isOpen);
            chevron.classList.toggle('tbl-card-open-chevron', !isOpen);
            header.setAttribute('aria-expanded', String(!isOpen));
        });

        card.appendChild(header);
        card.appendChild(body);
        return card;
    }

    /* ── Reconstruir toda la lista de cards ─────────────────── */
    function rebuildCards() {
        const tbody   = document.getElementById(tbodyId);
        const list    = document.getElementById(listId);
        const empty   = document.getElementById(emptyId);
        const loading = document.getElementById(loadingId);

        if (!tbody || !list) return;

        /* Limpiar lista anterior */
        list.innerHTML = '';

        const allRows = Array.from(tbody.querySelectorAll('tr'));

        /*
         * Filtrar filas vacías que el paginador inserta como placeholder
         * para mantener altura fija del contenedor.
         * Una fila es "real" si al menos su primera celda tiene texto visible.
         */
        const rows = allRows.filter(function (row) {
            const firstCell = row.querySelector('td');
            return firstCell && firstCell.textContent.trim() !== '';
        });

        /* Ocultar loading una vez que hay filas o vacío definitivo */
        if (loading) loading.classList.add('hidden');

        if (!rows.length) {
            if (empty) empty.classList.remove('hidden');
            return;
        }

        if (empty) empty.classList.add('hidden');

        rows.forEach(function (row, i) {
            const card = buildCard(row, i);
            if (card) list.appendChild(card);
        });
    }

    /* ── Inicializar con MutationObserver  */
    function init() {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) {
            /* tbody aún no existe, reintentar */
            setTimeout(init, 80);
            return;
        }

        /* Render inicial (por si ya hay filas al montar) */
        rebuildCards();

        /* Observar cambios */
        var obs = new MutationObserver(function () {
            rebuildCards();
        });
        obs.observe(tbody, { childList: true });
    }

    /* ── Arrancar cuando el DOM esté listo  */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>