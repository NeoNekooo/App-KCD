<!-- Modal Edit Pelanggaran -->
<div class="modal fade" id="modalEditPelanggaran" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Jenis Pelanggaran Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST" id="formEditPoin">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kategori Pelanggaran</label>
            <select name="IDpelanggaran_kategori" class="form-select" required>
              @foreach ($kategoriList as $kategori)
                <option value="{{ $kategori->ID }}">{{ $kategori->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Pelanggaran</label>
            <textarea name="nama" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Poin</label>
            <input type="number" name="poin" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Tindakan</label>
            <textarea name="tindakan" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Sanksi -->
<div class="modal fade" id="modalEditSanksi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Sanksi Pelanggaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST" id="formEditSanksi">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col">
              <label class="form-label">Poin Minimal</label>
              <input type="number" name="poin_min" class="form-control">
            </div>
            <div class="col">
              <label class="form-label">Poin Maksimal</label>
              <input type="number" name="poin_max" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Sanksi</label>
            <textarea name="nama" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Penindak</label>
            <input type="text" name="penindak" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
