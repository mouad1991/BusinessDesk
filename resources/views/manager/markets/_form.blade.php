{{-- Shared form partial for create & edit --}}
@php $market = $market ?? null; @endphp

<div class="doc-form-grid">

    {{-- Colonne 1 --}}
    <div>
        {{-- Informations principales --}}
        <div class="card">
            <div class="card-header"><h3>Informations principales</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Objet / Nom du marché <span class="required">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $market?->title) }}"
                        class="{{ $errors->has('title') ? 'is-invalid' : '' }}"
                        required placeholder="Ex : Marché de maintenance informatique">
                    @error('title')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type <span class="required">*</span></label>
                        <select name="market_type" required>
                            @foreach($types as $val => $label)
                            <option value="{{ $val }}" {{ old('market_type', $market?->market_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catégorie <span class="required">*</span></label>
                        <select name="category" required>
                            @foreach($categories as $val => $label)
                            <option value="{{ $val }}" {{ old('category', $market?->category) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Client</label>
                        <select name="client_id">
                            <option value="">— Aucun —</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $market?->client_id) == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Statut <span class="required">*</span></label>
                        <select name="status" required>
                            @foreach($statuses as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $market?->status ?? 'studying') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Date début</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $market?->start_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label>Date fin</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $market?->end_date?->format('Y-m-d')) }}">
                        @error('end_date')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Suivi financier --}}
        <div class="card mt-4">
            <div class="card-header"><h3>Suivi financier</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Montant total du marché (DH) <span class="required">*</span></label>
                    <input type="number" name="total_amount" step="0.01" min="0"
                        value="{{ old('total_amount', $market ? number_format($market->total_amount, 2, '.', '') : '') }}"
                        class="{{ $errors->has('total_amount') ? 'is-invalid' : '' }}"
                        required placeholder="0,00">
                    @error('total_amount')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Montant facturé (DH)</label>
                        <input type="number" name="invoiced_amount" step="0.01" min="0"
                            value="{{ old('invoiced_amount', $market ? number_format($market->invoiced_amount, 2, '.', '') : '0') }}"
                            placeholder="0,00">
                    </div>
                    <div class="form-group">
                        <label>Montant réglé (DH)</label>
                        <input type="number" name="paid_amount" step="0.01" min="0"
                            value="{{ old('paid_amount', $market ? number_format($market->paid_amount, 2, '.', '') : '0') }}"
                            placeholder="0,00">
                    </div>
                </div>
                @if($market)
                <div class="market-remaining-preview">
                    <span>Reste à encaisser :</span>
                    <strong class="{{ $market->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($market->remaining_amount, 2, ',', ' ') }} DH
                    </strong>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Colonne 2 --}}
    <div>
        {{-- Caution --}}
        <div class="card">
            <div class="card-header"><h3>Caution</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Montant caution (DH)</label>
                    <input type="number" name="caution_amount" step="0.01" min="0"
                        value="{{ old('caution_amount', $market?->caution_amount ? number_format($market->caution_amount, 2, '.', '') : '') }}"
                        placeholder="Laisser vide si pas de caution">
                </div>
                <div class="form-group">
                    <label>Statut caution</label>
                    <select name="caution_status">
                        <option value="">— Aucune caution —</option>
                        @foreach($cautionStatuses as $val => $label)
                        <option value="{{ $val }}" {{ old('caution_status', $market?->caution_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card mt-4">
            <div class="card-header"><h3>Observations / Notes</h3></div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <textarea name="notes" rows="5" placeholder="Informations complémentaires, conditions particulières…">{{ old('notes', $market?->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Pièces jointes --}}
        <div class="card mt-4">
            <div class="card-header"><h3>Pièces jointes</h3></div>
            <div class="card-body">
                @if($market && $market->attachments->isNotEmpty())
                <div class="attachments-current">
                    <p class="text-muted text-sm mb-2">Fichiers existants :</p>
                    @foreach($market->attachments as $att)
                    <div class="attachment-item">
                        <span class="attachment-icon">📎</span>
                        <a href="{{ route('manager.market-attachments.download', [$market, $att]) }}"
                           class="attachment-name" target="_blank">{{ $att->original_name }}</a>
                        <form method="POST"
                              action="{{ route('manager.market-attachments.destroy', [$market, $att]) }}"
                              style="display:inline" onsubmit="return confirm('Supprimer ce fichier ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-danger" title="Supprimer">✕</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="form-group mb-0 {{ $market && $market->attachments->isNotEmpty() ? 'mt-3' : '' }}">
                    <label>Ajouter des fichiers <span class="text-muted text-sm">(PDF, Word, Excel, images — max 10 Mo / fichier)</span></label>
                    <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                    @error('attachments.*')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>
</div>
