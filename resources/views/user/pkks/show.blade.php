@extends('layouts.admin')

@section('title', 'Isi Penilaian PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('admin.pkks.penilaian.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
                <div class="text-end">
                    <h5 class="mb-0 fw-bold text-primary">{{ $instrumen->nama }}</h5>
                    <small class="text-muted">Target: {{ $kepsek->nama }}</small>
                </div>
            </div>

            <form action="{{ route('admin.pkks.penilaian.store', $instrumen->id) }}" method="POST">
                @csrf
                
                @foreach($instrumen->kompetensis as $komp)
                <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-primary py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bx bx-folder me-2"></i>{{ $komp->nama }}</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60" class="ps-4">No</th>
                                        <th>Indikator Penilaian</th>
                                        <th width="300" class="text-center pe-4">Pilih Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komp->indikators as $ind)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $ind->nomor }}</td>
                                        <td class="py-3">
                                            <div class="fw-medium text-dark mb-1">{{ $ind->kriteria }}</div>
                                            @if($ind->bukti_identifikasi)
                                                <small class="text-muted"><i class="bx bx-info-circle me-1"></i>{{ $ind->bukti_identifikasi }}</small>
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

                <div class="card mb-5 shadow-sm border-0 bg-light p-4 text-center">
                    <div class="mb-3">
                        <i class="bx bx-check-double text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">Konfirmasi Penyelesaian</h5>
                    <p class="text-muted mb-4">Pastikan semua butir penilaian sudah terisi dengan objektif sebelum menekan tombol simpan.</p>
                    <div class="d-grid col-md-6 mx-auto">
                        <button type="submit" class="btn btn-success btn-lg shadow">
                            <i class="bx bx-save me-2"></i> Simpan Penilaian Selesai
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
</style>
@endsection
