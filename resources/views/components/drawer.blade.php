@props([
    'id' => 'customDrawer',
    'title' => 'Drawer Title',
    'width' => '480px'
])

<!-- Reusable Slide-Over Drawer Component -->
<div id="{{ $id }}" class="custom-drawer-backdrop" onclick="if(event.target === this) closeDrawer('{{ $id }}')" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9999; display: none; opacity: 0; transition: opacity 0.3s ease;" {{ $attributes }}>
    <div class="custom-drawer-panel" onclick="event.stopPropagation()" style="position: fixed; top: 0; right: 0; bottom: 0; width: 100%; max-width: {{ $width }}; background: var(--card-bg); box-shadow: -10px 0 30px rgba(0, 0, 0, 0.25); border-left: 1px solid var(--border-color); display: flex; flex-direction: column; transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
        <!-- Drawer Header -->
        <div style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: var(--card-bg); flex-shrink: 0;">
            <div>
                <h3 id="{{ $id }}Title" style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.01em;">{{ $title }}</h3>
                <p id="{{ $id }}Subtitle" style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.15rem;"></p>
            </div>
            <button type="button" onclick="closeDrawer('{{ $id }}')" class="toast-close" style="width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--bg-body); border: 1px solid var(--border-color); cursor: pointer; color: var(--text-muted); transition: all 0.15s ease;">
                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
            </button>
        </div>

        <!-- Drawer Body (Scrollable) -->
        <div style="padding: 1.75rem; color: var(--text-main); flex-grow: 1; overflow-y: auto;">
            {{ $slot }}
        </div>

        <!-- Drawer Footer (Sticky Bottom) -->
        @if (isset($footer))
            <div style="padding: 1.25rem 1.75rem; background: var(--bg-body); border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; flex-shrink: 0;">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

<script>
    if (typeof window.openDrawer === 'undefined') {
        window.openDrawer = function(id, title = null, subtitle = null) {
            const drawer = document.getElementById(id);
            if (!drawer) return;
            if (title) {
                const titleEl = document.getElementById(id + 'Title');
                if (titleEl) titleEl.textContent = title;
            }
            if (subtitle) {
                const subtitleEl = document.getElementById(id + 'Subtitle');
                if (subtitleEl) {
                    subtitleEl.textContent = subtitle;
                    subtitleEl.style.display = 'block';
                }
            }
            drawer.style.display = 'block';
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                drawer.style.opacity = '1';
                const panel = drawer.querySelector('.custom-drawer-panel');
                if (panel) panel.style.transform = 'translateX(0)';
            }, 10);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        };

        window.closeDrawer = function(id) {
            const drawer = document.getElementById(id);
            if (!drawer) return;
            drawer.style.opacity = '0';
            const panel = drawer.querySelector('.custom-drawer-panel');
            if (panel) panel.style.transform = 'translateX(100%)';
            setTimeout(() => {
                drawer.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        };
    }
</script>
