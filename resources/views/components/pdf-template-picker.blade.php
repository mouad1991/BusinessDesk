@php
$templates = config('pdf_templates');
$selected = $selectedTemplate ?? 'modern';
@endphp

<div class="card mt-4">
    <div class="card-header"><h3>Template PDF</h3></div>
    <div class="card-body">
        <p class="text-muted text-sm mb-3">Choisissez le style visuel appliqué à tous les documents PDF de cette société.</p>
        <div class="tpl-cards">
            @foreach($templates as $key => $tpl)
            <label class="tpl-card {{ $selected === $key ? 'tpl-card--selected' : '' }}" for="tpl_{{ $key }}">
                <input type="radio" name="pdf_template" id="tpl_{{ $key }}" value="{{ $key }}" {{ $selected === $key ? 'checked' : '' }} hidden>
                <div class="tpl-preview">
                    <img src="{{ asset('images/templates/' . $key . '.svg') }}" alt="{{ $tpl['name'] }}">
                </div>
                <div class="tpl-info">
                    <div class="tpl-header">
                        <span class="tpl-name">{{ $tpl['name'] }}</span>
                        <span class="tpl-dot" style="background:{{ $tpl['accent'] }}"></span>
                    </div>
                    <p class="tpl-desc">{{ $tpl['description'] }}</p>
                    <span class="tpl-select-btn">Sélectionner</span>
                </div>
            </label>
            @endforeach
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.tpl-card').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('tpl-card--selected'));
        this.classList.add('tpl-card--selected');
        this.querySelector('input[type=radio]').checked = true;
    });
});
</script>
