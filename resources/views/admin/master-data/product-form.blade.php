@extends('layouts.admin')

@section('title', $product ? 'Edit Produk: ' . $product->name : 'Tambah Produk Baru')

@section('styles')
<style>
    .tab-btn-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.75rem 1.25rem;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        font-weight: 600;
        font-size: 0.9375rem;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .tab-btn-pill:hover {
        color: var(--text-main);
    }
    .tab-btn-pill.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }
    .tab-badge {
        font-size: 0.7rem;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        background: var(--bg-hover);
        color: var(--text-muted);
    }
    .tab-btn-pill.active .tab-badge {
        background: var(--primary-light);
        color: var(--primary);
    }
    .dropzone-box {
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        background: var(--bg-hover);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .dropzone-box:hover, .dropzone-box.dragover {
        border-color: var(--primary);
        background: var(--primary-light);
    }
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .gallery-card {
        position: relative;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
        background: var(--surface);
        box-shadow: var(--shadow-sm);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .gallery-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .gallery-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        display: block;
    }
    .gallery-card-footer {
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--surface);
        border-top: 1px solid var(--border-color);
    }
    .primary-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: var(--warning);
        color: #FFFFFF;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .variant-matrix-table th {
        background: var(--bg-hover);
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color);
    }
    .variant-matrix-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .attr-checkbox-card {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: var(--surface);
    }
    .attr-checkbox-card.selected {
        border-color: var(--primary);
        background: var(--bg-main);
    }
</style>
@endsection

