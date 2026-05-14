@props(['items' => [], 'type' => 'standard', 'autocompleteUrl' => null])

<div class="items-section">
    <div class="items-header">
        <h3>Lignes de prestations</h3>
        <button type="button" class="btn-secondary btn-sm" onclick="addItemRow()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter une ligne
        </button>
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

function addItemRow() {
    const tbody = document.getElementById('items-body');
    const row = createRow(itemIndex);
    tbody.appendChild(row);
    itemIndex++;
    recalculate();
}

function createRow(idx) {
    const tr = document.createElement('tr');
    const refNum = String(tbody_count() + 1).padStart(2, '0');

    if (ITEM_TYPE === 'delivery') {
        tr.innerHTML = `
            <td><span class="ref-num">${refNum}</span></td>
            <td><input type="text" name="items[${idx}][description]" class="input-full autocomplete-input" placeholder="Description de la prestation" required></td>
            <td><input type="text" name="items[${idx}][unit]" value="Unité" class="input-center" style="width:70px"></td>
            <td><input type="number" name="items[${idx}][qty_ordered]" value="1" min="0" step="1" class="input-number qty-ordered" onchange="calcPending(this)"></td>
            <td><input type="number" name="items[${idx}][qty_shipped]" value="1" min="0" step="1" class="input-number qty-shipped" onchange="calcPending(this)"></td>
            <td><input type="number" name="items[${idx}][qty_pending]" value="0" min="0" step="1" class="input-number" readonly></td>
            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">×</button></td>
        `;
    } else {
        tr.innerHTML = `
            <td><span class="ref-num">${refNum}</span></td>
            <td><input type="text" name="items[${idx}][description]" class="input-full autocomplete-input" placeholder="Description de la prestation" required></td>
            <td><input type="text" name="items[${idx}][unit]" value="F" class="input-center" style="width:60px"></td>
            <td><input type="number" name="items[${idx}][quantity]" value="1" min="0" step="1" class="input-number qty-input" onchange="calcRow(this); recalculate()"></td>
            <td><input type="number" name="items[${idx}][unit_price_ht]" value="" min="0" step="0.01" class="input-number price-input" onchange="calcRow(this); recalculate()"></td>
            <td><input type="number" name="items[${idx}][total_price_ht]" value="" min="0" step="0.01" class="input-number total-input" readonly></td>
            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">×</button></td>
        `;
    }

    // Attach autocomplete
    const acInput = tr.querySelector('.autocomplete-input');
    if (acInput && AUTOCOMPLETE_URL) {
        attachAutocomplete(acInput);
    }

    return tr;
}

function tbody_count() {
    return document.getElementById('items-body').rows.length;
}

function removeRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    renumberRows();
    recalculate();
}

function renumberRows() {
    const rows = document.querySelectorAll('#items-body tr');
    rows.forEach((row, i) => {
        const ref = row.querySelector('.ref-num');
        if (ref) ref.textContent = String(i + 1).padStart(2, '0');
    });
}

function calcRow(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
    const total = qty * price;
    const totalInput = row.querySelector('.total-input');
    if (totalInput) totalInput.value = total.toFixed(2);
}

function calcPending(input) {
    const row = input.closest('tr');
    const ordered = parseFloat(row.querySelector('.qty-ordered')?.value) || 0;
    const shipped = parseFloat(row.querySelector('.qty-shipped')?.value) || 0;
    const pending = Math.max(0, ordered - shipped);
    const pendingInput = row.querySelector('input[name$="[qty_pending]"]');
    if (pendingInput) pendingInput.value = Math.round(pending);
}

function recalculate() {
    if (ITEM_TYPE === 'delivery') return;

    let totalHtBefore = 0;
    document.querySelectorAll('#items-body .total-input').forEach(input => {
        totalHtBefore += parseFloat(input.value) || 0;
    });

    const tvaRateInput = document.getElementById('tva_rate');
    const discountInput = document.getElementById('discount_percent');
    const tvaRate = tvaRateInput ? parseFloat(tvaRateInput.value) || 0 : 20;
    const discountPct = discountInput ? parseFloat(discountInput.value) || 0 : 0;

    const discountAmount = totalHtBefore * discountPct / 100;
    const totalHtAfter = totalHtBefore - discountAmount;
    const tvaAmount = totalHtAfter * tvaRate / 100;
    const totalTtc = totalHtAfter + tvaAmount;

    const fmt = (n) => n.toLocaleString('fr-MA', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' DH';

    document.getElementById('display-total-ht').textContent = fmt(totalHtBefore);
    document.getElementById('display-tva-rate').textContent = tvaRate;
    document.getElementById('display-tva-amount').textContent = fmt(tvaAmount);
    document.getElementById('display-total-ttc').textContent = fmt(totalTtc);

    const discountRow = document.getElementById('discount-display-row');
    const totalHtAfterRow = document.getElementById('total-ht-after-row');
    if (discountPct > 0) {
        discountRow.style.display = '';
        totalHtAfterRow.style.display = '';
        document.getElementById('display-discount-pct').textContent = discountPct;
        document.getElementById('display-discount-amount').textContent = '- ' + fmt(discountAmount);
        document.getElementById('display-total-ht-after').textContent = fmt(totalHtAfter);
    } else {
        discountRow.style.display = 'none';
        totalHtAfterRow.style.display = 'none';
    }
}

// Autocomplete
function attachAutocomplete(input) {
    input.addEventListener('input', debounce(async function() {
        const term = this.value.trim();
        if (term.length < 2 || !AUTOCOMPLETE_URL) return;

        const res = await fetch(`${AUTOCOMPLETE_URL}?q=${encodeURIComponent(term)}`);
        const items = await res.json();

        const dropdown = document.getElementById('autocomplete-dropdown');
        if (!items.length) { dropdown.style.display = 'none'; return; }

        dropdown.innerHTML = items.map(d => `<div class="ac-item" data-val="${escapeHtml(d)}">${escapeHtml(d)}</div>`).join('');
        dropdown.style.display = 'block';
        activeAutocompleteInput = input;

        const rect = input.getBoundingClientRect();
        dropdown.style.left = (rect.left + window.scrollX) + 'px';
        dropdown.style.top = (rect.bottom + window.scrollY) + 'px';
        dropdown.style.width = rect.width + 'px';
    }, 200));
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('autocomplete-dropdown');
    if (!dropdown) return;
    if (e.target.classList.contains('ac-item')) {
        if (activeAutocompleteInput) {
            activeAutocompleteInput.value = e.target.dataset.val;
        }
        dropdown.style.display = 'none';
    } else if (!e.target.classList.contains('autocomplete-input')) {
        dropdown.style.display = 'none';
    }
});

function debounce(fn, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

function escapeHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Init existing rows
document.querySelectorAll('#items-body .autocomplete-input').forEach(input => {
    if (AUTOCOMPLETE_URL) attachAutocomplete(input);
});
document.querySelectorAll('#items-body .qty-input, #items-body .price-input').forEach(input => {
    input.addEventListener('change', function() { calcRow(this); recalculate(); });
});
document.querySelectorAll('#items-body .qty-ordered, #items-body .qty-shipped').forEach(input => {
    input.addEventListener('change', function() { calcPending(this); });
});

// Recalc on TVA/discount change
document.getElementById('tva_rate')?.addEventListener('input', recalculate);
document.getElementById('discount_percent')?.addEventListener('input', recalculate);

recalculate();
</script>
@endpush
