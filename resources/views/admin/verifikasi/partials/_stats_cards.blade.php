{{-- Card 1: Tiket Baru --}}
<div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted fw-bold small text-uppercase">Tiket Baru</span>
                <h3 class="mb-0 mt-2 fw-bold text-primary">{{ $count_proses ?? 0 }}</h3>
            </div>
            <div class="avatar bg-label-primary rounded p-2">
                <i class="bx bx-list-plus fs-3"></i>
            </div>
        </div>
    </div>
</div>

{{-- Card 2: Menunggu Upload --}}
<div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted fw-bold small text-uppercase">Tunggu Berkas</span>
                <h3 class="mb-0 mt-2 fw-bold text-warning">{{ $count_upload ?? 0 }}</h3>
            </div>
            <div class="avatar bg-label-warning rounded p-2">
                <i class="bx bx-cloud-upload fs-3"></i>
            </div>
        </div>
    </div>
</div>

{{-- Card 3: Siap Diperiksa --}}
<div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted fw-bold small text-uppercase">Siap Periksa</span>
                <h3 class="mb-0 mt-2 fw-bold text-info">{{ $count_verifikasi ?? 0 }}</h3>
            </div>
            <div class="avatar bg-label-info rounded p-2">
                <i class="bx bx-search-alt fs-3"></i>
            </div>
        </div>
    </div>
</div>

{{-- Card 4: Selesai --}}
<div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted fw-bold small text-uppercase">Selesai (ACC)</span>
                <h3 class="mb-0 mt-2 fw-bold text-success">{{ $count_selesai ?? 0 }}</h3>
            </div>
            <div class="avatar bg-label-success rounded p-2">
                <i class="bx bx-check-double fs-3"></i>
            </div>
        </div>
    </div>
</div>