@props([
    'status' => 'pending'
])

@php
    $statusString = is_object($status) && isset($status->value) ? (string)$status->value : (string)$status;
    $statusLower = strtolower($statusString);
    $config = [
        'paid' => ['class' => 'status-paid', 'icon' => 'check-circle-2', 'label' => 'Paid'],
        'completed' => ['class' => 'status-paid', 'icon' => 'check-circle-2', 'label' => 'Completed'],
        'delivered' => ['class' => 'status-paid', 'icon' => 'check-circle-2', 'label' => 'Delivered'],
        'confirmed' => ['class' => 'status-processing', 'icon' => 'check-circle', 'label' => 'Confirmed'],
        'processing' => ['class' => 'status-processing', 'icon' => 'clock', 'label' => 'Processing'],
        'shipped' => ['class' => 'status-processing', 'icon' => 'truck', 'label' => 'Shipped'],
        'pending' => ['class' => 'status-pending', 'icon' => 'alert-circle', 'label' => 'Pending'],
        'cancelled' => ['class' => 'status-cancelled', 'icon' => 'x-circle', 'label' => 'Cancelled'],
        'failed' => ['class' => 'status-cancelled', 'icon' => 'alert-triangle', 'label' => 'Failed'],
    ];

    $item = $config[$statusLower] ?? ['class' => 'status-pending', 'icon' => 'info', 'label' => ucfirst($statusString)];
@endphp

<span {{ $attributes->merge(['class' => 'status-pill ' . $item['class']]) }}>
    <i data-lucide="{{ $item['icon'] }}" style="width: 12px; height: 12px;"></i>
    {{ $item['label'] }}
</span>
