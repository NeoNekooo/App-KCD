@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Izin /</span> Buat Izin Baru</h4>
<div class="card">
    <div class="card-body">
        
        <!-- ======================================================= -->
        <!-- ===      TAMBAHKAN BLOK KODE INI UNTUK ERROR        === -->
        <!-- ======================================================= -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <h6 class="alert-heading mb-1">Terdapat Kesalahan Validasi:</h6>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- ======================================================= -->

        <form action="{{ route('admin.absensi.izin-siswa.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="siswa_id" class="form-label">Pilih Siswa</label>
                    <select id="siswa_id" name="siswa_id" class="form-select @error('siswa_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach ($siswas as $siswa)
                            <option value="{{ $siswa->id }}" {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="tanggal_izin" class="form-label">Tanggal Izin</label>
                    <input class="form-control @error('tanggal_izin') is-invalid @enderror" type="date" id="tanggal_izin" name="tanggal_izin" value="{{ old('tanggal_izin', date('Y-m-d')) }}" required>
                </div>
                <div class="mb-3 col-12">
                    <label for="tipe_izin" class="form-label">Tipe Izin</label>
                    <select id="tipe_izin" name="tipe_izin" class="form-select @error('tipe_izin') is-invalid @enderror" required>
                        <option value="">-- Pilih Tipe Izin --</option>
                        <option value="DATANG_TERLAMBAT" {{ old('tipe_izin') == 'DATANG_TERLAMBAT' ? 'selected' : '' }}>Izin Datang Terlambat</option>
                        <option value="PULANG_AWAL" {{ old('tipe_izin') == 'PULANG_AWAL' ? 'selected' : '' }}>Izin Pulang Awal</option>
                        <option value="KELUAR_SEMENTARA" {{ old('tipe_izin') == 'KELUAR_SEMENTARA' ? 'selected' : '' }}>Izin Keluar Sementara</option>
                    </select>
                </div>
                
                <!-- Input Jam Dinamis -->
                <div id="jam_mulai_wrapper" class="mb-3 col-md-6 d-none">
                    <label for="jam_izin_mulai" class="form-label">Izin Berlaku Mulai Jam</label>
                    <input class="form-control @error('jam_izin_mulai') is-invalid @enderror" type="time" id="jam_izin_mulai" name="jam_izin_mulai" value="{{ old('jam_izin_mulai') }}">
                </div>
                <div id="jam_selesai_wrapper" class="mb-3 col-md-6 d-none">
                    <label for="jam_izin_selesai" class="form-label">Izin Berakhir Pada Jam</label>
                    <input class="form-control @error('jam_izin_selesai') is-invalid @enderror" type="time" id="jam_izin_selesai" name="jam_izin_selesai" value="{{ old('jam_izin_selesai') }}">
                </div>

                <div class="mb-3 col-12">
                    <label for="alasan" class="form-label">Alasan Izin</label>
                    <textarea class="form-control @error('alasan') is-invalid @enderror" id="alasan" name="alasan" rows="3" required>{{ old('alasan') }}</textarea>
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary me-2">Simpan Izin</button>
                <a href="{{ route('admin.absensi.izin-siswa.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk menampilkan/menyembunyikan input jam
    function handleTipeIzinChange() {
        const jamMulai = document.getElementById('jam_mulai_wrapper');
        const jamSelesai = document.getElementById('jam_selesai_wrapper');
        const tipe = document.getElementById('tipe_izin').value;

        // Reset
        jamMulai.classList.add('d-none');
        jamSelesai.classList.add('d-none');
        document.getElementById('jam_izin_mulai').required = false;
        document.getElementById('jam_izin_selesai').required = false;

        if (tipe === 'DATANG_TERLAMBAT') {
            jamSelesai.classList.remove('d-none');
            document.getElementById('jam_izin_selesai').required = true;
        } else if (tipe === 'PULANG_AWAL') {
            jamMulai.classList.remove('d-none');
            document.getElementById('jam_izin_mulai').required = true;
        } else if (tipe === 'KELUAR_SEMENTARA') {
            jamMulai.classList.remove('d-none');
            jamSelesai.classList.remove('d-none');
            document.getElementById('jam_izin_mulai').required = true;
            document.getElementById('jam_izin_selesai').required = true;
        }
    }

    // Panggil fungsi saat event change dan saat halaman dimuat (untuk old input)
    document.getElementById('tipe_izin').addEventListener('change', handleTipeIzinChange);
    document.addEventListener('DOMContentLoaded', handleTipeIzinChange);
</script>
@endpush

