@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <style>
        /* Gaya Hover Logo */
        .avatar-hover {
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            cursor: zoom-in;
            position: relative;
            transform-origin: center center; /* Zoom dari tengah biar aman */
            z-index: 1;
        }
        .avatar-hover:hover {
            transform: scale(2.5); /* Skala 2.5x lebih aman buat layout */
            z-index: 99 !important;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important;
            border-radius: 8px !important;
        }
        .instansi-name {
            white-space: normal !important;
            min-width: 250px;
            line-height: 1.4;
        }
        tr:hover {
            z-index: 50;
            position: relative;
            background-color: rgba(105, 108, 255, 0.02) !important;
        }
        td {
            position: relative;
        }
    </style>
    {{-- HERO --}}
    <div class="row g-3 mb-4 animate-entry">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(120deg, #696cff, #4345eb); border-radius: 12px;">
                <div class="card-body d-flex align-items-center text-white p-4">
                    <div class="me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(255, 255, 255, 0.2);">
                        <i class='bx bx-buildings text-white' style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h4 class="text-white fw-bold mb-0">Daftar Wilayah (KCD)</h4>
                        <p class="text-white opacity-75 mb-0">Kelola profil dan identitas masing-masing Kantor Cabang Dinas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="card border-0 shadow-lg animate-entry delay-1" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-dark">Data Instansi Wilayah</h5>
            <span class="badge bg-label-primary rounded-pill px-3">{{ $listInstansi->count() }} Wilayah</span>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle table-borderless">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted" style="width: 50px;">ID</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Nama Instansi</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Kepala KCD</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Lokasi</th>
                        <th class="pe-4 py-3 text-end text-uppercase small fw-bold text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listInstansi as $instansi)
                    <tr class="border-bottom">
                        <td class="ps-4 fw-bold text-primary">#{{ $instansi->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($instansi->logo)
                                    <img src="{{ Storage::url($instansi->logo) }}" class="rounded-3 me-3 avatar-hover shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="rounded-3 me-3 bg-label-primary d-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px;">
                                        <i class='bx bx-building fs-4'></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark instansi-name">{{ $instansi->nama_instansi }}</div>
                                    <small class="text-muted">{{ $instansi->nama_brand ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $instansi->nama_kepala ?? '-' }}</div>
                            <small class="text-muted font-monospace">{{ $instansi->nip_kepala ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="small fw-medium text-muted cursor-pointer" 
                                 data-bs-toggle="tooltip" 
                                 data-bs-placement="top" 
                                 data-bs-html="true" 
                                 title="<div class='text-start'><i class='bx bx-map-pin me-1 text-danger'></i><b>Alamat Lengkap:</b><br>{{ $instansi->alamat ?? 'Belum ada alamat' }}</div>">
                                <i class='bx bx-map text-danger me-1'></i>
                                {{ $instansi->alamat ? \Illuminate\Support\Str::limit($instansi->alamat, 40) : 'Belum diatur' }}
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.instansi.index', ['id' => $instansi->id]) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs">
                                <i class="bx bx-show me-1"></i> Lihat Profil
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class='bx bx-objects-horizontal-left text-muted opacity-25' style="font-size: 4rem;"></i>
                                <p class="text-muted mt-3">Belum ada data instansi yang terdaftar.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-entry {
        animation: slideInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        opacity: 0;
    }
    .delay-1 { animation-delay: 0.1s; }
    .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important; }
    .cursor-pointer { cursor: pointer; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
