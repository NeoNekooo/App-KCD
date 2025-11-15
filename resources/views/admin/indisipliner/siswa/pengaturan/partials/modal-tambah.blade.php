<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Pelanggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.indisipliner.siswa.pengaturan.kategori.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="namaKategori" class="form-label">Nama Kategori</label>
                        <input type="text" id="namaKategori" name="nama" class="form-control" placeholder="Contoh: Kerapihan" required />
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

<!-- Modal Tambah Pelanggaran -->
<div class="modal fade" id="modalTambahPelanggaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jenis Pelanggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.indisipliner.siswa.pengaturan.poin.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kategoriSelect" class="form-label">Kategori Pelanggaran</label>
                        <select id="kategoriSelect" name="IDpelanggaran_kategori" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategoriList as $kategori)
                            <option value="{{ $kategori->ID }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="namaPelanggaran" class="form-label">Nama Pelanggaran</label>
                        <textarea id="namaPelanggaran" name="nama" class="form-control" rows="2" placeholder="Contoh: Membuang sampah sembarangan" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="poinPelanggaran" class="form-label">Poin</label>
                        <input type="number" id="poinPelanggaran" name="poin" class="form-control" placeholder="Contoh: 5" required />
                    </div>
                    <div class="mb-3">
                        <label for="tindakanLangsung" class="form-label">Tindakan Langsung</label>
                        <textarea id="tindakanLangsung" name="tindakan" class="form-control" rows="2" placeholder="Contoh: Membersihkan area sekitar"></textarea>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Sanksi Pelanggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.indisipliner.siswa.pengaturan.sanksi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label">Poin Minimal</label>
                            <input type="number" name="poin_min" class="form-control" placeholder="Contoh: 10" required />
                        </div>
                        <div class="col">
                             <label class="form-label">Poin Maksimal</label>
                            <input type="number" name="poin_max" class="form-control" placeholder="Contoh: 20" required />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Sanksi</label>
                        <textarea name="nama" class="form-control" rows="2" placeholder="Contoh: Peringatan Lisan" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penindak</label>
                        <input type="text" name="penindak" class="form-control" placeholder="Contoh: Wali Kelas|BK" />
                        <div class="form-text">Pisahkan dengan tanda | jika penindak lebih dari satu.</div>
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

