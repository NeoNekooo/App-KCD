@extends('layouts.admin')

@section('content')
{{-- Toast Notifikasi --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="liveToast" class="toast hide align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

{{-- Header Halaman --}}
<div class="d-flex justify-content-between align-items-center py-3 mb-4">
    <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Kepegawaian /</span> Tugas Pegawai</h4>
    <div class="d-flex align-items-center">
        <form action="{{ route('admin.kepegawaian.tugas-pegawai.sync') }}" method="POST" class="me-3">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-sync me-1"></i> Sinkron Rombel</button>
        </form>
        <div class="badge bg-label-primary fs-6">{{ $tahunAktif }} - {{ $semesterAktif }}</div>
    </div>
</div>

{{-- Navigasi Tab --}}
<div class="nav-align-top mb-4">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-mapel">
                <i class="bx bx-book me-1"></i> Tugas Pokok
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-struktural">
                <i class="bx bx-briefcase me-1"></i> Tugas Tambahan
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- TAB 1: MATA PELAJARAN --}}
        <div class="tab-pane fade show active" id="tab-mapel">
            <div class="mb-3">
                <input type="text" id="search-mapel" class="form-control" placeholder="Cari nama guru...">
            </div>

            {{-- Container untuk AJAX Mapel --}}
            <div id="container-mapel">
                @include('admin.kepegawaian.tugas-pegawai.partials._table_mapel')
            </div>
        </div>

        {{-- TAB 2: JABATAN STRUKTURAL --}}
        <div class="tab-pane fade" id="tab-struktural">
            <div class="d-flex justify-content-between mb-3">
                <input type="text" id="search-struktural" class="form-control w-50" placeholder="Cari nama atau jabatan...">
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahStruktural">
                    <i class="bx bx-plus me-1"></i> Tambah Jabatan
                </button>
            </div>

            {{-- Container untuk AJAX Struktural --}}
            <div id="container-struktural">
                @include('admin.kepegawaian.tugas-pegawai.partials._table_struktural')
            </div>
        </div>
    </div>
</div>

