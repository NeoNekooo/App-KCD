@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kesiswaan /</span> Cetak Kartu Massal</h4>

<div class="card">
    <div class="card-header">
        <div class="row justify-content-between align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Pilih Kelas untuk Mencetak Kartu ID</h5>
            </div>
            <div class="col-md-4">
                <input type="text" id="search-input" class="form-control" placeholder="Cari nama kelas...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rombel-table-body">
                    @forelse ($rombels as $rombel)
                    {{-- Menambahkan class dan atribut data untuk pencarian --}}
                    <tr class="rombel-row" data-nama-rombel="{{ strtolower($rombel->nama) }}">
                        <td><strong>{{ $rombel->nama }}</strong></td>
                        <td class="text-center">
                            <a href="{{ route('admin.kesiswaan.siswa.cetak_massal_show', $rombel->id) }}" class="btn btn-primary" target="_blank">
                                <i class="bx bx-printer me-1"></i> Cetak Kartu Kelas Ini
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">Tidak ada data kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- === AWAL BAGIAN BARU: SCRIPT PENCARIAN REALTIME === --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('rombel-table-body');
        const rombelRows = tableBody.querySelectorAll('.rombel-row');

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            rombelRows.forEach(row => {
                const rombelName = row.getAttribute('data-nama-rombel');
                
                // Jika nama kelas mengandung kata kunci pencarian, tampilkan barisnya.
                // Jika tidak, sembunyikan.
                if (rombelName.includes(searchTerm)) {
                    row.style.display = ''; // Menampilkan baris
                } else {
                    row.style.display = 'none'; // Menyembunyikan baris
                }
            });
        });
    });
</script>
@endpush