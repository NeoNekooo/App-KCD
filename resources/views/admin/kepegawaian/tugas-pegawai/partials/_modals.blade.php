<div class="modal fade" id="modalDetailTugas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rincian Tugas: <span id="detailNamaGuru" class="fw-bold"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Tugas / Mata Pelajaran</th>
                                <th>Kelas / Rombel</th>
                                <th class="text-center">Jam</th>
                            </tr>
                        </thead>
                        <tbody id="isi-tabel-detail">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahStruktural" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.kepegawaian.tugas-pegawai.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jabatan Struktural</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Pilih Pegawai</label>
                    <select name="pegawai_id" class="form-select select2" data-parent="#modalTambahStruktural" required>
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($allGtk as $g)
                            <option value="{{ $g->id }}">{{ Str::title(strtolower($g->nama)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Jabatan</label>
                    <input type="text" name="tugas_pokok" class="form-control" placeholder="Misal: Kepala Lab, Pembina OSIS..." required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ekuivalensi Jam</label>
                        <input type="number" name="jumlah_jam" class="form-control" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor SK (Opsional)</label>
                        <input type="text" name="nomor_sk" class="form-control" placeholder="Isi jika berbeda">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">Simpan Jabatan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditStruktural" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="formEditStruktural" method="POST">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Jabatan Struktural</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2">
                    <small>Pegawai: <b id="edit_nama_pegawai"></b></small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Jabatan</label>
                    <input type="text" name="tugas_pokok" id="edit_tugas_pokok" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ekuivalensi Jam</label>
                        <input type="number" name="jumlah_jam" id="edit_jumlah_jam" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor SK</label>
                        <input type="text" name="nomor_sk" id="edit_nomor_sk" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning w-100">Update Data</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalCetakSk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formCetakSk" method="POST" target="_blank" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Cetak SK Pembagian Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar avatar-xl mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-printer fs-1"></i></span>
                </div>
                <h5><span id="targetNama" class="text-primary"></span></h5>
                <p class="text-muted">Pilih template surat yang akan digunakan untuk menggenerate file PDF.</p>
                <div class="text-start">
                    <label class="form-label">Template SK</label>
                    <select name="template_id" class="form-select" required>
                        <option value="">-- Pilih Template --</option>
                        @foreach(\App\Models\TipeSurat::where('kategori', 'sk')->get() as $tp)
                            <option value="{{ $tp->id }}">{{ $tp->judul_surat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-printer me-1"></i> Cetak Sekarang (PDF)</button>
            </div>
        </form>
    </div>
</div>
