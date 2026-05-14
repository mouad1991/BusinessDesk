{{-- PDF Header — 3 colonnes : Logo | Coordonnées | Document --}}
{{-- $document->company, $docTitle, $docTitleColor --}}
<table class="pdf-header">
    <tr>
        {{-- Colonne 1 : Logo ou nom --}}
        <td class="header-col-logo input-center">
            @php
                $logoPath = $document->company->logo
                    ? (file_exists(public_path('storage/' . $document->company->logo))
                        ? public_path('storage/' . $document->company->logo)
                        : (file_exists(storage_path('app/public/' . $document->company->logo))
                            ? storage_path('app/public/' . $document->company->logo)
                            : null))
                    : null;
            @endphp
            @if($logoPath)
                <img src="{{ $logoPath }}" class="company-logo" alt="{{ $document->company->name }}">
            @else
                <div class="company-name-text">{{ $document->company->name }}</div>
            @endif
        </td>

        {{-- Colonne 2 : Coordonnées --}}
        <td class="header-col-info">
            <div class="header-company-name">{{ $document->company->name }}</div>
            @if($document->company->address)
            <div class="header-line">{{ $document->company->address }}</div>
            @endif
            @if($document->company->phone)
            <div class="header-line">{{ $document->company->phone }}</div>
            @endif
            @if($document->company->email)
            <div class="header-line">{{ $document->company->email }}</div>
            @endif
        </td>

        {{-- Colonne 3 : Nature / Numéro / Date --}}
        <td class="header-col-doc">
            <div class="doc-title" style="color: {{ $docTitleColor ?? '#ffffff' }}">{{ $docTitle }}</div>
            <div class="doc-number">N° {{ $document->number }}</div>
            <div class="doc-date">Date : {{ $document->date->format('d / m / Y') }}</div>
        </td>
    </tr>
</table>
