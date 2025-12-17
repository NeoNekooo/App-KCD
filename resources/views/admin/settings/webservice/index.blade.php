@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center py-3 mb-2">
        <h4 class="fw-bold m-0"><span class="text-muted fw-light">Pengaturan /</span> Web Service Dapodik</h4>
        {{-- Indikator Status Singkat --}}
        @if($tokenValue)
            <span class="badge bg-label-success"><i class='bx bx-check-circle me-1'></i> Terkonfigurasi</span>
        @else
            <span class="badge bg-label-danger"><i class='bx bx-x-circle me-1'></i> Belum Diset</span>
        @endif
    </div>

    <div class="row">
        {{-- KOLOM KIRI: Form Input --}}
        <div class="col-md-7 col-lg-8">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Konfigurasi Token</h5>
                    <small class="text-muted">Sinkronisasi Data</small>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class='bx bx-check-circle me-2'></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.pengaturan.webservice.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="api_token" class="form-label fw-bold text-uppercase text-xs text-muted">Token Webservice</label>

                            {{-- Input Group Keren --}}
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light"><i class='bx bx-key'></i></span>
                                <input type="password"
                                       class="form-control form-control-lg"
                                       id="api_token"
                                       name="api_token"
                                       value="{{ old('api_token', $tokenValue) }}"
                                       placeholder="Tempel Token Dapodik di sini..."
                                       required>
                                <span class="input-group-text cursor-pointer" id="toggleToken">
                                    <i class="bx bx-show" id="toggleIcon"></i>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mt-2">
                                <div class="form-text text-danger">
                                    <i class='bx bx-error-circle'></i> Token bersifat rahasia. Jangan bagikan ke pihak luar.
                                </div>
                                {{-- Tombol Paste Cepat --}}
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="pasteFromClipboard()">
                                    <i class='bx bx-paste me-1'></i> Tempel dari Clipboard
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-label-secondary me-2">Batal</button>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class='bx bx-save me-2'></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Tambahan (Opsional) --}}
            <div class="card bg-transparent shadow-none border border-dashed">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class='bx bx-help-circle me-1'></i> Apa fungsi token ini?</h6>
                    <p class="text-muted mb-0 small text-justify">
                        Token ini berfungsi sebagai kunci otentikasi agar aplikasi ini dapat berkomunikasi (Handshake) dengan aplikasi Dapodik Lokal. Tanpa token yang cocok, proses tarik data Siswa, PTK, dan Rombel akan ditolak oleh sistem keamanan.
                    </p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Panduan --}}
        <div class="col-md-5 col-lg-4">
            <div class="card mb-4 bg-label-primary text-primary shadow-none">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3 text-primary"><i class='bx bx-bulb'></i> Panduan Singkat</h5>

                    <div class="timeline-simple">
                        <div class="d-flex mb-3">
                            <div class="avatar avatar-xs me-3">
                                <span class="avatar-initial rounded-circle bg-primary text-white">1</span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-primary">Buka Dapodik</h6>
                                <small class="text-primary opacity-75">Login ke aplikasi Dapodik Localhost (sebagai Admin).</small>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="avatar avatar-xs me-3">
                                <span class="avatar-initial rounded-circle bg-primary text-white">2</span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-primary">Menu Webservice</h6>
                                <small class="text-primary opacity-75">Masuk ke <strong>Pengaturan</strong> &rarr; <strong>Webservice</strong>.</small>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="avatar avatar-xs me-3">
                                <span class="avatar-initial rounded-circle bg-primary text-white">3</span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-primary">Salin Token</h6>
                                <small class="text-primary opacity-75">Buat koneksi baru atau salin token yang sudah ada.</small>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="avatar avatar-xs me-3">
                                <span class="avatar-initial rounded-circle bg-primary text-white">4</span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-primary">Simpan</h6>
                                <small class="text-primary opacity-75">Tempel token di form sebelah kiri dan simpan.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Javascript untuk interaksi UI --}}
<script>
    // 1. Fitur Toggle Show/Hide Password
    document.getElementById('toggleToken').addEventListener('click', function () {
        const input = document.getElementById('api_token');
        const icon = document.getElementById('toggleIcon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        } else {
            input.type = 'password';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        }
    });

    // 2. Fitur Paste dari Clipboard
    async function pasteFromClipboard() {
        try {
            const text = await navigator.clipboard.readText();
            document.getElementById('api_token').value = text;

            // Opsional: Berikan efek visual/notifikasi kecil
            const btn = event.currentTarget;
            const originalContent = btn.innerHTML;
            btn.innerHTML = "<i class='bx bx-check'></i> Tertempel!";
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');

            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.classList.add('btn-outline-primary');
                btn.classList.remove('btn-success');
            }, 1000);

        } catch (err) {
            alert('Gagal membaca clipboard. Pastikan browser mengizinkan akses.');
        }
    }
</script>
@endsection
