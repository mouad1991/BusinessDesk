@props(['items' => [], 'type' => 'standard', 'autocompleteUrl' => null])

<div class="items-section">
    <div class="items-header">
        <h3>Lignes de prestations</h3>
        <div style="display:flex;gap:8px;align-items:center">
            @if($type !== 'delivery')
            <button type="button" class="btn-secondary btn-sm" onclick="addCategoryRow()" title="Ajouter un titre de section">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M4 6h16M4 12h8M4 18h6"/></svg>
                Catégorie
            </button>
            @endif
            <button type="button" class="btn-secondary btn-sm" onclick="addItemRow()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Ajouter une ligne
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table items-table" id="items-table">
            <thead>
                <tr>
                    <th style="width:50px">Réf.</th>
                    <th>Désignation / Description</th>
                    <th style="width:80px">Unité</th>
                    @if($type === 'delivery')
                        <th style="width:110px">Qté commandée</th>
                        <th style="width:110px">Qté expédiée</th>
                        <th style="width:110px">Qté en attente</th>
                    @else
                        <th style="width:90px">Qté</th>
                        <th style="width:130px">P.U HT</th>
                        <th style="width:130px">P.T HT</th>
                    @endif
                    <th style="width:40px"></th>
                </tr>
            </thead>
            <tbody id="items-body">
                @forelse($items as $i => $item)
                    @include('components.item-row', ['item' => $item, 'index' => $i, 'type' => $type])
                @empty
                    @include('components.item-row', ['item' => null, 'index' => 0, 'type' => $type])
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($type !== 'delivery')
<div id="items-totals" class="totals-section">
    <div class="totals-table">
        <div class="totals-row">
            <span>Total HT :</span>
            <strong id="display-total-ht">0,00 DH</strong>
        </div>
        <div class="totals-row discount-row" id="discount-display-row" style="display:none">
            <span>Remise (<span id="display-discount-pct">0</span>%) :</span>
            <strong id="display-discount-amount">- 0,00 DH</strong>
        </div>
        <div class="totals-row" id="total-ht-after-row" style="display:none">
            <span>Total HT après remise :</span>
            <strong id="display-total-ht-after">0,00 DH</strong>
        </div>
        <div class="totals-row">
            <span>TVA (<span id="display-tva-rate">20</span>%) :</span>
            <strong id="display-tva-amount">0,00 DH</strong>
        </div>
        <div class="totals-row totals-ttc">
            <span>TOTAL TTC :</span>
            <strong id="display-total-ttc">0,00 DH</strong>
        </div>
    </div>
</div>
@endif

@if($autocompleteUrl)
<div id="autocomplete-dropdown" class="autocomplete-dropdown" style="display:none"></div>
@endif

@push('scripts')
<script>
const AUTOCOMPLETE_URL = @json($autocompleteUrl);
const ITEM_TYPE = @json($type);
let itemIndex = {{ count($items) > 0 ? count($items) : 1 }};
let activeAutocompleteInput = null;

// ── WYSIWYG helpers ───────────────────────────────────────────────────────
function syncDesc(editor) {
    const hidden = editor.closest('td').querySelector('.desc-hidden');
    if (hidden) hidden.value = editor.innerHTML;
}

function selectionInList(editor) {
    let node = window.getSelection().anchorNode;
    while (node && node !== editor) {
        if (node.nodeName === 'LI' || node.nodeName === 'UL' || node.nodeName === 'OL') return true;
        node = node.parentNode;
    }
    return false;
}

function handleDescKey(e, editor) {
    // Enter → saut de ligne (<br>), sauf dans une liste (nouvel élément natif)
    if (e.key === 'Enter' && !e.shiftKey && !selectionInList(editor)) {
        e.preventDefault();
        document.execCommand('insertLineBreak');
        syncDesc(editor);
        return;
    }
    // Raccourcis clavier
    if (e.ctrlKey || e.metaKey) {
        const k = e.key.toLowerCase();
        if (k === 'b') { e.preventDefault(); document.execCommand('bold', false, null); syncDesc(editor); }
        else if (k === 'i') { e.preventDefault(); document.execCommand('italic', false, null); syncDesc(editor); }
        else if (k === 'u') { e.preventDefault(); document.execCommand('underline', false, null); syncDesc(editor); }
    }
}

function execDescFormat(cmd, btn) {
    const editor = btn.closest('td').querySelector('.desc-editor');
    if (!editor) return;
    editor.focus();
    document.execCommand(cmd, false, null);
    syncDesc(editor);
}

function setupEditorEvents(editor) {
    editor.addEventListener('keydown', function(e) { handleDescKey(e, this); });
    editor.addEventListener('input', function() { syncDesc(this); });
    if (AUTOCOMPLETE_URL) attachAutocomplete(editor);
}

// ── Row management ────────────────────────────────────────────────────────
function addItemRow() {
    const tbody = document.getElementById('items-body');
    const row = createRow(itemIndex);
    tbody.appendChild(row);
    itemIndex++;
    renumberRows();
    recalculate();
    row.querySelector('.desc-editor')?.focus();
}

