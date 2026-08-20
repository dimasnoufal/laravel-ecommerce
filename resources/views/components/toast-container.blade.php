@props([
    'id' => 'toastContainer',
    'position' => 'top-right'
])

@php
    $positionStyles = [
        'top-right' => 'top: 1.5rem; right: 1.5rem;',
        'top-left' => 'top: 1.5rem; left: 1.5rem;',
        'bottom-right' => 'bottom: 1.5rem; right: 1.5rem;',
        'bottom-left' => 'bottom: 1.5rem; left: 1.5rem;',
    ];
    $posStyle = $positionStyles[$position] ?? $positionStyles['top-right'];
@endphp

<!-- Reusable Toast Notification Container -->
<div id="{{ $id }}" class="toast-container" style="position: fixed; {{ $posStyle }} z-index: 9999; display: flex; flex-direction: column; gap: 0.75rem; pointer-events: none;" {{ $attributes }}></div>

<script>
    window.showToast = function(type, title, message, actionText = null, actionCallback = null, containerId = '{{ $id }}') {
        const container = document.getElementById(containerId);
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-card toast-${type}`;

        let iconName = 'check-circle';
        if (type === 'error') iconName = 'alert-circle';
        if (type === 'info') iconName = 'info';

        let actionHtml = '';
        if (actionText) {
            actionHtml = `
                <div class="toast-actions">
                    <button type="button" class="toast-btn primary" id="toastActionBtn">${actionText}</button>
                    <button type="button" class="toast-btn secondary" onclick="this.closest('.toast-card').remove()">Dismiss</button>
                </div>
            `;
        }

        toast.innerHTML = `
            <div class="toast-icon-wrap">
                <i data-lucide="${iconName}" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="toast-body">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
                ${actionHtml}
            </div>
            <button type="button" class="toast-close" onclick="this.closest('.toast-card').remove()">
                <i data-lucide="x" style="width: 16px; height: 16px;"></i>
            </button>
        `;

        container.appendChild(toast);
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        setTimeout(() => {
            toast.classList.add('show');
        }, 50);

        if (actionText && actionCallback) {
            const actionBtn = toast.querySelector('#toastActionBtn');
            if (actionBtn) {
                actionBtn.addEventListener('click', () => {
                    actionCallback();
                    toast.remove();
                });
            }
        }

        if (!actionText) {
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 350);
            }, 4500);
        }
    };
</script>
