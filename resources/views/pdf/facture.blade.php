<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
@include('pdf.themes.styles', ['pdfTheme' => $pdfTheme ?? 'modern', 'docAccentColor' => $docAccentColor ?? '#1a73c8', 'sigAccentColor' => $sigAccentColor ?? '#e8a020'])
</style>
</head>
<body>

@include('pdf.partials.footer')

<!-- HEADER -->
@include('pdf.partials.header', ['docTitle' => 'FACTURE', 'docTitleColor' => '#e8a020'])

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
        <div class="section-label">Commande / Période</div>
        <div class="info-line"><span class="info-label">Marché n° : </span><span class="info-value">{{ $document->marche_number ?? '' }}</span></div>
        <div class="info-line">
            <span class="info-label">Période : </span>
            @if($document->period_start && $document->period_end)
                <span class="info-value">{{ $document->period_start->format('d/m/Y') }} au {{ $document->period_end->format('d/m/Y') }}</span>
            @endif
        </div>
        <div class="info-line"><span class="info-label">Mode de règlement : </span><span class="info-value">{{ $document->payment_mode ?? '' }}</span></div>
    </div>
</div>

@if($document->subject)
<div class="object-row">
    <span class="object-label">Objet : </span>{{ $document->subject }}
</div>
@endif

<!-- ITEMS TABLE -->
<table class="items-table">
    <thead>
        <tr>
            <th style="width:40px" class="center">Réf.</th>
            <th class="center">Désignation / Description</th>
            <th style="width:50px" class="center">Unité</th>
            <th style="width:40px" class="center">Qté</th>
            <th style="width:80px" class="center">P.U HT</th>
            <th style="width:110px" class="center">P.T HT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document->items as $item)
        <tr>
            <td class="center">{{ $item->ref }}</td>
            <td>{{ $item->description }}</td>
            <td class="center">{{ $item->unit }}</td>
            <td class="center">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
            <td class="right">{{ number_format($item->unit_price_ht, 2, ',', ' ') }} DH</td>
            <td class="right bold">{{ number_format($item->total_price_ht, 2, ',', ' ') }} DH</td>
        </tr>
        @endforeach
        @php $minRows = ($pdfTheme ?? 'modern') === 'compact' ? 8 : 12; @endphp
        @for($i = count($document->items); $i < $minRows; $i++)
        <tr class="empty-row"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        @endfor
    </tbody>
</table>

@php
    $hasPayments = isset($document->payments) && $document->payments->isNotEmpty();
    if ($hasPayments) {
        $psLabel = match($document->payment_status) {
            'paid'    => 'Payée',
            'partial' => 'Partiellement payée',
            default   => 'Non payée',
        };
        $psBg = match($document->payment_status) {
            'paid'    => '#16a34a',
            'partial' => '#d97706',
            default   => '#64748b',
        };
    }
@endphp

<!-- TOTALS + RÈGLEMENTS côte à côte -->
<table class="totals-payments-wrap">
    <tr>
        {{-- Colonne gauche : règlements (visible seulement si paiements) --}}
        <td class="totals-payments-left">
            @if($hasPayments)
            <div class="pdf-payments" style="margin-bottom:8px">
                <div class="pdf-payments-header">
                    <span class="pdf-payments-title">RÈGLEMENTS</span>
                    <span class="pdf-payment-badge" style="background:{{ $psBg }}">{{ $psLabel }}</span>
                </div>
                <table class="pdf-payments-table">
                    <thead>
                        <tr>
                            <th style="width:68px">Date</th>
                            <th>Mode</th>
                            <th>Référence</th>
                            <th style="width:75px" class="right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($document->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>{{ $payment->payment_method_label }}</td>
                            <td>{{ $payment->reference ?? '' }}</td>
                            <td class="right bold">{{ number_format($payment->amount, 2, ',', ' ') }} DH</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pdf-payments-footer">
                    <span>Réglé : <strong>{{ number_format($document->paid_amount, 2, ',', ' ') }} DH</strong></span>
                    @if($document->remaining_amount > 0)
                    <span class="pdf-payments-remaining">Reste à payer : <strong>{{ number_format($document->remaining_amount, 2, ',', ' ') }} DH</strong></span>
                    @else
                    <span class="pdf-payments-paid">Intégralement réglée ✓</span>
                    @endif
                </div>
            </div>
            @endif
            @if(!empty($document->notes))
            <div class="pdf-notes">
                <div class="pdf-notes-title">Notes / Observations</div>
                <div class="pdf-notes-body">{!! nl2br(e($document->notes)) !!}</div>
            </div>
            @endif
        </td>
        {{-- Colonne droite : tableau des totaux --}}
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

<!-- BOTTOM: Amount words + Bank details -->
<div class="bottom-section">
    <div class="bottom-left">
        <div class="amount-label">Arrêtée la présente facture à la somme de :</div>
        <span class="amount-value">{{ $document->amount_in_words }} TTC</span>
    </div>
    <div class="bottom-right">
        @if($document->company->bank || $document->company->rib)
        <div class="bank-label">Détails Bancaires</div>
        @if($document->company->bank)<div class="bank-line"><strong>Banque : </strong>{{ $document->company->bank }}</div>@endif
        @if($document->company->rib)<div class="bank-line"><strong>RIB : </strong>{{ $document->company->rib }}</div>@endif
        @if($document->company->bank_account_name)<div class="bank-line"><strong>Nom du compte : </strong>{{ $document->company->bank_account_name }}</div>@endif
        @endif
    </div>
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
