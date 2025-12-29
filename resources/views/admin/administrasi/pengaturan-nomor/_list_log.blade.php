<div class="list-group list-group-flush bg-white rounded shadow-sm">
    @forelse($dataLogs as $log)
    <div class="list-group-item list-group-item-action py-3">
        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
            <h6 class="mb-0 fw-bold text-primary">{{ $log->nomor_surat_final }}</h6>
            <small class="text-muted">{{ $log->tanggal_dibuat->format('d M Y, H:i') }}</small>
        </div>
        <div class="d-flex align-items-center justify-content-between">
            <p class="mb-1 text-truncate" style="max-width: 80%;">{{ $log->tujuan }}</p>
            <span class="badge bg-label-secondary" style="font-size: 0.7rem;">{{ $log->kategori }}</span>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="bx bx-history text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2">Belum ada riwayat nomor surat untuk kategori ini.</p>
    </div>
    @endforelse
</div>