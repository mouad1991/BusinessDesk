<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
@include('pdf.themes.styles', ['pdfTheme' => $pdfTheme ?? 'modern', 'docAccentColor' => $docAccentColor ?? '#c048c8'])
</style>
</head>
<body>

@include('pdf.partials.footer')

<!-- HEADER -->
@include('pdf.partials.header', ['docTitle' => 'BON DE LIVRAISON', 'docTitleColor' => '#c048c8'])

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
        <div class="section-label">Informations Commande</div>
        <div class="info-line"><span class="info-label">Votre N° commande : </span><span class="info-value">{{ $document->order_reference ?? '' }}</span></div>
        <div class="info-line"><span class="info-label">Adresse de livraison : </span>{{ $document->delivery_address ?? '' }}</div>
        <div class="info-line"><span class="info-label">Référence devis : </span><span class="info-value">{{ $document->quote_reference ?? '' }}</span></div>
    </div>
</div>

<!-- ITEMS TABLE -->
<table class="items-table">
    <thead>
        <tr>
            <th style="width:40px" class="center">Réf.</th>
            <th class="center">Désignation / Description</th>
            <th style="width:60px" class="center">Unité</th>
            <th style="width:80px" class="center">Qté<br>commandée</th>
            <th style="width:80px" class="center">Qté<br>expédiée</th>
            <th style="width:80px" class="center">Qté<br>en attente</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document->items as $item)
        <tr>
            <td class="center">{{ $item->ref }}</td>
            <td>{{ $item->description }}</td>
            <td class="center">{{ $item->unit }}</td>
            <td class="center">{{ number_format($item->qty_ordered, 2, ',', ' ') }}</td>
            <td class="center">{{ number_format($item->qty_shipped, 2, ',', ' ') }}</td>
            <td class="center">{{ number_format($item->qty_pending, 2, ',', ' ') }}</td>
        </tr>
        @endforeach
        @for($i = count($document->items); $i < 12; $i++)
        <tr class="empty-row"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        @endfor
    </tbody>
</table>

@if(!empty($document->notes))
<!-- NOTES -->
<div class="pdf-notes" style="margin-top:10px">
    <div class="pdf-notes-title">Notes / Observations</div>
    <div class="pdf-notes-body">{!! nl2br(e($document->notes)) !!}</div>
</div>
@endif

<!-- SIGNATURE -->
<div class="sig-section">
    <div class="sig-title">Signature du client</div>
    <div style="height:30px"></div>
    <div class="sig-line"></div>
    <div class="sig-info">Date : _____ / _____ / __________</div>
    <div class="sig-info" style="margin-top:6px">Par sa signature, le client confirme que cette commande a été reçue complète et en bonne condition.</div>
</div>

</body>
</html>
