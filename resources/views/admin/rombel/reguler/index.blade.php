@extends('layouts.admin')

@section('content')
{{-- Header Halaman --}}
<h4 class="fw-bold py-3 mb-4">
<span class="text-muted fw-light">Rombongan Belajar /</span> Reguler
</h4>

{{-- Konten Card --}}
<div class="card">
    {{-- Card Header sekarang berisi Toolbar Aksi --}}
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Rombel Reguler</h5>
            <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                {{-- Tombol Aksi Baru --}}
                <a href="{{ route('admin.rombel.reguler.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah
                </a>
                <button type="button" class="btn btn-secondary">
                    <i class="bx bx-pencil me-1"></i> Ubah
                </button>
                <button type="button" class="btn btn-success">
                    <i class="bx bx-save me-1"></i> Simpan
                </button>
                <button type="button" class="btn btn-danger">
                    <i class="bx bx-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
    
    {{-- Div untuk tabel responsif --}}
    <div class="table-responsive text-nowrap"> 
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Kolom Checkbox untuk seleksi --}}
                    <th style="width: 1%;">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </th>
                    <th>Jenis Rombel</th>
                    <th>Tingkat</th>
                    <th>Prog/Komp Keahlian</th>
                    <th>Kurikulum</th>
                    <th>Nama Rombel</th>
                    <th>Wali/Guru Kelas</th>
                    <th>Ruang</th>
                    <th>Moving Class</th>
                    <th>Kebutuhan Khusus</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($rombels as $rombel)
                    <tr>
                        {{-- Checkbox untuk setiap baris data --}}
                        <td>
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $rombel->id }}">
                        </td>
                        <td>{{ $rombel->jenis_rombel }}</td>
                        <td>{{ $rombel->tingkat }}</td>
                        <td>{{ $rombel->jurusan->nama_jurusan ?? 'N/A' }}</td>
                        <td>{{ $rombel->kurikulum->nama_kurikulum ?? 'N/A' }}</td>
                        <td><strong>{{ $rombel->nama_rombel }}</strong></td>
                        <td>{{ $rombel->wali->nama ?? 'Belum ada' }}</td>
                        <td>{{ $rombel->ruang }}</td>
                        <td>
                            @if ($rombel->is_moving_class)
                                <span class="badge bg-label-success me-1">Ya</span>
                            @else
                                <span class="badge bg-label-secondary me-1">Tidak</span>
                            @endif
                        </td>
                        <td>
                            @if ($rombel->melayani_kebutuhan_khusus)
                                <span class="badge bg-label-info me-1">Ya</span>
                            @else
                                <span class="badge bg-label-secondary me-1">Tidak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{-- Colspan disesuaikan dengan jumlah kolom (10) --}}
                        <td colspan="10" class="text-center">Belum ada data rombel reguler.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Link Paginasi --}}
    <div class="card-footer">
        {{ $rombels->links() }}
    </div>
</div>

@endsection

@push('scripts')
{{-- Script untuk fungsionalitas checkbox "select all" --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
const selectAllCheckbox = document.getElementById('selectAll');
const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAllCheckbox.addEventListener(&#39;change&#39;, function () {
        rowCheckboxes.forEach(checkbox =&gt; {
            checkbox.checked = this.checked;
        });
    });
});

</script>

@endpush