@section('content')
<form id="productForm" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data">
    <input type="hidden" id="productId" name="id" value="{{ $product?->id }}">
    <input type="hidden" id="productType" name="product_type" value="{{ ($product && $product->variants->count() > 1) ? 'variant' : 'single' }}">
    <input type="hidden" id="primaryImageIndex" name="primary_image_index" value="0">
    <input type="hidden" id="primaryImageId" name="primary_image_id" value="{{ $product?->images->where('is_primary', true)->first()?->id }}">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <a href="{{ route('admin.products.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Kembali ke Master Produk</span>
                </a>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin: 0;">
                {{ $product ? 'Edit Katalog: ' . $product->name : 'Tambah Produk Baru' }}
            </h1>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" id="saveProductBtn">
                <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                <span id="saveProductBtnText">{{ $product ? 'Perbarui Produk' : 'Simpan Produk' }}</span>
            </button>
        </div>
    </div>

    <!-- Tab Headers -->
    <div class="panel-card" style="padding: 0; margin-bottom: 1.5rem;">
        <div style="display: flex; border-bottom: 1px solid var(--border-color); padding: 0 1rem; overflow-x: auto;">
            <button type="button" class="tab-btn-pill active" onclick="switchTab('tabInfo')">
                <i data-lucide="info" style="width: 18px; height: 18px;"></i>
                <span>1. Informasi Dasar</span>
            </button>
            <button type="button" class="tab-btn-pill" onclick="switchTab('tabGallery')">
                <i data-lucide="image" style="width: 18px; height: 18px;"></i>
                <span>2. Galeri Foto</span>
                <span class="tab-badge" id="imageCountBadge">{{ $product ? $product->images->count() : 0 }}</span>
            </button>
            <button type="button" class="tab-btn-pill" onclick="switchTab('tabVariants')">
                <i data-lucide="layers" style="width: 18px; height: 18px;"></i>
                <span>3. Harga, Stok & Varian</span>
                <span class="tab-badge" id="variantCountBadge">{{ $product ? $product->variants->count() : 1 }} SKU</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: INFORMASI DASAR -->
    <div id="tabInfo" class="tab-pane active" style="display: block;">
        <div class="panel-card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--text-main);">Informasi Utama Produk</h3>
            
            <div class="form-group">
                <label for="productName" class="form-label">Nama Produk <span style="color: var(--danger);">*</span></label>
                <input type="text" id="productName" name="name" class="form-control" placeholder="Contoh: iPhone 15 Pro Max 256GB" value="{{ old('name', $product?->name) }}" required>
                <span id="nameError" class="form-error" style="display: none;"></span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                <div class="form-group">
                    <label class="form-label">Kategori Produk</label>
                    @php
                        $catOptions = $categories->pluck('name', 'id')->toArray();
                    @endphp
                    <x-searchable-select id="category_id" name="category_id" placeholder="-- Pilih Kategori --" :options="$catOptions" :value="$product?->category_id" />
                </div>

                <div class="form-group">
                    <label class="form-label">Brand / Merek</label>
                    @php
                        $brandOptions = $brands->pluck('name', 'id')->toArray();
                    @endphp
                    <x-searchable-select id="brand_id" name="brand_id" placeholder="-- Pilih Brand --" :options="$brandOptions" :value="$product?->brand_id" />
                </div>

                <div class="form-group">
                    <label for="productIsActive" class="form-label">Status Penjualan</label>
                    <select id="productIsActive" name="is_active" class="form-control">
                        <option value="1" {{ ($product && $product->is_active) ? 'selected' : '' }}>Aktif (Tampilkan di Toko)</option>
                        <option value="0" {{ ($product && !$product->is_active) ? 'selected' : '' }}>Nonaktif (Draft / Sembunyikan)</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-top: 0.5rem;">
                <label for="productDescription" class="form-label">Deskripsi Lengkap Produk</label>
                <textarea id="productDescription" name="description" class="form-control" rows="6" placeholder="Tuliskan spesifikasi, keunggulan, dan detail produk...">{{ old('description', $product?->description) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" class="btn-primary" onclick="switchTab('tabGallery')">
                    <span>Lanjut: Galeri Foto</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- TAB 2: GALERI FOTO -->
    <div id="tabGallery" class="tab-pane" style="display: none;">
        <div class="panel-card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text-main);">Galeri Foto Produk</h3>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                Upload foto produk resolusi jernih. Pilih salah satu sebagai <strong>Foto Utama (Sampul)</strong>.
            </p>

            <!-- Dropzone -->
            <div class="dropzone-box" id="dropzoneBox" onclick="document.getElementById('imageFileInput').click()">
                <input type="file" id="imageFileInput" multiple accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="handleFileSelect(event)">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                    <div style="width: 54px; height: 54px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i data-lucide="upload-cloud"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">Klik untuk unggah atau tarik file foto ke sini</div>
                        <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">Format: JPG, PNG, WEBP (Maksimal 3MB per foto)</div>
                    </div>
                </div>
            </div>

            <!-- Existing & New Images Grid -->
            <div class="gallery-grid" id="galleryGrid">
                @if($product)
                    @foreach($product->images as $img)
                        <div class="gallery-card" id="existingImage_{{ $img->id }}" data-image-id="{{ $img->id }}">
                            @if($img->is_primary)
                                <div class="primary-badge"><i data-lucide="star" style="width: 12px; height: 12px;"></i> Utama</div>
                            @endif
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Foto Produk">
                            <div class="gallery-card-footer">
                                <label style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; cursor: pointer; color: var(--text-muted);">
                                    <input type="radio" name="primary_image_choice" value="existing_{{ $img->id }}" {{ $img->is_primary ? 'checked' : '' }} onchange="setPrimaryChoice('existing', {{ $img->id }})">
                                    <span>Utama</span>
                                </label>
                                <button type="button" onclick="markDeleteExistingImage({{ $img->id }})" style="background: transparent; border: none; color: var(--danger); cursor: pointer; padding: 0.2rem;" title="Hapus Foto">
                                    <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Container for deleted image IDs in edit mode -->
            <div id="deletedImagesContainer"></div>

            <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                <button type="button" class="btn-secondary" onclick="switchTab('tabInfo')">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Kembali</span>
                </button>
                <button type="button" class="btn-primary" onclick="switchTab('tabVariants')">
                    <span>Lanjut: Harga & Varian</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- TAB 3: HARGA, STOK & VARIAN -->
    <div id="tabVariants" class="tab-pane" style="display: none;">
        <div class="panel-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text-main);">Penetapan Harga & Varian SKU</h3>
                    <p style="font-size: 0.8125rem; color: var(--text-muted);">
                        Pilih apakah produk ini merupakan produk tunggal atau memiliki kombinasi varian (seperti Warna, Ukuran).
                    </p>
                </div>
                <!-- Mode Toggle -->
                <div style="display: inline-flex; background: var(--bg-hover); border-radius: 999px; padding: 0.3rem;">
                    <button type="button" id="toggleSingleBtn" onclick="setProductTypeMode('single')" style="border: none; border-radius: 999px; padding: 0.45rem 1rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                        Produk Tunggal (Single SKU)
                    </button>
                    <button type="button" id="toggleVariantBtn" onclick="setProductTypeMode('variant')" style="border: none; border-radius: 999px; padding: 0.45rem 1rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                        Produk Bervarian (Multi-Attribute)
                    </button>
                </div>
            </div>

            <!-- SECTION A: PRODUK TUNGGAL (SINGLE) -->
            <div id="singleProductSection">
                @php
                    $firstVariant = $product?->variants->first();
                @endphp
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; padding: 1.5rem; background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="singleSku" class="form-label">Kode SKU Produk <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="singleSku" name="single_sku" class="form-control" placeholder="Contoh: IPHONE-15PM-NAT" value="{{ $firstVariant?->sku ?? '' }}">
                        <span id="singleSkuError" class="form-error" style="display: none;"></span>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="singlePriceDisplay" class="form-label">Harga Jual (Rp) <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="singlePriceDisplay" class="form-control rupiah-input" placeholder="Rp 0" value="{{ $firstVariant ? 'Rp ' . number_format($firstVariant->price, 0, ',', '.') : '' }}" oninput="handleSinglePriceInput(this)" required>
                        <input type="hidden" id="singlePrice" name="single_price" value="{{ $firstVariant ? (int)$firstVariant->price : '' }}">
                        <span id="singlePriceError" class="form-error" style="display: none;"></span>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="singleStock" class="form-label">Stok Tersedia <span style="color: var(--danger);">*</span></label>
                        <input type="number" id="singleStock" name="single_stock" class="form-control" placeholder="0" min="0" value="{{ $firstVariant ? $firstVariant->stock : '' }}">
                        <span id="singleStockError" class="form-error" style="display: none;"></span>
                    </div>
                </div>
            </div>

            <!-- SECTION B: PRODUK BERVARIAN (MULTI-VARIANT MATRIX) -->
            <div id="variantProductSection" style="display: none;">
                <!-- Step 1: Attribute Selection -->
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <label class="form-label" style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 0;">
                            Pilih Atribut yang Digunakan:
                        </label>
                    </div>

                    <div id="attributesSelectorContainer">
                        @foreach($attributes as $attr)
                            <div class="attr-checkbox-card" id="attrCard_{{ $attr->id }}">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer; color: var(--text-main);">
                                        <input type="checkbox" class="attribute-enable-check" value="{{ $attr->id }}" data-name="{{ $attr->name }}" onchange="handleAttributeToggle({{ $attr->id }})">
                                        <span>{{ $attr->name }}</span>
                                    </label>
                                </div>
                                <div class="attribute-values-box" id="attrValuesBox_{{ $attr->id }}" style="display: none; padding-top: 0.5rem; border-top: 1px solid var(--border-color);">
                                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                                        @foreach($attr->values as $val)
                                            <label class="status-pill" style="background: var(--bg-hover); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.8125rem; padding: 0.35rem 0.65rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                                <input type="checkbox" class="attr-val-check val-check-{{ $attr->id }}" value="{{ $val->id }}" data-val-name="{{ $val->value }}" data-attr-id="{{ $attr->id }}">
                                                <span>{{ $val->value }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                        <button type="button" class="btn-primary" onclick="generateVariantMatrix()" style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);">
                            <i data-lucide="sparkles" style="width: 16px; height: 16px;"></i>
                            <span>Generate Kombinasi Matriks Varian</span>
                        </button>
                    </div>
                </div>

                <!-- Matrix Table Container -->
                <div id="matrixTableContainer" style="margin-top: 1.5rem; overflow-x: auto; display: none;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--text-main); margin: 0;">
                            Daftar Kombinasi Varian (<span id="variantCountLabel">0</span> Varian)
                        </h4>
                        <!-- Quick Fill Action -->
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="button" class="btn-secondary" onclick="applyBulkPrice()" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
                                Samakan Harga
                            </button>
                            <button type="button" class="btn-secondary" onclick="applyBulkStock()" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
                                Samakan Stok
                            </button>
                        </div>
                    </div>

                    <table class="variant-matrix-table" style="width: 100%; border-collapse: collapse; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--surface);">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">No</th>
                                <th>Kombinasi Varian</th>
                                <th style="width: 220px;">Kode SKU <span style="color: var(--danger);">*</span></th>
                                <th style="width: 180px;">Harga (Rp) <span style="color: var(--danger);">*</span></th>
                                <th style="width: 130px;">Stok <span style="color: var(--danger);">*</span></th>
                                <th style="width: 100px; text-align: center;">Status</th>
                                <th style="width: 60px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="variantMatrixBody">
                            <!-- In edit mode, existing variants will be populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <button type="button" class="btn-secondary" onclick="switchTab('tabGallery')">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Kembali: Galeri Foto</span>
                </button>
                <button type="submit" class="btn-primary">
                    <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                    <span>{{ $product ? 'Perbarui Produk' : 'Simpan Produk' }}</span>
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let uploadedFiles = [];
    const isEditMode = {{ $product ? 'true' : 'false' }};
    const existingVariants = @json($product ? $product->variants->load('attributeValues') : []);

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Ensure Tab 1 (Informasi Dasar) is opened by default
        switchTab('tabInfo');

        const initialType = $('#productType').val();
        setProductTypeMode(initialType);

        if (isEditMode && existingVariants.length > 1) {
            populateExistingVariants();
        }

        // Dropzone drag & drop events
        const dropzone = document.getElementById('dropzoneBox');
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                }, false);
            });
            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }, false);
        }
    });

    // Rupiah Formatter Helpers
    function formatRupiah(val, withPrefix = true) {
        if (val === null || val === undefined || val === '') return '';
        let str = val.toString().replace(/[^0-9]/g, '');
        if (!str) return '';
        let sisa = str.length % 3;
        let rupiah = str.substr(0, sisa);
        let ribuan = str.substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return withPrefix ? 'Rp ' + rupiah : rupiah;
    }

    function parseRupiah(val) {
        if (!val) return 0;
        return parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
    }

    function handleSinglePriceInput(el) {
        let raw = parseRupiah($(el).val());
        $('#singlePrice').val(raw > 0 ? raw : '');
        if (raw > 0) {
            $(el).val(formatRupiah(raw, true));
        } else if ($(el).val().trim() === '') {
            $(el).val('');
            $('#singlePrice').val('');
        } else {
            $(el).val('Rp 0');
        }
    }

    function handleVariantPriceInput(el, idx) {
        let raw = parseRupiah($(el).val());
        $(`#varPriceRaw_${idx}`).val(raw);
        if (raw > 0) {
            $(el).val(formatRupiah(raw, true));
        } else if ($(el).val().trim() === '') {
            $(el).val('');
            $(`#varPriceRaw_${idx}`).val(0);
        } else {
            $(el).val('Rp 0');
        }
    }

    // Tab Switching
    function switchTab(tabId) {
        $('.tab-pane').hide().removeClass('active');
        $(`#${tabId}`).show().addClass('active');
        $('.tab-btn-pill').removeClass('active');
        $(`button[onclick="switchTab('${tabId}')"]`).addClass('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Product Type Mode Switch
    function setProductTypeMode(mode) {
        $('#productType').val(mode);
        if (mode === 'single') {
            $('#toggleSingleBtn').css({ background: 'var(--primary)', color: '#FFFFFF' });
            $('#toggleVariantBtn').css({ background: 'transparent', color: 'var(--text-muted)' });
            $('#singleProductSection').show();
            $('#variantProductSection').hide();
        } else {
            $('#toggleVariantBtn').css({ background: 'var(--primary)', color: '#FFFFFF' });
            $('#toggleSingleBtn').css({ background: 'transparent', color: 'var(--text-muted)' });
            $('#singleProductSection').hide();
            $('#variantProductSection').show();
        }
    }

    let selectedPrimary = {
        type: isEditMode && {{ ($product && $product->images->where('is_primary', true)->first()) ? 'true' : 'false' }} ? 'existing' : 'new',
        target: isEditMode ? '{{ $product?->images->where("is_primary", true)->first()?->id }}' : 0
    };

    // Image Upload Handling
    function handleFileSelect(e) {
        handleFiles(e.target.files);
        e.target.value = ''; // Reset input to allow selecting same file again
    }

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                uploadedFiles.push(file);
                previewNewImage(file, uploadedFiles.length - 1);
            }
        });
        updateImageCountBadge();
    }

    function previewNewImage(file, index) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const card = $(`
                <div class="gallery-card new-image-card" id="newImage_${index}" data-index="${index}">
                    <img src="${e.target.result}" alt="Preview Foto">
                    <div class="gallery-card-footer">
                        <label style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; cursor: pointer; color: var(--text-muted);">
                            <input type="radio" name="primary_image_choice" value="new_${index}" onchange="setPrimaryChoice('new', ${index})">
                            <span>Utama</span>
                        </label>
                        <button type="button" onclick="removeNewImage(${index})" style="background: transparent; border: none; color: var(--danger); cursor: pointer; padding: 0.2rem;" title="Hapus Foto">
                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                        </button>
                    </div>
                </div>
            `);
            $('#galleryGrid').append(card);
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            // If no primary is selected yet, select this first image
            if ($('input[name="primary_image_choice"]:checked').length === 0) {
                card.find('input[type="radio"]').prop('checked', true);
                setPrimaryChoice('new', index);
            }
        };
        reader.readAsDataURL(file);
    }

    function setPrimaryChoice(type, target) {
        selectedPrimary = { type: type, target: target };
        $('.primary-badge').remove();
        if (type === 'existing') {
            $('#primaryImageId').val(target);
            $('#primaryImageIndex').val('');
            $(`#existingImage_${target}`).prepend('<div class="primary-badge"><i data-lucide="star" style="width: 12px; height: 12px;"></i> Utama</div>');
        } else {
            $('#primaryImageId').val('');
            $('#primaryImageIndex').val(target);
            $(`#newImage_${target}`).prepend('<div class="primary-badge"><i data-lucide="star" style="width: 12px; height: 12px;"></i> Utama</div>');
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeNewImage(index) {
        $(`#newImage_${index}`).remove();
        uploadedFiles[index] = null;
        updateImageCountBadge();

        // If removed image was selected as primary, pick another available image
        if (selectedPrimary.type === 'new' && parseInt(selectedPrimary.target) === parseInt(index)) {
            const remainingExisting = $('.gallery-card:not(.new-image-card)');
            const remainingNew = $('.new-image-card');
            if (remainingExisting.length > 0) {
                const firstExistingId = $(remainingExisting[0]).data('image-id');
                $(`input[name="primary_image_choice"][value="existing_${firstExistingId}"]`).prop('checked', true);
                setPrimaryChoice('existing', firstExistingId);
            } else if (remainingNew.length > 0) {
                const firstNewIdx = $(remainingNew[0]).data('index');
                $(`input[name="primary_image_choice"][value="new_${firstNewIdx}"]`).prop('checked', true);
                setPrimaryChoice('new', firstNewIdx);
            } else {
                selectedPrimary = { type: null, target: null };
                $('#primaryImageId').val('');
                $('#primaryImageIndex').val('');
            }
        }
    }

    function markDeleteExistingImage(id) {
        $(`#existingImage_${id}`).remove();
        $('#deletedImagesContainer').append(`<input type="hidden" name="delete_image_ids[]" value="${id}">`);
        updateImageCountBadge();

        // If deleted existing image was primary, pick another available image
        if (selectedPrimary.type === 'existing' && parseInt(selectedPrimary.target) === parseInt(id)) {
            const remainingExisting = $('.gallery-card:not(.new-image-card)');
            const remainingNew = $('.new-image-card');
            if (remainingExisting.length > 0) {
                const firstExistingId = $(remainingExisting[0]).data('image-id');
                $(`input[name="primary_image_choice"][value="existing_${firstExistingId}"]`).prop('checked', true);
                setPrimaryChoice('existing', firstExistingId);
            } else if (remainingNew.length > 0) {
                const firstNewIdx = $(remainingNew[0]).data('index');
                $(`input[name="primary_image_choice"][value="new_${firstNewIdx}"]`).prop('checked', true);
                setPrimaryChoice('new', firstNewIdx);
            } else {
                selectedPrimary = { type: null, target: null };
                $('#primaryImageId').val('');
                $('#primaryImageIndex').val('');
            }
        }
    }

    function updateImageCountBadge() {
        const total = $('.gallery-card').length;
        $('#imageCountBadge').text(total);
    }

    // Attribute Toggle
    function handleAttributeToggle(attrId) {
        const checkbox = $(`.attribute-enable-check[value="${attrId}"]`);
        const isChecked = checkbox.is(':checked');
        const box = $(`#attrValuesBox_${attrId}`);
        const card = $(`#attrCard_${attrId}`);
        
        if (isChecked) {
            box.slideDown(150);
            card.addClass('selected');
        } else {
            box.slideUp(150);
            card.removeClass('selected');
            box.find('.attr-val-check').prop('checked', false);
        }
    }

    // Generate Variant Matrix (Cartesian Product)
    function generateVariantMatrix() {
        const selectedAttributes = [];
        
        $('.attribute-enable-check:checked').each(function() {
            const attrId = $(this).val();
            const attrName = $(this).data('name');
            const values = [];
            
            $(`.val-check-${attrId}:checked`).each(function() {
                values.push({
                    id: $(this).val(),
                    name: $(this).data('val-name')
                });
            });

            if (values.length > 0) {
                selectedAttributes.push({
                    id: attrId,
                    name: attrName,
                    values: values
                });
            }
        });

        if (selectedAttributes.length === 0) {
            showToast('error', 'Peringatan', 'Silakan pilih minimal 1 atribut dan centang nilai opsinya.');
            return;
        }

        // Cartesian product
        let combinations = [[]];
        selectedAttributes.forEach(attr => {
            const temp = [];
            combinations.forEach(combo => {
                attr.values.forEach(val => {
                    temp.push(combo.concat({
                        attrName: attr.name,
                        valId: val.id,
                        valName: val.name
                    }));
                });
            });
            combinations = temp;
        });

        renderMatrixTable(combinations);
    }

    function renderMatrixTable(combinations) {
        const tbody = $('#variantMatrixBody');
        tbody.empty();
        
        const productName = $('#productName').val().trim();
        const baseSlug = productName ? productName.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 8) : 'SKU';

        combinations.forEach((combo, idx) => {
            const label = combo.map(c => `${c.attrName}: <strong>${escapeHtml(c.valName)}</strong>`).join(' | ');
            const valIds = combo.map(c => c.valId);
            const skuSuffix = combo.map(c => c.valName.toUpperCase().replace(/[^A-Z0-9]/g, '')).join('-');
            const generatedSku = `${baseSlug}-${skuSuffix}`;

            let hiddenValuesInputs = '';
            valIds.forEach(id => {
                hiddenValuesInputs += `<input type="hidden" name="variants[${idx}][attribute_values][]" value="${id}">`;
            });

            const row = $(`
                <tr id="variantRow_${idx}">
                    <td style="text-align: center; color: var(--text-muted); font-size: 0.8125rem;">${idx + 1}</td>
                    <td>
                        <div style="font-size: 0.875rem; color: var(--text-main);">${label}</div>
                        ${hiddenValuesInputs}
                    </td>
                    <td>
                        <input type="text" name="variants[${idx}][sku]" class="form-control var-sku" value="${generatedSku}" required style="font-size: 0.8125rem; padding: 0.35rem 0.6rem;">
                    </td>
                    <td>
                        <input type="text" class="form-control rupiah-input var-price-input" placeholder="Rp 0" oninput="handleVariantPriceInput(this, ${idx})" required style="font-size: 0.8125rem; padding: 0.35rem 0.6rem;">
                        <input type="hidden" id="varPriceRaw_${idx}" name="variants[${idx}][price]" class="var-price-raw" value="0">
                    </td>
                    <td>
                        <input type="number" name="variants[${idx}][stock]" class="form-control var-stock" placeholder="0" min="0" required style="font-size: 0.8125rem; padding: 0.35rem 0.6rem;">
                    </td>
                    <td style="text-align: center;">
                        <input type="hidden" name="variants[${idx}][is_active]" value="1">
                        <span class="status-pill" style="background: var(--success-bg); color: var(--success); font-size: 0.75rem;">AKTIF</span>
                    </td>
                    <td style="text-align: center;">
                        <button type="button" onclick="removeVariantRow(${idx})" style="background: transparent; border: none; color: var(--danger); cursor: pointer;" title="Hapus Varian">
                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                        </button>
                    </td>
                </tr>
            `);
            tbody.append(row);
        });

        $('#matrixTableContainer').slideDown(200);
        $('#variantCountLabel').text(combinations.length);
        $('#variantCountBadge').text(combinations.length + ' SKU');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function populateExistingVariants() {
        const tbody = $('#variantMatrixBody');
        tbody.empty();

        existingVariants.forEach((v, idx) => {
            const label = v.attribute_values.map(val => `<strong>${escapeHtml(val.value)}</strong>`).join(' | ');
            
            let hiddenValuesInputs = '';
            v.attribute_values.forEach(val => {
                hiddenValuesInputs += `<input type="hidden" name="variants[${idx}][attribute_values][]" value="${val.id}">`;
                // check checkbox in selector
                $(`.val-check-${val.attribute_id}[value="${val.id}"]`).prop('checked', true);
                $(`.attribute-enable-check[value="${val.attribute_id}"]`).prop('checked', true);
                $(`#attrValuesBox_${val.attribute_id}`).show();
                $(`#attrCard_${val.attribute_id}`).addClass('selected');
            });

            const rawPrice = parseInt(v.price) || 0;
            const formattedPrice = formatRupiah(rawPrice, true);

            const row = $(`
                <tr id="variantRow_${idx}">
                    <td style="text-align: center; color: var(--text-muted); font-size: 0.8125rem;">${idx + 1}</td>
                    <td>
                        <div style="font-size: 0.875rem; color: var(--text-main);">${label || 'Varian ' + (idx + 1)}</div>
                        <input type="hidden" name="variants[${idx}][id]" value="${v.id}">
                        ${hiddenValuesInputs}
                    </td>
                    <td>
                        <input type="text" name="variants[${idx}][sku]" class="form-control var-sku" value="${escapeHtml(v.sku)}" required style="font-size: 0.8125rem; padding: 0.35rem 0.6rem;">
                    </td>
                    <td>
                        <input type="text" class="form-control rupiah-input var-price-input" value="${formattedPrice}" oninput="handleVariantPriceInput(this, ${idx})" required style="font-size: 0.8125rem; padding: 0.35rem 0.6rem;">
                        <input type="hidden" id="varPriceRaw_${idx}" name="variants[${idx}][price]" class="var-price-raw" value="${rawPrice}">
                    </td>
                    <td>
                        <input type="number" name="variants[${idx}][stock]" class="form-control var-stock" value="${v.stock}" min="0" required style="font-size: 0.8125rem; padding: 0.35rem 0.6rem;">
                    </td>
                    <td style="text-align: center;">
                        <input type="hidden" name="variants[${idx}][is_active]" value="${v.is_active ? 1 : 0}">
                        <span class="status-pill" style="background: ${v.is_active ? 'var(--success-bg)' : 'var(--danger-bg)'}; color: ${v.is_active ? 'var(--success)' : 'var(--danger)'}; font-size: 0.75rem;">
                            ${v.is_active ? 'AKTIF' : 'NONAKTIF'}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <button type="button" onclick="removeVariantRow(${idx})" style="background: transparent; border: none; color: var(--danger); cursor: pointer;" title="Hapus Varian">
                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                        </button>
                    </td>
                </tr>
            `);
            tbody.append(row);
        });

        $('#matrixTableContainer').show();
        $('#variantCountLabel').text(existingVariants.length);
        $('#variantCountBadge').text(existingVariants.length + ' SKU');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeVariantRow(idx) {
        $(`#variantRow_${idx}`).remove();
        const count = $('#variantMatrixBody tr').length;
        $('#variantCountLabel').text(count);
        $('#variantCountBadge').text(count + ' SKU');
    }

    function applyBulkPrice() {
        const price = prompt('Masukkan harga (Rp) untuk semua varian:');
        if (price !== null && price.trim() !== '') {
            const raw = parseRupiah(price);
            const formatted = formatRupiah(raw, true);
            $('.var-price-input').val(formatted);
            $('.var-price-raw').val(raw);
        }
    }

    function applyBulkStock() {
        const stock = prompt('Masukkan jumlah stok untuk semua varian:');
        if (stock !== null && stock.trim() !== '') {
            $('.var-stock').val(parseInt(stock.replace(/[^0-9]/g, '')) || 0);
        }
    }

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    // Submit Form
    function handleFormSubmit(e) {
        e.preventDefault();

        // Sync Rupiah raw values before sending
        if ($('#productType').val() === 'single') {
            $('#singlePrice').val(parseRupiah($('#singlePriceDisplay').val()));
        } else {
            $('.var-price-input').each(function(idx) {
                let raw = parseRupiah($(this).val());
                $(`#varPriceRaw_${idx}`).val(raw);
            });
        }

        $('#saveProductBtn').prop('disabled', true);
        $('#saveProductBtnText').text('Menyimpan...');
        showPreloader('Menyimpan Produk', 'Sedang memproses katalog, foto, dan varian...');

        const formData = new FormData($('#productForm')[0]);
        // Remove any auto-captured images from FormData
        formData.delete('images[]');

        // Append non-null files and calculate actual 0-based index for primary image
        let validFileIdx = 0;
        let finalPrimaryIndex = null;

        uploadedFiles.forEach((file, origIdx) => {
            if (file) {
                formData.append('images[]', file);
                if (selectedPrimary.type === 'new' && parseInt(selectedPrimary.target) === parseInt(origIdx)) {
                    finalPrimaryIndex = validFileIdx;
                }
                validFileIdx++;
            }
        });

        if (selectedPrimary.type === 'new' && finalPrimaryIndex !== null) {
            formData.set('primary_image_index', finalPrimaryIndex);
            formData.set('primary_image_id', '');
        } else if (selectedPrimary.type === 'existing') {
            formData.set('primary_image_id', selectedPrimary.target);
            formData.set('primary_image_index', '');
        }

        const id = $('#productId').val();
        let url = id ? `/admin/master-data/products/${id}` : `{{ route('admin.products.store') }}`;
        if (id) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hidePreloader();
                showToast('success', 'Berhasil', response.message);
                setTimeout(() => {
                    window.location.href = response.redirect_url || "{{ route('admin.products.index') }}";
                }, 800);
            },
            error: function(xhr) {
                hidePreloader();
                $('#saveProductBtn').prop('disabled', false);
                $('#saveProductBtnText').text(id ? 'Perbarui Produk' : 'Simpan Produk');
                
                let errorMsg = 'Terjadi kesalahan sistem.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.errors) {
                        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        errorMsg = xhr.responseJSON.errors[firstKey][0];
                    }
                }
                showToast('error', 'Gagal Menyimpan', errorMsg);
            }
        });
    }
</script>
@endpush
