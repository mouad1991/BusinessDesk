@extends('layouts.app')
@section('title', 'Modifier BC ' . $purchaseOrder->number)
@section('page-title', 'Modifier le bon de commande')
@section('breadcrumb')
    <a href="{{ route('manager.purchase-orders.index') }}">Bons de commande</a> /
    <a href="{{ route('manager.purchase-orders.show', $purchaseOrder) }}">{{ $purchaseOrder->number }}</a> / Modifier
@endsection

@section('content')
<form method="POST" action="{{ route('manager.purchase-orders.update', $purchaseOrder) }}" id="doc-form">
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
                                {{ old('client_id', $purchaseOrder->client_id) == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div id="client-info-preview" class="client-info-preview">
                    <div id="ci-address" class="text-muted text-sm">{{ $purchaseOrder->client->address }}</div>
                    <div id="ci-phone" class="text-muted text-sm">{{ $purchaseOrder->client->phone ? 'Tél : '.$purchaseOrder->client->phone : '' }}</div>
                    <div id="ci-ice" class="text-muted text-sm">{{ $purchaseOrder->client->ice ? 'ICE : '.$purchaseOrder->client->ice : '' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Informations bon de commande</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Date <span class="required">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $purchaseOrder->date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Date de commande</label>
                        <input type="date" name="order_date" value="{{ old('order_date', $purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Réf. devis</label>
                    <input type="text" name="quote_reference" value="{{ old('quote_reference', $purchaseOrder->quote_reference) }}">
                </div>
                <div class="form-group">
                    <label>Mode de paiement</label>
                    <input type="text" name="payment_mode" value="{{ old('payment_mode', $purchaseOrder->payment_mode) }}">
                </div>
                <div class="form-group">
                    <label>Objet</label>
                    <input type="text" name="subject" value="{{ old('subject', $purchaseOrder->subject) }}">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Taux TVA (%)</label>
                        <input type="number" id="tva_rate" name="tva_rate" value="{{ old('tva_rate', $purchaseOrder->tva_rate) }}" step="0.01" oninput="recalculate()">
                    </div>
                    <div class="form-group">
                        <label>Remise (%)</label>
                        <input type="number" id="discount_percent" name="discount_percent" value="{{ old('discount_percent', $purchaseOrder->discount_percent) }}" step="0.01" oninput="recalculate()">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <x-items-table
                :items="$purchaseOrder->items"
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
                <textarea name="notes" rows="3" placeholder="Ex : Conditions particulières, mention spécifique, remarque...">{{ old('notes', $purchaseOrder->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-actions mt-4">
        <a href="{{ route('manager.purchase-orders.show', $purchaseOrder) }}" class="btn-secondary">Annuler</a>
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
