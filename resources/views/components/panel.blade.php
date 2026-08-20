@props([
    'title' => null,
    'subtitle' => null,
    'action' => null
])

<div {{ $attributes->merge(['class' => 'panel-card']) }}>
    @if ($title || $action)
        <div class="panel-header">
            <div>
                @if ($title)
                    <h3 class="panel-title">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <span style="font-size: 0.8125rem; color: var(--text-muted);">{{ $subtitle }}</span>
                @endif
            </div>

            @if ($action)
                <div class="panel-action">
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif

    <div class="panel-content">
        {{ $slot }}
    </div>
</div>
