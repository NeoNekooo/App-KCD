<!-- Modal Edit Kategori -->
{{-- Anda bisa menambahkan modal edit kategori di sini jika diperlukan --}}

<!-- Modal Edit Pelanggaran (Poin) -->
<div class="modal fade" id="modalEditPelanggaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jenis Pelanggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST"> {{-- Action akan diisi oleh JS --}}
                @csrf
                @method('PUT')
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
                        <input type="number" name="poin" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tindakan Langsung</label>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sanksi Pelanggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST"> {{-- Action akan diisi oleh JS --}}
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label">Poin Minimal</label>
                            <input type="number" name="poin_min" class="form-control" required />
                        </div>
                        <div class="col">
                             <label class="form-label">Poin Maksimal</label>
                            <input type="number" name="poin_max" class="form-control" required />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Sanksi</label>
                        <textarea name="nama" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penindak</label>
                        <input type="text" name="penindak" class="form-control" />
                        <div class="form-text">Pisahkan dengan tanda | jika penindak lebih dari satu.</div>
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

