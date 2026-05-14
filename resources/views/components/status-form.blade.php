{{-- Usage: @include('components.status-form', ['status' => $doc->status, 'updateUrl' => route(...), 'statuses' => [...]] ) --}}
@php
$allLabels = [
    'draft'    => 'Brouillon',
    'sent'     => 'Envoyé',
    'signed'   => 'Signé',
    'archived' => 'Archivé',
];
@endphp

<h4>Statut</h4>
@include('components.status-badge', ['status' => $status])

@php $lockedStatuses = ['converted', 'paid', 'delivered', 'accepted', 'rejected']; @endphp
@if(!in_array($status, $lockedStatuses))
<form method="POST" action="{{ $updateUrl }}" class="status-form">
    @csrf
    @method('PATCH')
    <select name="status">
        @foreach($allLabels as $value => $label)
            <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-status">Changer</button>
</form>
@endif
