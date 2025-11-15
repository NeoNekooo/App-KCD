    {{-- Halaman ini bisa meng-extend layout utama admin Anda --}}
    @extends('layouts.admin') {{-- Ganti dengan path layout Anda --}}

    @section('content')
    <div class="container mt-5">
        <h2>Laporan Absensi Bulanan</h2>
        <p>Silakan pilih kelas dan periode untuk menampilkan laporan absensi dalam format bulanan.</p>
        <hr>
        <form action="{{ route('admin.laporan.absensi.bulanan.cetak') }}" method="GET" class="card p-4" target="_blank">
            <div class="mb-3">
                <label for="rombel_id" class="form-label">Pilih Kelas</label>
                <select name="rombel_id" id="rombel_id" class="form-select" required>
                    <option value="">-- Pilih Rombongan Belajar --</option>
                    @foreach($rombels as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option> {{-- Sesuaikan nama kolom jika berbeda --}}
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select" required>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tahun" class="form-label">Tahun</label>
                    <input type="number" name="tahun" id="tahun" class="form-control" value="{{ date('Y') }}" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </form>
    </div>
    @endsection
    
