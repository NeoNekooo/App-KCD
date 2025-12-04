<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bx bx-error-circle me-1"></i> Konfirmasi Hapus
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="formHapusGlobal">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <p class="mb-0">
                        Apakah Anda yakin ingin menghapus data ini?<br>
                        <strong class="text-danger">Tindakan ini tidak dapat dibatalkan.</strong>
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash me-1"></i> Hapus
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
