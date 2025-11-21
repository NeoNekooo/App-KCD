@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">PPDB /</span> Kompetensi Pendaftaran
</h4>

<div class="row">
  <div class="col-md-12">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="">Kompetensi Pendaftaran</h5>
        <!-- Button trigger modal -->
        <button
          type="button"
          class="btn btn-primary d-flex align-items-center"
          data-bs-toggle="modal"
          data-bs-target="#modalTambah">
          <i class='bx bx-plus me-1'></i> Tambah Kompetensi
        </button>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No</th>
              <th>KODE</th>
              <th>Kompetensi</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($kompetensiPendaftaran as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->kompetensi }}</td>
                <td>
                  <div class="d-flex align-items-center">

                    {{-- Edit --}}
                    <a href="javascript:void(0);" 
                       class="btn btn-icon btn-sm btn-outline-primary me-1 btn-edit"
                       data-id="{{ $item->id }}"
                       data-kode="{{ $item->kode }}"
                       data-kompetensi="{{ $item->kompetensi }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="Edit">
                       <i class="bx bx-edit-alt"></i>
                    </a>

                    {{-- Hapus --}}
                    <form action="{{ route('admin.ppdb.kompetensi-ppdb.destroy', $item->id) }}" method="POST" 
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
                <td colspan="4" class="text-center">Belum ada data kompetensi</td>
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
        <h5 class="modal-title">Tambah Kompetensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.ppdb.kompetensi-ppdb.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-6 mb-3">
            <div class="col">
              <label for="tahunPelajaran" class="form-label">Tahun Pelajaran</label>
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
              <input type="text" id="kode" name="kode" class="form-control" placeholder="Contoh : TKJ01" required />
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="kompetensi" class="form-label">Kompetensi</label>
              <input type="text" id="kompetensi" name="kompetensi" class="form-control" placeholder="Contoh : Teknik Komputer dan Jaringan" required />
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
        <input type="hidden" name="tahun_id" value="{{ $tahunPpdb ? $tahunPpdb->id : '' }}">
        <div class="modal-header">
          <h5 class="modal-title">Edit Kompetensi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-6 mb-3">
            <div class="col">
              <label for="editKode" class="form-label">KODE</label>
              <input type="text" id="editKode" name="kode" class="form-control" required />
            </div>
            <div class="col">
              <label for="editKompetensi" class="form-label">Kompetensi</label>
              <input type="text" id="editKompetensi" name="kompetensi" class="form-control" required />
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
            const kompetensi = this.dataset.kompetensi;

            // isi form
            formEdit.action = `/admin/kesiswaan/ppdb/kompetensi-ppdb/${id}`;
            document.getElementById("editKode").value = kode;
            document.getElementById("editKompetensi").value = kompetensi;

            modal.show();
        });
    });
});
</script>

@endsection
