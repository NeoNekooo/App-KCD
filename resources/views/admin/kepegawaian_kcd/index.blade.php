@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Pegawai KCD</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class='bx bx-user-plus me-1'></i> Tambah Pegawai
        </button>
    </div>

    {{-- ALERT SUKSES/ERROR --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class='bx bx-check-circle me-1'></i> {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class='bx bx-error me-1'></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ALERT VALIDASI --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class='bx bx-error-circle'></i> Gagal Menyimpan Data:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama / NIP</th>
                        <th>Jabatan</th>
                        <th>Username Login</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawais as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama }}</div>
                                <div class="small text-muted">{{ $item->nip ?? 'Tanpa NIP' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $item->jabatan }}</span>
                            </td>
                            <td>
                                @if($item->user)
                                    <span class="badge bg-label-success">
                                        <i class='bx bx-user me-1'></i> {{ $item->user->username }}
                                    </span>
                                @else
                                    <span class="badge bg-label-danger">No Access</span>
                                @endif
                            </td>
                            
                            {{-- KOLOM AKSI (BUTTONS) --}}
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    {{-- TOMBOL EDIT --}}
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEdit{{ $item->id }}"
                                            data-bs-placement="top" 
                                            title="Edit Data">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    {{-- TOMBOL RESET PASSWORD --}}
                                    <form action="{{ route('admin.kcd.pegawai.reset', $item->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-info" 
                                                onclick="return confirm('Yakin reset password jadi kcd123?')"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Reset Password (kcd123)">
                                            <i class="bx bx-key"></i>
                                        </button>
                                    </form>

                                    {{-- TOMBOL HAPUS --}}
                                    <form action="{{ route('admin.kcd.pegawai.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Hapus pegawai {{ $item->nama }}? Akun login juga akan terhapus.')"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Hapus Pegawai">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- MODAL EDIT (Hidden) --}}
                                <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Pegawai</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.kcd.pegawai.update', $item->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Lengkap</label>
                                                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $item->nama) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">NIP</label>
                                                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $item->nip) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Jabatan</label>
                                                        <select name="jabatan" class="form-select" required>
                                                            @foreach(['Administrator', 'Kepala', 'Kasubag', 'Kepegawaian', 'Kesiswaan', 'Sarpras', 'Divisi IT', 'Staff'] as $jab)
                                                                <option value="{{ $jab }}" {{ $item->jabatan == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">No HP</label>
                                                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $item->no_hp) }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <img src="{{ asset('assets/img/illustrations/empty.png') }}" alt="Kosong" width="150" class="mb-3" onerror="this.style.display='none'">
                                <p class="text-muted">Belum ada data pegawai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-3">
            <div class="d-flex justify-content-end">
                {{ $pegawais->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Tambah Pegawai Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kcd.pegawai.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <i class='bx bx-info-circle me-2 fs-4'></i>
                        <div>
                            Akun login akan dibuat otomatis.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso" value="{{ old('nama') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">NIP (Username Login)</label>
                        <input type="text" name="nip" class="form-control" placeholder="Isi NIP atau Kosongkan" value="{{ old('nip') }}">
                        <div class="form-text text-muted small">Jika kosong, username login akan menggunakan Nama Depan + Angka Acak.</div>
                    </div>

                    {{-- FITUR PASSWORD CUSTOM / GENERATE --}}
                    <div class="mb-3">
                        <label class="form-label">Password Login</label>
                        <div class="input-group">
                            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Isi manual atau generate..." autocomplete="new-password">
                            
                            {{-- Tombol Generate --}}
                            <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()" title="Buat Password Acak" data-bs-toggle="tooltip">
                                <i class='bx bx-refresh'></i>
                            </button>

                            {{-- Tombol Lihat Password --}}
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">
                                <i class='bx bx-show' id="iconEye"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted small">
                            Kosongkan jika ingin menggunakan password default: <strong>kcd123</strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jabatan / Divisi</label>
                        <select name="jabatan" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Kepala">Kepala</option>
                            <option value="Kasubag">Kasubag</option>
                            <option value="Kepegawaian">Kepegawaian</option>
                            <option value="Kesiswaan">Kesiswaan</option>
                            <option value="Sarpras">Sarpras</option>
                            <option value="Divisi IT">Divisi IT</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">No HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08..." value="{{ old('no_hp') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT GABUNGAN --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Init Tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    // 1. Fungsi Generate Password Acak
    function generatePassword() {
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%";
        const length = 10;
        let password = "";

        for (let i = 0; i < length; i++) {
            const randomNumber = Math.floor(Math.random() * chars.length);
            password += chars.substring(randomNumber, randomNumber + 1);
        }

        const inputPass = document.getElementById('inputPassword');
        inputPass.value = password;
        
        // Otomatis tampilkan password biar bisa dicopy/dilihat
        inputPass.type = "text";
        document.getElementById('iconEye').classList.remove('bx-show');
        document.getElementById('iconEye').classList.add('bx-hide');
    }

    // 2. Fungsi Lihat/Sembunyikan Password
    function togglePasswordVisibility() {
        const inputPass = document.getElementById('inputPassword');
        const icon = document.getElementById('iconEye');

        if (inputPass.type === "password") {
            inputPass.type = "text";
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        } else {
            inputPass.type = "password";
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        }
    }
</script>

@endsection