@props([
    'id' => 'globalDeleteConfirmModal'
])

<!-- Reusable Dynamic Confirmation & Delete Modal Component -->
<div id="{{ $id }}" class="custom-modal-backdrop" onclick="if(event.target === this) closeDeleteConfirm()" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 10000; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;" {{ $attributes }}>
    <div class="custom-modal-dialog" onclick="event.stopPropagation()" style="background: var(--card-bg); border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-xl); width: 100%; max-width: 440px; margin: 1.25rem; overflow: hidden; transform: scale(0.95); transition: transform 0.25s ease;">
        <!-- Modal Content -->
        <div style="padding: 2rem 1.75rem 1.5rem 1.75rem; text-align: center; display: flex; flex-direction: column; align-items: center;">
            <div id="{{ $id }}IconBox" style="width: 56px; height: 56px; border-radius: 50%; background: var(--danger-bg); color: var(--danger); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; box-shadow: 0 8px 16px -4px rgba(239, 68, 68, 0.25);">
                <i data-lucide="alert-triangle" style="width: 28px; height: 28px;"></i>
            </div>
            
            <h3 id="{{ $id }}Title" style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.01em;">
                Konfirmasi Hapus
            </h3>
            
            <p id="{{ $id }}Message" style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem;">
                Apakah Anda yakin ingin menghapus data ini?
            </p>

            <!-- Dynamic Reason Field (Optional) -->
            <div id="{{ $id }}ReasonContainer" style="width: 100%; text-align: left; margin-bottom: 1.25rem; display: none;">
                <label for="{{ $id }}ReasonInput" class="form-label" style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600;">
                    Alasan Penghapusan (Opsional):
                </label>
                <textarea id="{{ $id }}ReasonInput" class="form-control" rows="2" placeholder="Tuliskan catatan atau alasan penghapusan..." style="resize: vertical;"></textarea>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 0.75rem; width: 100%; margin-top: 0.5rem;">
                <button type="button" class="btn-secondary" onclick="closeDeleteConfirm()" style="flex: 1; justify-content: center; padding: 0.75rem 1rem;">
                    Batal
                </button>
                <button type="button" id="{{ $id }}ConfirmBtn" class="btn-primary" style="flex: 1; justify-content: center; background: var(--danger); border-color: var(--danger); padding: 0.75rem 1rem; box-shadow: 0 4px 12px -2px rgba(239, 68, 68, 0.35);">
                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                    <span id="{{ $id }}ConfirmBtnText">Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let globalDeleteCallback = null;

    window.showDeleteConfirm = function({
        title = 'Konfirmasi Hapus Data',
        message = 'Apakah Anda yakin ingin menghapus data ini?',
        itemName = '',
        showReason = false,
        confirmBtnText = 'Ya, Hapus',
        onConfirm = null
    }) {
        const modal = document.getElementById('globalDeleteConfirmModal');
        if (!modal) return;

        document.getElementById('globalDeleteConfirmModalTitle').textContent = title;
        
        let msgHtml = message;
        if (itemName) {
            msgHtml = `Apakah Anda yakin ingin menghapus <strong style="color: var(--text-main); font-weight: 700;">"${itemName}"</strong>? Tindakan ini tidak dapat dibatalkan.`;
        }
        document.getElementById('globalDeleteConfirmModalMessage').innerHTML = msgHtml;

        const reasonContainer = document.getElementById('globalDeleteConfirmModalReasonContainer');
        const reasonInput = document.getElementById('globalDeleteConfirmModalReasonInput');
        if (reasonContainer && reasonInput) {
            reasonInput.value = '';
            reasonContainer.style.display = showReason ? 'block' : 'none';
        }

        document.getElementById('globalDeleteConfirmModalConfirmBtnText').textContent = confirmBtnText;
        globalDeleteCallback = onConfirm;

        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.opacity = '1';
            const dialog = modal.querySelector('.custom-modal-dialog');
            if (dialog) dialog.style.transform = 'scale(1)';
        }, 10);

        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    window.closeDeleteConfirm = function() {
        const modal = document.getElementById('globalDeleteConfirmModal');
        if (!modal) return;
        modal.style.opacity = '0';
        const dialog = modal.querySelector('.custom-modal-dialog');
        if (dialog) dialog.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 250);
        globalDeleteCallback = null;
    };

    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('globalDeleteConfirmModalConfirmBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                const reasonInput = document.getElementById('globalDeleteConfirmModalReasonInput');
                const reason = reasonInput ? reasonInput.value : '';
                const callback = globalDeleteCallback;
                closeDeleteConfirm();
                if (typeof callback === 'function') {
                    callback(reason);
                }
            });
        }
    });
</script>
