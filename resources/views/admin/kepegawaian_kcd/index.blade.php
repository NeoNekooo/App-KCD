@extends('layouts.admin')

@section('content')

    {{-- 🔥 CSS PREMIUM: ROUNDED, ANIMATED & MODERN 🔥 --}}
    <style>
        .rounded-4 { border-radius: 1rem !important; }
        .rounded-5 { border-radius: 1.25rem !important; }
        .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important; }
        .shadow-soft { box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08) !important; }
        
        /* Table Enhancements */
        .table-hover > tbody > tr { transition: all 0.2s ease; }
        .table-hover > tbody > tr:hover { background-color: rgba(105, 108, 255, 0.03); transform: translateY(-1px); }
        .table > :not(caption) > * > * { padding: 1rem 1.25rem; }
        
        /* Pagination Styling */
        .small-pagination .pagination { margin-bottom: 0; justify-content: flex-end; }
        .small-pagination .page-link { border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin: 0 2px; padding: 0; font-size: 0.85rem; font-weight: 600; color: #566a7f; border: none; transition: all 0.2s; }
        .small-pagination .page-item.active .page-link { background-color: #696cff; color: #fff; box-shadow: 0 2px 6px rgba(105, 108, 255, 0.4); }
        .small-pagination .page-item.disabled .page-link { color: #b4bdc6; background-color: transparent; }
        .small-pagination .page-link:hover:not(.active):not(.disabled) { background-color: rgba(105, 108, 255, 0.1); color: #696cff; }

        /* Animation Keyframes */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        
        /* Utility */
        .hover-primary:hover { color: #696cff !important; }
        
        #tableLoadingOverlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.7);
            z-index: 10;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: opacity 0.2s ease;
        }
        #tableLoadingOverlay.show {
            opacity: 1; pointer-events: auto;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- 1. HERO SECTION (Compact Style) --}}
        <div class="row g-3 mb-4 animate-fade-in-up">
            {{-- Kiri: Judul & Deskripsi --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-soft h-100 overflow-hidden rounded-4 position-relative" style="background: linear-gradient(135deg, #696cff 0%, #4345eb 100%);">
                    {{-- Ornamen BG --}}
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -30px; right: 10%; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    
                    <div class="card-body d-flex align-items-center text-white p-4 position-relative z-1">
                        <div class="me-4 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);">
                            <i class='bx bx-id-card text-white' style="font-size: 1.8rem;"></i>
                        </div>
                        <div>
                            <h4 class="text-white fw-bolder mb-1">Manajemen Kepegawai KCD</h4>
                            <p class="text-white opacity-75 mb-0 small">Kelola data kepegawaian, jabatan, dan hak akses login sistem.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Statistik Ringkas --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Pegawai Aktif</span>
                            <h2 class="mb-0 fw-bolder text-primary" style="font-size: 2.2rem;">{{ $pegawais->total() }}</h2>
                        </div>
                        <div class="avatar avatar-lg shadow-xs rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bx bx-user fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Display Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show animate-fade-in-up" role="alert">
                <i class='bx bx-check-circle me-1'></i> {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate-fade-in-up" role="alert">
                <i class='bx bx-error-circle me-1'></i> {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 2. MAIN CARD (Action + Table Gabung) --}}
        <div class="card border-0 shadow-soft rounded-4 animate-fade-in-up" style="animation-delay: 0.1s;">

            {{-- Header: Search & Buttons --}}
            <div class="card-header bg-transparent py-4 border-bottom">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    
                    {{-- 🔥 KIRI: Search & Sort Beneran Sejajar 🔥 --}}
                    <div class="d-flex flex-row align-items-center gap-2">
                        
                        {{-- Form Search Custom (AJAX Driven) --}}
                        <form action="{{ route('admin.kepegawaian.index') }}" method="GET" class="input-group input-group-merge rounded-pill border-0 shadow-xs bg-light m-0" style="width: 250px; padding: 2px;">
                            @if(request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            <span class="input-group-text border-0 bg-transparent ps-3 pe-2"><i class="bx bx-search text-muted fs-5"></i></span>
                            <input type="text" id="ajaxSearchInput" value="{{ request('search') }}" class="form-control border-0 bg-transparent shadow-none px-2" placeholder="Cari nama/NIP...">
                        </form>

                        {{-- Tombol Sorting (AJAX Driven) --}}
                        <div class="dropdown flex-shrink-0">
                            <button class="btn btn-white border shadow-xs rounded-pill dropdown-toggle px-3 fw-semibold text-dark d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding-top: 0.55rem; padding-bottom: 0.55rem;">
                                <i class="bx bx-sort-down text-primary me-1 fs-5"></i> 
                                <span class="d-none d-sm-inline text-muted fw-normal me-1">Urutan:</span>
                                <span id="sortLabelText">
                                    @if(request('sort') == 'latest') Terbaru
                                    @elseif(request('sort') == 'alpha') A - Z
                                    @else Terlama @endif
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start shadow-soft border-0 mt-2 py-2 rounded-3" style="min-width: 180px;">
                                <li>
                                    <a class="dropdown-item ajax-sort-btn d-flex align-items-center justify-content-between py-2 {{ !$request->get('sort') || $request->get('sort') == 'oldest' ? 'text-primary fw-bold bg-label-primary' : 'text-body' }}" href="javascript:void(0)" data-sort="oldest">
                                        Terlama
                                        <i class="bx bx-check fs-5 sort-icon {{ !$request->get('sort') || $request->get('sort') == 'oldest' ? '' : 'd-none' }}"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item ajax-sort-btn d-flex align-items-center justify-content-between py-2 {{ $request->get('sort') == 'latest' ? 'text-primary fw-bold bg-label-primary' : 'text-body' }}" href="javascript:void(0)" data-sort="latest">
                                        Terbaru
                                        <i class="bx bx-check fs-5 sort-icon {{ $request->get('sort') == 'latest' ? '' : 'd-none' }}"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item ajax-sort-btn d-flex align-items-center justify-content-between py-2 {{ $request->get('sort') == 'alpha' ? 'text-primary fw-bold bg-label-primary' : 'text-body' }}" href="javascript:void(0)" data-sort="alpha">
                                        Nama [A - Z]
                                        <i class="bx bx-check fs-5 sort-icon {{ $request->get('sort') == 'alpha' ? '' : 'd-none' }}"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- 🔥 KANAN: Action Buttons 🔥 --}}
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-label-secondary fw-bold rounded-pill px-3 py-2 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalGantiPass">
                            <i class='bx bx-lock-alt me-1 fs-6'></i> Password Saya
                        </button>
                        <button class="btn btn-primary fw-bold rounded-pill shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class='bx bx-plus me-1 fs-6'></i> Tambah Pegawai
                        </button>
                    </div>

                </div>
            </div>

            {{-- 🌟 BAGIAN YANG AKAN DI-REFRESH AJAX 🌟 --}}
            <div id="ajaxTableContainer" class="position-relative" style="min-height: 250px;">
                
                {{-- Spinner Loading --}}
                <div id="tableLoadingOverlay" class="table-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Profil Pegawai</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Jabatan</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Kontak</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Akun Login</th>
                                <th class="py-3 text-uppercase text-muted text-end pe-4" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($pegawais as $item)
                                <tr>
                                    {{-- Nama & NIP --}}
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md me-3 flex-shrink-0">
                                                @if ($item->foto && Storage::disk('public')->exists($item->foto))
                                                    <img src="{{ Storage::url($item->foto) }}" alt="Avatar" class="rounded-circle shadow-xs" style="object-fit: cover; border: 2px solid #fff;">
                                                @else
                                                    {{-- Ganti Inisial jadi Ikon User --}}
                                                    <span class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center shadow-xs" style="border: 2px solid #fff;">
                                                        <i class='bx bx-user fs-4'></i>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column text-truncate" style="max-width: 250px;">
                                                <a href="{{ route('admin.kepegawaian.show', $item->id) }}" class="fw-bold text-dark text-truncate text-decoration-none hover-primary mb-1" title="Lihat Detail Profil">
                                                    {{ $item->nama }}
                                                </a>
                                                <div class="d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                                                    <i class="bx bx-id-card me-1 opacity-75"></i> 
                                                    <span class="font-monospace">{{ $item->nip ?? 'NIP Tidak Tersedia' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Jabatan --}}
                                    <td>
                                        <span class="badge bg-label-primary rounded-pill px-3 py-2 fw-bold text-wrap" style="font-size: 0.7rem; max-width: 150px; line-height: 1.2;">
                                            {{ $item->jabatanKcd->nama ?? $item->jabatan }}
                                        </span>
                                    </td>

                                    {{-- Kontak --}}
                                    <td>
                                        @if ($item->no_hp)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_hp) }}" target="_blank" class="badge bg-label-success rounded-pill px-3 py-2 text-decoration-none d-inline-flex align-items-center hover-primary">
                                                <i class='bx bxl-whatsapp fs-6 me-1'></i>
                                                <span class="fw-bold" style="font-size: 0.75rem;">{{ $item->no_hp }}</span>
                                            </a>
                                        @else
                                            <span class="badge bg-label-secondary rounded-pill px-3 py-2 fw-medium" style="font-size: 0.7rem;">Tidak Ada Kontak</span>
                                        @endif
                                    </td>

                                    {{-- Status Akun --}}
                                    <td>
                                        @if ($item->user)
                                            <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1" style="width: fit-content;">
                                                <div class="badge badge-dot bg-success me-2" style="width: 8px; height: 8px;"></div>
                                                <span class="text-dark fw-bold small font-monospace">{{ $item->user->username }}</span>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center bg-light-danger rounded-pill px-3 py-1" style="width: fit-content;">
                                                <div class="badge badge-dot bg-danger me-2" style="width: 8px; height: 8px;"></div>
                                                <span class="text-danger fw-semibold small">Belum Aktif</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.kepegawaian.show', $item->id) }}" class="btn btn-sm btn-icon btn-label-primary rounded-circle shadow-none" data-bs-toggle="tooltip" title="Lihat Profil">
                                                <i class="bx bx-show-alt"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-icon btn-label-warning rounded-circle shadow-none" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}" title="Edit Cepat">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                            
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-icon btn-label-secondary rounded-circle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-soft border-0 mt-1 py-2 rounded-3">
                                                    <li>
                                                        <form action="{{ route('admin.kepegawaian.reset', $item->id) }}" method="POST">
                                                            @csrf @method('PUT')
                                                            <button type="submit" class="dropdown-item d-flex align-items-center py-2" onclick="return confirm('Yakin ingin mereset password user ini menjadi: kcd123 ?')">
                                                                <i class="bx bx-key text-info me-2"></i> Reset Password
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <form action="{{ route('admin.kepegawaian.destroy', $item->id) }}" method="POST">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item d-flex align-items-center py-2 text-danger" onclick="return confirm('PERINGATAN! Menghapus data ini akan menghapus permanen Profil dan Akun Loginnya. Lanjutkan?')">
                                                                <i class="bx bx-trash me-2"></i> Hapus Permanen
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        {{-- MODAL EDIT CEPAT --}}
                                        <div class="modal fade text-start" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4">
                                                    <div class="modal-header border-bottom py-3 px-4 bg-light-subtle">
                                                        <h5 class="modal-title fw-bold text-dark m-0"><i class="bx bx-edit text-primary me-2 fs-4" style="vertical-align: middle;"></i>Edit Data Pegawai</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.kepegawaian.update', $item->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body p-4">
                                                            <div class="mb-4">
                                                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                                                <input type="text" name="nama" class="form-control form-control-lg fs-6 fw-semibold bg-light border-0 shadow-none" value="{{ $item->nama }}" required>
                                                            </div>
                                                            <div class="row g-4">
                                                                <div class="col-sm-6">
                                                                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">NIP</label>
                                                                    <input type="text" name="nip" class="form-control bg-light border-0 shadow-none fw-medium" value="{{ $item->nip }}">
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Jabatan <span class="text-danger">*</span></label>
                                                                    <select name="jabatan_kcd_id" class="form-select bg-light border-0 shadow-none fw-medium" required>
                                                                        @foreach ($jabatans as $jab)
                                                                            <option value="{{ $jab->id }}" {{ $item->jabatan_kcd_id == $jab->id ? 'selected' : '' }}>{{ $jab->nama }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top bg-light-subtle py-3 px-4">
                                                            <button type="button" class="btn btn-label-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center my-3">
                                            <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 d-flex justify-content-center align-items-center">
                                                <i class='bx bx-user-x fs-1'></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Belum Ada Data Pegawai</h6>
                                            <p class="text-muted small mb-0">Ketikan pencarian yang lain atau tambah pegawai baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer Pagination --}}
                <div class="card-footer border-top bg-transparent py-3 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                        <small class="text-muted fw-semibold">Menampilkan <span class="text-dark">{{ $pegawais->count() }}</span> dari <span class="text-dark">{{ $pegawais->total() }}</span> pegawai</small>
                        <div class="small-pagination">{{ $pegawais->links() }}</div>
                    </div>
                </div>
            </div>
            {{-- 🌟 END BAGIAN AJAX 🌟 --}}
            
        </div>
    </div>

    {{-- MODAL TAMBAH PEGAWAI --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white py-3 px-4">
                    <h5 class="modal-title text-white fw-bold m-0"><i class='bx bx-user-plus me-2 fs-4' style="vertical-align: middle;"></i>Tambah Pegawai Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.kepegawaian.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control bg-light border-0 shadow-none fw-medium" placeholder="Contoh: Budi Santoso, S.Kom" required autofocus>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">NIP / Username</label>
                                <input type="text" name="nip" class="form-control bg-light border-0 shadow-none fw-medium" placeholder="Kosongkan jika belum punya">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Posisi Jabatan <span class="text-danger">*</span></label>
                                <select name="jabatan_kcd_id" class="form-select bg-light border-0 shadow-none fw-medium" required>
                                    <option value="" selected disabled>-- Pilih Jabatan --</option>
                                    @foreach ($jabatans as $jab)
                                    <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select bg-light border-0 shadow-none fw-medium" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="p-3 bg-label-warning rounded-4 border border-warning border-opacity-25">
                                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                        <input class="form-check-input mt-0" type="checkbox" id="customPassCheck" onclick="toggleCustomPass()" style="width: 2.5rem; height: 1.25rem; cursor: pointer;">
                                        <label class="form-check-label small fw-bold text-dark" for="customPassCheck" style="cursor: pointer;">
                                            Buat Password Manual <br>
                                            <small class="text-muted fw-normal">Jika tidak dicentang, password default otomatis: <span class="text-danger fw-bold">kcd123</span></small>
                                        </label>
                                    </div>
                                    <div class="mt-3 d-none" id="customPassBox">
                                        <input type="password" name="password" class="form-control bg-white border-0 shadow-sm" placeholder="Ketik minimal 6 karakter...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light-subtle py-3 px-4">
                        <button type="button" class="btn btn-label-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan Data Pegawai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL GANTI PASSWORD SAYA --}}
    <div class="modal fade" id="modalGantiPass" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom py-3 px-4 bg-light-subtle">
                    <h6 class="modal-title fw-bold text-dark m-0"><i class="bx bx-lock-open text-primary me-2 fs-5" style="vertical-align: text-bottom;"></i>Ubah Sandi Akun</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.profil-saya.change-password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Password Lama</label>
                            <input type="password" name="current_password" class="form-control bg-light border-0 shadow-none" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Password Baru</label>
                            <input type="password" name="new_password" class="form-control bg-light border-0 shadow-none" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div>
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Ulangi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control bg-light border-0 shadow-none" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light-subtle py-3 px-4">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">Simpan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Butuh Jquery buat load AJAX Pjax --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Toggle Switch Password Manual
        function toggleCustomPass() {
            var checkBox = document.getElementById("customPassCheck");
            var text = document.getElementById("customPassBox");
            if (checkBox.checked == true) {
                text.classList.remove("d-none");
                setTimeout(() => { text.querySelector('input').focus(); }, 100);
            } else {
                text.classList.add("d-none");
            }
        }
        
        // Aktifkan Tooltip
        function initTooltips() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }

        // 🔥 LOGIC AJAX SEARCH & SORT & PAGINATION (SINGLE FILE TRICK) 🔥
        $(document).ready(function() {
            initTooltips(); // Inisialisasi awal

            let currentSort = '{{ request("sort", "oldest") }}';
            let searchTimer;

            // Fungsi Load Potongan HTML Pake Jquery Load
            function fetchPegawaiData(url) {
                $('#tableLoadingOverlay').addClass('show'); // Tunjukin spinner
                
                // Ambil id #ajaxTableContainer aja dari request url
                $('#ajaxTableContainer').load(url + ' #ajaxTableContainer > *', function(response, status, xhr) {
                    $('#tableLoadingOverlay').removeClass('show'); // Ilangin spinner
                    initTooltips(); // Reset tooltip krn DOM baru
                    
                    // Update state URL di browser bar biar tetep bisa dishare / dicopas
                    window.history.pushState({"html":response,"pageTitle":document.title},"", url);
                });
            }

            // 1. Trigger pas ngetik Search
            $('#ajaxSearchInput').on('keyup', function() {
                clearTimeout(searchTimer);
                let searchValue = $(this).val();
                
                searchTimer = setTimeout(function() {
                    let url = "{{ route('admin.kepegawaian.index') }}?search=" + searchValue + "&sort=" + currentSort;
                    fetchPegawaiData(url);
                }, 500); 
            });

            // 2. Trigger pas ganti Urutan (Sort)
            $('.ajax-sort-btn').on('click', function(e) {
                e.preventDefault();
                currentSort = $(this).data('sort');
                let searchValue = $('#ajaxSearchInput').val();
                
                // Ubah Teks Label Urutan
                let sortText = $(this).text().trim();
                $('#sortLabelText').text(sortText);

                // Update Class Active & Ikon Check di Dropdown
                $('.ajax-sort-btn').removeClass('text-primary fw-bold bg-label-primary').addClass('text-body');
                $('.sort-icon').addClass('d-none');
                
                $(this).removeClass('text-body').addClass('text-primary fw-bold bg-label-primary');
                $(this).find('.sort-icon').removeClass('d-none');

                let url = "{{ route('admin.kepegawaian.index') }}?search=" + searchValue + "&sort=" + currentSort;
                fetchPegawaiData(url);
            });

            // 3. Trigger pas klik tombol Pagination
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                
                // Pastikan bawa data search & sort ke link pagination
                let searchValue = $('#ajaxSearchInput').val();
                if(searchValue) {
                    url += (url.includes('?') ? '&' : '?') + 'search=' + searchValue;
                }
                url += '&sort=' + currentSort;

                fetchPegawaiData(url);
            });
        });
    </script>
@endpush