@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PPDB /</span> Tahun PPDB</h4>

<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="">Daftar Tahun PPDB</h5>
        <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class='bx bx-plus me-1'></i> Tambah Tahun
        </button>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No</th>
              <th>Tahun</th>
              <th>Keterangan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tahunPpdb as $tahun)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $tahun->tahun_pelajaran }}</td>
                <td>{{ $tahun->keterangan ?? '-' }}</td>
                <td>
                  @if($tahun->is_active)
                    <span class="badge bg-success">Aktif</span>
                  @else
                    <span class="badge bg-secondary">Non Aktif</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    
                    {{-- Toggle Active --}}
                    <form action="{{ route('admin.kesiswaan.ppdb.tahun-ppdb.toggleActive', $tahun->id) }}" method="POST" class="me-1">
                      @csrf
                      <button type="submit" 
                        class="btn btn-icon btn-sm @if($tahun->is_active) text-danger @else text-info @endif"
                        data-bs-toggle="tooltip" data-bs-placement="top" 
                        title="@if($tahun->is_active) Non Aktifkan @else Aktifkan Jalur @endif">
                        <i class='bx @if($tahun->is_active) bx-block @else bx-power-off @endif'></i>
                      </button>
                    </form>

                    {{-- Edit --}}
                    <button type="button" 
                      class="btn btn-icon btn-sm btn-outline-primary me-1 btn-edit"
                      data-id="{{ $tahun->id }}"
                      data-tahun="{{ $tahun->tahun_pelajaran }}"
                      data-keterangan="{{ $tahun->keterangan }}"
                      title="Edit">
                      <i class="bx bx-edit-alt"></i>
                    </button>

                    {{-- Hapus --}}
                    <form action="{{ route('admin.kesiswaan.ppdb.tahun-ppdb.destroy', $tahun->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                        <i class="bx bx-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">Belum ada data tahun PPDB</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.kesiswaan.ppdb.tahun-ppdb.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tambah Tahun PPDB</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Tahun Pelajaran</label>
            <input type="text" name="tahun_pelajaran" class="form-control" placeholder="2025/2026" required>
          </div>
          <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" placeholder="Misal: Tahun ajaran baru"></textarea>
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
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="formEdit" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Tahun PPDB</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Tahun Pelajaran</label>
            <input type="text" name="tahun_pelajaran" class="form-control" id="editTahun" required>
          </div>
          <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" id="editKeterangan"></textarea>
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
      const tahun = this.dataset.tahun;
      const keterangan = this.dataset.keterangan;

      formEdit.action = `/admin/kesiswaan/ppdb/tahun-ppdb/${id}`;

      document.getElementById("editTahun").value = tahun;
      document.getElementById("editKeterangan").value = keterangan;

      modal.show();
    });
  });
});
</script>
@endsection
