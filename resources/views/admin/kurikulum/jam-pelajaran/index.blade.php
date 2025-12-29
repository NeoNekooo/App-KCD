@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Pengaturan Jam Pelajaran (Time Slots)</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg"></i> Tambah Jam
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabs Navigasi Hari --}}
    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        @foreach($daftarHari as $index => $hari)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                        id="{{ $hari }}-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#content-{{ $hari }}"
                        type="button" role="tab">
                    {{ $hari }}
                </button>
            </li>
        @endforeach
    </ul>

    {{-- Isi Konten Tabs --}}
    <div class="tab-content" id="myTabContent">
        @foreach($daftarHari as $index => $hari)
            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="content-{{ $hari }}" role="tabpanel">

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        @php $dataJam = $jamPelajarans[$hari] ?? collect([]); @endphp

                        @if($dataJam->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history fs-1"></i>
                                <p>Belum ada pengaturan jam untuk hari {{ $hari }}</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px">Urut</th>
                                            <th>Waktu</th>
                                            <th>Nama Sesi</th>
                                            <th>Tipe</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dataJam as $jam)
                                            {{-- Memberikan warna background baris khusus untuk Istirahat dan Upacara agar mencolok --}}
                                            <tr class="{{ $jam->tipe == 'istirahat' ? 'table-warning' : ($jam->tipe == 'upacara' ? 'table-info' : '') }}">
                                                <td class="text-center fw-bold">{{ $jam->urutan }}</td>
                                                <td>
                                                    <span class="badge bg-secondary font-monospace">
                                                        {{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }}
                                                    </span>
                                                </td>
                                                <td>{{ $jam->nama }}</td>
                                                <td>
                                                    {{-- Logika Badge Warna-warni sesuai Tipe --}}
                                                    @if($jam->tipe == 'kbm')
                                                        <span class="badge bg-success">KBM</span>
                                                    @elseif($jam->tipe == 'upacara')
                                                        <span class="badge bg-primary">Upacara</span>
                                                    @elseif($jam->tipe == 'keagamaan')
                                                        <span class="badge bg-success bg-gradient">Keagamaan</span>
                                                    @elseif($jam->tipe == 'literasi')
                                                        <span class="badge bg-info text-dark">Literasi</span>
                                                    @elseif($jam->tipe == 'wali_kelas')
                                                        <span class="badge bg-primary bg-gradient">Wali Kelas</span>
                                                    @elseif($jam->tipe == 'istirahat')
                                                        <span class="badge bg-warning text-dark">Istirahat</span>
                                                    @else
                                                        <span class="badge bg-secondary">Lainnya</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary btn-edit"
                                                            data-data="{{ json_encode($jam) }}"
                                                            data-bs-toggle="modal" data-bs-target="#modalEdit">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    <form action="{{ route('admin.kurikulum.jam-pelajaran.destroy', $jam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jam ini?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @endforeach
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.kurikulum.jam-pelajaran.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Slot Waktu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Pilihan Hari (Multiple) --}}
                    <div class="mb-3">
                        <label class="form-label d-block">Berlaku untuk Hari:</label>
                        <div class="btn-group" role="group">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                <input type="checkbox" class="btn-check" name="hari[]" id="check{{ $h }}" value="{{ $h }}" checked>
                                <label class="btn btn-outline-secondary btn-sm" for="check{{ $h }}">{{ substr($h, 0, 3) }}</label>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-1">Anda bisa memilih lebih dari satu hari.</small>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">Urutan Ke-</label>
                            <input type="number" name="urutan" class="form-control" placeholder="Contoh: 1" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tipe</label>
                            {{-- UPDATE: Pilihan Tipe Lengkap --}}
                            <select name="tipe" class="form-select" required>
                                <option value="kbm">KBM (Belajar)</option>
                                <option value="upacara">Upacara</option>
                                <option value="keagamaan">Keagamaan</option>
                                <option value="literasi">Literasi</option>
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="istirahat">Istirahat</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Sesi</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Jam Ke-1" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Slot Waktu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="hari" id="edit_hari">

                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">Urutan Ke-</label>
                            <input type="number" name="urutan" id="edit_urutan" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tipe</label>
                            {{-- UPDATE: Pilihan Tipe Lengkap di Edit --}}
                            <select name="tipe" id="edit_tipe" class="form-select" required>
                                <option value="kbm">KBM (Belajar)</option>
                                <option value="upacara">Upacara</option>
                                <option value="keagamaan">Keagamaan</option>
                                <option value="literasi">Literasi</option>
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="istirahat">Istirahat</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Sesi</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="edit_jam_selesai" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        const formEdit = document.getElementById('formEdit');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.getAttribute('data-data'));

                // Set Action URL
                formEdit.action = `/admin/kurikulum/jam-pelajaran/${data.id}`;

                // Set Values
                document.getElementById('edit_urutan').value = data.urutan;
                document.getElementById('edit_tipe').value = data.tipe; // Ini akan otomatis memilih opsi yang sesuai
                document.getElementById('edit_nama').value = data.nama;
                document.getElementById('edit_hari').value = data.hari;

                // Format Time (ambil HH:mm saja)
                document.getElementById('edit_jam_mulai').value = data.jam_mulai.substring(0, 5);
                document.getElementById('edit_jam_selesai').value = data.jam_selesai.substring(0, 5);
            });
        });
    });
</script>
@endpush

@endsection
