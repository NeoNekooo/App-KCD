@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- 1. HERO SECTION (Compact Style) --}}
        <div class="row g-3 mb-4">
            {{-- Kiri: Judul & Deskripsi --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100 overflow-hidden"
                    style="background: linear-gradient(120deg, #696cff, #8592a3); border-radius: 12px;">
                    <div class="card-body d-flex align-items-center text-white p-3">
                        <div class="me-3 rounded d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; min-width: 48px; background: rgba(255, 255, 255, 0.2);">
                            <i class='bx bx-id-card text-white' style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h5 class="text-white fw-bold mb-0 text-nowrap">Pegawai KCD</h5>
                            <small class="text-white opacity-75 text-nowrap">Kelola data kepegawaian & akses login.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Statistik Ringkas --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Total
                                Aktif</span>
                            <h4 class="mb-0 fw-bolder text-primary">{{ $pegawais->total() }}</h4>
                        </div>
                        <div class="avatar avatar-md">
                            <span
                                class="avatar-initial rounded bg-label-primary d-flex align-items-center justify-content-center">
                                <i class="bx bx-user fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MAIN CARD (Action + Table Gabung) --}}
        <div class="card border-0 shadow-lg" style="border-radius: 12px;">

            {{-- Header: Search & Buttons --}}
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group input-group-merge rounded-pill" style="max-width: 300px;">
                            <span class="input-group-text border-light bg-light ps-3"><i
                                    class="bx bx-search text-muted"></i></span>
                            <input type="text" class="form-control border-light bg-light shadow-none"
                                placeholder="Cari Pegawai..." id="searchPegawai">
                        </div>

                        {{-- Tombol Sorting --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-label-secondary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-sort-down me-1"></i> Urutkan
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                <li><a class="dropdown-item {{ !$request->get('sort') || $request->get('sort') == 'oldest' ? 'active' : '' }}" href="{{ route('admin.kepegawaian.index', ['sort' => 'oldest']) }}">Terlama</a></li>
                                <li><a class="dropdown-item {{ $request->get('sort') == 'latest' ? 'active' : '' }}" href="{{ route('admin.kepegawaian.index', ['sort' => 'latest']) }}">Terbaru</a></li>
                                <li><a class="dropdown-item {{ $request->get('sort') == 'alpha' ? 'active' : '' }}" href="{{ route('admin.kepegawaian.index', ['sort' => 'alpha']) }}">Nama [A-Z]</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-label-secondary fw-semibold rounded-pill" data-bs-toggle="modal"
                            data-bs-target="#modalGantiPass">
                            <i class='bx bx-lock-alt me-1'></i> Password Saya
                        </button>
                        <button class="btn btn-sm btn-primary fw-bold rounded-pill shadow-sm px-3" data-bs-toggle="modal"
                            data-bs-target="#modalTambah">
                            <i class='bx bx-plus me-1'></i> Tambah Baru
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table Content --}}
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle table-borderless">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Pegawai</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Jabatan</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Kontak</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Akun Login</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($pegawais as $item)
                            <tr class="border-bottom hover-bg-soft">
                                {{-- Nama & NIP --}}
                                <td class="ps-4">
                                    <div class="d-flex align-items-center" style="max-width: 100%;">
                                        <div class="avatar avatar-sm me-2 flex-shrink-0">
                                            @if ($item->foto && Storage::disk('public')->exists($item->foto))
                                                <img src="{{ Storage::url($item->foto) }}" alt="Avatar"
                                                    class="rounded-circle" style="object-fit: cover;">
                                            @else
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-primary fw-bold d-flex align-items-center justify-content-center">
                                                    {{ substr($item->nama, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column text-truncate">
                                            <a href="{{ route('admin.kepegawaian.show', $item->id) }}"
                                                class="fw-bold text-dark text-truncate text-decoration-none hover-primary"
                                                title="Lihat Detail">
                                                {{ $item->nama }}
                                            </a>
                                            <small class="text-muted font-monospace"
                                                style="font-size: 0.75rem;">{{ $item->nip ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Jabatan --}}
                                <td>
                                    <span class="badge bg-label-info rounded px-2 text-uppercase fw-bold"
                                        style="font-size: 0.65rem;">
                                        {{ $item->jabatanKcd->nama ?? $item->jabatan }}
                                    </span>
                                </td>

                                {{-- Kontak --}}
                                <td>
                                    @if ($item->no_hp)
                                        <a href="https://wa.me/{{ $item->no_hp }}" target="_blank"
                                            class="text-body d-inline-flex align-items-center text-decoration-none">
                                            <i class='bx bxl-whatsapp text-success me-1'></i>
                                            <span class="small">{{ $item->no_hp }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                {{-- Status Akun --}}
                                <td>
                                    @if ($item->user)
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="badge badge-dot bg-success"></div>
                                            <small class="text-muted">{{ $item->user->username }}</small>
                                        </div>
                                    @else
                                        <div class="badge badge-dot bg-danger"></div> <small class="text-muted">No
                                            User</small>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.kepegawaian.show', $item->id) }}"
                                            class="btn btn-xs btn-icon btn-label-primary" title="Detail Profil">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <button type="button" class="btn btn-xs btn-icon btn-label-warning"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}"
                                            title="Edit Cepat">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <form action="{{ route('admin.kepegawaian.reset', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-xs btn-icon btn-label-info"
                                                title="Reset Password"
                                                onclick="return confirm('Reset password jadi kcd123?')">
                                                <i class="bx bx-key"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.kepegawaian.destroy', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-icon btn-label-danger"
                                                title="Hapus Permanen" onclick="return confirm('Hapus permanen?')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- MODAL EDIT CEPAT --}}
                                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-bottom py-3 bg-light">
                                                    <h6 class="modal-title fw-bold"><i class="bx bx-edit me-2"></i>Edit
                                                        Data Utama</h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.kepegawaian.update', $item->id) }}"
                                                    method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body p-4 text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Nama Lengkap</label>
                                                            <input type="text" name="nama" class="form-control"
                                                                value="{{ $item->nama }}" required>
                                                        </div>
                                                        <div class="row g-3">
                                                            <div class="col-6">
                                                                <label class="form-label small fw-bold">NIP</label>
                                                                <input type="text" name="nip" class="form-control"
                                                                    value="{{ $item->nip }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label small fw-bold">Jabatan</label>
                                                                <select name="jabatan_kcd_id" class="form-select" required>
                                                                    @foreach ($jabatans as $jab)
                                                                        <option value="{{ $jab->id }}"
                                                                            {{ $item->jabatan_kcd_id == $jab->id ? 'selected' : '' }}>
                                                                            {{ $jab->nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <label class="form-label small fw-bold text-danger">Reset
                                                                Password (Opsional)</label>
                                                            <input type="password" name="password"
                                                                class="form-control form-control-sm"
                                                                placeholder="Isi password baru...">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top bg-light py-2">
                                                        <button type="button"
                                                            class="btn btn-sm btn-label-secondary rounded-pill"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit"
                                                            class="btn btn-sm btn-primary rounded-pill px-3">Simpan</button>
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
                                    <div class="d-flex flex-column align-items-center">
                                        <i class='bx bx-folder-open text-muted fs-1 opacity-50'></i>
                                        <small class="mt-2 text-muted">Belum ada data pegawai.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-0 bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Total: {{ $pegawais->total() }}</small>
                    <div class="small-pagination">{{ $pegawais->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white py-3">
                    <h6 class="modal-title text-white fw-bold"><i class='bx bx-user-plus me-2'></i>Tambah Baru</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.kepegawaian.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" placeholder="Budi Santoso"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">NIP / Username</label>
                                <input type="text" name="nip" class="form-control" placeholder="Isi NIP...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Jabatan <span
                                        class="text-danger">*</span></label>
                                <select name="jabatan_kcd_id" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($jabatans as $jab)
                                    <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="customPassCheck"
                                        onclick="toggleCustomPass()">
                                    <label class="form-check-label small" for="customPassCheck">Buat password manual
                                        (Default: <b>kcd123</b>)</label>
                                </div>
                                <div class="mt-2 d-none" id="customPassBox">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Min. 6 Karakter">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light py-2">
                        <button type="button" class="btn btn-sm btn-label-secondary rounded-pill"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL GANTI PASSWORD SAYA --}}
    <div class="modal fade" id="modalGantiPass" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom py-3">
                    <h6 class="modal-title fw-bold">Ganti Password</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.profil-saya.change-password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Password Lama</label>
                            <input type="password" name="current_password" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Password Baru</label>
                            <input type="password" name="new_password" class="form-control form-control-sm" required>
                        </div>
                        <div>
                            <label class="form-label small text-muted">Konfirmasi</label>
                            <input type="password" name="new_password_confirmation" class="form-control form-control-sm"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light py-2">
                        <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCustomPass() {
            var checkBox = document.getElementById("customPassCheck");
            var text = document.getElementById("customPassBox");
            if (checkBox.checked == true) {
                text.classList.remove("d-none");
            } else {
                text.classList.add("d-none");
            }
        }

        // Live Search
        document.getElementById('searchPegawai').addEventListener('keyup', function() {
            let value = this.value.toLowerCase();
            let rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    </script>
@endsection


