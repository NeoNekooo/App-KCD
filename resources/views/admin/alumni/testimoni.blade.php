@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary">Manajemen Testimoni</h4>
            <span class="text-muted fw-light">Alumni / Daftar Testimoni Masuk</span>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">{{ $data->count() }}</h5>
                        <small class="text-muted">Total Masuk</small>
                    </div>
                    <div class="card-icon cursor-pointer">
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-message-square-dots fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">{{ $data->where('tampilkan_testimoni', 1)->count() }}</h5>
                        <small class="text-muted">Ditayangkan</small>
                    </div>
                    <div class="card-icon cursor-pointer">
                        <span class="badge bg-label-success rounded p-2">
                            <i class="bx bx-show fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">{{ $data->where('tampilkan_testimoni', 0)->count() }}</h5>
                        <small class="text-muted">Menunggu / Draft</small>
                    </div>
                    <div class="card-icon cursor-pointer">
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="bx bx-hide fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-check-circle fs-4 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary"><i class="bx bx-list-ul me-2"></i>Daftar Testimoni</h5>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="25%">Profil Alumni</th>
                        <th width="40%">Isi Testimoni</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($item->siswa->nama ?? 'A') }}&background=0D8ABC&color=fff&size=64" 
                                     alt="Avatar" class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <div class="fw-bold text-dark">{{ $item->siswa->nama ?? 'Tanpa Nama' }}</div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        NISN: {{ $item->siswa->nisn ?? '-' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-wrap fst-italic text-secondary" style="max-width: 450px; line-height: 1.5;">
                                    "{{ Str::limit($item->testimoni, 80) }}"
                                </span>
                                @if(strlen($item->testimoni) > 80)
                                    <a href="javascript:void(0);" 
                                       class="small text-primary mt-1 text-decoration-none fw-semibold"
                                       data-bs-toggle="modal" 
                                       data-bs-target="#modalDetail{{ $item->id }}">
                                       Lihat Selengkapnya <i class="bx bx-chevron-right" style="font-size: 10px;"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if($item->tampilkan_testimoni)
                                <span class="badge bg-label-success px-3 py-2 rounded-pill">
                                    <i class="bx bx-check-circle me-1"></i> Tayang
                                </span>
                            @else
                                <span class="badge bg-label-secondary px-3 py-2 rounded-pill">
                                    <i class="bx bx-time-five me-1"></i> Draft
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.alumni.testimoni.toggle', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-sm btn-icon rounded-pill {{ $item->tampilkan_testimoni ? 'btn-outline-danger' : 'btn-outline-primary' }}"
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top"
                                        title="{{ $item->tampilkan_testimoni ? 'Sembunyikan dari Web' : 'Tayangkan di Web' }}">
                                    @if($item->tampilkan_testimoni)
                                        <i class="bx bx-hide"></i>
                                    @else
                                        <i class="bx bx-show"></i>
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title text-white fw-bold">
                                        <i class="bx bxs-quote-left me-2"></i> Testimoni Alumni
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                    <div class="mb-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($item->siswa->nama ?? 'A') }}&background=0D8ABC&color=fff&size=100" 
                                             alt="Avatar" class="rounded-circle shadow-sm mb-2" width="80">
                                        <h5 class="fw-bold mb-0">{{ $item->siswa->nama ?? '-' }}</h5>
                                        <small class="text-muted">{{ $item->siswa->nisn ?? '-' }}</small>
                                    </div>
                                    <div class="bg-light p-3 rounded-3 fst-italic text-secondary">
                                        "{{ $item->testimoni }}"
                                    </div>
                                    <div class="mt-3 text-start">
                                        <small class="text-muted d-block">Status Pekerjaan:</small>
                                        <span class="badge bg-label-info">{{ $item->status_kegiatan ?? 'Belum Diisi' }}</span>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0 justify-content-center">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="bg-label-secondary p-3 rounded-circle mb-3">
                                    <i class="bx bx-message-square-x fs-1 text-secondary"></i>
                                </div>
                                <h6 class="text-muted fw-bold">Belum ada data testimoni masuk</h6>
                                <p class="text-muted small mb-0">Silakan input data manual atau tunggu alumni mengisi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Aktifkan Tooltip Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush

@endsection