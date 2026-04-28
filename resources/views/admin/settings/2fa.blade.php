@extends('layouts.admin')

@section('title', 'Keamanan Akun (2FA)')

@section('content')
<style>
    .card-2fa {
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        overflow: hidden;
        background: #fff;
    }
    .gradient-header {
        background: linear-gradient(135deg, #008493 0%, #00b4c5 100%);
        padding: 2.5rem;
        color: #fff;
    }
    .qr-container {
        background: #fff;
        padding: 1.5rem;
        border-radius: 1.25rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        display: inline-block;
        border: 1px solid #f0f0f0;
    }
    .step-number {
        width: 32px;
        height: 32px;
        background: rgba(0, 132, 147, 0.1);
        color: #008493;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .status-badge {
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
    }
    .btn-2fa {
        background: #008493;
        border: none;
        color: #fff;
        padding: 0.8rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-2fa:hover {
        background: #006f7b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 132, 147, 0.3);
    }
    .instruction-card {
        background: #f8f9fa;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #eee;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card-2fa animate-fade-in">
                {{-- Header dengan Gradient --}}
                <div class="gradient-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h3 class="text-white fw-bold mb-1">Dua Faktor Otentikasi (2FA)</h3>
                        <p class="text-white text-opacity-75 mb-0">Lapisan perlindungan ekstra untuk akun Anda.</p>
                    </div>
                    <div>
                        @if(!$user->google2fa_enabled)
                            <span class="status-badge bg-white text-danger">
                                <i class='bx bxs-shield-x me-2'></i> Status: Belum Aktif
                            </span>
                        @else
                            <span class="status-badge bg-white text-success">
                                <i class='bx bxs-check-shield me-2'></i> Status: Terlindungi
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible animate-fade-in" role="alert">
                            <i class='bx bx-check-circle me-2'></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible animate-fade-in" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row g-5">
                        @if(!$user->google2fa_enabled)
                        {{-- SISI KIRI: QR CODE --}}
                        <div class="col-lg-5 text-center">
                            {{-- Tombol Kembali atau Keluar --}}
                            <div class="text-start mb-4">
                                @if($user->google2fa_enabled)
                                    <a href="{{ route('admin.profil-saya.show') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                        <i class='bx bx-left-arrow-alt me-1'></i> Kembali ke Profil
                                    </a>
                                @else
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                            <i class='bx bx-log-out me-1'></i> Keluar dari Sesi
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <h6 class="fw-bold mb-4 text-start text-uppercase text-muted">Langkah 1: Scan QR Code</h6>
                                <div class="qr-container mb-3">
                                    {!! QrCode::size(220)->margin(1)->generate($qrCodeUrl) !!}
                                </div>
                                
                                {{-- Setup Key Manual di bawah QR --}}
                                <div class="mb-4">
                                    <p class="small text-muted mb-1 fw-bold">Setup Key:</p>
                                    <code class="fs-6 fw-bold text-primary px-3 py-1 bg-light rounded border">{{ $user->google2fa_secret }}</code>
                                </div>

                                <p class="small text-muted mb-0">Gunakan aplikasi <b>Google Authenticator</b> atau <b>Authy</b> untuk memindai kode di atas.</p>
                            </div>

                            {{-- SISI KANAN: INSTRUKSI & FORM --}}
                            <div class="col-lg-7">
                                <h6 class="fw-bold mb-4 text-uppercase text-muted">Langkah-langkah Aktivasi:</h6>
                                
                                <div class="instruction-card mb-4">
                                    <div class="d-flex mb-3">
                                        <div class="step-number">1</div>
                                        <div class="text-dark">Instal aplikasi <b>Google Authenticator</b> (atau Microsoft Authenticator) di smartphone Anda.</div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="step-number">2</div>
                                        <div class="text-dark">Buka aplikasi tersebut dan pilih <b>Scan a QR code</b>.</div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="step-number">3</div>
                                        <div class="text-dark">Scan kode QR yang ada di samping atau masukkan <b>kode manual</b>.</div>
                                    </div>
                                    <div class="d-flex mb-0">
                                        <div class="step-number">4</div>
                                        <div class="text-dark">Masukkan 6 digit kode yang muncul di aplikasi Anda ke form di bawah ini untuk memverifikasi.</div>
                                    </div>
                                </div>

                                <form action="{{ route('admin.settings.2fa.enable') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label fw-bold small">KODE KONFIRMASI 6-DIGIT</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text bg-light border-2 border-end-0"><i class="bx bx-mobile-vibration text-primary"></i></span>
                                            <input type="text" name="one_time_password" class="form-control form-control-lg border-2 border-start-0 bg-light" placeholder="Contoh: 123456" required maxlength="6" autofocus autocomplete="off" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-2fa w-100 py-3">
                                        <i class='bx bxs-zap me-2'></i> AKTIFKAN SEKARANG
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- TAMPILAN JIKA SUDAH AKTIF --}}
                            <div class="col-12 text-center py-5">
                                <div class="mb-4">
                                    <i class='bx bxs-check-shield text-success' style="font-size: 6rem;"></i>
                                </div>
                                <h2 class="fw-bold text-dark mb-2">Google Authenticator Aktif!</h2>
                                <p class="text-muted mb-5 mx-auto" style="max-width: 500px;">Keamanan akun Anda sangat baik. Sistem akan meminta kode 2FA setiap kali Anda login atau melakukan tindakan sensitif.</p>
                                
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="{{ route('admin.profil-saya.show') }}" class="btn btn-outline-secondary px-5 rounded-pill">
                                        <i class='bx bx-left-arrow-alt me-2'></i> Kembali ke Profil
                                    </a>
                                    <form action="{{ route('admin.settings.2fa.disable') }}" method="POST" onsubmit="return confirm('Peringatan: Keamanan akun Anda akan berkurang jika 2FA dimatikan. Lanjutkan?')">
                                        @csrf
                                        <button type="submit" class="btn btn-label-danger px-5 rounded-pill border-danger text-danger">
                                            <i class='bx bx-power-off me-2'></i> Nonaktifkan 2FA
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="bg-light p-4 text-center border-top">
                    <p class="mb-0 small text-muted">
                        Butuh bantuan? Silahkan hubungi <a href="#" class="text-primary fw-bold">Admin Pusat</a> jika Anda kehilangan akses ke perangkat 2FA Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
