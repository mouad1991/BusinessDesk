<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
@include('pdf.themes.styles', ['pdfTheme' => $pdfTheme ?? 'modern', 'docAccentColor' => $docAccentColor ?? '#1a73c8'])
</style>
</head>
<body>

<!-- FOOTER (fixed) -->
@include('pdf.partials.footer')

<!-- HEADER -->
@include('pdf.partials.header', ['docTitle' => 'DEVIS', 'docTitleColor' => '#ffffff'])

<!-- INFO SECTIONS -->
<div class="info-row">
    <div class="info-col">
        <div class="section-label">Client / Destinataire</div>
        <div class="info-line"><span class="info-label">Société : </span><span class="info-value">{{ $document->client->company_name }}</span></div>
        <div class="info-line"><span class="info-label">Adresse : </span>{{ $document->client->address ?? '' }}</div>
        <div class="info-line"><span class="info-label">Tél : </span>{{ $document->client->phone ?? '' }}</div>
        <div class="info-line"><span class="info-label">ICE : </span>{{ $document->client->ice ?? '' }}</div>
    </div>
    <div class="info-col">
        <div class="section-label">Informations</div>
        <div class="info-line"><span class="info-label">Validité : </span><span class="info-value">{{ $document->validity_days }} jours</span></div>
        <div class="info-line"><span class="info-label">Référence : </span></div>
        <div class="info-line"><span class="info-label">Paiement : </span><span class="info-value">{{ $document->payment_mode ?? '' }}</span></div>
        <div class="info-line"><span class="info-label">Objet : </span><span class="info-value">{{ $document->subject }}</span></div>
    </div>
</div>

<!-- ITEMS TABLE -->
<table class="items-table">
    <thead>
        <tr>
            <th style="width:38px" class="center">Réf.</th>
            <th class="center">Désignation / Description</th>
            <th style="width:48px" class="center">Unité</th>
            <th style="width:62px" class="center">Qté</th>
            <th style="width:72px" class="center">P.U HT (DH)</th>
            <th style="width:82px" class="center">P.T HT (DH)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document->items as $item)
        @if($item->is_category)
        <tr class="pdf-category-row">
            <td colspan="6" class="pdf-category-title">{{ $item->description }}</td>
        </tr>
        @else
        <tr>
            <td class="center">{{ $item->ref }}</td>
            <td>{!! $item->description !!}</td>
            <td class="center">{{ $item->unit }}</td>
            <td class="center">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
            <td class="right">{{ number_format($item->unit_price_ht, 2, ',', ' ') }}</td>
            <td class="right bold">{{ number_format($item->total_price_ht, 2, ',', ' ') }}</td>
        </tr>
        @endif
        @endforeach
        @include('pdf.partials.empty-rows', ['cols' => 6])
    </tbody>
</table>

<!-- NOTES + TOTALS côte à côte -->
<table class="totals-payments-wrap">
    <tr>
        <td class="totals-payments-left">
            @if(!empty($document->notes))
            <div class="pdf-notes">
                <div class="pdf-notes-title">Notes / Observations</div>
                <div class="pdf-notes-body">{!! nl2br(e($document->notes)) !!}</div>
            </div>
            @endif
        </td>
        <td class="totals-payments-right">
            <table class="totals-inner-table">
                @if($document->discount_percent > 0)
                <tr><td>Total HT avant remise :</td><td>{{ number_format($document->total_ht_before_discount, 2, ',', ' ') }} DH</td></tr>
                <tr><td>Remise ({{ $document->discount_percent }}%) :</td><td>- {{ number_format($document->discount_amount, 2, ',', ' ') }} DH</td></tr>
                @endif
                <tr><td>Total HT :</td><td>{{ number_format($document->total_ht, 2, ',', ' ') }} DH</td></tr>
                <tr><td>TVA ({{ $document->tva_rate }}%) :</td><td>{{ number_format($document->tva_amount, 2, ',', ' ') }} DH</td></tr>
                <tr class="ttc-row"><td>TOTAL TTC :</td><td>{{ number_format($document->total_ttc, 2, ',', ' ') }} DH</td></tr>
            </table>
        </td>
    </tr>
</table>

<!-- AMOUNT IN WORDS -->
<div class="amount-words">
    <span class="amount-words-label">Arrêtée le présent devis à la somme de : </span>
    <span class="amount-words-value">{{ $document->amount_in_words }} TTC</span>
</div>

<!-- SIGNATURES -->
<div class="signatures">
    <div class="sig-col">
        <div class="sig-title">Cachet et signature du client</div>
        <div style="height:40px"></div>
        <div class="sig-line"></div>
        <div class="sig-approved">Lu et approuvé</div>
    </div>
    <div class="sig-col">
        <div class="sig-title">Cachet et signature {{ $document->company->name }}</div>
        <div style="height:40px"></div>
        <div class="sig-line"></div>
        <div class="sig-approved">Lu et approuvé</div>
    </div>
</div>

</body>
</html>
