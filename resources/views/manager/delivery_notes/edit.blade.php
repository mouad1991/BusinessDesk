@extends('layouts.app')
@section('title', 'Modifier BL ' . $deliveryNote->number)
@section('page-title', 'Modifier le bon de livraison')
@section('breadcrumb')
    <a href="{{ route('manager.delivery-notes.index') }}">Bons de livraison</a> /
    <a href="{{ route('manager.delivery-notes.show', $deliveryNote) }}">{{ $deliveryNote->number }}</a> / Modifier
@endsection

@section('content')
<form method="POST" action="{{ route('manager.delivery-notes.update', $deliveryNote) }}" id="doc-form">
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
                                {{ old('client_id', $deliveryNote->client_id) == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div id="client-info-preview" class="client-info-preview">
                    <div id="ci-address" class="text-muted text-sm">{{ $deliveryNote->client->address }}</div>
                    <div id="ci-phone" class="text-muted text-sm">{{ $deliveryNote->client->phone ? 'Tél : '.$deliveryNote->client->phone : '' }}</div>
                    <div id="ci-ice" class="text-muted text-sm">{{ $deliveryNote->client->ice ? 'ICE : '.$deliveryNote->client->ice : '' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Informations bon de livraison</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Date <span class="required">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $deliveryNote->date->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Réf. commande</label>
                    <input type="text" name="order_reference" value="{{ old('order_reference', $deliveryNote->order_reference) }}">
                </div>
                <div class="form-group">
                    <label>Adresse de livraison</label>
                    <textarea name="delivery_address" rows="2">{{ old('delivery_address', $deliveryNote->delivery_address) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Réf. devis</label>
                    <input type="text" name="quote_reference" value="{{ old('quote_reference', $deliveryNote->quote_reference) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <x-items-table
                :items="$deliveryNote->items"
                type="delivery"
                :autocomplete-url="route('manager.autocomplete')"
            />
        </div>
    </div>

    <!-- Notes -->
    <div class="card mt-4">
        <div class="card-header"><h3>Notes / Observations</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label>Note (affichée sur le PDF)</label>
                <textarea name="notes" rows="3" placeholder="Ex : Instructions de livraison, mention particulière, remarque...">{{ old('notes', $deliveryNote->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-actions mt-4">
        <a href="{{ route('manager.delivery-notes.show', $deliveryNote) }}" class="btn-secondary">Annuler</a>
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
