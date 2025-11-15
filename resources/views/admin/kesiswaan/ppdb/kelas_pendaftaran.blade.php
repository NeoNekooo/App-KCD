@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">PPDB /</span> Kelas
</h4>

<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Data Kelas</h5>
        <button type="button" class="btn btn-primary d-flex align-items-center"
                data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class='bx bx-plus me-1'></i> Tambah Kelas
        </button>
      </div>

      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
          <tr>
              <th>No</th>
              @if($tingkat->tingkat == 10)
                  <th>Kode</th>
                  <th>Kompetensi</th>
              @endif
              <th>Tingkat</th>
              <th>Rombel</th>
              <th>Kelas</th>
              <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($kelas as $item)
          <tr>
              <td>{{ $loop->iteration }}</td>
          
              {{-- ini dimunculkan untuk Tingkat 10 --}}
              @if($tingkat->tingkat == 10)
                  <td>{{ $item->kompetensiPpdb->kode ?? '-' }}</td>
                  <td>{{ $item->kompetensiPpdb->kompetensi ?? '-' }}</td>
              @endif
          
              <td>{{ $item->tingkat }}</td>
              <td>{{ $item->rombel }}</td>

              <td>
                @if ($item->tingkat == 10)
                  {{ $item->tingkat_romawi }} {{ $item->kompetensiPpdb->kode }} {{ $item->rombel }}
                @elseif ($item->tingkat != 10)
                  Kelas {{ $item->tingkat_romawi }} {{ $item->rombel }} 
                @endif
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <a href="javascript:void(0);"
                     class="btn btn-icon btn-sm btn-outline-primary me-1 btn-edit"
                     data-id="{{ $item->id }}"
                     data-kompetensi_id="{{ $item->kompetensiPendaftaran_id ?? '' }}"
                     data-tingkat="{{ $item->tingkat }}"
                     data-rombel="{{ $item->rombel }}">
                    <i class="bx bx-edit-alt"></i>
                  </a>
                  <form action="{{ route('admin.kesiswaan.ppdb.kelas-ppdb.destroy', $item->id) }}"
                        method="POST"
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
              <td colspan="{{ $tingkat->tingkat == 10 ? 6 : 4 }}" class="text-center">Belum ada data kelas</td>
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
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.kesiswaan.ppdb.kelas-ppdb.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tambah Kelas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          {{-- Tahun Pelajaran --}}
          <div class="mb-3">
            <label class="form-label">Tahun Pelajaran</label>
            <input type="text" class="form-control"
                   value="{{ $tahunPpdb ? $tahunPpdb->tahun_pelajaran : 'Tidak ada Tahun Aktif' }}" readonly>
            <input type="hidden" name="tahunPelajaran_id" value="{{ $tahunPpdb ? $tahunPpdb->id : '' }}">
          </div>

          {{-- Tingkat --}}
          <div class="mb-3">
            <label class="form-label">Tingkat</label>
            @if($tingkat->count())
              <input type="text" id="tingkatInput" class="form-control" value="{{ $tingkat->tingkat }}" readonly>
              <input type="hidden" name="tingkat" value="{{ $tingkat->tingkat }}">
            @else
              <input type="text" class="form-control" value="Tidak ada tingkat aktif" readonly>
            @endif
          </div>

          {{-- Kompetensi (hanya SMA) --}}
          <div class="mb-3" id="kompetensiContainer" style="display: {{ $tingkat->tingkat == 10 ? 'block' : 'none' }};">
            <label class="form-label">Kompetensi</label>
            <select name="kompetensiPendaftaran_id" id="kompetensiSelect" class="form-select">
              <option value="">-- Pilih Kompetensi --</option>
              @foreach ($kompetensi as $k)
                <option value="{{ $k->id }}">{{ $k->kode }} | {{ $k->kompetensi }}</option>
              @endforeach
            </select>
          </div>

          {{-- Rombel --}}
          <div class="mb-3">
            <label class="form-label">Rombel</label>
            <input type="text" name="rombel" class="form-control" placeholder="Contoh: A atau 1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
          <h5 class="modal-title">Edit Kelas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          {{-- Tingkat --}}
          <div class="mb-3">
            <label class="form-label">Tingkat</label>
            <input type="text" id="editTingkat" class="form-control" readonly>
          </div>

          {{-- Kompetensi --}}
          <div class="mb-3" id="editKompetensiContainer">
            <label class="form-label">Kompetensi</label>
            <select id="editKompetensi" name="kompetensiPendaftaran_id" class="form-select">
              <option value="">-- Pilih Kompetensi --</option>
              @foreach ($kompetensi as $k)
                <option value="{{ $k->id }}">{{ $k->kode }} | {{ $k->kompetensi }}</option>
              @endforeach
            </select>
          </div>

          {{-- Rombel --}}
          <div class="mb-3">
            <label class="form-label">Rombel</label>
            <input type="text" id="editRombel" name="rombel" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
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
  const kompetensiContainer = document.getElementById("editKompetensiContainer");
  const kompetensiSelect = document.getElementById("editKompetensi");

  editButtons.forEach(btn => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const kompetensi_id = this.dataset.kompetensi_id;
      const tingkat = parseInt(this.dataset.tingkat);
      const rombel = this.dataset.rombel;

      formEdit.action = `/admin/kesiswaan/ppdb/kelas-ppdb/${id}`;
      document.getElementById("editTingkat").value = tingkat;
      document.getElementById("editRombel").value = rombel;

      if(tingkat === 10){
        kompetensiContainer.style.display = "block";
        kompetensiSelect.value = kompetensi_id;
      } else {
        kompetensiContainer.style.display = "none";
        kompetensiSelect.value = '';
      }

      modal.show();
    });
  });
});
</script>
@endsection
