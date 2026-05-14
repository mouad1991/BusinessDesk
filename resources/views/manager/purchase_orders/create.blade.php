@extends('layouts.app')
@section('title', 'Nouveau bon de commande')
@section('page-title', 'Nouveau bon de commande')
@section('breadcrumb')
    <a href="{{ route('manager.purchase-orders.index') }}">Bons de commande</a> / Nouveau
@endsection

@section('content')
<form method="POST" action="{{ route('manager.purchase-orders.store') }}" id="doc-form">
    @csrf

    <div class="doc-form-grid">
        <!-- Left: Client -->
        <div class="card">
            <div class="card-header"><h3>Client / Destinataire</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Client <span class="required">*</span></label>
                    <select name="client_id" id="client_id" class="{{ $errors->has('client_id') ? 'is-invalid' : '' }}" required onchange="fillClientInfo(this)">
                        <option value="">— Sélectionner un client —</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}"
                                data-address="{{ $client->address }}"
                                data-phone="{{ $client->phone }}"
                                data-ice="{{ $client->ice }}"
                                {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('client_id')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div id="client-info-preview" class="client-info-preview" style="display:none">
                    <div id="ci-address" class="text-muted text-sm"></div>
                    <div id="ci-phone" class="text-muted text-sm"></div>
                    <div id="ci-ice" class="text-muted text-sm"></div>
                </div>
                @if($clients->isEmpty())
                <div class="alert alert-info" style="margin-top:8px">
                    <a href="{{ route('manager.clients.create') }}">Créer un client d'abord →</a>
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Infos BC -->
        <div class="card">
            <div class="card-header"><h3>Informations bon de commande</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Date <span class="required">*</span></label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="{{ $errors->has('date') ? 'is-invalid' : '' }}" required>
                        @error('date')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Date de commande</label>
                        <input type="date" name="order_date" value="{{ old('order_date') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Réf. devis</label>
                    <input type="text" name="quote_reference" value="{{ old('quote_reference') }}" placeholder="Numéro du devis associé">
                </div>
                <div class="form-group">
                    <label>Mode de paiement</label>
                    <input type="text" name="payment_mode" value="{{ old('payment_mode') }}" placeholder="Ex : Virement bancaire">
                </div>
                <div class="form-group">
                    <label>Objet</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Objet du bon de commande">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Taux TVA (%) <span class="required">*</span></label>
                        <input type="number" id="tva_rate" name="tva_rate" value="{{ old('tva_rate', 20) }}" min="0" max="100" step="0.01" required oninput="recalculate()">
                    </div>
                    <div class="form-group">
                        <label>Remise (%)</label>
                        <input type="number" id="discount_percent" name="discount_percent" value="{{ old('discount_percent') }}" min="0" max="100" step="0.01" oninput="recalculate()">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="card mt-4">
        <div class="card-body">
            <x-items-table
                :items="[]"
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
                <textarea name="notes" rows="3" placeholder="Ex : Conditions particulières, mention spécifique, remarque...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-actions mt-4">
        <a href="{{ route('manager.purchase-orders.index') }}" class="btn-secondary">Annuler</a>
        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Enregistrer le bon de commande
        </button>
    </div>
</form>

@push('scripts')
<script>
function fillClientInfo(sel) {
    const opt = sel.options[sel.selectedIndex];
    const preview = document.getElementById('client-info-preview');
    if (!opt.value) { preview.style.display = 'none'; return; }
    document.getElementById('ci-address').textContent = opt.dataset.address || '';
    document.getElementById('ci-phone').textContent = opt.dataset.phone ? 'Tél : ' + opt.dataset.phone : '';
    document.getElementById('ci-ice').textContent = opt.dataset.ice ? 'ICE : ' + opt.dataset.ice : '';
    preview.style.display = 'block';
}
const sel = document.getElementById('client_id');
if (sel && sel.value) fillClientInfo(sel);
</script>
@endpush
@endsection
