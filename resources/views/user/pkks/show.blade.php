@extends('layouts.admin')

@section('title', 'Portal Penilaian PKKS')

@section('content')
{{-- Custom Font --}}
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

<div class="container-fluid py-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    {{-- Top Slim Progress --}}
    <div class="fixed-top" style="top: 0; z-index: 1100; height: 6px;">
        <div id="top-progress" class="progress-bar bg-primary" style="width: 0%; height: 100%; transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 0 15px rgba(105, 108, 255, 0.8);"></div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-11">
            
            {{-- Modern Hero Header --}}
            <div class="hero-header mb-5 p-5 rounded-5 position-relative overflow-hidden shadow-lg animate__animated animate__fadeIn">
                <div class="header-overlay"></div>
                <div class="position-relative z-index-1 d-flex flex-wrap justify-content-between align-items-center gap-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-4 shadow-lg animate__animated animate__zoomIn">
                            <i class="bx bxs-award text-white fs-1"></i>
                        </div>
                        <div>
                            <span class="badge bg-white text-primary mb-2 px-3 py-2 rounded-pill fw-bold shadow-sm">PKKS MODUL v2.0</span>
                            <h1 class="display-5 fw-800 text-white mb-1 tracking-tight">{{ $instrumen->nama }}</h1>
                            <p class="text-white opacity-75 mb-0 fs-5"><i class="bx bx-user-circle me-1"></i> Penilai untuk: <strong>{{ $kepsek->nama ?? '-' }}</strong></p>
                        </div>
                    </div>
                    <div class="text-md-end text-white">
                        <div class="fs-2 fw-800 mb-0 animate__animated animate__fadeInRight" id="stat-percent">0%</div>
                        <div class="small text-uppercase tracking-wider opacity-50">Kesiapan Penilaian</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Side Nav - Compact & Modern --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="sticky-top" style="top: 100px;">
                        <div class="glass-sidebar rounded-4 p-4 shadow-sm">
                            <h6 class="text-uppercase small fw-800 text-muted mb-4 tracking-widest">Navigasi Kategori</h6>
                            <nav id="pkks-nav" class="nav flex-column gap-2">
                                @foreach($kompetensis as $parent)
                                <a class="nav-link custom-nav-link d-flex align-items-center rounded-3 p-3" href="#cat-{{ $parent->id }}">
                                    <span class="nav-dot me-3"></span>
                                    <span class="fw-bold small">{{ $parent->nama }}</span>
                                </a>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Main Questions --}}
                <div class="col-lg-9">
                    <form action="{{ route('admin.pkks.penilaian.store', $instrumen->id) }}" id="form-penilaian" method="POST">
                        @csrf
                        
                        @foreach($kompetensis as $parent)
                        <div id="cat-{{ $parent->id }}" class="mb-5 section-group">
                            <div class="d-flex align-items-center mb-4 ps-2 border-start border-primary border-5">
                                <h3 class="fw-800 text-dark mb-0 ms-3">{{ $parent->nama }}</h3>
                            </div>

                            @foreach($parent->children as $child)
                            <div class="sub-category-box mb-4 animate__animated animate__fadeInUp">
                                <div class="p-4 bg-white shadow-sm border-bottom rounded-top-4 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-chevron-right text-primary"></i> {{ $child->nama }}</h5>
                                    <span class="small text-muted">{{ $child->indikators->count() }} Indikator</span>
                                </div>
                                
                                <div class="bg-white rounded-bottom-4 shadow-sm overflow-hidden">
                                    @foreach($child->indikators as $ind)
                                    <div class="question-item p-4 border-bottom position-relative transition-all">
                                        <div class="row align-items-center">
                                            <div class="col-md-7 mb-4 mb-md-0">
                                                <div class="d-flex align-items-start">
                                                    <div class="q-number me-3">{{ $ind->nomor }}</div>
                                                    <div>
                                                        <p class="fs-5 fw-bold text-dark mb-2 lh-base">{{ $ind->kriteria }}</p>
                                                        @if($ind->bukti_identifikasi)
                                                        <div class="evidence-box p-2 rounded-3 small">
                                                            <i class="bx bx-info-circle text-primary me-1"></i>
                                                            <span class="text-muted">Bukti: {{ $ind->bukti_identifikasi }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="score-selector d-flex justify-content-center justify-content-md-end gap-1">
                                                    @for($i = 1; $i <= $instrumen->skor_maks; $i++)
                                                    <div class="score-pill">
                                                        <input type="radio" name="jawaban[{{ $ind->id }}]" id="q{{ $ind->id }}-v{{ $i }}" value="{{ $i }}" class="btn-check score-radio" required>
                                                        <label class="score-label shadow-sm" for="q{{ $ind->id }}-v{{ $i }}">
                                                            {{ $i }}
                                                        </label>
                                                    </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach

                        {{-- Modern Note Section --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
                            <div class="card-header bg-dark p-4">
                                <h5 class="mb-0 text-white fw-bold"><i class="bx bx-edit me-2"></i>Catatan Tambahan</h5>
                            </div>
                            <div class="card-body p-4 bg-light">
                                <textarea name="catatan" class="form-control border-0 shadow-none fs-5" rows="4" placeholder="Tuliskan catatan hasil pemantauan di sini..." style="background: transparent; resize: none;"></textarea>
                            </div>
                        </div>

                        {{-- Final Action --}}
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <div class="pulse-indicator mx-auto"></div>
                            </div>
                            <h2 class="fw-800 text-dark mb-3">Selesaikan Penilaian?</h2>
                            <p class="text-muted mb-5 fs-5">Data yang telah dikirimkan akan diproses menjadi laporan akhir.</p>
                            <button type="submit" class="btn btn-primary btn-submit-custom px-5 py-3 rounded-pill shadow-lg animate__animated animate__pulse animate__infinite">
                                <i class="bx bx-cloud-upload fs-3 me-2"></i> KIRIM DATA PENILAIAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #696cff;
        --secondary-color: #3f42ff;
        --dark-color: #232333;
    }

    .fw-800 { font-weight: 800; }
    
    /* Hero Header Custom */
    .hero-header {
        background: linear-gradient(135deg, var(--dark-color) 0%, #4a4a6a 100%);
        border-radius: 2.5rem !important;
    }
    .header-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .icon-box {
        width: 80px; height: 80px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: center;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Sidebar Custom */
    .glass-sidebar {
        background: #fff;
        border: 1px solid #eee;
    }
    .custom-nav-link {
        color: #666;
        transition: all 0.3s;
    }
    .custom-nav-link:hover {
        background: #f8f9ff;
        color: var(--primary-color);
        transform: translateX(5px);
    }
    .nav-dot {
        width: 8px; height: 8px;
        background: #ddd;
        border-radius: 50%;
        transition: all 0.3s;
    }
    .custom-nav-link.active {
        background: var(--primary-color);
        color: #fff;
    }
    .custom-nav-link.active .nav-dot {
        background: #fff;
        transform: scale(1.5);
    }

    /* Questions Custom */
    .question-item {
        background: #fff;
    }
    .question-item:hover {
        background: #fcfcff;
    }
    .q-number {
        font-size: 0.9rem; font-weight: 800;
        color: var(--primary-color);
        background: rgba(105, 108, 255, 0.1);
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 8px;
    }
    .evidence-box {
        background: #f8f9fa;
        border-left: 3px solid var(--primary-color);
    }

    /* Score Selector Custom (Segmented Style) */
    .score-selector {
        background: #f0f1f4;
        padding: 5px;
        border-radius: 12px;
        display: inline-flex;
    }
    .score-label {
        width: 45px; height: 45px;
        display: flex; align-items: center; justify-content: center;
        background: #fff;
        color: #444;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 800;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .score-label:hover {
        background: #fafafa;
        transform: translateY(-2px);
    }
    .btn-check:checked + .score-label {
        background: var(--primary-color) !important;
        color: #fff !important;
        box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4) !important;
        transform: scale(1.05);
    }

    /* Submit Button */
    .btn-submit-custom {
        background: var(--primary-color);
        border: none;
        font-weight: 800;
        letter-spacing: 1px;
    }
    .pulse-indicator {
        width: 20px; height: 20px;
        background: var(--primary-color);
        border-radius: 50%;
        box-shadow: 0 0 0 rgba(105, 108, 255, 0.4);
        animation: pulse-blue 2s infinite;
    }
    @keyframes pulse-blue {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 20px rgba(105, 108, 255, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(105, 108, 255, 0); }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const total = document.querySelectorAll('.score-selector').length;
        const progress = document.getElementById('top-progress');
        const percentText = document.getElementById('stat-percent');
        const radios = document.querySelectorAll('.score-radio');

        function updateProgress() {
            const answered = new Set();
            document.querySelectorAll('.score-radio:checked').forEach(r => answered.add(r.name));
            const pc = Math.round((answered.size / total) * 100);
            progress.style.width = pc + '%';
            percentText.innerText = pc + '%';
        }

        radios.forEach(r => {
            r.addEventListener('change', function() {
                updateProgress();
                // Scroll effect highlight
                this.closest('.question-item').style.background = '#f8f9ff';
            });
        });

        // Initialize
        updateProgress();

        // Scrollspy-like effect
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.section-group');
            const navLinks = document.querySelectorAll('.custom-nav-link');
            let current = "";

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 250) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    });
</script>
@endpush
@endsection
