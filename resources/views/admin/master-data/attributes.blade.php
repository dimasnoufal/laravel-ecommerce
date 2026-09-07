@extends('layouts.admin')

@section('title', 'Attribute Management')

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <div>
            <h2 class="panel-title" style="font-size: 1.25rem;">Attribute Management</h2>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola atribut produk (seperti Warna, Ukuran, Kapasitas) beserta daftar nilainya untuk varian SKU.
            </p>
        </div>
        <button type="button" class="btn-primary" onclick="openCreateAttributeDrawer()">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            <span>Tambah Atribut</span>
        </button>
    </div>
    
    <div class="panel-content">
        <div class="table-responsive">
            <table id="attributesTable" class="dataTable display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama & Slug Atribut</th>
                        <th>Nilai / Opsi Pilihan (Values)</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($initialAttributes) && $initialAttributes->count() > 0)
                        @foreach($initialAttributes as $attr)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                                        <div class="category-icon-box" style="background: var(--primary-light); color: var(--primary);">
                                            <i data-lucide="sliders" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-main);">{{ $attr->name }}</div>
                                            <code class="code-pill" style="font-size: 0.7rem; padding: 0.1rem 0.35rem;">{{ $attr->slug }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($attr->values->isEmpty())
                                        <span style="color: var(--text-light); font-size: 0.8125rem;">Belum ada nilai</span>
                                    @else
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                            @foreach($attr->values->take(6) as $val)
                                                <span class="status-pill" style="background: var(--bg-hover); color: var(--text-main); font-weight: 500; font-size: 0.75rem;">{{ $val->value }}</span>
                                            @endforeach
                                            @if($attr->values->count() > 6)
                                                <span class="status-pill" style="background: var(--primary-light); color: var(--primary); font-weight: 600; font-size: 0.75rem;">+{{ $attr->values->count() - 6 }} lainnya</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="table-actions">
                                        <button type="button" class="tbl-btn tbl-btn-edit" onclick="editAttribute({{ $attr->id }})" title="Edit Atribut">
                                            <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteAttribute({{ $attr->id }}, '{{ addslashes(htmlspecialchars($attr->name, ENT_QUOTES, 'UTF-8')) }}')" title="Hapus Atribut">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Slide-Over Drawer for Create / Edit Attribute -->
<x-drawer id="attributeDrawer" title="Tambah Atribut Baru" width="500px">
    <form id="attributeForm" onsubmit="handleAttributeSubmit(event)">
        <input type="hidden" id="attributeId" name="id">
        
        <div class="form-group">
            <label for="attributeName" class="form-label">Nama Atribut <span style="color: var(--danger);">*</span></label>
            <input type="text" id="attributeName" name="name" class="form-control" placeholder="Contoh: Warna, Ukuran, Kapasitas" required>
            <span id="attributeNameError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label class="form-label">Nilai / Opsi Atribut (Values)</label>
            <p style="font-size: 0.775rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                Ketik nilai lalu tekan <strong>Enter</strong> atau klik tombol <strong>+ Tambah</strong>.
            </p>
            
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                <input type="text" id="newValueInput" class="form-control" placeholder="Contoh: Hitam, XL, 256GB" onkeypress="handleValueInputKeypress(event)">
                <button type="button" class="btn-secondary" onclick="addValueFromInput()" style="white-space: nowrap;">
                    <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                    <span>Tambah</span>
                </button>
            </div>

            <!-- Value Chips Container -->
            <div id="valuesChipsContainer" style="display: flex; flex-wrap: wrap; gap: 0.5rem; min-height: 48px; padding: 0.75rem; background: var(--bg-hover); border: 1px dashed var(--border-color); border-radius: 8px;">
                <span id="emptyValuesNotice" style="color: var(--text-light); font-size: 0.8125rem; font-style: italic;">Belum ada nilai yang ditambahkan</span>
            </div>
            <span id="attributeValuesError" class="form-error" style="display: none;"></span>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn-secondary" onclick="closeDrawer('attributeDrawer')">Batal</button>
            <button type="submit" class="btn-primary" id="saveAttributeBtn">
                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                <span id="saveAttributeBtnText">Simpan Atribut</span>
            </button>
        </div>
    </form>
</x-drawer>
@endsection

@push('scripts')
<script>
    let attributesTable;
    let currentValues = [];
    
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        attributesTable = $('#attributesTable').DataTable({
            processing: true,
            serverSide: true,
            deferLoading: {{ $totalAttributes ?? 0 }},
            order: [],
            ajax: "{{ route('admin.attributes.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'name_badge', name: 'name' },
                { data: 'values_pills', name: 'values_pills', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: createDataTableLanguage('Memuat Data Atribut', 'Mengambil daftar atribut dan variasi pilihan...', {
                searchPlaceholder: "Cari atribut..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });
    });

    function renderValuesChips() {
        const container = $('#valuesChipsContainer');
        container.empty();
        
        if (currentValues.length === 0) {
            container.append('<span id="emptyValuesNotice" style="color: var(--text-light); font-size: 0.8125rem; font-style: italic;">Belum ada nilai yang ditambahkan</span>');
            return;
        }

        currentValues.forEach((val, idx) => {
            const chip = $(`
                <div class="status-pill" style="background: var(--surface); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.8125rem; padding: 0.35rem 0.65rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span>${escapeHtml(val)}</span>
                    <input type="hidden" name="values[]" value="${escapeHtml(val)}">
                    <button type="button" onclick="removeValueChip(${idx})" style="background: transparent; border: none; padding: 0; cursor: pointer; color: var(--text-light); display: flex; align-items: center;" title="Hapus nilai">
                        <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>
            `);
            container.append(chip);
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function addValueFromInput() {
        const input = $('#newValueInput');
        const val = input.val().trim();
        if (val) {
            if (!currentValues.includes(val)) {
                currentValues.push(val);
                renderValuesChips();
            }
            input.val('').focus();
        }
    }

    function handleValueInputKeypress(e) {
        if (e.which === 13) {
            e.preventDefault();
            addValueFromInput();
        }
    }

    function removeValueChip(idx) {
        currentValues.splice(idx, 1);
        renderValuesChips();
    }

    function openCreateAttributeDrawer() {
        $('#attributeForm')[0].reset();
        $('#attributeId').val('');
        $('#attributeNameError').hide();
        $('#attributeValuesError').hide();
        currentValues = [];
        renderValuesChips();
        $('#saveAttributeBtn').prop('disabled', false);
        $('#saveAttributeBtnText').text('Simpan Atribut');
        openDrawer('attributeDrawer', 'Tambah Atribut Baru', 'Buat atribut dan tentukan nilai pilihannya');
    }

    function editAttribute(id) {
        showPreloader('Mengambil Data Atribut', 'Menyiapkan opsi nilai dan spesifikasi atribut...');
        $.get(`/admin/master-data/attributes/${id}/edit`, function(response) {
            hidePreloader();
            $('#attributeForm')[0].reset();
            $('#attributeNameError').hide();
            $('#attributeValuesError').hide();
            $('#saveAttributeBtn').prop('disabled', false);
            $('#saveAttributeBtnText').text('Perbarui Atribut');
            
            const attr = response.data;
            $('#attributeId').val(attr.id);
            $('#attributeName').val(attr.name);
            
            currentValues = attr.values ? attr.values.map(v => v.value) : [];
            renderValuesChips();
            
            openDrawer('attributeDrawer', 'Edit Atribut', 'Perbarui nama atribut dan nilai opsinya');
        }).fail(function() {
            hidePreloader();
            showToast('error', 'Gagal', 'Tidak dapat mengambil data atribut.');
        });
    }

    function handleAttributeSubmit(e) {
        e.preventDefault();
        
        let id = $('#attributeId').val();
        $('#saveAttributeBtn').prop('disabled', true);
        $('#saveAttributeBtnText').text('Menyimpan...');
        $('#attributeNameError').hide();
        $('#attributeValuesError').hide();
        
        showPreloader(id ? 'Memperbarui Atribut' : 'Menyimpan Atribut', 'Sedang menyimpan atribut dan pilihan nilainya ke database...');
        
        let url = id ? `/admin/master-data/attributes/${id}` : `{{ route('admin.attributes.store') }}`;
        let type = id ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: type,
            data: $('#attributeForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeDrawer('attributeDrawer');
                showToast('success', 'Berhasil', response.message);
                attributesTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                $('#saveAttributeBtn').prop('disabled', false);
                $('#saveAttributeBtnText').text(id ? 'Perbarui Atribut' : 'Simpan Atribut');
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    if (xhr.responseJSON.errors.name) {
                        $('#attributeNameError').text(xhr.responseJSON.errors.name[0]).show();
                    }
                } else {
                    showToast('error', 'Gagal', 'Terjadi kesalahan sistem saat menyimpan atribut.');
                }
            }
        });
    }

    function deleteAttribute(id, name) {
        showDeleteConfirm({
            title: 'Konfirmasi Hapus Atribut',
            itemName: name,
            showReason: false,
            confirmBtnText: 'Ya, Hapus Atribut',
            onConfirm: function(reason) {
                showPreloader('Menghapus Atribut', 'Memproses penghapusan atribut dan variasi nilainya...');
                $.ajax({
                    url: `/admin/master-data/attributes/${id}`,
                    type: 'DELETE',
                    data: { reason: reason },
                    success: function(response) {
                        hidePreloader();
                        showToast('success', 'Berhasil', response.message);
                        attributesTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        hidePreloader();
                        let msg = 'Gagal menghapus atribut.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast('error', 'Gagal', msg);
                    }
                });
            }
        });
    }
</script>
@endpush
