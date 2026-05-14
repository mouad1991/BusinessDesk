@extends('layouts.app')
@section('title', 'Modifier devis ' . $quote->number)
@section('page-title', 'Modifier le devis')
@section('breadcrumb')
    <a href="{{ route('manager.quotes.index') }}">Devis</a> /
    <a href="{{ route('manager.quotes.show', $quote) }}">{{ $quote->number }}</a> / Modifier
@endsection

@section('content')
<form method="POST" action="{{ route('manager.quotes.update', $quote) }}" id="doc-form">
    @csrf @method('PUT')

    <div class="doc-form-grid">
        <div class="card">
            <div class="card-header"><h3>Client / Destinataire</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Client <span class="required">*</span></label>
                    <select name="client_id" id="client_id" required onchange="fillClientInfo(this)">
                        <option value="">— Sélectionner —</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}"
                                data-address="{{ $client->address }}"
                                data-phone="{{ $client->phone }}"
                                data-ice="{{ $client->ice }}"
                                {{ old('client_id', $quote->client_id) == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div id="client-info-preview" class="client-info-preview">
                    <div id="ci-address" class="text-muted text-sm">{{ $quote->client->address }}</div>
                    <div id="ci-phone" class="text-muted text-sm">{{ $quote->client->phone ? 'Tél : '.$quote->client->phone : '' }}</div>
                    <div id="ci-ice" class="text-muted text-sm">{{ $quote->client->ice ? 'ICE : '.$quote->client->ice : '' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Informations devis</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Date <span class="required">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $quote->date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Validité (jours)</label>
                        <input type="number" name="validity_days" value="{{ old('validity_days', $quote->validity_days) }}" min="1">
                    </div>
                </div>
                <div class="form-group">
                    <label>Mode de paiement</label>
                    <input type="text" name="payment_mode" value="{{ old('payment_mode', $quote->payment_mode) }}">
                </div>
                <div class="form-group">
                    <label>Objet <span class="required">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject', $quote->subject) }}" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Taux TVA (%)</label>
                        <input type="number" id="tva_rate" name="tva_rate" value="{{ old('tva_rate', $quote->tva_rate) }}" step="0.01" oninput="recalculate()">
                    </div>
                    <div class="form-group">
                        <label>Remise (%)</label>
                        <input type="number" id="discount_percent" name="discount_percent" value="{{ old('discount_percent', $quote->discount_percent) }}" step="0.01" oninput="recalculate()">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <x-items-table
                :items="$quote->items"
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
                <label>Note</label>
                <textarea name="notes" rows="3" placeholder="Ex : Conditions particulières, mention spécifique au client, remarque...">{{ old('notes', $quote->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-actions mt-4">
        <a href="{{ route('manager.quotes.show', $quote) }}" class="btn-secondary">Annuler</a>
        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
    </div>
</form>

@push('scripts')
<script>
function fillClientInfo(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    document.getElementById('ci-address').textContent = opt.dataset.address || '';
    document.getElementById('ci-phone').textContent = opt.dataset.phone ? 'Tél : ' + opt.dataset.phone : '';
    document.getElementById('ci-ice').textContent = opt.dataset.ice ? 'ICE : ' + opt.dataset.ice : '';
}
</script>
@endpush
@endsection
