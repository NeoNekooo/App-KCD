@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PPDB /</span> Syarat Pendaftaran PPDB</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Syarat Pendaftaran</h5>
                <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class='bx bx-plus me-1'></i> Tambah Syarat
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun Pelajaran</th>
                            <th>Jalur</th>
                            <th>Syarat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($syaratPendaftaran as $syarat)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $syarat->tahunPpdb->tahun_pelajaran ?? '-' }}</td>
                            <td>{{ $syarat->jalurPendaftaran->jalur ?? '-' }}</td>
                            <td>{{ $syarat->syarat }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    {{-- Toggle Active --}}
                                    <form action="{{ route('admin.ppdb.syarat-ppdb.toggleActive', $syarat->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            class="btn btn-icon btn-sm @if($syarat->is_active) text-danger @else text-success @endif"
                                            data-bs-toggle="tooltip" 
                                            title="{{ $syarat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class='bx {{ $syarat->is_active ? "bx-block" : "bx-power-off" }}'></i>
                                        </button>
                                    </form>

                                    {{-- Edit --}}
                                    <button type="button" 
                                        class="btn btn-icon btn-sm btn-outline-primary btn-edit"
                                        data-id="{{ $syarat->id }}"
                                        data-tahun="{{ $syarat->tahunPpdb->tahun_pelajaran ?? '-' }}"
                                        data-tahunid="{{ $syarat->tahunPpdb->id ?? '' }}"
                                        data-jalur="{{ $syarat->jalurPendaftaran->id ?? '' }}"
                                        data-syarat="{{ $syarat->syarat }}"
                                        title="Edit">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.ppdb.syarat-ppdb.destroy', $syarat->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="5" class="text-center">Belum ada data syarat pendaftaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Syarat Pendaftaran -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.ppdb.syarat-ppdb.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tambah Syarat Pendaftaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-6 mb-3">
            <div class="col">
              <label for="tahunPendaftaran" class="form-label">Tahun Pendaftaran</label>
              <input type="text" id="tahunPendaftaran" class="form-control" value="{{ $tahunPpdb?->tahun_pelajaran ?? '-' }}" readonly>
              <input type="hidden" name="tahun_id" value="{{ $tahunPpdb?->id }}">
            </div>
            <div class="col">
              <label for="jalurSelect" class="form-label">Jalur</label>
              <select name="jalur_id" id="jalurSelect" class="form-select" required>
                <option value="">Pilih Jalur</option>
                @foreach($jalursAktif as $jalur)
                  <option value="{{ $jalur->id }}">{{ $jalur->jalur }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row g-6 mb-3">
            <div class="col">
              <label for="syarat" class="form-label">Syarat</label>
              <textarea name="syarat" id="syarat" class="form-control" required></textarea>
            </div>
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

<!-- Modal Edit Syarat Pendaftaran -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="formEdit" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Syarat Pendaftaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-6 mb-3">
            <div class="col">
              <label for="editTahun" class="form-label">Tahun Pendaftaran</label>
              <input type="text" id="editTahun" class="form-control" readonly>
              <input type="hidden" name="tahun_id" id="editTahunId">
            </div>
            <div class="col">
              <label for="editJalur" class="form-label">Jalur</label>
              <select name="jalur_id" id="editJalur" class="form-select" required>
                <option value="">Pilih Jalur</option>
                @foreach($jalursAktif as $jalur)
                  <option value="{{ $jalur->id }}">{{ $jalur->jalur }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row g-6 mb-3">
            <div class="col">
              <label for="editSyarat" class="form-label">Syarat</label>
              <textarea name="syarat" id="editSyarat" class="form-control" required></textarea>
            </div>
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
document.addEventListener("DOMContentLoaded", () => {
    const editButtons = document.querySelectorAll(".btn-edit");
    const formEdit = document.getElementById("formEdit");
    const editModal = new bootstrap.Modal(document.getElementById("editModal"));

    editButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const { id, tahun, tahunid, jalur, syarat } = btn.dataset;

            formEdit.action = `/admin/kesiswaan/ppdb/syarat-ppdb/${id}`;
            document.getElementById("editTahun").value = tahun;
            document.getElementById("editTahunId").value = tahunid;
            document.getElementById("editJalur").value = jalur;
            document.getElementById("editSyarat").value = syarat;

            editModal.show();
        });
    });
});
</script>
@endsection
