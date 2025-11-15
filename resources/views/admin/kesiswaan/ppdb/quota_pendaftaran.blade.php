@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PPDB /</span> Quota Pendaftaran PPDB</h4>

<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5>Quota Pendaftaran</h5>
    @if(!$quotaExists)
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class="bx bx-plus"></i> Tambah Quota
      </button>
    @else
      <button class="btn btn-secondary" disabled>
          <i class="bx bx-check"></i> Quota sudah ada
      </button>
    @endif

  </div>

  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>No</th>
          <th>Tahun Pelajaran</th>
          @if($tingkat && $tingkat->tingkat == 10)
            <th>Keahlian</th>
          @endif
          <th>Jumlah Kelas</th>
          <th>Quota</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($quotas as $quota)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $quota->tahunPpdb?->tahun_pelajaran ?? '-' }}</td>
          @if($tingkat && $tingkat->tingkat == 10)
            <td>{{ $quota->keahlian }}</td>
          @endif
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
                 title="Edit">
                 <i class="bx bx-edit-alt"></i>
              </a>
              <form action="{{ route('admin.kesiswaan.ppdb.quota-ppdb.destroy', $quota->id) }}" method="POST" 
                    onsubmit="return confirm('Yakin hapus data ini?');">
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
          <td colspan="6" class="text-center">Belum ada data quota pendaftaran</td>
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
              <label class="form-label">Tahun Pendaftaran</label>
              <input type="text" class="form-control" 
                     value="{{ $tahunPpdb?->tahun_pelajaran ?? 'Tidak ada tahun aktif' }}" readonly>
              <input type="hidden" name="tahun_id" value="{{ $tahunPpdb?->id }}">
            </div>

            @if($tingkat && $tingkat->tingkat == 10)
            <div class="col">
              <label class="form-label">Keahlian</label>
              <select name="keahlian" class="form-select" required>
                <option value="">-- Pilih Keahlian --</option>
                @foreach($jurusans as $jurusan)
                  <option value="{{ $jurusan->kode }}">{{ $jurusan->kode }}</option>
                @endforeach
              </select>
            </div>
            @endif
          </div>

          <div class="row g-6 mb-3">
            <div class="col">
              <label class="form-label">Jumlah Kelas</label>
              <input type="number" name="jumlah_kelas" class="form-control" required>
            </div>
            <div class="col">
              <label class="form-label">Quota</label>
              <input type="number" name="quota" class="form-control" required>
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
          <div class="row g-6 mb-3">
            <div class="col">
              <label class="form-label">Tahun Pendaftaran</label>
              <input type="text" id="editTahun" class="form-control" readonly>
              <input type="hidden" name="tahun_id" id="editTahunId">
            </div>

            @if($tingkat && $tingkat->tingkat == 10)
            <div class="col">
              <label class="form-label">Keahlian</label>
              <select name="keahlian" id="editKeahlian" class="form-select" required>
                <option value="">-- Pilih Keahlian --</option>
                @foreach($jurusans as $jurusan)
                  <option value="{{ $jurusan->kode }}">{{ $jurusan->kode }}</option>
                @endforeach
              </select>
            </div>
            @endif
          </div>

          <div class="row g-6 mb-3">
            <div class="col">
              <label class="form-label">Jumlah Kelas</label>
              <input type="number" id="editJumlah" name="jumlah_kelas" class="form-control" required>
            </div>
            <div class="col">
              <label class="form-label">Quota</label>
              <input type="number" id="editQuota" name="quota" class="form-control" required>
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
document.addEventListener("DOMContentLoaded", function() {
  const editButtons = document.querySelectorAll(".btn-edit");
  const modal = new bootstrap.Modal(document.getElementById("editModal"));
  const formEdit = document.getElementById("formEdit");

  editButtons.forEach(btn => {
    btn.addEventListener("click", function() {
      const id = this.dataset.id;
      formEdit.action = `/admin/kesiswaan/ppdb/quota-ppdb/${id}`;
      Object.keys(this.dataset).forEach(key => {
        const input = document.getElementById("edit" + key.charAt(0).toUpperCase() + key.slice(1));
        if (input) input.value = this.dataset[key];
      });
      modal.show();
    });
  });
});
</script>
@endsection