@include('admin.kepegawaian.tugas-pegawai.partials._modals')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup CSRF untuk semua AJAX request
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ============================================================
    // 1. CORE FUNCTION: LOAD DATA DENGAN SPINNER
    // ============================================================
    function loadData(url, containerId) {
        // HTML Spinner Loading
        let spinner = `
            <div class="d-flex flex-column justify-content-center align-items-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted small">Memuat data...</div>
            </div>
        `;

        // Tampilkan spinner sebelum request selesai
        $(containerId).html(spinner);

        // Lakukan Request
        $.get(url, function(data) {
            $(containerId).html(data);
        }).fail(function() {
            $(containerId).html(`
                <div class="alert alert-danger text-center">
                    <i class="bx bx-error me-1"></i> Gagal memuat data. Silakan coba lagi.
                </div>
            `);
        });
    }

    // ============================================================
    // 2. LIVE SEARCH LOGIC
    // ============================================================
    let searchTimer;
    $(document).on('keyup', '#search-mapel, #search-struktural', function() {
        clearTimeout(searchTimer);

        let inputId = $(this).attr('id');
        let val = $(this).val();

        // Tentukan target container & parameter query
        let target, param;
        if (inputId === 'search-mapel') {
            target = '#container-mapel';
            param = 'search_mapel';
        } else {
            target = '#container-struktural';
            param = 'search_struktural';
        }

        // Delay search 500ms
        searchTimer = setTimeout(function() {
            let url = "{{ route('admin.kepegawaian.tugas-pegawai.index') }}?" + param + "=" + val;
            loadData(url, target);
        }, 500);
    });

    // ============================================================
    // 3. PAGINATION CLICK LOGIC (AJAX)
    // ============================================================
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault(); // Stop refresh halaman

        let url = $(this).attr('href');

        // Cari container parent terdekat (apakah ini tabel mapel atau struktural?)
        let container = $(this).closest('[id^="container-"]');

        if (url && container.length) {
            let containerId = '#' + container.attr('id');
            loadData(url, containerId);
        }
    });


    // ============================================================
    // 4. FITUR EDIT SK (INLINE)
    // ============================================================
    window.toggleEditSk = function(id) {
        $('.sk-input-group').addClass('d-none');
        $('.sk-text').removeClass('d-none');
        $(`.edit-sk-container[data-id="${id}"] .sk-text`).addClass('d-none');
        $(`#input-group-${id}`).removeClass('d-none').find('input').focus();
    };

    window.saveSk = function(id) {
        let val = $('#input-'+id).val();
        // Tampilkan loading kecil/disable button jika perlu

        $.post("{{ route('admin.kepegawaian.tugas-pegawai.update-sk') }}", { id: id, nomor_sk: val }, function(res) {
            showToast('Nomor SK berhasil disimpan');
            $(`.edit-sk-container[data-id="${id}"] .sk-text`)
                .html(`${val || 'Klik isi SK...'} <i class="bx bx-pencil small"></i>`)
                .removeClass('d-none');
            $(`#input-group-${id}`).addClass('d-none');
        }).fail(function() {
            showToast('Gagal menyimpan SK', 'danger');
        });
    };

    // ============================================================
    // 5. MODAL & HELPER FUNCTIONS
    // ============================================================

    // Detail Modal
    window.showDetail = function(id, nama) {
        $('#detailNamaGuru').text(nama);
        $('#isi-tabel-detail').html('<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div> Loading detail...</td></tr>');

        let modal = new bootstrap.Modal(document.getElementById('modalDetailTugas'));
        modal.show();

        $.get("{{ url('admin/kepegawaian/tugas-pegawai/detail') }}/" + id, function(data) {
            let html = '';
            if(data.length > 0) {
                data.forEach((item, index) => {
                    html += `<tr>
                        <td>${index + 1}</td>
                        <td>${item.tugas_pokok}</td>
                        <td>${item.kelas ?? '-'}</td>
                        <td class="text-center"><span class="badge bg-label-info">${item.jumlah_jam} Jam</span></td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center text-muted">Tidak ada rincian tugas.</td></tr>';
            }
            $('#isi-tabel-detail').html(html);
        });
    };

    // Cetak Modal
    window.openCetakModal = function(id, nama) {
        $('#targetNama').text(nama);
        $('#formCetakSk').attr('action', "{{ route('admin.kepegawaian.tugas-pegawai.cetak', ':id') }}".replace(':id', id));
        new bootstrap.Modal(document.getElementById('modalCetakSk')).show();
    };

    // Edit Struktural Modal
    window.editStruktural = function(id, nama, tugas, jam, sk) {
        $('#edit_nama_pegawai').text(nama);
        $('#edit_tugas_pokok').val(tugas);
        $('#edit_jumlah_jam').val(jam);
        $('#edit_nomor_sk').val(sk);

        let url = "{{ route('admin.kepegawaian.tugas-pegawai.update', ':id') }}".replace(':id', id);
        $('#formEditStruktural').attr('action', url);

        let modalEdit = new bootstrap.Modal(document.getElementById('modalEditStruktural'));
        modalEdit.show();
    };

    // Toast Helper
    window.showToast = function(msg, type = 'success') {
        const t = document.getElementById('liveToast');
        const m = document.getElementById('toastMessage');
        // Reset class list
        t.className = `toast align-items-center text-white border-0 bg-${type}`;
        m.innerText = msg;
        const toast = new bootstrap.Toast(t);
        toast.show();
    };

    // Confirm Delete
    window.confirmDeleteDetail = function(id) {
        Swal.fire({
            title: 'Hapus Jabatan?',
            text: "Data jabatan ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#ff3e1d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + id).submit();
            }
        });
    };
});
</script>

<style>
    .cursor-pointer { cursor: pointer; }
    .sk-text:hover { color: #696cff !important; text-decoration: underline; }
    .toast-container { pointer-events: none; }
    .toast { pointer-events: auto; }

    /* Style tambahan untuk transisi loading */
    .table-responsive { min-height: 200px; }
</style>
@endpush
