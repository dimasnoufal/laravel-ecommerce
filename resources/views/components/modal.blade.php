@props([
    'id' => 'customModal',
    'title' => 'Modal Title',
    'maxWidth' => '480px'
])

<div id="{{ $id }}" class="custom-modal-backdrop" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); z-index: 9999; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;" {{ $attributes }}>
    <div class="custom-modal-dialog" style="background: var(--card-bg); border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-xl); width: 100%; max-width: {{ $maxWidth }}; margin: 1rem; overflow: hidden; transform: scale(0.95); transition: transform 0.25s ease;">
        <!-- Modal Header -->
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-main);">{{ $title }}</h4>
            <button type="button" onclick="closeModal('{{ $id }}')" class="toast-close">
                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 1.5rem; color: var(--text-main);">
            {{ $slot }}
        </div>

        <!-- Modal Footer -->
        @if (isset($footer))
            <div style="padding: 1rem 1.5rem; background: var(--bg-body); border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

<script>
    window.openModal = function(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.opacity = '1';
            const dialog = modal.querySelector('.custom-modal-dialog');
            if (dialog) dialog.style.transform = 'scale(1)';
        }, 10);
    };

    window.closeModal = function(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.style.opacity = '0';
        const dialog = modal.querySelector('.custom-modal-dialog');
        if (dialog) dialog.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 250);
    };
</script>
