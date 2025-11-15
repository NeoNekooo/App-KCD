<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Kategori Pelanggaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.indisipliner.guru.pengaturan.kategori.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <label class="form-label">Nama Kategori</label>
          <input type="text" name="nama" class="form-control" placeholder="Contoh: Etika" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tambah Jenis Pelanggaran -->
<div class="modal fade" id="modalTambahPelanggaran" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Jenis Pelanggaran Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.indisipliner.guru.pengaturan.poin.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kategori Pelanggaran</label>
            <select name="IDpelanggaran_kategori" class="form-select" required>
              <option value="">Pilih Kategori</option>
              @foreach ($kategoriList as $kategori)
                <option value="{{ $kategori->ID }}">{{ $kategori->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Pelanggaran</label>
            <textarea name="nama" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Poin</label>
            <input type="number" name="poin" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tindakan Langsung</label>
            <textarea name="tindakan" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tambah Sanksi -->
<div class="modal fade" id="modalTambahSanksi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Sanksi Pelanggaran Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.indisipliner.guru.pengaturan.sanksi.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col">
              <label class="form-label">Poin Minimal</label>
              <input type="number" name="poin_min" class="form-control" required>
            </div>
            <div class="col">
              <label class="form-label">Poin Maksimal</label>
              <input type="number" name="poin_max" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Sanksi</label>
            <textarea name="nama" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Penindak</label>
            <input type="text" name="penindak" class="form-control" placeholder="Contoh: Kepala Sekolah|Waka Humas">
            <div class="form-text">Gunakan tanda "|" jika lebih dari satu penindak.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
