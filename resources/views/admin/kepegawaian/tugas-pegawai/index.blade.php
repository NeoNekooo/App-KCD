@extends('layouts.admin')

@section('content')
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

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

<div class="nav-align-top mb-4">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-mapel">
                <i class="bx bx-book me-1"></i> Mata Pelajaran
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-struktural">
                <i class="bx bx-briefcase me-1"></i> Jabatan Struktural
            </button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-mapel">
            <div class="mb-3">
                <input type="text" id="search-mapel" class="form-control" placeholder="Cari guru mapel...">
            </div>
            <div id="container-mapel">
                @include('admin.kepegawaian.tugas-pegawai.partials._table_mapel')
            </div>
        </div>

        <div class="tab-pane fade" id="tab-struktural">
            <div class="d-flex justify-content-between mb-3">
                <input type="text" id="search-struktural" class="form-control w-50" placeholder="Cari nama atau jabatan...">
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahStruktural">
                    <i class="bx bx-plus me-1"></i> Tambah Jabatan
                </button>
            </div>
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
// Memastikan script jalan setelah jQuery siap
function initTugasPegawai() {
    if (typeof $ === 'undefined') {
        console.error("jQuery masih belum terbaca!");
        return;
    }

    // Setup CSRF untuk AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // --- 1. SEARCH LOGIC ---
    let searchTimer;
    $(document).on('keyup', '#search-mapel, #search-struktural', function() {
        clearTimeout(searchTimer);
        let id = $(this).attr('id');
        let target = (id === 'search-mapel') ? '#container-mapel' : '#container-struktural';
        let param = (id === 'search-mapel') ? 'search_mapel' : 'search_struktural';
        let val = $(this).val();

        searchTimer = setTimeout(function() {
            $.get("{{ route('admin.kepegawaian.tugas-pegawai.index') }}?" + param + "=" + val, function(data) {
                $(target).html(data);
            });
        }, 500);
    });

    // --- 2. EDIT SK INLINE ---
    window.toggleEditSk = function(id) {
        $('.sk-input-group').addClass('d-none');
        $('.sk-text').removeClass('d-none');
        $(`.edit-sk-container[data-id="${id}"] .sk-text`).addClass('d-none');
        $(`#input-group-${id}`).removeClass('d-none').find('input').focus();
    };

    window.saveSk = function(id) {
    let val = $('#input-'+id).val();

    $.ajax({
        url: "{{ route('admin.kepegawaian.tugas-pegawai.update-sk') }}",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // Mengambil token dari meta tag
            id: id,
            nomor_sk: val
        },
        success: function(response) {
            showToast('Nomor SK berhasil disimpan');
            $(`.edit-sk-container[data-id="${id}"] .sk-text`)
                .html(`${val || 'Isi SK...'} <i class="bx bx-pencil small"></i>`)
                .removeClass('d-none');
            $(`#input-group-${id}`).addClass('d-none');
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            showToast('Gagal menyimpan SK. Silahkan refresh halaman.', 'danger');
        }
    });
};

    // --- 3 & 4. MODAL EDIT & CETAK ---
    window.editStruktural = function(id, nama, tugas, jam, sk) {
        $('#edit_nama_pegawai').text(nama);
        $('#edit_tugas_pokok').val(tugas);
        $('#edit_jumlah_jam').val(jam);
        $('#edit_nomor_sk').val(sk);
        $('#formEditStruktural').attr('action', "{{ route('admin.kepegawaian.tugas-pegawai.update', ':id') }}".replace(':id', id));

        let modalEdit = new bootstrap.Modal(document.getElementById('modalEditStruktural'));
        modalEdit.show();
    };

    window.openCetakModal = function(id, nama) {
        $('#targetNama').text(nama);
        $('#formCetakSk').attr('action', "{{ route('admin.kepegawaian.tugas-pegawai.cetak', ':id') }}".replace(':id', id));

        let modalCetak = new bootstrap.Modal(document.getElementById('modalCetakSk'));
        modalCetak.show();
    };

    // --- 5. HAPUS (SweetAlert2) ---
    window.confirmDelete = function(e, form) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus data?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    };
}

// Jalankan saat halaman load
document.addEventListener('DOMContentLoaded', initTugasPegawai);

// Toast Helper
window.showToast = function(msg, type = 'success') {
    const t = document.getElementById('liveToast');
    const m = document.getElementById('toastMessage');
    if(t) {
        t.className = `toast align-items-center text-white border-0 bg-${type}`;
        m.innerText = msg;
        new bootstrap.Toast(t).show();
    }
};
</script>
<style>
    .cursor-pointer { cursor: pointer; }
    .sk-text:hover { color: #696cff !important; text-decoration: underline; }
</style>
@endpush
