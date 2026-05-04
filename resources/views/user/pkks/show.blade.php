@extends('layouts.admin')

@section('title', 'Penilaian PKKS - Focus Mode')

@section('content')
{{-- Custom Modern Fonts --}}
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800;900&family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">

<div class="assessment-wrapper">
    {{-- Mesh Gradient Background --}}
    <div class="mesh-gradient"></div>

    {{-- Top Navigation --}}
    <div class="top-nav animate__animated animate__fadeIn">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center h-100">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.dashboard.sekolah') }}" class="btn btn-icon btn-light rounded-circle me-3 shadow-sm">
                    <i class="bx bx-chevron-left fs-4"></i>
                </a>
                <h5 class="mb-0 fw-800 text-dark tracking-tight d-none d-md-block">{{ $instrumen->nama }}</h5>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="progress-circle-container">
                    <svg class="progress-ring" width="50" height="50">
                        <circle class="progress-ring__circle-bg" stroke="#eee" stroke-width="4" fill="transparent" r="20" cx="25" cy="25"/>
                        <circle class="progress-ring__circle" id="circle-progress" stroke="#696cff" stroke-width="4" fill="transparent" r="20" cx="25" cy="25"/>
                    </svg>
                    <span class="progress-text" id="progress-percent">0%</span>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="submitForm()">
                    <i class="bx bx-check-double me-1"></i> Kirim
                </button>
            </div>
        </div>
    </div>

    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                
                {{-- Welcome Hero --}}
                <div class="welcome-hero mb-5 animate__animated animate__fadeInUp text-center text-md-start">
                    <span class="badge bg-soft-primary text-primary mb-3 px-3 py-2 rounded-pill fw-bold">Penilaian Aktif</span>
                    <h1 class="display-4 fw-900 text-dark mb-2 lh-1">{{ $instrumen->nama }}</h1>
                    <p class="text-secondary fs-5 mb-0">Instansi: <span class="text-dark fw-bold">{{ $kepsek->nama ?? '-' }}</span> • Tahun {{ $instrumen->tahun }}</p>
                </div>

                <form action="{{ route('admin.pkks.penilaian.store', $instrumen->id) }}" id="pkks-form" method="POST">
                    @csrf
                    
                    @foreach($kompetensis as $parent)
                    <div class="parent-section mb-5">
                        <div class="section-badge mb-4 d-inline-block px-4 py-2 rounded-3 bg-dark text-white fw-800 text-uppercase tracking-widest small shadow-sm">
                            {{ $parent->nama }}
                        </div>

                        @foreach($parent->children as $child)
                        <div class="child-group mb-5">
                            <h4 class="fw-800 text-primary mb-4 ps-3 border-start border-primary border-4">{{ $child->nama }}</h4>
                            
                            @foreach($child->indikators as $ind)
                            <div class="question-card mb-4 p-4 p-md-5 transition-all animate__animated animate__fadeInUp" id="q-{{ $ind->id }}">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="q-number-box me-3 shadow-sm">{{ $ind->nomor }}</div>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-700 text-dark mb-3 lh-base">{{ $ind->kriteria }}</h3>
                                        @if($ind->bukti_identifikasi)
                                        <div class="evidence-pill px-3 py-2 rounded-pill d-inline-flex align-items-center">
                                            <i class="bx bx-paperclip me-2"></i>
                                            <span>Bukti: {{ $ind->bukti_identifikasi }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Modern Score Tiles --}}
                                <div class="score-tiles-container">
                                    <div class="row g-3">
                                        @for($i = 1; $i <= $instrumen->skor_maks; $i++)
                                        <div class="col-3 col-md-3">
                                            <div class="score-tile-item">
                                                <input type="radio" name="jawaban[{{ $ind->id }}]" id="q{{ $ind->id }}-v{{ $i }}" value="{{ $i }}" class="score-input btn-check" required>
                                                <label class="score-tile-label w-100" for="q{{ $ind->id }}-v{{ $i }}">
                                                    <div class="tile-val">{{ $i }}</div>
                                                    <div class="tile-label small text-uppercase fw-bold opacity-50 d-none d-md-block">Skor</div>
                                                </label>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    @endforeach

                    {{-- Dynamic Note Box --}}
                    <div class="note-card p-5 mb-5 rounded-5 shadow-lg border-0 animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle bg-soft-info text-info me-3">
                                <i class="bx bx-edit-alt fs-2"></i>
                            </div>
                            <h4 class="mb-0 fw-800 text-dark">Catatan Tambahan</h4>
                        </div>
                        <textarea name="catatan" class="form-control glass-input fs-5" rows="4" placeholder="Tuliskan catatan observasi Anda di sini..."></textarea>
                    </div>

                    {{-- Centered Submit --}}
                    <div class="text-center py-5">
                        <button type="submit" class="btn btn-primary btn-xl rounded-pill px-5 py-4 shadow-primary hover-lift">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-cloud-upload fs-2 me-3"></i>
                                <div class="text-start">
                                    <div class="fw-900 lh-1 fs-3">KIRIM PENILAIAN</div>
                                    <div class="small opacity-75">Proses data sekarang</div>
                                </div>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #696cff;
        --dark: #121212;
        --bg-color: #fbfbfd;
        --card-bg: rgba(255, 255, 255, 0.9);
    }

    body { 
        background-color: var(--bg-color); 
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--dark);
    }

    .fw-700 { font-weight: 700; }
    .fw-800 { font-weight: 800; }
    .fw-900 { font-family: 'Outfit', sans-serif; font-weight: 900; }
    
    .assessment-wrapper { position: relative; overflow-x: hidden; }

    /* Mesh Gradient BG */
    .mesh-gradient {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
        background-color: #fbfbfd;
        background-image: 
            radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
            radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
            radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
        opacity: 0.03;
    }

    /* Top Nav */
    .top-nav {
        position: fixed; top: 0; left: 0; width: 100%; height: 80px;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(20px);
        z-index: 1050;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    /* Progress Circle */
    .progress-circle-container { position: relative; display: flex; align-items: center; justify-content: center; }
    .progress-ring__circle { 
        transition: stroke-dashoffset 0.35s; transform: rotate(-90deg); transform-origin: 50% 50%;
    }
    .progress-text { position: absolute; font-size: 0.75rem; font-weight: 800; color: var(--primary); }

    /* Question Card */
    .question-card {
        background: #fff;
        border-radius: 2rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.02);
    }
    .question-card.active {
        box-shadow: 0 20px 60px rgba(105, 108, 255, 0.1);
        border-color: var(--primary);
        transform: scale(1.02);
    }
    .q-number-box {
        width: 50px; height: 50px; min-width: 50px;
        background: var(--dark); color: #fff;
        border-radius: 15px; display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 1.2rem;
    }
    .evidence-pill { background: #f0f1f4; color: #666; font-size: 0.85rem; font-weight: 700; }

    /* Score Tiles */
    .score-tile-label {
        background: #f8f9fa; border: 2px solid transparent; border-radius: 1.2rem;
        padding: 1.5rem 1rem; text-align: center; cursor: pointer; transition: all 0.3s;
    }
    .score-tile-label:hover { background: #f0f1f4; transform: translateY(-5px); }
    .tile-val { font-size: 1.8rem; font-weight: 900; line-height: 1; margin-bottom: 5px; color: var(--dark); }
    
    .btn-check:checked + .score-tile-label {
        background: #fff !important;
        border-color: var(--primary) !important;
        box-shadow: 0 15px 30px rgba(105, 108, 255, 0.2) !important;
    }
    .btn-check:checked + .score-tile-label .tile-val { color: var(--primary); }

    /* Note Card */
    .note-card { background: #fff; }
    .glass-input { 
        background: #f8f9fa; border: 2px solid transparent; border-radius: 1.5rem; padding: 1.5rem;
        transition: all 0.3s;
    }
    .glass-input:focus { background: #fff; border-color: var(--primary); box-shadow: none; }

    /* Utils */
    .bg-soft-primary { background: rgba(105, 108, 255, 0.1); }
    .bg-soft-info { background: rgba(3, 195, 236, 0.1); }
    .shadow-primary { box-shadow: 0 15px 40px rgba(105, 108, 255, 0.3) !important; }
    .btn-xl { padding: 1.5rem 3rem !important; }
    .hover-lift:hover { transform: translateY(-5px); }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const circle = document.getElementById('circle-progress');
        const radius = circle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;
        
        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference;

        function setProgress(percent) {
            const offset = circumference - (percent / 100 * circumference);
            circle.style.strokeDashoffset = offset;
            document.getElementById('progress-percent').innerText = Math.round(percent) + '%';
        }

        const total = document.querySelectorAll('.score-tiles-container').length;
        const radios = document.querySelectorAll('.score-input');

        function updateProgress() {
            const answered = new Set();
            document.querySelectorAll('.score-input:checked').forEach(r => answered.add(r.name));
            const pc = (answered.size / total) * 100;
            setProgress(pc);
        }

        radios.forEach(r => {
            r.addEventListener('change', function() {
                updateProgress();
                // Focus Mode Effect
                document.querySelectorAll('.question-card').forEach(c => c.classList.remove('active'));
                this.closest('.question-card').classList.add('active');
            });
        });

        window.submitForm = function() {
            document.getElementById('pkks-form').submit();
        }

        // Init
        updateProgress();
    });
</script>
@endpush
@endsection
