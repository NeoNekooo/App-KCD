@extends('layouts.admin')

@push('styles')
<style>
    /* ============================================= */
    /*   PREMIUM ORGANIZATIONAL CHART - CSS TREE     */
    /* ============================================= */

    .premium-org-tree * { box-sizing: border-box; }
    .premium-org-tree {
        display: flex;
        justify-content: center;
        padding: 2.5rem 1rem 3rem;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* ---- CONNECTOR LINES ---- */
    .premium-org-tree ul {
        padding-top: 40px;
        position: relative;
        display: flex;
        justify-content: center;
        margin: 0;
        padding-inline-start: 0;
    }
    .premium-org-tree ul ul::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        border-left: 2px solid #94a3b8;
        width: 0;
        height: 40px;
        transform: translateX(-50%);
    }
    .premium-org-tree li {
        float: left;
        text-align: center;
        list-style-type: none;
        position: relative;
        padding: 40px 8px 0 8px;
    }
    .premium-org-tree li::before,
    .premium-org-tree li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid #94a3b8;
        width: 50%;
        height: 40px;
    }
    .premium-org-tree li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #94a3b8;
    }
    .premium-org-tree li:only-child::after,
    .premium-org-tree li:only-child::before { display: none; }
    .premium-org-tree li:only-child { padding-top: 0; }
    .premium-org-tree li:first-child::before,
    .premium-org-tree li:last-child::after { border: 0 none; }
    .premium-org-tree li:last-child::before {
        border-right: 2px solid #94a3b8;
        border-radius: 0 8px 0 0;
    }
    .premium-org-tree li:first-child::after {
        border-radius: 8px 0 0 0;
    }

    /* ---- NODE WRAPPER (Flex Container for assistants + main card) ---- */
    .org-node-wrapper {
        display: inline-flex;
        align-items: center;
        position: relative;
    }

    /* ---- MAIN CARD ---- */
    .org-card {
        width: 200px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 12px rgba(0,0,0,0.04);
        overflow: visible;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
    }
    .org-card:hover {
        box-shadow: 0 8px 25px rgba(59,130,246,0.12), 0 4px 12px rgba(0,0,0,0.06);
        transform: translateY(-2px);
    }
    .org-card-accent {
        height: 5px;
        border-radius: 16px 16px 0 0;
        background: linear-gradient(90deg, #3b82f6, #6366f1);
    }
    .org-card-root .org-card-accent {
        height: 6px;
        background: linear-gradient(90deg, #1e40af, #3b82f6, #6366f1);
    }
    .org-card-body {
        padding: 28px 14px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* ---- AVATAR RING ---- */
    .org-avatar-ring {
        position: absolute;
        top: -24px;
        left: 50%;
        transform: translateX(-50%);
        width: 52px;
        height: 52px;
        border-radius: 50%;
        padding: 3px;
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        box-shadow: 0 4px 12px rgba(59,130,246,0.25);
    }
    .org-card-root .org-avatar-ring {
        width: 58px;
        height: 58px;
        top: -28px;
        background: linear-gradient(135deg, #1e40af, #3b82f6, #818cf8);
        box-shadow: 0 4px 16px rgba(30,64,175,0.3);
    }
    .org-avatar-ring img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ffffff;
    }

    /* ---- TYPOGRAPHY ---- */
    .org-title {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        margin: 4px 0 2px;
        line-height: 1.3;
        text-align: center;
        letter-spacing: -0.01em;
    }
    .org-card-root .org-title {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }
    .org-name {
        font-size: 10px;
        font-weight: 500;
        color: #64748b;
        margin: 0;
        text-align: center;
        line-height: 1.3;
    }

    /* ---- ASSISTANT BRANCH ---- */
    .assistant-branch {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 5;
    }
    .assistant-line {
        width: 28px;
        min-width: 28px;
        border-top: 2px dashed #f59e0b;
    }
    .assistant-cards {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .assistant-card {
        width: 170px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 10px 10px 10px 12px;
        position: relative;
        box-shadow: 0 1px 4px rgba(245,158,11,0.08);
        transition: all 0.3s ease;
    }
    .assistant-card:hover {
        box-shadow: 0 4px 12px rgba(245,158,11,0.15);
        transform: translateY(-1px);
    }
    .assistant-left .assistant-card {
        border-left: 3px solid #f59e0b;
    }
    .assistant-right .assistant-card {
        border-right: 3px solid #f59e0b;
    }

    .assistant-badge {
        position: absolute;
        top: -8px;
        right: 8px;
        background: linear-gradient(90deg, #f59e0b, #d97706);
        color: white;
        font-size: 8px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 10px;
        letter-spacing: 0.03em;
        box-shadow: 0 2px 6px rgba(217,119,6,0.25);
    }
    .assistant-inner {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .assistant-avatar {
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fde68a;
        background: #fff;
    }
    .assistant-info h4 {
        font-size: 10px;
        font-weight: 700;
        color: #92400e;
        margin: 0;
        line-height: 1.3;
    }
    .assistant-info p {
        font-size: 9px;
        color: #a16207;
        margin: 2px 0 0;
        line-height: 1.3;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bolder m-0 text-dark">Struktur Organisasi</h4>
        <span class="text-muted small">Kelola hierarki susunan kepemimpinan untuk ditampilkan di Frontend.</span>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class='bx bx-plus me-1'></i> Tambah Jabatan
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-1'></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <!-- PANEL KIRI: PREVIEW VISUAL -->
    <div class="col-lg-12 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold text-primary mb-0"><i class='bx bx-network-chart me-1'></i> Live Preview Bagan</h6>
            </div>
            <div class="card-body overflow-auto p-4" style="min-height: 500px; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                @if($struktur->count() > 0)
                    @php
                        $roots = $struktur->where('parent_id', null)->where('jenis_hubungan', 'struktural');
                        if($roots->isEmpty() && $struktur->count() > 0) {
                            $roots = $struktur->where('jenis_hubungan', 'struktural')->take(1); 
                        }
                    @endphp
                    
                    <div class="premium-org-tree">
                        <ul>
                            @foreach($roots as $root)
                                <x-org-tree-node :node="$root" :allNodes="$struktur" />
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center text-muted align-self-center">
                        <i class='bx bx-sitemap fs-1 opacity-25 mb-2'></i>
                        <p class="mb-0">Belum ada struktur organisasi.<br>Silakan tambah jabatan pertama (misal: Kepala Dinas).</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- PANEL KANAN: DAFTAR DATA -->
    <div class="col-lg-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom pt-4">
                <h6 class="fw-bold text-dark mb-0"><i class='bx bx-list-ul me-1'></i> Daftar Jabatan</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($struktur as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <div class="d-flex align-items-center">
                                @if($item->foto_pejabat)
                                    <img src="{{ Storage::url($item->foto_pejabat) }}" class="rounded-circle me-3 border" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle me-3 bg-light text-secondary d-flex justify-content-center align-items-center border" style="width: 40px; height: 40px;">
                                        <i class='bx bx-user'></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0 fw-bold">
                                        {{ $item->jabatan }}
                                        @if(in_array($item->jenis_hubungan, ['asisten', 'asisten_kiri', 'asisten_kanan']))
                                            <span class="badge bg-warning ms-1" style="font-size:10px;">
                                                @if($item->jenis_hubungan == 'asisten_kiri') ← Kiri
                                                @else → Kanan
                                                @endif
                                            </span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $item->nama_pejabat ?? '-' }}</small>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-icon btn-text-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}"><i class='bx bx-edit'></i></button>
                                <form action="{{ route('admin.website.struktur.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jabatan ini? Jabatan di bawahnya mungkin akan ikut terhapus atau kehilangan parent.');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon btn-text-danger"><i class='bx bx-trash'></i></button>
                                </form>
                            </div>
                        </li>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.website.struktur.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold">Edit Jabatan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Bawahan Dari (Atasan Laporkan)</label>
                                                <select name="parent_id" class="form-select">
                                                    <option value="">-- Paling Atas (Pucuk Pimpinan) --</option>
                                                    @foreach($struktur as $parent)
                                                        @if($parent->id != $item->id)
                                                            <option value="{{ $parent->id }}" {{ $item->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->jabatan }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Jabatan <span class="text-danger">*</span></label>
                                                <select name="jabatan" class="form-select" required>
                                                    <option value="">-- Pilih Jabatan --</option>
                                                    @foreach($daftar_jabatan as $jbt)
                                                        <option value="{{ $jbt->nama }}" {{ $item->jabatan == $jbt->nama ? 'selected' : '' }}>{{ $jbt->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Jenis Hubungan</label>
                                                <select name="jenis_hubungan" class="form-select" required>
                                                    <option value="struktural" {{ $item->jenis_hubungan == 'struktural' ? 'selected' : '' }}>Struktural (Ke Bawah ↓)</option>
                                                    <option value="asisten_kiri" {{ $item->jenis_hubungan == 'asisten_kiri' ? 'selected' : '' }}>Asisten / Staf Khusus (← Ke Kiri)</option>
                                                    <option value="asisten_kanan" {{ in_array($item->jenis_hubungan, ['asisten_kanan', 'asisten']) ? 'selected' : '' }}>Asisten / Staf Khusus (Ke Kanan →)</option>
                                                </select>
                                                <div class="form-text">Asisten akan digambarkan menyamping (kiri/kanan) dari struktur utama.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Pejabat Sekarang</label>
                                                <input type="text" name="nama_pejabat" class="form-control" value="{{ $item->nama_pejabat }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Ganti Foto Pejabat (Opsional)</label>
                                                <input type="file" name="foto_pejabat" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="submit" class="btn btn-primary w-100 rounded-pill">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <li class="list-group-item text-center p-4 text-muted border-bottom-0">Kosong</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.website.struktur.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Jabatan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Bawahan Dari (Atasan Laporkan)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Paling Atas (Pucuk Pimpinan) --</option>
                            @foreach($struktur as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->jabatan }} ({{ $parent->nama_pejabat ?? 'Kosong' }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Biarkan "Paling Atas" jika ini adalah Ketua/Kepala.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Jabatan <span class="text-danger">*</span></label>
                        <select name="jabatan" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($daftar_jabatan as $jbt)
                                <option value="{{ $jbt->nama }}">{{ $jbt->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jenis Hubungan</label>
                        <select name="jenis_hubungan" class="form-select" required>
                            <option value="struktural" selected>Struktural (Ke Bawah ↓)</option>
                            <option value="asisten_kiri">Asisten / Staf Khusus (← Ke Kiri)</option>
                            <option value="asisten_kanan">Asisten / Staf Khusus (Ke Kanan →)</option>
                        </select>
                        <div class="form-text">Asisten akan digambarkan menyamping (kiri/kanan) dari struktur utama.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Pejabat (Opsional)</label>
                        <input type="text" name="nama_pejabat" class="form-control" placeholder="Cth: Dr. Jhon Doe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Foto Pejabat (Opsional)</label>
                        <input type="file" name="foto_pejabat" class="form-control" accept="image/png, image/jpeg, image/webp">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Tambah ke Bagan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
   <!-- Premium CSS Tree - No external JS needed -->
@endpush
@endsection
