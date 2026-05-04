@extends('layouts.admin')

@section('title', 'Penilaian PKKS - ' . ($kepsek->nama ?? 'Unit Kerja'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Sticky Header with Progress Bar --}}
    <div class="sticky-top-panel mb-4 animate__animated animate__fadeInDown">
        <div class="card border-0 shadow-lg glass-header overflow-hidden">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-label-primary me-3 rounded-circle d-none d-md-flex shadow-sm">
                            <span class="avatar-initial rounded-circle bg-gradient-primary text-white">
                                <i class="bx bx-spreadsheet fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-black text-dark tracking-tight">{{ $instrumen->nama }}</h4>
                            <div class="d-flex align-items-center text-muted small">
                                <span class="badge bg-label-primary rounded-pill me-2">Tahun {{ $instrumen->tahun }}</span>
                                <i class="bx bx-buildings me-1"></i> {{ $kepsek->nama ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="progress-container text-end" style="min-width: 200px;">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="fw-bold text-primary">Progress Pengisian</span>
                            <span id="progress-percent" class="fw-bold text-primary">0%</span>
                        </div>
                        <div class="progress shadow-sm" style="height: 10px; border-radius: 10px;">
                            <div id="main-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-gradient-primary" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Floating Navigation Sidebar --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sticky-sidebar">
                <div class="card border-0 shadow-sm glass-sidebar rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 fw-bold text-white"><i class="bx bx-list-ul me-2"></i>Daftar Kategori</h6>
                    </div>
                    <div class="list-group list-group-flush" id="category-nav">
                        @foreach($kompetensis as $parent)
                            <a href="#section-{{ $parent->id }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center border-0">
                                <i class="bx bx-chevron-right me-2 opacity-50"></i>
                                <span class="small fw-bold text-uppercase">{{ $parent->nama }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="alert alert-label-info border-0 rounded-4 shadow-sm p-4">
                    <div class="d-flex">
                        <i class="bx bx-info-circle fs-3 me-2"></i>
                        <small>Klik butir skor pada setiap indikator. Pastikan semua indikator terisi sebelum mengirim jawaban.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Questions --}}
        <div class="col-lg-9">
            <form action="{{ route('admin.pkks.penilaian.store', $instrumen->id) }}" id="pkks-form" method="POST">
                @csrf
                
                @foreach($kompetensis as $parent)
                <div id="section-{{ $parent->id }}" class="mb-5 section-anchor">
                    <div class="section-title mb-3 d-flex align-items-center">
                        <div class="avatar avatar-sm bg-label-primary rounded me-2">
                            <span class="avatar-initial rounded bg-gradient-primary text-white"><i class="bx bx-layer"></i></span>
                        </div>
                        <h5 class="mb-0 fw-black text-dark text-uppercase tracking-tight">{{ $parent->nama }}</h5>
                    </div>

                    @foreach($parent->children as $child)
                    <div class="card mb-4 border-0 shadow-md glass-question rounded-4 overflow-hidden border-start border-primary border-5">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-folder me-2"></i>{{ $child->nama }}</h6>
                            <span class="badge bg-label-secondary rounded-pill">{{ $child->indikators->count() }} Indikator</span>
                        </div>
                        <div class="card-body p-0">
                            @foreach($child->indikators as $ind)
                            <div class="question-row p-4 border-bottom transition-all">
                                <div class="row align-items-center">
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <div class="d-flex">
                                            <span class="badge bg-label-primary rounded me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; min-width: 35px;">{{ $ind->nomor }}</span>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark lh-base">{{ $ind->kriteria }}</h6>
                                                @if($ind->bukti_identifikasi)
                                                    <div class="d-flex align-items-start mt-2">
                                                        <i class="bx bx-file-find me-1 text-info mt-1"></i>
                                                        <small class="text-muted fst-italic">Bukti: {{ $ind->bukti_identifikasi }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex justify-content-center justify-content-md-end gap-2 score-group" data-ind="{{ $ind->id }}">
                                            @for($i = 1; $i <= $instrumen->skor_maks; $i++)
                                            <div class="score-item">
                                                <input type="radio" name="jawaban[{{ $ind->id }}]" id="score-{{ $ind->id }}-{{ $i }}" value="{{ $i }}" class="btn-check btn-pkks" required>
                                                <label class="btn btn-outline-score rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                                       for="score-{{ $ind->id }}-{{ $i }}" style="width: 45px; height: 45px;">
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

                {{-- Catatan Penilaian --}}
                <div class="card border-0 shadow-md glass-question rounded-4 overflow-hidden mb-4 border-start border-info border-5">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-info"><i class="bx bx-comment-detail me-2"></i>Catatan Pengawas (Opsional)</h6>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="catatan" class="form-control border-2 rounded-3" rows="4" placeholder="Tuliskan catatan atau rekomendasi hasil penilaian di sini..."></textarea>
                    </div>
                </div>

                {{-- Footer Submit --}}
                <div class="card mb-5 shadow-lg border-0 bg-gradient-primary rounded-4 p-5 text-center text-white overflow-hidden position-relative">
                    {{-- Decorative Circle --}}
                    <div class="position-absolute opacity-10" style="top: -50px; right: -50px;">
                        <i class="bx bxs-check-circle" style="font-size: 200px;"></i>
                    </div>
                    
                    <div class="position-relative">
                        <div class="mb-3 animate__animated animate__pulse animate__infinite">
                            <i class="bx bxs-send" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-black text-white mb-2">Penilaian Selesai?</h3>
                        <p class="mb-4 opacity-75">Pastikan semua data sudah terisi dengan benar. Hasil penilaian tidak dapat diubah setelah dikirim.</p>
                        <div class="d-grid col-md-6 mx-auto">
                            <button type="submit" class="btn btn-white btn-lg shadow-lg hover-scale py-3 rounded-pill fw-bold text-primary">
                                <i class="bx bx-paper-plane me-2 fs-4"></i> KIRIM JAWABAN SEKARANG
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');

    body { background-color: #f8f9ff; }
    .fw-black { font-family: 'Outfit', sans-serif; font-weight: 900; }
    .tracking-tight { letter-spacing: -1px; }

    .sticky-top-panel {
        position: sticky;
        top: 80px;
        z-index: 1020;
    }

    .glass-header {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 1.5rem !important;
    }

    .glass-question {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #696cff 0%, #3f42ff 100%) !important;
    }

    .btn-white { background: #fff; color: #696cff; border: none; }
    .btn-white:hover { background: #f8f9ff; color: #3f42ff; transform: translateY(-3px); }

    .sticky-sidebar {
        position: sticky;
        top: 180px;
    }

    .glass-sidebar {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .list-group-item { background: transparent; transition: all 0.2s; }
    .list-group-item:hover { background: rgba(105, 108, 255, 0.1); color: #696cff; padding-left: 1.5rem !important; }
    .list-group-item.active { background: #696cff; color: #fff; }

    .btn-outline-score {
        border: 2px solid #e0e0e0;
        color: #888;
        font-weight: 700;
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-outline-score:hover {
        border-color: #696cff;
        color: #696cff;
        background-color: rgba(105, 108, 255, 0.05);
    }

    .btn-check:checked + .btn-outline-score {
        background: #696cff !important;
        border-color: #696cff !important;
        color: #fff !important;
        box-shadow: 0 8px 15px rgba(105, 108, 255, 0.4) !important;
        transform: scale(1.15);
    }

    .question-row { transition: all 0.3s; border-radius: 1rem; margin: 5px; }
    .question-row:hover { background-color: rgba(105, 108, 255, 0.04); }
    .question-row.active { background-color: rgba(105, 108, 255, 0.08); }

    .hover-scale { transition: all 0.3s; }
    .hover-scale:hover { transform: scale(1.03); }

    /* Custom Alert Label */
    .alert-label-info {
        background-color: #e7f3ff;
        color: #03c3ec;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalQuestions = document.querySelectorAll('.score-group').length;
        const progressBar = document.getElementById('main-progress-bar');
        const progressPercent = document.getElementById('progress-percent');
        const radios = document.querySelectorAll('.btn-pkks');

        function updateProgress() {
            const answeredQuestions = new Set();
            document.querySelectorAll('.btn-pkks:checked').forEach(radio => {
                answeredQuestions.add(radio.name);
            });

            const percent = Math.round((answeredQuestions.size / totalQuestions) * 100);
            progressBar.style.width = percent + '%';
            progressPercent.innerText = percent + '%';

            if(percent === 100) {
                progressBar.classList.remove('bg-gradient-primary');
                progressBar.classList.add('bg-success');
            } else {
                progressBar.classList.add('bg-gradient-primary');
                progressBar.classList.remove('bg-success');
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Highlight current row
                const row = this.closest('.question-row');
                row.classList.add('active');
                updateProgress();
            });
        });

        // Smooth Scroll for Sidebar
        document.querySelectorAll('#category-nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                const headerOffset = 200;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Active State
                document.querySelectorAll('#category-nav a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Initialize progress
        updateProgress();
    });
</script>
@endpush

{{-- Animate.css --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection
