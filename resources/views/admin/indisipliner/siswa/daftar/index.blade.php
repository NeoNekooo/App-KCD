@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Indisipliner / Siswa /</span> Daftar Indisipliner</h4>

@if (session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    {{-- Header dengan Judul dan Tombol Aksi --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Pelanggaran Siswa</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInputPelanggaran">
            <i class="bx bx-plus me-1"></i> Input Pelanggaran
        </button>
    </div>

    {{-- Panel Filter --}}
    <div class="card-body border-top">
        <form action="{{ route('admin.indisipliner.siswa.daftar.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="rombel_id_filter">Filter Berdasarkan Kelas</label>
                    <select name="rombel_id" id="rombel_id_filter" class="form-select">
                        <option value="">- Semua Kelas -</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>{{ $rombel->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="nis_filter">Filter Berdasarkan Siswa</label>
                    <select name="nis" id="nis_filter" class="form-select" data-initial-nis="{{ request('nis') }}">
                        {{-- Opsi siswa akan diisi oleh JavaScript --}}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="search_filter">Cari Nama/NIPD</label>
                    <input type="search" class="form-control" name="search" id="search_filter" placeholder="Ketik di sini..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100"><i class="bx bx-search"></i> Cari</button>
                    <a href="{{ route('admin.indisipliner.siswa.daftar.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Filter"><i class="bx bx-refresh"></i></a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat Pelanggaran --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>NIPD</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Pelanggaran</th>
                    <th>Tanggal & Waktu</th>
                    <th>Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($pelanggaranList as $key => $pelanggaran)
                <tr>
                    <td>{{ $pelanggaranList->firstItem() + $key }}</td>
                    <td>{{ $pelanggaran->NIS }}</td>
                    <td><strong>{{ $pelanggaran->siswa->nama ?? 'Siswa Dihapus' }}</strong></td>
                    <td>{{ $pelanggaran->rombel->nama ?? '-' }}</td>
                    <td style="white-space: normal; min-width: 250px;">{{ $pelanggaran->detailPoin->nama ?? 'Tidak Diketahui' }}</td>
                    <td>{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}, {{ $pelanggaran->jam }}</td>
                    <td><span class="badge bg-danger rounded-pill">{{ $pelanggaran->poin }}</span></td>
                    <td>
                        <form action="{{ route('admin.indisipliner.siswa.daftar.destroy', $pelanggaran->ID) }}" method="POST" class="d-inline form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Data">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="bx bx-info-circle bx-lg text-muted mb-2"></i>
                        <p class="text-muted mb-0">Tidak ada data pelanggaran untuk ditampilkan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer dengan Paginasi --}}
    <div class="card-footer d-flex justify-content-between align-items-center">
        @if($pelanggaranList->total() > 0)
        <small class="text-muted">Menampilkan {{ $pelanggaranList->firstItem() }} sampai {{ $pelanggaranList->lastItem() }} dari {{ $pelanggaranList->total() }} hasil</small>
        @endif
        {{ $pelanggaranList->links() }}
    </div>
</div>

{{-- Memanggil Modal dari file terpisah --}}
@include('admin.indisipliner.siswa.daftar._modal-form')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('#rombel_id_filter, #nis_filter').select2({
        theme: "bootstrap-5",
        placeholder: "- Pilih -",
    });

    $('#modalInputPelanggaran .form-select').select2({
        theme: "bootstrap-5",
        dropdownParent: $('#modalInputPelanggaran'),
        placeholder: "- Pilih -",
    });

    function loadSiswa(rombelID, nisSelect, selectedNis, placeholder) {
        nisSelect.prop('disabled', true).html(`<option value="">- ${placeholder} -</option>`).select2();
        if (rombelID) {
            nisSelect.html('<option value="">- Memuat Siswa... -</option>').select2();
            $.ajax({
                url: "{{ url('admin/indisipliner-siswa/get-siswa-by-rombel') }}/" + rombelID,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    nisSelect.prop('disabled', false).empty().append('<option value="">- Semua Siswa -</option>');
                    $.each(data, function(key, value) {
                        var selected = (value.nipd == selectedNis) ? 'selected' : '';
                        nisSelect.append(`<option value="${value.nipd}" ${selected}>${value.nama}</option>`);
                    });
                    nisSelect.val(selectedNis).trigger('change');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);
                    nisSelect.html('<option value="">- Gagal Memuat -</option>').select2();
                }
            });
        }
    }

    // Filter di halaman utama
    var initialRombelID = $('#rombel_id_filter').val();
    var initialNis = $('#nis_filter').data('initial-nis') || null;

    if (initialRombelID) {
        loadSiswa(initialRombelID, $('#nis_filter'), initialNis, "Pilih Kelas Dulu");
    }

    $('#rombel_id_filter').on('change', function() {
        loadSiswa($(this).val(), $('#nis_filter'), null, "Semua Siswa");
    });

    // Untuk form di dalam modal
    $('#IDkelas').on('change', function() {
        var rombelID = $(this).val();
        var nisSelect = $('#NIS');
        if (rombelID) {
            nisSelect.prop('disabled', true).html('<option value="">- Memuat Siswa... -</option>').select2();
            $.ajax({
                url: "{{ url('admin/indisipliner-siswa/get-siswa-by-rombel') }}/" + rombelID,

                type: "GET",
                dataType: "json",
                success: function(data) {
                    nisSelect.prop('disabled', false).empty().append('<option value="">- Pilih Siswa -</option>');
                    $.each(data, function(key, value) {
                        nisSelect.append(`<option value="${value.nipd}">${value.nama} (${value.nipd})</option>`);
                    });
                    nisSelect.trigger('change');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);
                    nisSelect.html('<option value="">- Gagal Memuat -</option>').select2();
                }
            });
        } else {
            nisSelect.html('<option value="">- Pilih Kelas Terlebih Dahulu -</option>').prop('disabled', true).select2();
        }
    });

    // Inisialisasi Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Konfirmasi Hapus
    $('.form-delete').on('submit', function(event) {
        event.preventDefault();
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            this.submit();
        }
    });
});
</script>
@endpush
