@extends('layouts.admin')

@section('content')
<div class="row">
    <!-- Kolom Form Tambah Jadwal -->
    <div class="col-md-4">
        <div class="card mb-4">
            <h5 class="card-header">Tambah Jadwal untuk {{ $rombel->nama }}</h5>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.akademik.jadwal.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="rombel_id" value="{{ $rombel->id }}">
                    <div class="mb-3">
    <label for="mata_pelajaran_info" class="form-label">Mata Pelajaran & Guru</label>
    <select name="mata_pelajaran_info" id="mata_pelajaran_info" class="form-select" required>
        <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
        @foreach ($mataPelajaranList as $mapel)
            <option value="{{ $mapel['ptk_id'] . '|' . $mapel['nama_mata_pelajaran'] }}">
                {{-- Menggunakan 'nama_guru' yang sudah kita siapkan di controller --}}
                {{ $mapel['nama_mata_pelajaran'] }} - ({{ $mapel['nama_guru'] }})
            </option>
        @endforeach
    </select>
</div>
                    <div class="mb-3">
                        <label for="hari" class="form-label">Hari</label>
                        <select name="hari" id="hari" class="form-select">
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                    <a href="{{ route('admin.akademik.jadwal.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Tampilan Jadwal -->
    <div class="col-md-8">
        <div class="card">
            <h5 class="card-header">Jadwal Pelajaran Kelas {{ $rombel->nama }}</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                    <th class="text-center">{{ $hari }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                    <td class="align-top">
                                        @if (isset($jadwalByHari[$hari]))
                                            @foreach ($jadwalByHari[$hari] as $jadwal)
                                                <div class="alert alert-info p-2 mb-2">
                                                    <strong>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</strong><br>
                                                    {{ $jadwal->mata_pelajaran }}
                                                    <form action="{{ route('admin.akademik.jadwal.destroy', $jadwal->id) }}" method="POST" class="d-inline float-end">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-danger p-0" style="width: 20px; height: 20px; line-height: 1;" onclick="return confirm('Yakin ingin hapus?')">&times;</button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        @else
                                            <small class="text-muted">Kosong</small>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
