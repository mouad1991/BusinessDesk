{{-- PDF Footer — company legal info + optional rainbow stripe (colorful theme) --}}
<div class="footer">
    {{ $document->company->name }} s.a.r.l
    @if($document->company->capital) · Capital : {{ number_format($document->company->capital, 2, ',', ' ') }} MAD @endif
    @if($document->company->rc) · RC : {{ $document->company->rc }} @endif
    @if($document->company->ice) · ICE : {{ $document->company->ice }} @endif
    @if($document->company->if_number) · IF : {{ $document->company->if_number }} @endif
    @if($document->company->tp) · TP : {{ $document->company->tp }} @endif
</div>

@if(($pdfTheme ?? 'modern') === 'colorful')
<table class="rainbow-stripe" cellspacing="0" cellpadding="0">
    <tr>
        <td style="background:#ef4444"></td>
        <td style="background:#f97316"></td>
        <td style="background:#eab308"></td>
        <td style="background:#22c55e"></td>
        <td style="background:#3b82f6"></td>
        <td style="background:#a855f7"></td>
    </tr>
</table>
@endif
