@extends('layouts.admin')

@section('title', 'Manajemen Instrumen PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS /</span> Instrumen</h4>
            <p class="text-muted mb-0 small">Kelola kumpulan indikator penilaian kinerja kepala sekolah</p>
        </div>
        <a href="{{ route('admin.pkks.instrumen.create') }}" class="btn btn-primary shadow">
            <i class="bx bx-plus-circle me-1"></i> Buat Paket Baru
        </a>
    </div>

    <div class="row">
        @forelse($instrumens as $item)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 instrument-card overflow-hidden">
                <div class="bg-primary bg-opacity-10 py-3 px-4 d-flex justify-content-between align-items-center border-bottom border-primary border-opacity-10">
                    <div class="d-flex gap-2">
                        <span class="badge bg-white text-primary shadow-sm px-3 py-2"><i class="bx bx-calendar me-1"></i> Tahun {{ $item->tahun }}</span>
                        <span class="badge bg-primary shadow-sm px-3 py-2 text-white"><i class="bx bx-buildings me-1"></i> {{ $item->jenjang }}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm text-primary dropdown-toggle hide-arrow p-0" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-4"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow">
                            <a class="dropdown-item" href="{{ route('admin.pkks.instrumen.manage', $item->id) }}"><i class="bx bx-cog me-2"></i>Kelola Soal</a>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item text-danger" onclick="tampilKonfirmasiHapus('{{ route('admin.pkks.instrumen.destroy', $item->id) }}', 'Paket instrumen dan semua soal di dalamnya akan dihapus permanen!')">
                                <i class="bx bx-trash me-2"></i>Hapus Paket
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h5 class="card-title fw-bold text-dark mb-1">{{ $item->nama }}</h5>
                        <div class="text-muted small">Dibuat pada {{ $item->created_at->format('d M Y') }}</div>
                    </div>
                    
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="bg-light rounded p-3 text-center border">
                                <div class="h5 fw-bold mb-0 text-primary">{{ $item->kompetensis_count }}</div>
                                <small class="text-muted d-block" style="font-size: 10px;">KOMPETENSI</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-3 text-center border">
                                <div class="h5 fw-bold mb-0 text-success">1 - {{ $item->skor_maks }}</div>
                                <small class="text-muted d-block" style="font-size: 10px;">SKALA SKOR</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-4">
                        <div class="col-12">
                            <div class="bg-light rounded p-2 border small">
                                <div class="text-muted mb-1" style="font-size: 10px;"><i class="bx bx-time me-1"></i> JADWAL PENGISIAN:</div>
                                <div class="fw-bold text-dark">
                                    {{ $item->start_at ? $item->start_at->format('d/m/Y H:i') : '-' }} s/d 
                                    {{ $item->end_at ? $item->end_at->format('d/m/Y H:i') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.pkks.instrumen.manage', $item->id) }}" class="btn btn-label-primary flex-grow-1 border-2 fw-bold">
                            <i class="bx bx-cog me-2"></i> Kelola
                        </a>
                        <button class="btn btn-label-danger border-2 fw-bold" onclick="tampilKonfirmasiHapus('{{ route('admin.pkks.instrumen.destroy', $item->id) }}', 'Hapus paket ini?')">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted fst-italic">Belum ada paket instrumen.</p>
        </div>
        @endforelse
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger py-3">
                    <h5 class="modal-title text-white fw-bold"><i class="bx bx-trash me-2"></i>Konfirmasi</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="avatar avatar-xl bg-label-danger mx-auto mb-3">
                        <span class="avatar-initial rounded-circle"><i class="bx bx-error fs-1"></i></span>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus Paket?</h5>
                    <p class="text-muted small" id="teks-konfirmasi">Semua soal di dalamnya akan hilang permanen.</p>
                </div>
                <div class="modal-footer border-top p-3 d-flex justify-content-center">
                    <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <form id="form-hapus-fix" action="" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 shadow">Ya, Hapus!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .instrument-card { transition: all 0.3s ease; border-radius: 12px !important; }
    .instrument-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(105, 108, 255, 0.1) !important; }
    .btn-label-primary { background-color: #f0f1ff; color: #696cff; border: none; }
    .btn-label-primary:hover { background-color: #696cff; color: #fff; }
    .btn-label-danger { background-color: #fff1f0; color: #ff3e1d; border: none; }
    .btn-label-danger:hover { background-color: #ff3e1d; color: #fff; }

    .btn-close-custom {
        background-color: #ffffff;
        border: none;
        color: #333333;
        font-size: 1.5rem;
        font-weight: bold;
        line-height: 1;
        padding: 0 8px;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .btn-close-custom:hover { background-color: #f8f9fa; color: #ff3e1d; transform: scale(1.1); }
</style>
@endsection

@push('scripts')
<script>
    function tampilKonfirmasiHapus(url, text = 'Data ini akan dihapus secara permanen.') {
        const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus'));
        const form = document.getElementById('form-hapus-fix');
        const teks = document.getElementById('teks-konfirmasi');
        form.action = url;
        teks.innerText = text;
        modal.show();
    }
</script>
@endpush
