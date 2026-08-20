@props([
    'id' => 'globalPreloader',
    'title' => 'Processing Request',
    'subtext' => 'Please wait a moment while we process your data...'
])

<!-- Reusable Pre-loading Modal Component -->
<div class="preloader-backdrop" id="{{ $id }}" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(6px); z-index: 10000; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;" {{ $attributes }}>
    <div class="preloader-card" style="background: var(--card-bg, #ffffff); border-radius: var(--radius-xl, 16px); padding: 2.25rem 2.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid var(--border-color, #E2E8F0); display: flex; flex-direction: column; align-items: center; text-align: center; width: 320px;">
        <div class="spinner-ring" style="width: 52px; height: 52px; border: 4px solid var(--border-color, #E2E8F0); border-top-color: var(--primary, #2563EB); border-radius: 50%; animation: spin 0.85s linear infinite; margin-bottom: 1.25rem;"></div>
        <h4 class="preloader-title" id="{{ $id }}Title" style="font-size: 1.05rem; font-weight: 700; color: var(--text-main, #0F172A); margin-bottom: 0.35rem;">
            {{ $title }}
        </h4>
        <p class="preloader-subtext" id="{{ $id }}Subtext" style="font-size: 0.8125rem; color: var(--text-muted, #64748B);">
            {{ $subtext }}
        </p>
    </div>
</div>

<script>
    window.showPreloader = function(title = '{{ $title }}', subtext = '{{ $subtext }}', targetId = '{{ $id }}') {
        const preloader = document.getElementById(targetId);
        if (!preloader) return;
        const titleEl = document.getElementById(targetId + 'Title');
        const subtextEl = document.getElementById(targetId + 'Subtext');
        if (titleEl) titleEl.textContent = title;
        if (subtextEl) subtextEl.textContent = subtext;
        preloader.style.display = 'flex';
        setTimeout(() => {
            preloader.style.opacity = '1';
        }, 10);
    };

    window.hidePreloader = function(targetId = '{{ $id }}') {
        const preloader = document.getElementById(targetId);
        if (!preloader) return;
        preloader.style.opacity = '0';
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 250);
    };
</script>
