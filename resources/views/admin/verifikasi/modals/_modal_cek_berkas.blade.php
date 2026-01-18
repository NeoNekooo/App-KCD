<div class="modal fade" id="modalSyarat{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Atur Persyaratan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.verifikasi.set_syarat', $item->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body bg-light text-start">
                    <div class="alert alert-primary border-0 mb-3 small">
                        <i class="bx bx-info-circle me-1"></i> Tentukan dokumen yang wajib diunggah oleh sekolah untuk layanan ini.
                    </div>
                    
                    <label class="form-label fw-bold text-muted small">DAFTAR DOKUMEN</label>
                    <div id="containerManual{{ $item->id }}">
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-white"><i class='bx bx-file'></i></span>
                            <input type="text" name="syarat[]" class="form-control" placeholder="Contool: Surat Pengantar Sekolah" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 dashed-border mt-2" onclick="tambahSyaratManual({{ $item->id }})">
                        <i class="bx bx-plus"></i> Tambah Dokumen Lain
                    </button>

                    <hr>
                    <label class="form-label fw-bold text-muted small">OPSI: TOLAK AWAL</label>
                    <textarea name="catatan_awal" class="form-control" rows="2" placeholder="Isi jika pengajuan awal ini langsung ditolak..."></textarea>
                </div>
                <div class="modal-footer border-top bg-white">
                    <button type="submit" name="action" value="reject" class="btn btn-label-danger">Tolak Permohonan</button>
                    <button type="submit" name="action" value="approve" class="btn btn-primary">Minta Upload Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>