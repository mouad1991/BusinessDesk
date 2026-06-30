@php
    $idx      = $index ?? 0;
    $ref      = str_pad(($idx + 1), 2, '0', STR_PAD_LEFT);
    $rowType  = $type ?? 'standard';
    $isCategory = $item
        ? (is_array($item) ? !empty($item['is_category']) : !empty($item->is_category))
        : false;
    $descHtml = $item
        ? (is_array($item) ? ($item['description'] ?? '') : ($item->description ?? ''))
        : '';
@endphp

@if($isCategory)
<tr data-is-category="1">
    <td colspan="7" class="category-cell">
        <div class="category-row-wrap">
            <input type="hidden" name="items[{{ $idx }}][is_category]" value="1">
            <input type="text"
                   name="items[{{ $idx }}][description]"
                   value="{{ $descHtml }}"
                   class="input-full category-title-input"
                   placeholder="Titre de la catégorie…">
            <button type="button" class="btn-remove-row" onclick="removeRow(this)">×</button>
        </div>
    </td>
</tr>
@else
<tr>
    <td><span class="ref-num">{{ $ref }}</span></td>
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
        <div contenteditable="true"
             class="desc-editor"
             data-placeholder="Description de la prestation">{!! $descHtml !!}</div>
        <input type="hidden"
               name="items[{{ $idx }}][description]"
               class="desc-hidden"
               value="{{ $descHtml }}">
    </td>
    <td>
        @if($rowType === 'delivery')
            <input type="text" name="items[{{ $idx }}][unit]"
                   value="{{ $item ? (is_array($item) ? ($item['unit'] ?? 'Unité') : ($item->unit ?? 'Unité')) : 'Unité' }}"
                   class="input-center" style="width:70px">
        @else
            <input type="text" name="items[{{ $idx }}][unit]"
                   value="{{ $item ? (is_array($item) ? ($item['unit'] ?? 'F') : ($item->unit ?? 'F')) : 'F' }}"
                   class="input-center" style="width:60px">
        @endif
    </td>

    @if($rowType === 'delivery')
        <td><input type="number" name="items[{{ $idx }}][qty_ordered]"
                   value="{{ $item ? (is_array($item) ? ($item['qty_ordered'] ?? 1) : ($item->qty_ordered ?? 1)) : 1 }}"
                   min="0" step="1" class="input-number qty-ordered" onchange="calcPending(this)"></td>
        <td><input type="number" name="items[{{ $idx }}][qty_shipped]"
                   value="{{ $item ? (is_array($item) ? ($item['qty_shipped'] ?? 0) : ($item->qty_shipped ?? 0)) : 0 }}"
                   min="0" step="1" class="input-number qty-shipped" onchange="calcPending(this)"></td>
        <td><input type="number" name="items[{{ $idx }}][qty_pending]"
                   value="{{ $item ? (is_array($item) ? ($item['qty_pending'] ?? 0) : ($item->qty_pending ?? 0)) : 0 }}"
                   min="0" step="1" class="input-number" readonly></td>
    @else
        <td><input type="number" name="items[{{ $idx }}][quantity]"
                   value="{{ $item ? (is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1)) : 1 }}"
                   min="0" step="1" class="input-number qty-input" onchange="calcRow(this); recalculate()"></td>
        <td><input type="number" name="items[{{ $idx }}][unit_price_ht]"
                   value="{{ $item ? (is_array($item) ? ($item['unit_price_ht'] ?? '') : ($item->unit_price_ht ?? '')) : '' }}"
                   min="0" step="0.01" class="input-number price-input" placeholder="0.00" onchange="calcRow(this); recalculate()"></td>
        <td><input type="number" name="items[{{ $idx }}][total_price_ht]"
                   value="{{ $item ? (is_array($item) ? ($item['total_price_ht'] ?? '') : ($item->total_price_ht ?? '')) : '' }}"
                   min="0" step="0.01" class="input-number total-input" readonly></td>
    @endif

    <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">×</button></td>
</tr>
@endif
