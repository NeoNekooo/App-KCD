@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PPDB /</span> Quota Pendaftaran PPDB</h4>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Quota Pendaftaran</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class='bx bx-plus me-1'></i> Tambah Quota
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tahun Pelajaran</th>
                    <th>Keahlian</th>
                    <th>Jumlah Kelas</th>
                    <th>Quota</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotas as $quota)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $quota->tahunPpdb?->tahun_pelajaran ?? '-' }}</td>
                    <td>{{ $quota->keahlian }}</td>
                    <td>{{ $quota->jumlah_kelas }}</td>
                    <td>{{ $quota->quota }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:void(0);" 
                               class="btn btn-icon btn-sm btn-outline-primary me-1 btn-edit"
                               data-id="{{ $quota->id }}"
                               data-tahunid="{{ $quota->tahunPpdb?->id ?? '' }}"
                               data-tahun="{{ $quota->tahunPpdb->tahun_pelajaran ?? '-' }}"
                               data-keahlian="{{ $quota->keahlian }}"
                               data-jumlah="{{ $quota->jumlah_kelas }}"
                               data-quota="{{ $quota->quota }}"
                               data-bs-toggle="tooltip"
                               title="Edit">
                               <i class="bx bx-edit-alt"></i>
                            </a>
                            <form action="{{ route('admin.kesiswaan.ppdb.quota-ppdb.destroy', $quota->id) }}" method="POST" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center">Belum ada data quota pendaftaran</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.kesiswaan.ppdb.quota-ppdb.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Quota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-6 mb-3">
                        <div class="col">
                            <label for="tahunPendaftaran" class="form-label">Tahun Pendaftaran</label>
                            <input type="text" id="tahunPendaftaran" class="form-control" 
                                   value="{{ $tahunPpdb?->tahun_pelajaran ?? 'Tahun Pendaftaran Tidak ada yang aktif' }}" readonly>
                            <input type="hidden" name="tahun_id" value="{{ $tahunPpdb ? $tahunPpdb->id : '' }}">
                        </div>
                        <div class="col">
                            <label for="keahlian" class="form-label">Keahlian</label>
                            <select name="keahlian" class="form-select" required>
                                <option value="">-- Semua Jurusan --</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan }}" {{ request('jurusan') == $jurusan ? 'selected' : '' }}>
                                        {{ $jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                
                    <div class="row g-6 mb-3">
                        <div class="col">
                            <label for="jumlah_kelas" class="form-label">Jumlah Kelas</label>
                            <input type="number" id="jumlah_kelas" name="jumlah_kelas" class="form-control" placeholder="Contoh : 3" required>
                        </div>
                        <div class="col">
                            <label for="quota" class="form-label">Quota</label>
                            <input type="number" id="quota" name="quota" class="form-control" placeholder="Contoh : 96" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Quota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tahun Pendaftaran</label>
                        <input type="text" class="form-control" id="editTahun" readonly>
                        <input type="hidden" name="editTahun_id" id="editTahunId">
                    </div>
                    <div class="mb-3">
                        <label>Keahlian</label>
                        <select name="keahlian" id="editKeahlian" class="form-select" required>
                            <option value="">-- Pilih Keahlian --</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan }}">{{ $jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Kelas</label>
                        <input type="number" id="editJumlah" name="jumlah_kelas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Quota</label>
                        <input type="number" id="editQuota" name="quota" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const editButtons = document.querySelectorAll(".btn-edit");
    const modal = new bootstrap.Modal(document.getElementById("editModal"));
    const formEdit = document.getElementById("formEdit");

    editButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.dataset.id;
            formEdit.action = `/admin/kesiswaan/ppdb/quota-ppdb/${id}`;

            // otomatis mapping semua dataset ke input dengan id yang cocok
            Object.keys(this.dataset).forEach(key => {
                const input = document.getElementById("edit" + key.charAt(0).toUpperCase() + key.slice(1));
                if (input) {
                    input.value = this.dataset[key];
                }
            });

            modal.show();
        });
    });
});
</script>


@endsection
