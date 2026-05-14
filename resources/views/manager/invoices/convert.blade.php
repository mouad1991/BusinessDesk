@extends('layouts.app')
@section('title', 'Convertir en facture')
@section('page-title', 'Créer une facture')
@section('breadcrumb')
    Conversion {{ isset($quote) ? 'Devis' : 'BC' }} → Facture
@endsection

@php
$source = $quote ?? $purchaseOrder;
$sourceType = isset($quote) ? 'quote' : 'purchase_order';
@endphp

@section('content')
<div class="alert alert-info">
    Conversion de <strong>{{ $source->number }}</strong> en facture.
    Vous pouvez ajuster les informations avant de confirmer.
</div>

<form method="POST" action="{{ route('manager.invoices.from-source') }}" id="doc-form">
    @csrf
    <input type="hidden" name="source_type" value="{{ $sourceType }}">
    <input type="hidden" name="source_id" value="{{ $source->id }}">

    <div class="doc-form-grid">
        <div class="card">
            <div class="card-header"><h3>Client / Destinataire</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Client <span class="required">*</span></label>
                    <select name="client_id" required>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $client->id == $source->client_id ? 'selected' : '' }}>
                            {{ $client->company_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Informations facture</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Date <span class="required">*</span></label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Marché n°</label>
                        <input type="text" name="marche_number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Période du</label>
                        <input type="date" name="period_start">
                    </div>
                    <div class="form-group">
                        <label>au</label>
                        <input type="date" name="period_end">
                    </div>
                </div>
                <div class="form-group">
                    <label>Mode de règlement</label>
                    <input type="text" name="payment_mode" value="{{ $source->payment_mode ?? '' }}">
                </div>
                <div class="form-group">
                    <label>Objet</label>
                    <input type="text" name="subject" value="{{ $source->subject ?? '' }}">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Taux TVA (%)</label>
                        <input type="number" id="tva_rate" name="tva_rate" value="{{ $source->tva_rate }}" step="0.01" oninput="recalculate()">
                    </div>
                    <div class="form-group">
                        <label>Remise (%)</label>
                        <input type="number" id="discount_percent" name="discount_percent" value="{{ isset($quote) ? $source->discount_percent : '' }}" step="0.01" oninput="recalculate()">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <x-items-table
                :items="$source->items"
                type="standard"
                :autocomplete-url="route('manager.autocomplete')"
            />
        </div>
    </div>

    <!-- Notes -->
    <div class="card mt-4">
        <div class="card-header"><h3>Notes / Observations</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label>Note (affichée à gauche du tableau des totaux sur le PDF, sous les règlements le cas échéant)</label>
                <textarea name="notes" rows="3" placeholder="Ex : Conditions particulières, mention spécifique au client, remarque...">{{ old('notes', $source->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-actions mt-4">
        <a href="{{ isset($quote) ? route('manager.quotes.show', $source) : route('manager.purchase-orders.show', $source) }}" class="btn-secondary">Annuler</a>
        <button type="submit" class="btn-primary">Créer la facture</button>
    </div>
</form>
@endsection
