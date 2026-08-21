@props([
    'id' => 'searchableSelect',
    'name' => 'select_value',
    'placeholder' => '-- Pilih Opsi --',
    'options' => [],
    'value' => '',
    'required' => false
])

@php
    $selectedLabel = $placeholder;
    $hasSelectedValue = ($value !== '' && $value !== null && isset($options[$value]));
    if ($hasSelectedValue) {
        $selectedLabel = $options[$value];
    }
@endphp

<!-- Reusable Searchable & Scrollable Dropdown Component -->
<div class="searchable-select-wrapper" id="{{ $id }}_wrapper" style="position: relative; width: 100%;" {{ $attributes }}>
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" {{ $required ? 'required' : '' }}>
    
    <button type="button" class="form-control searchable-select-trigger" id="{{ $id }}_trigger" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; text-align: left; background: var(--bg-body); padding: 0.65rem 0.875rem;">
        <span class="selected-text" id="{{ $id }}_text" style="color: {{ $hasSelectedValue ? 'var(--text-main)' : 'var(--text-muted)' }}; font-weight: 500;">
            {{ $selectedLabel }}
        </span>
        <i data-lucide="chevron-down" class="dropdown-chevron" style="width: 16px; height: 16px; color: var(--text-muted); transition: transform 0.2s ease;"></i>
    </button>

    <div class="searchable-select-menu" id="{{ $id }}_menu" style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); z-index: 1000; display: none; flex-direction: column; overflow: hidden; animation: slideDown 0.15s ease;">
        <!-- Search Input -->
        <div style="padding: 0.5rem; border-bottom: 1px solid var(--border-color); background: var(--card-bg);">
            <div style="position: relative; display: flex; align-items: center;">
                <i data-lucide="search" style="width: 14px; height: 14px; position: absolute; left: 0.65rem; color: var(--text-light); pointer-events: none;"></i>
                <input type="text" class="search-input" id="{{ $id }}_search" placeholder="Cari opsi..." style="width: 100%; padding: 0.4rem 0.65rem 0.4rem 2rem; font-size: 0.8125rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-body); color: var(--text-main); outline: none;">
            </div>
        </div>

        <!-- Scrollable Options List -->
        <div class="options-list custom-scrollbar" id="{{ $id }}_options" style="max-height: 210px; overflow-y: auto; padding: 0.35rem;">
            <div class="select-option {{ !$hasSelectedValue ? 'selected' : '' }}" data-value="" data-placeholder="{{ $placeholder }}" style="padding: 0.55rem 0.75rem; border-radius: var(--radius-md); font-size: 0.875rem; cursor: pointer; color: var(--text-muted); transition: all 0.15s ease;">
                {{ $placeholder }}
            </div>
            @foreach($options as $val => $label)
                @php
                    $isSelected = ((string)$val === (string)$value && $value !== '' && $value !== null);
                @endphp
                <div class="select-option {{ $isSelected ? 'selected' : '' }}" data-value="{{ $val }}" style="padding: 0.55rem 0.75rem; border-radius: var(--radius-md); font-size: 0.875rem; cursor: pointer; color: var(--text-main); transition: all 0.15s ease;">
                    {{ $label }}
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}
.select-option:hover {
    background: var(--primary-light) !important;
    color: var(--primary) !important;
}
.select-option.selected {
    background: var(--primary-light) !important;
    color: var(--primary) !important;
    font-weight: 700;
}
</style>

<script>
(function() {
    const wrapper = document.getElementById('{{ $id }}_wrapper');
    if (!wrapper) return;

    const input = document.getElementById('{{ $id }}');
    const trigger = document.getElementById('{{ $id }}_trigger');
    const menu = document.getElementById('{{ $id }}_menu');
    const search = document.getElementById('{{ $id }}_search');
    const optionsContainer = document.getElementById('{{ $id }}_options');
    const textEl = document.getElementById('{{ $id }}_text');
    const chevron = trigger.querySelector('.dropdown-chevron');

    function toggleMenu(show = null) {
        const isOpen = show !== null ? show : menu.style.display === 'flex';
        if (isOpen) {
            menu.style.display = 'none';
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        } else {
            // Close other open menus
            document.querySelectorAll('.searchable-select-menu').forEach(m => m.style.display = 'none');
            menu.style.display = 'flex';
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            search.value = '';
            filterOptions('');
            setTimeout(() => search.focus(), 50);
        }
    }

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMenu();
    });

    search.addEventListener('input', (e) => {
        filterOptions(e.target.value.toLowerCase());
    });

    function filterOptions(query) {
        const options = optionsContainer.querySelectorAll('.select-option');
        options.forEach(opt => {
            const text = opt.textContent.trim().toLowerCase();
            if (text.includes(query)) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    optionsContainer.addEventListener('click', (e) => {
        const option = e.target.closest('.select-option');
        if (!option) return;
        
        const val = option.getAttribute('data-value');
        const text = option.textContent.trim();
        
        input.value = val;
        textEl.textContent = text;
        textEl.style.color = val ? 'var(--text-main)' : 'var(--text-muted)';
        
        optionsContainer.querySelectorAll('.select-option').forEach(o => o.classList.remove('selected'));
        option.classList.add('selected');
        
        toggleMenu(true);
        $(input).trigger('change');
    });

    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            toggleMenu(true);
        }
    });

    // Global helper to set value programmatically
    window.setSearchableSelectValue = function(id, val, text = null) {
        const inp = document.getElementById(id);
        const txt = document.getElementById(id + '_text');
        const optCont = document.getElementById(id + '_options');
        if (!inp || !txt) return;

        inp.value = val;
        if (optCont) {
            optCont.querySelectorAll('.select-option').forEach(o => o.classList.remove('selected'));
            const opt = optCont.querySelector(`.select-option[data-value="${val}"]`);
            if (opt) {
                opt.classList.add('selected');
                txt.textContent = opt.textContent.trim();
                txt.style.color = val ? 'var(--text-main)' : 'var(--text-muted)';
            } else if (text) {
                txt.textContent = text;
                txt.style.color = 'var(--text-main)';
            } else {
                const defaultOpt = optCont.querySelector('.select-option[data-value=""]');
                txt.textContent = defaultOpt ? defaultOpt.textContent.trim() : '-- Pilih --';
                txt.style.color = 'var(--text-muted)';
            }
        }
    };
})();
</script>
