@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PPDB /</span> Penempatan Kelas</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">Penempatan Kelas Siswa</h5>
            <div class="card-body">

                {{-- Form filter jurusan --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.kesiswaan.ppdb.penempatan-kelas.index') }}">
                            <label class="form-label">Filter Jurusan</label>
                            <select name="jurusan" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Semua Jurusan --</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>
                                        {{ $jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <label for="pilih_kelas" class="form-label">Pilih Kelas Tujuan</label>
                        <select id="pilih_kelas" class="form-select">
                            <option value="">- Pilih Kelas Tujuan -</option>
                            @foreach ($kelas as $rombel)
                                <option value="{{ $rombel->nama }}">{{ $rombel->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Form penempatan kelas --}}
                <form method="POST" action="{{ route('admin.kesiswaan.ppdb.penempatan-kelas.update-kelas') }}">
                    @csrf
                    <input type="hidden" name="kelas_tujuan" id="kelas_tujuan_hidden">

                    <div class="row">
                        {{-- Tabel siswa belum ditempatkan --}}
                        <div class="col-md-6">
                            <h6>Daftar Siswa Belum Ditempatkan</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>NIS</th>
                                            <th>NAMA SISWA</th>
                                            <th>SEKOLAH ASAL</th>
                                            <th><input type="checkbox" id="checkAll"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($belumDitempatkan as $index => $formulir)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $formulir->nis ?? '-' }}</td>
                                            <td>{{ $formulir->nama_lengkap }}</td>
                                            <td>{{ $formulir->asal_sekolah ?? '-' }}</td>
                                            <td>
                                                <input type="checkbox" name="siswa_id[]" value="{{ $formulir->id }}">
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Maaf, data tidak ditemukan</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tabel siswa sudah ditempatkan --}}
                        <div class="col-md-6">
                            <h6>Daftar Siswa Sudah Ditempatkan</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>NIS</th>
                                            <th>NAMA SISWA</th>
                                            <th>KELAS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sudahDitempatkan as $index => $siswa)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $siswa->nis ?? '-' }}</td>
                                            <td>{{ $siswa->nama_lengkap }}</td>
                                            <td>{{ $siswa->kelas_tujuan }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="btnTempatkan" style="display:none;">Tempatkan Kelas</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    const pilihKelas = document.getElementById('pilih_kelas');
    const btnTempatkan = document.getElementById('btnTempatkan');
    const kelasHidden = document.getElementById('kelas_tujuan_hidden');

    pilihKelas.addEventListener('change', function() {
        if (this.value) {
            kelasHidden.value = this.value;
            btnTempatkan.style.display = 'inline-block';
        } else {
            btnTempatkan.style.display = 'none';
        }
    });

    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="siswa_id[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
