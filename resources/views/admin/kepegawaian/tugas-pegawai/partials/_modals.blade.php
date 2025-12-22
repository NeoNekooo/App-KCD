<div class="modal fade" id="modalTambahStruktural" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('admin.kepegawaian.tugas-pegawai.store') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Tambah Jabatan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Pegawai</label>
                    <select name="pegawai_id" class="form-select select2" required>
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($allGtk as $g) <option value="{{ $g->id }}">{{ Str::title(strtolower($g->nama)) }}</option> @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Jabatan (Pisah Koma)</label>
                    <textarea name="tugas_pokok" id="input_jabatan" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3"><label class="form-label">Total Jam</label><input type="number" name="jumlah_jam" id="input_jam" class="form-control" value="0"></div>
                <div class="mb-3"><label class="form-label">Nomor SK</label><input type="text" name="nomor_sk" class="form-control"></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditStruktural" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="formEditStruktural" method="POST">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title">Edit Jabatan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p>Pegawai: <b id="edit_nama_pegawai"></b></p>
                <div class="mb-3"><label class="form-label">Jabatan</label><textarea name="tugas_pokok" id="edit_tugas_pokok" class="form-control" rows="3" required></textarea></div>
                <div class="mb-3"><label class="form-label">Total Jam</label><input type="number" name="jumlah_jam" id="edit_jumlah_jam" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Nomor SK</label><input type="text" name="nomor_sk" id="edit_nomor_sk" class="form-control"></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-warning">Update</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalCetakSk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="formCetakSk" target="_blank">
            <div class="modal-header"><h5 class="modal-title">Cetak SK</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p>Cetak untuk: <b id="targetNama"></b></p>
                <select name="template_id" class="form-select" required>
                    @foreach(\App\Models\TipeSurat::where('kategori', 'sk')->get() as $t)
                        <option value="{{ $t->id }}">{{ $t->judul_surat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Preview</button></div>
        </form>
    </div>
</div>
