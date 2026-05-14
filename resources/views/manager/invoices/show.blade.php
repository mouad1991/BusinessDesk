@extends('layouts.app')
@section('title', 'Facture ' . $invoice->number)
@section('page-title', $invoice->number)
@section('breadcrumb')
    <a href="{{ route('manager.invoices.index') }}">Factures</a> / {{ $invoice->number }}
@endsection

@section('page-actions')
    <a href="{{ route('manager.invoices.pdf', $invoice) }}" class="btn-danger" target="_blank">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Télécharger PDF
    </a>
    <a href="{{ route('manager.invoices.edit', $invoice) }}" class="btn-secondary">Modifier</a>
@endsection

@section('content')
<div class="doc-view-grid">
    <div class="doc-view-main">

        <!-- Header info -->
        <div class="doc-info-grid card">
            <div class="card-body">
                <div class="doc-info-2col">
                    <div>
                        <h4 class="section-label">Client / Destinataire</h4>
                        <p class="font-bold">{{ $invoice->client->company_name }}</p>
                        @if($invoice->client->address)<p>{{ $invoice->client->address }}</p>@endif
                        @if($invoice->client->phone)<p>Tél : {{ $invoice->client->phone }}</p>@endif
                        @if($invoice->client->ice)<p>ICE : {{ $invoice->client->ice }}</p>@endif
                    </div>
                    <div>
                        <h4 class="section-label">Informations</h4>
                        <table class="info-table">
                            <tr><td>Date</td><td>{{ $invoice->date->format('d/m/Y') }}</td></tr>
                            @if($invoice->marche_number)
                            <tr><td>Marché n°</td><td>{{ $invoice->marche_number }}</td></tr>
                            @endif
                            @if($invoice->period_start && $invoice->period_end)
                            <tr><td>Période</td><td>{{ $invoice->period_start->format('d/m/Y') }} → {{ $invoice->period_end->format('d/m/Y') }}</td></tr>
                            @endif
                            @if($invoice->payment_mode)
                            <tr><td>Mode de règlement</td><td>{{ $invoice->payment_mode }}</td></tr>
                            @endif
                            @if($invoice->subject)
                            <tr><td>Objet</td><td>{{ $invoice->subject }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items table -->
        <div class="card mt-4">
            <div class="card-body p-0">
                <table class="table items-view-table">
                    <thead>
                        <tr>
                            <th>Réf.</th>
                            <th>Désignation / Description</th>
                            <th>Unité</th>
                            <th class="text-right">Qté</th>
                            <th class="text-right">P.U HT</th>
                            <th class="text-right">P.T HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->ref }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->unit }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($item->unit_price_ht, 2, ',', ' ') }} DH</td>
                            <td class="text-right font-bold">{{ number_format($item->total_price_ht, 2, ',', ' ') }} DH</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="totals-view">
                    <div class="totals-row"><span>Total HT :</span><strong>{{ number_format($invoice->total_ht_before_discount, 2, ',', ' ') }} DH</strong></div>
                    @if($invoice->discount_percent > 0)
                    <div class="totals-row"><span>Remise ({{ $invoice->discount_percent }}%) :</span><strong>- {{ number_format($invoice->discount_amount, 2, ',', ' ') }} DH</strong></div>
                    <div class="totals-row"><span>Total HT après remise :</span><strong>{{ number_format($invoice->total_ht, 2, ',', ' ') }} DH</strong></div>
                    @endif
                    <div class="totals-row"><span>TVA ({{ $invoice->tva_rate }}%) :</span><strong>{{ number_format($invoice->tva_amount, 2, ',', ' ') }} DH</strong></div>
                    <div class="totals-row totals-ttc"><span>TOTAL TTC :</span><strong>{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</strong></div>
                </div>
                <div class="amount-words mt-3">
                    <em>Arrêtée la présente facture à la somme de : <strong>{{ $invoice->amount_in_words }}</strong></em>
                </div>
            </div>
        </div>

        <!-- Détails bancaires -->
        @if($company->bank)
        <div class="card mt-4">
            <div class="card-header"><h3>Coordonnées bancaires</h3></div>
            <div class="card-body">
                <table class="info-table">
                    @if($company->bank_account_name)<tr><td>Intitulé</td><td>{{ $company->bank_account_name }}</td></tr>@endif
                    <tr><td>Banque</td><td>{{ $company->bank }}</td></tr>
                    @if($company->rib)<tr><td>RIB</td><td>{{ $company->rib }}</td></tr>@endif
                </table>
            </div>
        </div>
        @endif

        <!-- ═══ RÈGLEMENTS ═══ -->
        <div class="card mt-4" id="payments-card">
            <div class="card-header" style="display:flex; align-items:center; justify-content:space-between;">
                <h3>Règlements</h3>
                @if($invoice->payment_status !== 'paid')
                <button type="button" class="btn-primary btn-sm" id="btn-add-payment" onclick="toggleAddPayment()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Ajouter un règlement
                </button>
                @endif
            </div>
            <div class="card-body">

                <!-- Summary bar -->
                <div class="payment-summary-bar">
                    <div class="payment-stat">
                        <span class="pstat-label">Total TTC</span>
                        <span class="pstat-value">{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</span>
                    </div>
                    <div class="payment-stat pstat-success">
                        <span class="pstat-label">Réglé</span>
                        <span class="pstat-value">{{ number_format($invoice->paid_amount, 2, ',', ' ') }} DH</span>
                    </div>
                    <div class="payment-stat {{ $invoice->remaining_amount > 0 ? 'pstat-danger' : 'pstat-success' }}">
                        <span class="pstat-label">Reste à payer</span>
                        <span class="pstat-value">{{ number_format($invoice->remaining_amount, 2, ',', ' ') }} DH</span>
                    </div>
                </div>

                @if($invoice->payments->isEmpty())
                <p class="text-muted text-sm" style="margin-top:14px">Aucun règlement enregistré pour cette facture.</p>
                @else
                <table class="table mt-3" id="payments-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Mode</th>
                            <th>Référence</th>
                            <th class="text-right">Montant</th>
                            <th style="width:130px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr id="payment-row-{{ $payment->id }}">
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>{{ $payment->payment_method_label }}</td>
                            <td>{{ $payment->reference ?? '—' }}</td>
                            <td class="text-right font-bold">{{ number_format($payment->amount, 2, ',', ' ') }} DH</td>
                            <td class="text-right">
                                <button type="button" class="btn-sm btn-secondary"
                                    onclick="toggleEditPayment({{ $payment->id }})">Modifier</button>
                                <form method="POST"
                                    action="{{ route('manager.invoice-payments.destroy', [$invoice, $payment]) }}"
                                    style="display:inline"
                                    onsubmit="return confirm('Supprimer ce règlement ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <!-- Inline edit form -->
                        <tr id="edit-row-{{ $payment->id }}" style="display:none; background:#f8fafc;">
                            <td colspan="5" style="padding:14px 16px;">
                                <form method="POST"
                                    action="{{ route('manager.invoice-payments.update', [$invoice, $payment]) }}"
                                    class="payment-inline-form">
                                    @csrf @method('PATCH')
                                    <div class="payment-form-grid">
                                        <div class="form-group">
                                            <label>Montant (DH) <span class="required">*</span></label>
                                            <input type="number" name="amount" step="0.01" min="0.01"
                                                value="{{ old('amount', $payment->amount) }}"
                                                class="{{ $errors->has('amount') ? 'is-invalid' : '' }}" required>
                                            @error('amount')<span class="error-msg">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Mode de règlement <span class="required">*</span></label>
                                            <select name="payment_method" required>
                                                @foreach(['especes' => 'Espèces', 'virement' => 'Virement bancaire', 'cheque' => 'Chèque', 'cb' => 'Carte bancaire'] as $val => $label)
                                                <option value="{{ $val }}" {{ old('payment_method', $payment->payment_method) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Date <span class="required">*</span></label>
                                            <input type="date" name="payment_date"
                                                value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Référence</label>
                                            <input type="text" name="reference"
                                                value="{{ old('reference', $payment->reference) }}"
                                                placeholder="N° chèque, réf. virement…">
                                        </div>
                                    </div>
                                    <div style="display:flex; gap:8px; margin-top:8px;">
                                        <button type="submit" class="btn-primary btn-sm">Enregistrer</button>
                                        <button type="button" class="btn-secondary btn-sm" onclick="toggleEditPayment({{ $payment->id }})">Annuler</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <!-- Add payment form -->
                <div id="add-payment-form" style="display:none; margin-top:16px; padding:16px; background:#f8fafc; border:1px solid var(--gray-200); border-radius:var(--radius);">
                    <p class="font-bold" style="margin-bottom:12px; color:var(--primary);">Nouveau règlement</p>
                    <form method="POST" action="{{ route('manager.invoice-payments.store', $invoice) }}"
                        class="payment-inline-form">
                        @csrf
                        <div class="payment-form-grid">
                            <div class="form-group">
                                <label>Montant (DH) <span class="required">*</span></label>
                                <input type="number" name="amount" step="0.01" min="0.01"
                                    max="{{ $invoice->remaining_amount }}"
                                    value="{{ old('amount', number_format($invoice->remaining_amount, 2, '.', '')) }}"
                                    class="{{ $errors->has('amount') ? 'is-invalid' : '' }}" required>
                                @error('amount')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label>Mode de règlement <span class="required">*</span></label>
                                <select name="payment_method" required>
                                    <option value="">— Choisir —</option>
                                    @foreach(['especes' => 'Espèces', 'virement' => 'Virement bancaire', 'cheque' => 'Chèque', 'cb' => 'Carte bancaire'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('payment_method') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label>Date de règlement <span class="required">*</span></label>
                                <input type="date" name="payment_date"
                                    value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label>Référence <span class="text-muted">(optionnel)</span></label>
                                <input type="text" name="reference" value="{{ old('reference') }}"
                                    placeholder="N° chèque, réf. virement…">
                            </div>
                        </div>
                        <div style="display:flex; gap:8px; margin-top:8px;">
                            <button type="submit" class="btn-primary btn-sm">Enregistrer le règlement</button>
                            <button type="button" class="btn-secondary btn-sm" onclick="toggleAddPayment()">Annuler</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        {{-- /RÈGLEMENTS --}}

    </div>

    <!-- Sidebar -->
    <div class="doc-view-sidebar">
        <div class="card">
            <div class="card-body">
                @include('components.status-form', [
                    'status'    => $invoice->status,
                    'updateUrl' => route('manager.invoices.update-status', $invoice),
                ])
            </div>
        </div>

        <!-- Payment status card -->
        <div class="card mt-3">
            <div class="card-body">
                <h4 style="margin-bottom:10px;">Statut paiement</h4>
                @php
                    $psBadge = match($invoice->payment_status) {
                        'paid'    => 'badge-green',
                        'partial' => 'badge-orange',
                        default   => 'badge-gray',
                    };
                @endphp
                <span class="badge {{ $psBadge }}">{{ $invoice->payment_status_label }}</span>

                @if($invoice->payment_status !== 'unpaid')
                <div class="mt-3">
                    <div class="sidebar-stat">
                        <span class="sidebar-stat-label">Réglé</span>
                        <span class="sidebar-stat-value text-success">{{ number_format($invoice->paid_amount, 2, ',', ' ') }} DH</span>
                    </div>
                    @if($invoice->remaining_amount > 0)
                    <div class="sidebar-stat mt-1">
                        <span class="sidebar-stat-label">Reste à payer</span>
                        <span class="sidebar-stat-value text-danger">{{ number_format($invoice->remaining_amount, 2, ',', ' ') }} DH</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4>Société</h4>
                <p class="font-bold">{{ $company->name }}</p>
                <p class="text-muted text-sm">{{ $company->address }}</p>
            </div>
        </div>
    </div>
</div>

@if($errors->any() && $errors->has('amount'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('add-payment-form').style.display = 'block';
        document.getElementById('btn-add-payment') && (document.getElementById('btn-add-payment').style.display = 'none');
    });
</script>
@endif
@endsection

@push('scripts')
<script>
function toggleAddPayment() {
    const form = document.getElementById('add-payment-form');
    const btn  = document.getElementById('btn-add-payment');
    const open = form.style.display !== 'block';
    form.style.display = open ? 'block' : 'none';
    if (btn) btn.style.display = open ? 'none' : '';
}

function toggleEditPayment(id) {
    const row  = document.getElementById('edit-row-' + id);
    const open = row.style.display !== 'table-row';
    // Close all other edit rows first
    document.querySelectorAll('[id^="edit-row-"]').forEach(r => r.style.display = 'none');
    row.style.display = open ? 'table-row' : 'none';
}
</script>
@endpush
