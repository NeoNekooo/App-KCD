@extends('layouts.admin')

@section('title', 'Isi Penilaian PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header Info --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="mb-1 fw-bold text-primary">{{ $instrumen->nama }}</h4>
                    <p class="text-muted mb-0"><i class="bx bx-buildings me-1"></i> Penilaian untuk: <strong>{{ $kepsek->nama ?? '-' }}</strong></p>
                </div>
                <div class="text-end">
                    <span class="badge bg-label-primary fs-6">Tahun {{ $instrumen->tahun }}</span>
                </div>
            </div>

            <form action="{{ route('admin.pkks.penilaian.store', $instrumen->id) }}" method="POST">
                @csrf
                
                {{-- LOOP POINT UTAMA (LEVEL 1) --}}
                @foreach($kompetensis as $parent)
                <div class="mb-5">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm bg-primary rounded me-2">
                            <span class="avatar-initial"><i class="bx bx-layer"></i></span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark text-uppercase">{{ $parent->nama }}</h5>
                    </div>

                    {{-- LOOP SUB KATEGORI (LEVEL 2: MANAJERIAL, DLL) --}}
                    @foreach($parent->children as $child)
                    <div class="card mb-4 border-0 shadow-sm overflow-hidden border-start border-primary border-4">
                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-folder me-2"></i>{{ $child->nama }}</h6>
                            <span class="badge bg-white text-muted shadow-sm">{{ $child->indikators->count() }} Butir Soal</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light small text-uppercase">
                                        <tr>
                                            <th width="60" class="ps-4">No</th>
                                            <th>Indikator Penilaian</th>
                                            <th width="300" class="text-center pe-4">Pilih Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($child->indikators as $ind)
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">{{ $ind->nomor }}</td>
                                            <td class="py-3">
                                                <div class="fw-medium text-dark mb-1">{{ $ind->kriteria }}</div>
                                                @if($ind->bukti_identifikasi)
                                                    <small class="text-muted"><i class="bx bx-info-circle me-1 text-info"></i>{{ $ind->bukti_identifikasi }}</small>
                                                @endif
                                            </td>
                                            <td class="pe-4 py-3">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @for($i = 1; $i <= $instrumen->skor_maks; $i++)
                                                    <div class="score-option">
                                                        <input type="radio" name="jawaban[{{ $ind->id }}]" id="score-{{ $ind->id }}-{{ $i }}" value="{{ $i }}" class="btn-check" required>
                                                        <label class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                                                               for="score-{{ $ind->id }}-{{ $i }}" style="width: 35px; height: 35px;">
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
                    <div class="mb-3">
                        <i class="bx bx-check-double text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Penilaian Selesai?</h5>
                    <p class="text-muted mb-4">Pastikan semua butir skor sudah terisi sebelum mengirimkan hasil penilaian.</p>
                    <div class="d-grid col-md-6 mx-auto">
                        <button type="submit" class="btn btn-primary btn-lg shadow">
                            <i class="bx bx-save me-2"></i> Kirim Jawaban Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-check:checked + .btn-outline-primary {
        background-color: #696cff !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(105, 108, 255, 0.4);
        transform: scale(1.1);
    }
    .score-option label { transition: all 0.2s ease; cursor: pointer; border-width: 2px; }
    .score-option label:hover { background-color: rgba(105, 108, 255, 0.1); }
    table tr:hover { background-color: rgba(105, 108, 255, 0.02); }
    .border-start-primary { border-left: 4px solid #696cff !important; }
</style>
@endsection
