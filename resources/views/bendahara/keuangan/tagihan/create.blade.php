@extends('layouts.admin')

@section('title', 'Generate Tagihan Siswa')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Keuangan / Tagihan /</span> Generate Baru
    </h4>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <span class="alert-icon text-success me-2"><i class="ti ti-check ti-xs"></i></span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <span class="alert-icon text-danger me-2"><i class="ti ti-ban ti-xs"></i></span>
            {{ session('error') }}
        </div>
    @endif
     @if (session('warning'))
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <span class="alert-icon text-warning me-2"><i class="ti ti-alert-triangle ti-xs"></i></span>
            {{ session('warning') }}
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">Formulir Generate Tagihan</h5>
        <form action="{{ route('bendahara.keuangan.tagihan.store') }}" method="POST" class="card-body">
            @csrf

            <div class="mb-3">
                <label for="iuran_id" class="form-label">Pilih Jenis Iuran <span class="text-danger">*</span></label>
                <select id="iuran_id" name="iuran_id" class="form-select @error('iuran_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Iuran --</option>
                    @foreach ($iurans as $iuran)
                        <option value="{{ $iuran->id }}" {{ old('iuran_id') == $iuran->id ? 'selected' : '' }}>
                            {{ $iuran->nama_iuran }}
                        </option>
                    @endforeach
                </select>
                @error('iuran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan (Rp) <span class="text-danger">*</span></label>
                <input type="number" id="jumlah_tagihan" name="jumlah_tagihan" class="form-control @error('jumlah_tagihan') is-invalid @enderror" value="{{ old('jumlah_tagihan') }}" placeholder="Contoh: 150000" required>
                @error('jumlah_tagihan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="tanggal_mulai" class="form-label">Tanggal Berlaku Tagihan <span class="text-danger">*</span></label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="rombel_id" class="form-label">Terapkan Ke Rombel (Kelas) - Opsional</label>
               <select id="rombel_id" name="rombel_id[]" class="form-select select2 ..." multiple>
    @foreach ($rombels as $rombel)
        {{-- UBAH: $rombel->id menjadi $rombel->rombongan_belajar_id --}}
        <option value="{{ $rombel->rombongan_belajar_id }}" {{ in_array($rombel->rombongan_belajar_id, old('rombel_id', [])) ? 'selected' : '' }}>
            {{ $rombel->nama }} ({{ $rombel->siswas_count ?? 'N/A' }} Siswa)
        </option>
    @endforeach
</select>
                <small class="text-muted">Kosongkan jika ingin menerapkan ke SEMUA siswa aktif.</small>
                @error('rombel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary"><i class="ti ti-file-plus me-1"></i> Generate Tagihan</button>
        </form>
    </div>
</div>
@endsection
