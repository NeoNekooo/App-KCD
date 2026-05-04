@extends('layouts.admin')

@section('title', 'Isi Penilaian PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header Info --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-1 text-primary">{{ $instrumen->nama }}</h4>
                    <p class="text-muted mb-0">
                        <i class="bx bx-user-circle me-1"></i> Penilaian untuk: <strong>{{ $kepsek->nama ?? '-' }}</strong> 
                        <span class="mx-2">|</span>
                        <i class="bx bx-calendar me-1"></i> Tahun {{ $instrumen->tahun }}
                    </p>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-bold">Progress Pengisian</small>
                        <small id="progress-percent" class="text-primary fw-bold">0%</small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div id="main-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.pkks.penilaian.store', $instrumen->id) }}" method="POST">
        @csrf
        
        @foreach($kompetensis as $parent)
        <div class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <div class="avatar avatar-sm bg-primary rounded me-2">
                    <span class="avatar-initial"><i class="bx bx-layer"></i></span>
                </div>
                <h5 class="mb-0 fw-bold text-dark text-uppercase">{{ $parent->nama }}</h5>
            </div>

            @foreach($parent->children as $child)
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-folder me-2"></i>{{ $child->nama }}</h6>
                    <span class="badge bg-white text-muted shadow-sm">{{ $child->indikators->count() }} Indikator</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light small text-uppercase">
                                <tr>
                                    <th width="60" class="ps-4">No</th>
                                    <th>Indikator Penilaian</th>
                                    <th width="300" class="text-center pe-4">Pilih Skor</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($child->indikators as $ind)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $ind->nomor }}</td>
                                    <td class="text-wrap py-3" style="min-width: 300px;">
                                        <div class="fw-bold text-dark mb-1">{{ $ind->kriteria }}</div>
                                        @if($ind->bukti_identifikasi)
                                            <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Bukti: {{ $ind->bukti_identifikasi }}</small>
                                        @endif
                                    </td>
                                    <td class="pe-4 py-3">
                                        <div class="d-flex justify-content-center gap-2">
                                            @for($i = 1; $i <= $instrumen->skor_maks; $i++)
                                            <div class="score-option">
                                                <input type="radio" name="jawaban[{{ $ind->id }}]" id="score-{{ $ind->id }}-{{ $i }}" value="{{ $i }}" class="btn-check score-input" required>
                                                <label class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                                                       for="score-{{ $ind->id }}-{{ $i }}" style="width: 38px; height: 38px;">
                                                    {{ $i }}
                                                </label>
                                            </div>
                                            @endfor
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach

        {{-- Footer Submit --}}
        <div class="card mb-5 shadow-sm border-0 bg-primary bg-opacity-10 p-4 text-center">
            <h5 class="fw-bold mb-2">Penilaian Selesai?</h5>
            <p class="text-muted mb-4 small">Pastikan semua skor sudah terisi dengan benar sebelum mengirimkan hasil penilaian.</p>
            <div class="d-grid col-md-6 mx-auto">
                <button type="submit" class="btn btn-primary btn-lg shadow">
                    <i class="bx bx-save me-2"></i> Kirim Jawaban Sekarang
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .btn-check:checked + .btn-outline-primary {
        background-color: #696cff !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(105, 108, 255, 0.4);
        transform: scale(1.1);
    }
    .score-option label { transition: all 0.2s ease; cursor: pointer; border-width: 2px; font-weight: bold; }
    .score-option label:hover { background-color: rgba(105, 108, 255, 0.1); }
    table tr:hover { background-color: rgba(105, 108, 255, 0.02); }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const total = document.querySelectorAll('.score-input').length / {{ $instrumen->skor_maks }};
        const bar = document.getElementById('main-progress-bar');
        const text = document.getElementById('progress-percent');
        const radios = document.querySelectorAll('.score-input');

        function updateProgress() {
            const answered = new Set();
            document.querySelectorAll('.score-input:checked').forEach(r => answered.add(r.name));
            const pc = Math.round((answered.size / total) * 100);
            bar.style.width = pc + '%';
            text.innerText = pc + '%';
        }

        radios.forEach(r => {
            r.addEventListener('change', updateProgress);
        });

        // Init
        updateProgress();
    });
</script>
@endpush
@endsection