function addCategoryRow() {
    const tbody = document.getElementById('items-body');
    const tr = document.createElement('tr');
    tr.dataset.isCategory = '1';
    tr.innerHTML = `
        <td colspan="7" class="category-cell">
            <div class="category-row-wrap">
                <input type="hidden" name="items[${itemIndex}][is_category]" value="1">
                <input type="text" name="items[${itemIndex}][description]"
                       class="input-full category-title-input"
                       placeholder="Titre de la catégorie (ex : Fournitures, Main d'œuvre…)">
                <button type="button" class="btn-remove-row" onclick="removeRow(this)">×</button>
            </div>
        </td>
    `;
    tbody.appendChild(tr);
    itemIndex++;
    tr.querySelector('.category-title-input')?.focus();
}

function createRow(idx) {
    const tr = document.createElement('tr');
    const refNum = String(nonCategoryCount() + 1).padStart(2, '0');

    const descCell = `
        <td class="desc-cell">
            <div class="desc-toolbar-mini">
                <button type="button" class="desc-btn" onmousedown="event.preventDefault()" onclick="execDescFormat('bold',this)" title="Gras (Ctrl+B)"><strong>G</strong></button>
                <button type="button" class="desc-btn" onmousedown="event.preventDefault()" onclick="execDescFormat('italic',this)" title="Italique (Ctrl+I)"><em>I</em></button>
                <button type="button" class="desc-btn" onmousedown="event.preventDefault()" onclick="execDescFormat('underline',this)" title="Souligné (Ctrl+U)"><span style="text-decoration:underline">S</span></button>
                <span class="desc-sep"></span>
                <button type="button" class="desc-btn" onmousedown="event.preventDefault()" onclick="execDescFormat('insertUnorderedList',this)" title="Liste à puces">&bull;</button>
                <button type="button" class="desc-btn" onmousedown="event.preventDefault()" onclick="execDescFormat('insertOrderedList',this)" title="Liste numérotée">1.</button>
                <span class="desc-sep"></span>
                <button type="button" class="desc-btn" onmousedown="event.preventDefault()" onclick="execDescFormat('justifyLeft',this)" title="Aligner à gauche">&#8676;</button>
                <button type="button" class="desc-btn" onmousedown="event.preventDefault()" onclick="execDescFormat('justifyCenter',this)" title="Centrer">&#8801;</button>
                <button type="button" class="desc-btn" onmousedown="event.preventDefault()" onclick="execDescFormat('justifyRight',this)" title="Aligner à droite">&#8677;</button>
            </div>
            <div contenteditable="true" class="desc-editor" data-placeholder="Description de la prestation"></div>
            <input type="hidden" name="items[${idx}][description]" class="desc-hidden">
        </td>
    `;

    if (ITEM_TYPE === 'delivery') {
        tr.innerHTML = `
            <td><span class="ref-num">${refNum}</span></td>
            ${descCell}
            <td><input type="text" name="items[${idx}][unit]" value="Unité" class="input-center" style="width:70px"></td>
            <td><input type="number" name="items[${idx}][qty_ordered]" value="1" min="0" step="1" class="input-number qty-ordered" onchange="calcPending(this)"></td>
            <td><input type="number" name="items[${idx}][qty_shipped]" value="1" min="0" step="1" class="input-number qty-shipped" onchange="calcPending(this)"></td>
            <td><input type="number" name="items[${idx}][qty_pending]" value="0" min="0" step="1" class="input-number" readonly></td>
            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">×</button></td>
        `;
    } else {
        tr.innerHTML = `
            <td><span class="ref-num">${refNum}</span></td>
            ${descCell}
            <td><input type="text" name="items[${idx}][unit]" value="F" class="input-center" style="width:60px"></td>
            <td><input type="number" name="items[${idx}][quantity]" value="1" min="0" step="1" class="input-number qty-input" onchange="calcRow(this); recalculate()"></td>
            <td><input type="number" name="items[${idx}][unit_price_ht]" value="" min="0" step="0.01" class="input-number price-input" placeholder="0.00" onchange="calcRow(this); recalculate()"></td>
            <td><input type="number" name="items[${idx}][total_price_ht]" value="" min="0" step="0.01" class="input-number total-input" readonly></td>
            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">×</button></td>
        `;
    }

    const editor = tr.querySelector('.desc-editor');
    if (editor) setupEditorEvents(editor);

    return tr;
}

function nonCategoryCount() {
    return document.querySelectorAll('#items-body tr:not([data-is-category])').length;
}

function removeRow(btn) {
    btn.closest('tr').remove();
    renumberRows();
    recalculate();
}

function renumberRows() {
    let num = 1;
    document.querySelectorAll('#items-body tr').forEach(row => {
        const ref = row.querySelector('.ref-num');
        if (ref) { ref.textContent = String(num).padStart(2, '0'); num++; }
    });
}

// ── Calculations ──────────────────────────────────────────────────────────
function calcRow(input) {
    const row = input.closest('tr');
    const qty   = parseFloat(row.querySelector('.qty-input')?.value)   || 0;
    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
    const totalInput = row.querySelector('.total-input');
    if (totalInput) totalInput.value = (qty * price).toFixed(2);
}

