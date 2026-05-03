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
                    <span class="badge bg-white text-primary shadow-sm px-3 py-2"><i class="bx bx-calendar me-1"></i> Tahun {{ $item->tahun }}</span>
                    <div class="dropdown">
                        <button class="btn p-0 text-primary opacity-50 hover-opacity-100" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-4"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('admin.pkks.instrumen.destroy', $item->id) }}', 'Paket instrumen dan semua soal di dalamnya akan dihapus permanen!')">
                                <i class="bx bx-trash me-2"></i> Hapus Paket
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
                    
                    <div class="d-grid">
                        <a href="{{ route('admin.pkks.instrumen.manage', $item->id) }}" class="btn btn-label-primary border-2 fw-bold">
                            <i class="bx bx-cog me-2"></i> Kelola Butir Soal
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-none border-dashed text-center py-5 bg-transparent" style="border: 2px dashed #d9dee3 !important;">
                <div class="card-body">
                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3">
                        <span class="avatar-initial rounded-circle"><i class="bx bx-file" style="font-size: 3rem;"></i></span>
                    </div>
                    <h5>Belum Ada Instrumen</h5>
                    <p class="text-muted mb-4">Mulai dengan membuat paket instrumen penilaian pertama kamu.</p>
                    <a href="{{ route('admin.pkks.instrumen.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Buat Sekarang
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Hidden Delete Form --}}
<form id="form-delete" action="" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>

<style>
    .instrument-card { transition: all 0.3s ease; }
    .instrument-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(105, 108, 255, 0.1) !important; }
    .btn-label-primary { background-color: #f0f1ff; color: #696cff; border: none; }
    .btn-label-primary:hover { background-color: #696cff; color: #fff; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(url, text = 'Data yang dihapus tidak bisa dikembalikan!') {
        Swal.fire({
            title: 'Hapus Paket Ini?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#ff3e1d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('form-delete');
                form.action = url;
                form.submit();
            }
        })
    }
</script>
@endpush
