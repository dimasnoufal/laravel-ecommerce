@props([
    'id' => 'customModal',
    'title' => 'Modal Title',
    'maxWidth' => '480px'
])

<div id="{{ $id }}" class="custom-modal-backdrop" onclick="if(event.target === this) closeModal('{{ $id }}')" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(8px); z-index: 9999; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;" {{ $attributes }}>
    <div class="custom-modal-dialog" onclick="event.stopPropagation()" style="background: var(--card-bg); border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-xl); width: 100%; max-width: {{ $maxWidth }}; margin: 1.25rem; overflow: hidden; transform: scale(0.95); transition: transform 0.25s ease;">
        <!-- Modal Header -->
        <div style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: var(--card-bg);">
            <h4 id="{{ $id }}Title" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.01em;">{{ $title }}</h4>
            <button type="button" onclick="closeModal('{{ $id }}')" class="toast-close" style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--bg-body); border: 1px solid var(--border-color); cursor: pointer;">
                <i data-lucide="x" style="width: 16px; height: 16px;"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 1.75rem; color: var(--text-main);">
            {{ $slot }}
        </div>

        <!-- Modal Footer -->
        @if (isset($footer))
            <div style="padding: 1.15rem 1.75rem; background: var(--bg-body); border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

<script>
    if (typeof window.openModal === 'undefined') {
        window.openModal = function(id, customTitle = null) {
            const modal = document.getElementById(id);
            if (!modal) return;
            if (customTitle) {
                const titleEl = document.getElementById(id + 'Title');
                if (titleEl) titleEl.textContent = customTitle;
            }
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                const dialog = modal.querySelector('.custom-modal-dialog');
                if (dialog) dialog.style.transform = 'scale(1)';
            }, 10);
            if (typeof lucide !== 'undefined') lucide.createIcons();
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
    }
</script>
