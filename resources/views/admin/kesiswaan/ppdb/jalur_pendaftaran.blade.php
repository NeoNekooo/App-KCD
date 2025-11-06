@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">PPDB /</span> Jalur Pendaftaran PPDB
</h4>

<div class="row">
  <div class="col-md-12">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="">Jalur Pendaftaran</h5>
        <!-- Button trigger modal -->
        <button
          type="button"
          class="btn btn-primary d-flex align-items-center"
          data-bs-toggle="modal"
          data-bs-target="#modalTambah">
          <i class='bx bx-plus me-1'></i> Tambah Jalur
        </button>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No</th>
              <th>KODE</th>
              <th>Jalur</th>
              <th>Keterangan</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($jalurPendaftaran as $jalur)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $jalur->kode }}</td>
                <td>{{ $jalur->jalur }}</td>
                <td>{{ $jalur->keterangan }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    {{-- Toggle Active --}}
                    <form action="{{ route('admin.kesiswaan.ppdb.jalur-ppdb.toggleActive', $jalur->id) }}" method="POST" class="me-1">
                      @csrf
                      <button type="submit" 
                        class="btn btn-icon btn-sm @if($jalur->is_active) text-danger @else text-info @endif"
                        data-bs-toggle="tooltip" data-bs-placement="top" 
                        title="@if($jalur->is_active) Non Aktifkan @else Aktifkan Jalur @endif">
                        <i class='bx @if($jalur->is_active) bx-block @else bx-power-off @endif'></i>
                      </button>
                    </form>

                    {{-- Edit --}}
                    <a href="javascript:void(0);" 
                       class="btn btn-icon btn-sm btn-outline-primary me-1 btn-edit"
                       data-id="{{ $jalur->id }}"
                       data-kode="{{ $jalur->kode }}"
                       data-jalur="{{ $jalur->jalur }}"
                       data-keterangan="{{ $jalur->keterangan }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="Edit">
                       <i class="bx bx-edit-alt"></i>
                    </a>


                    {{-- Hapus --}}
                    <form action="{{ route('admin.kesiswaan.ppdb.jalur-ppdb.destroy', $jalur->id) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" 
                              data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                        <i class="bx bx-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">Belum ada data jalur pendaftaran</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Jalur Pendaftaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.kesiswaan.ppdb.jalur-ppdb.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-6 mb-3">
            <div class="col">
              <label for="tahunPendaftaran" class="form-label">Tahun Pendaftaran</label>
              <input
                type="text"
                id="tahunPendaftaran"
                class="form-control"
                value="{{ $tahunPpdb ? $tahunPpdb->tahun_pelajaran : 'Tahun Pendaftaran Tidak ada yang aktif' }}"
                readonly />
              <input type="hidden" name="tahun_id" value="{{ $tahunPpdb ? $tahunPpdb->id : '' }}">
            </div>
            <div class="col">
              <label for="kode" class="form-label">KODE</label>
              <input type="text" id="kode" name="kode" class="form-control" placeholder="Contoh : ZONASI" required />
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="jalur" class="form-label">Jalur</label>
              <input type="text" id="jalur" name="jalur" class="form-control" placeholder="Contoh : Zonasi" required />
            </div>
          </div>
          <div class="row">
            <div class="col">
              <label for="keterangan" class="form-label">Keterangan</label>
              <textarea id="keterangan" name="keterangan" class="form-control" required
                placeholder="Contoh : Jalur ini diperuntukan bagi calon peserta didik yang berdomisili dekat sekolah"></textarea>
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
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="formEdit" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Jalur Pendaftaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-6 mb-3">
            <div class="col">
              <label for="editKode" class="form-label">KODE</label>
              <input type="text" id="editKode" name="kode" class="form-control" required />
            </div>
            <div class="col">
              <label for="editJalur" class="form-label">Jalur</label>
              <input type="text" id="editJalur" name="jalur" class="form-control" required />
            </div>
          </div>
          <div class="row">
            <div class="col">
              <label for="editKeterangan" class="form-label">Keterangan</label>
              <textarea id="editKeterangan" name="keterangan" class="form-control" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            Batal
          </button>
          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".btn-edit");
    const modal = new bootstrap.Modal(document.getElementById("editModal"));
    const formEdit = document.getElementById("formEdit");

    editButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const kode = this.dataset.kode;
            const jalur = this.dataset.jalur;
            const keterangan = this.dataset.keterangan;

            // isi form
            formEdit.action = `/admin/kesiswaan/ppdb/jalur-ppdb/${id}`;
            document.getElementById("editKode").value = kode;
            document.getElementById("editJalur").value = jalur;
            document.getElementById("editKeterangan").value = keterangan;

            // tampilkan modal
            modal.show();
        });
    });
});
</script>


@endsection
