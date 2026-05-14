@php
    $idx = $index ?? 0;
    $ref = str_pad(($idx + 1), 2, '0', STR_PAD_LEFT);
    $rowType = $type ?? 'standard';
@endphp
<tr>
    <td><span class="ref-num">{{ $ref }}</span></td>
    <td>
        <input type="text"
               name="items[{{ $idx }}][description]"
               value="{{ $item['description'] ?? ($item->description ?? '') }}"
               class="input-full autocomplete-input"
               placeholder="Description de la prestation">
    </td>
    <td>
        @if($rowType === 'delivery')
            <input type="text" name="items[{{ $idx }}][unit]" value="{{ $item['unit'] ?? ($item->unit ?? 'Unité') }}" class="input-center" style="width:70px">
        @else
            <input type="text" name="items[{{ $idx }}][unit]" value="{{ $item['unit'] ?? ($item->unit ?? 'F') }}" class="input-center" style="width:60px">
        @endif
    </td>

    @if($rowType === 'delivery')
        <td><input type="number" name="items[{{ $idx }}][qty_ordered]" value="{{ $item['qty_ordered'] ?? ($item->qty_ordered ?? 1) }}" min="0" step="1" class="input-number qty-ordered" onchange="calcPending(this)"></td>
        <td><input type="number" name="items[{{ $idx }}][qty_shipped]" value="{{ $item['qty_shipped'] ?? ($item->qty_shipped ?? 0) }}" min="0" step="1" class="input-number qty-shipped" onchange="calcPending(this)"></td>
        <td><input type="number" name="items[{{ $idx }}][qty_pending]" value="{{ $item['qty_pending'] ?? ($item->qty_pending ?? 0) }}" min="0" step="1" class="input-number" readonly></td>
    @else
        <td><input type="number" name="items[{{ $idx }}][quantity]" value="{{ $item['quantity'] ?? ($item->quantity ?? 1) }}" min="0" step="1" class="input-number qty-input" onchange="calcRow(this); recalculate()"></td>
        <td><input type="number" name="items[{{ $idx }}][unit_price_ht]" value="{{ $item['unit_price_ht'] ?? ($item->unit_price_ht ?? '') }}" min="0" step="0.01" class="input-number price-input" placeholder="0.00" onchange="calcRow(this); recalculate()"></td>
        <td><input type="number" name="items[{{ $idx }}][total_price_ht]" value="{{ $item['total_price_ht'] ?? ($item->total_price_ht ?? '') }}" min="0" step="0.01" class="input-number total-input" readonly></td>
    @endif

    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">×</button></td>
</tr>
