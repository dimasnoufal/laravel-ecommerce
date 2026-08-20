@props([
    'status' => 'pending'
])

@php
    $statusLower = strtolower($status);
    $config = [
        'paid' => ['class' => 'status-paid', 'icon' => 'check-circle-2', 'label' => 'Paid'],
        'completed' => ['class' => 'status-paid', 'icon' => 'check-circle-2', 'label' => 'Completed'],
        'processing' => ['class' => 'status-processing', 'icon' => 'clock', 'label' => 'Processing'],
        'shipped' => ['class' => 'status-processing', 'icon' => 'truck', 'label' => 'Shipped'],
        'pending' => ['class' => 'status-pending', 'icon' => 'alert-circle', 'label' => 'Pending'],
        'cancelled' => ['class' => 'status-cancelled', 'icon' => 'x-circle', 'label' => 'Cancelled'],
        'failed' => ['class' => 'status-cancelled', 'icon' => 'alert-triangle', 'label' => 'Failed'],
    ];

    $item = $config[$statusLower] ?? ['class' => 'status-pending', 'icon' => 'info', 'label' => ucfirst($status)];
@endphp

<span {{ $attributes->merge(['class' => 'status-pill ' . $item['class']]) }}>
    <i data-lucide="{{ $item['icon'] }}" style="width: 12px; height: 12px;"></i>
    {{ $item['label'] }}
</span>
