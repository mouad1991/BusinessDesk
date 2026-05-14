@php
$labels = [
    'draft'     => ['Brouillon', 'badge-gray'],
    'sent'      => ['Envoyé',    'badge-blue'],
    'signed'    => ['Signé',     'badge-green'],
    'archived'  => ['Archivé',   'badge-dark'],
    'accepted'  => ['Accepté',   'badge-green'],
    'rejected'  => ['Refusé',    'badge-red'],
    'converted' => ['Converti',  'badge-purple'],
    'paid'      => ['Payé',      'badge-green'],
    'delivered' => ['Livré',     'badge-green'],
];
$info = $labels[$status] ?? [$status, 'badge-gray'];
@endphp
<span class="badge {{ $info[1] }}">{{ $info[0] }}</span>