function calcPending(input) {
    const row = input.closest('tr');
    const ordered = parseFloat(row.querySelector('.qty-ordered')?.value) || 0;
    const shipped = parseFloat(row.querySelector('.qty-shipped')?.value) || 0;
    const pendingInput = row.querySelector('input[name$="[qty_pending]"]');
    if (pendingInput) pendingInput.value = Math.max(0, Math.round(ordered - shipped));
}

function recalculate() {
    if (ITEM_TYPE === 'delivery') return;

    let totalHtBefore = 0;
    document.querySelectorAll('#items-body tr:not([data-is-category]) .total-input').forEach(i => {
        totalHtBefore += parseFloat(i.value) || 0;
    });

    const tvaRate    = parseFloat(document.getElementById('tva_rate')?.value)       || 20;
    const discountPct = parseFloat(document.getElementById('discount_percent')?.value) || 0;

    const discountAmount = totalHtBefore * discountPct / 100;
    const totalHtAfter   = totalHtBefore - discountAmount;
    const tvaAmount      = totalHtAfter * tvaRate / 100;
    const totalTtc       = totalHtAfter + tvaAmount;

    const fmt = n => n.toLocaleString('fr-MA', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' DH';

    document.getElementById('display-total-ht').textContent     = fmt(totalHtBefore);
    document.getElementById('display-tva-rate').textContent     = tvaRate;
    document.getElementById('display-tva-amount').textContent   = fmt(tvaAmount);
    document.getElementById('display-total-ttc').textContent    = fmt(totalTtc);

    const discountRow    = document.getElementById('discount-display-row');
    const totalHtAfterRow = document.getElementById('total-ht-after-row');
    if (discountPct > 0) {
        discountRow.style.display     = '';
        totalHtAfterRow.style.display = '';
        document.getElementById('display-discount-pct').textContent    = discountPct;
        document.getElementById('display-discount-amount').textContent = '- ' + fmt(discountAmount);
        document.getElementById('display-total-ht-after').textContent  = fmt(totalHtAfter);
    } else {
        discountRow.style.display     = 'none';
        totalHtAfterRow.style.display = 'none';
    }
}

// ── Autocomplete ──────────────────────────────────────────────────────────
function getEditorText(el) {
    return el.tagName === 'DIV' ? el.textContent.trim() : el.value;
}
function setEditorText(el, val) {
    if (el.tagName === 'DIV') { el.textContent = val; syncDesc(el); }
    else el.value = val;
}

function attachAutocomplete(input) {
    input.addEventListener('input', debounce(async function() {
        const term = getEditorText(this);
        if (term.length < 2 || !AUTOCOMPLETE_URL) return;
        const res   = await fetch(`${AUTOCOMPLETE_URL}?q=${encodeURIComponent(term)}`);
        const items = await res.json();
        const dropdown = document.getElementById('autocomplete-dropdown');
        if (!items.length) { dropdown.style.display = 'none'; return; }
        dropdown.innerHTML = items.map(d => `<div class="ac-item" data-val="${escapeHtml(d)}">${escapeHtml(d)}</div>`).join('');
        dropdown.style.display = 'block';
        activeAutocompleteInput = input;
        const rect = input.getBoundingClientRect();
        dropdown.style.left  = (rect.left + window.scrollX) + 'px';
        dropdown.style.top   = (rect.bottom + window.scrollY) + 'px';
        dropdown.style.width = rect.width + 'px';
    }, 200));
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('autocomplete-dropdown');
    if (!dropdown) return;
    if (e.target.classList.contains('ac-item')) {
        if (activeAutocompleteInput) setEditorText(activeAutocompleteInput, e.target.dataset.val);
        dropdown.style.display = 'none';
    } else if (!e.target.classList.contains('desc-editor') && !e.target.classList.contains('autocomplete-input')) {
        dropdown.style.display = 'none';
    }
});

function debounce(fn, delay) {
    let t;
    return function(...a) { clearTimeout(t); t = setTimeout(() => fn.apply(this, a), delay); };
}
function escapeHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Init ──────────────────────────────────────────────────────────────────
document.querySelectorAll('#items-body .desc-editor').forEach(setupEditorEvents);

document.querySelectorAll('#items-body .qty-input, #items-body .price-input').forEach(input => {
    input.addEventListener('change', function() { calcRow(this); recalculate(); });
});
document.querySelectorAll('#items-body .qty-ordered, #items-body .qty-shipped').forEach(input => {
    input.addEventListener('change', function() { calcPending(this); });
});

document.getElementById('tva_rate')?.addEventListener('input', recalculate);
document.getElementById('discount_percent')?.addEventListener('input', recalculate);

// Sync all WYSIWYG editors to hidden inputs before submit
document.getElementById('doc-form')?.addEventListener('submit', function() {
    document.querySelectorAll('#items-body .desc-editor').forEach(syncDesc);
});

recalculate();
</script>
@endpush
