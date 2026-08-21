@props([
    'id' => 'globalPreloader',
    'title' => 'Memproses Permintaan',
    'subtext' => 'Mohon tunggu sebentar...'
])

<!-- Full-Screen Glassmorphism Preloader with Single 3-Dots Horizontal Indicator -->
<div class="preloader-backdrop" id="{{ $id }}" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 99999; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;" {{ $attributes }}>
    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 2rem;">
        <!-- Single Horizontal 3-Dots Wave Loader -->
        <div class="horizontal-dots-loader" style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 1.5rem;">
            <span style="width: 14px; height: 14px; background: #3B82F6; border-radius: 50%; display: inline-block; animation: horizontalDotPulse 1.4s infinite ease-in-out both; animation-delay: -0.32s; box-shadow: 0 0 14px rgba(59, 130, 246, 0.8);"></span>
            <span style="width: 14px; height: 14px; background: #60A5FA; border-radius: 50%; display: inline-block; animation: horizontalDotPulse 1.4s infinite ease-in-out both; animation-delay: -0.16s; box-shadow: 0 0 14px rgba(96, 165, 250, 0.8);"></span>
            <span style="width: 14px; height: 14px; background: #93C5FD; border-radius: 50%; display: inline-block; animation: horizontalDotPulse 1.4s infinite ease-in-out both; animation-delay: 0s; box-shadow: 0 0 14px rgba(147, 197, 253, 0.8);"></span>
        </div>
        
        <h4 id="{{ $id }}Title" style="font-size: 1.1rem; font-weight: 700; color: #FFFFFF; letter-spacing: -0.01em; margin-bottom: 0.35rem; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
            {{ $title }}
        </h4>
        <p id="{{ $id }}Subtext" style="font-size: 0.875rem; color: #E2E8F0; max-width: 280px; line-height: 1.4; text-shadow: 0 1px 4px rgba(0,0,0,0.4);">
            {{ $subtext }}
        </p>
    </div>
</div>

<style>
@keyframes horizontalDotPulse {
    0%, 80%, 100% {
        transform: scale(0.45);
        opacity: 0.35;
    }
    40% {
        transform: scale(1.25);
        opacity: 1;
        filter: brightness(1.2);
    }
}
</style>

<script>
    window.showPreloader = function(title = 'Memproses Permintaan', subtext = 'Mohon tunggu sebentar...', targetId = '{{ $id }}') {
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
