<style>
    /* Tambahan animasi detak jantung untuk icon love */
    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.25); }
    }
    .animate-heartbeat { 
        animation: heartbeat 1.5s ease-in-out infinite; 
        display: inline-block; 
    }
    .footer-link-hover { transition: all 0.3s ease; text-decoration: none !important; }
    .footer-link-hover:hover { color: #696cff !important; transform: translateX(2px); }
</style>

<footer class="content-footer footer bg-footer-theme border-top border-light">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-3 flex-md-row flex-column align-items-center">
        
        {{-- KIRI: Copyright & Maker --}}
        <div class="mb-2 mb-md-0 text-muted fw-medium d-flex align-items-center justify-content-center justify-content-md-start text-sm">
            <span>© {{ date('Y') }}</span>
            <span class="mx-2 d-none d-sm-inline-block">|</span>
            <span>Dikembangkan dengan </span>
            <i class="bx bxs-heart text-danger mx-1 animate-heartbeat"></i> 
            <span>oleh</span>
            <a href="https://hexanusa.com" target="_blank" class="footer-link fw-bolder text-primary ms-1" style="letter-spacing: 0.5px;">
                Hexanusa
            </a>
        </div>

        {{-- KANAN: Pintasan & Versi --}}
        <div class="d-flex align-items-center gap-3 mt-2 mt-md-0 text-sm">
            <a href="#" class="text-muted fw-medium footer-link-hover d-none d-md-inline-flex align-items-center">
                <i class="bx bx-support me-1"></i> Bantuan
            </a>
            <a href="#" class="text-muted fw-medium footer-link-hover d-none d-md-inline-flex align-items-center">
                <i class="bx bx-file-blank me-1"></i> Dokumentasi
            </a>
            <span class="badge bg-label-primary rounded-pill px-3 py-1 shadow-xs fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                v1.0.0
            </span>
        </div>
        
    </div>
</footer>