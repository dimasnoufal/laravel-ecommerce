@props([
    'label' => 'Metric Label',
    'value' => '0',
    'change' => null,
    'isUp' => true,
    'subtext' => null,
    'icon' => null
])

<div {{ $attributes->merge(['class' => 'kpi-card']) }}>
    <div class="kpi-header">
        <span class="kpi-label">{{ $label }}</span>
        @if ($change !== null)
            <span class="kpi-badge {{ $isUp ? 'badge-up' : 'badge-down' }}">
                <i data-lucide="{{ $isUp ? 'arrow-up-right' : 'arrow-down-right' }}" style="width: 12px; height: 12px;"></i>
                {{ $change }}
            </span>
        @elseif($icon)
            <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px; color: var(--text-light);"></i>
        @endif
    </div>
    <div class="kpi-value">{{ $value }}</div>
    @if ($subtext)
        <div class="kpi-subtext">{{ $subtext }}</div>
    @endif
</div>
