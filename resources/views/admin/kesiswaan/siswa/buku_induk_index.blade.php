@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Kesiswaan /</span> Buku Induk
</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Buku Induk - Pilih Kelas</h5>
            <form action="{{ route('admin.kesiswaan.buku_induk.index') }}" method="GET" class="d-flex">
                <select name="rombel_id" class="form-select form-select-sm me-2">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ (isset($selectedRombel) && $selectedRombel->id == $r->id) || request('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm">Tampilkan</button>
            </form>
        </div>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width:1%"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                    <th>Nama</th>
                    <th>NIPD / NISN</th>
                    <th>NIK</th>
                    <th>Tempat, Tgl Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Kelas</th>
                    <th style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $siswa)
                    <tr>
                        <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $siswa->id }}"></td>
                        <td><a href="{{ route('admin.kesiswaan.siswa.show', $siswa->id) }}">{{ $siswa->nama }}</a></td>
                        <td>{{ $siswa->nipd ?? '-' }} / {{ $siswa->nisn ?? '-' }}</td>
                        <td>{{ $siswa->nik ?? '-' }}</td>
                        <td>{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td>
                        <td>{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td>{{ $siswa->nama_rombel ?? ($siswa->rombel->nama ?? '-') }}</td>
                        <td>
                            <a href="{{ route('admin.kesiswaan.buku_induk.cetak', $siswa->id) }}" target="_blank" class="btn btn-sm btn-danger">Cetak Buku Induk</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Pilih kelas untuk menampilkan siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{-- Placeholder for pagination if needed in future --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    }
});
</script>
@endpush
