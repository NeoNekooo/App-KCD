@extends('layouts.admin')

{{-- [1] CSS PRINT BARU - LEBIH SPESIFIK DAN KUAT --}}
@push('styles')
<style>
    @media print {
        /* Sembunyikan komponen layout template secara paksa */
        .layout-navbar,       /* Navbar atas (tempat search bar) */
        .content-footer,      /* Footer (tempat tulisan ThemeSelection) */
        .layout-menu,         /* Sidebar menu kiri */
        .no-print {           /* Semua yang kita tandai .no-print secara manual */
            display: none !important;
        }

        /* Atur ulang body dan pastikan tidak ada sisa-sisa style */
        body {
            background-color: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Angkat area cetak keluar dari flow layout template */
        .print-area {
            display: block !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        /* Atur dan pusatkan konten struk di tengah halaman cetak */
        .print-area-content {
            /* Sesuaikan lebar ini dengan kertas printer Anda (mis: 78mm atau 56mm) */
            width: 78mm; 
            margin: 0 auto; /* Membuat struk berada di tengah halaman */
            padding-top: 5mm;
            font-family: 'Courier New', monospace; /* Font umum untuk struk */
            font-size: 10pt;
            color: #000;
        }

        /* Styling konten di dalam struk */
        .print-area-content h5, .print-area-content p, .print-area-content div {
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .print-area-content h5 {
            font-size: 11pt;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        .print-area-content p {
            margin-bottom: 4px;
        }
        .print-area-content .qr-code {
            margin: 10px 0;
        }
        .print-area-content .footer-note {
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px dashed #000;
            font-size: 8pt;
        }
    }
</style>
@endpush


@section('content')
{{-- Konten layar tidak berubah, tetap dibungkus .no-print --}}
<div class="no-print">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Izin /</span> Detail Izin</h4>
    <div class="card">
        {{-- ... Isi card tidak perlu diubah ... --}}
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>Detail Izin untuk: <strong>{{ $izinSiswa->siswa->nama }}</strong></h5>
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 200px;">Tanggal Izin</td>
                            <td>: {{ \Carbon\Carbon::parse($izinSiswa->tanggal_izin)->isoFormat('dddd, D MMMM Y') }}</td>
                        </tr>
                        <tr>
                            <td>Tipe Izin</td>
                            <td>: {{ str_replace('_', ' ', $izinSiswa->tipe_izin) }}</td>
                        </tr>
                        @if($izinSiswa->jam_izin_mulai)
                        <tr>
                            <td>Waktu Mulai</td>
                            <td>: {{ \Carbon\Carbon::parse($izinSiswa->jam_izin_mulai)->format('H:i') }} WIB</td>
                        </tr>
                        @endif
                        @if($izinSiswa->jam_izin_selesai)
                        <tr>
                            <td>Waktu Selesai</td>
                            <td>: {{ \Carbon\Carbon::parse($izinSiswa->jam_izin_selesai)->format('H:i') }} WIB</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Alasan</td>
                            <td>: {{ $izinSiswa->alasan }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: <span class="badge bg-label-info">{{ $izinSiswa->status }}</span></td>
                        </tr>
                        <tr>
                            <td>Dicatat Oleh</td>
                            <td>: {{ $izinSiswa->pencatat->name }}</td>
                        </tr>
                    </table>
                </div>

                @if($izinSiswa->tipe_izin == 'KELUAR_SEMENTARA' && $izinSiswa->status == 'DISETUJUI')
                <div class="col-md-4 text-center">
                    <h5>QR Code Tiket Kembali</h5>
                    <div class="my-3">
                        {!! QrCode::size(250)->generate($izinSiswa->token_sementara) !!}
                    </div>
                    <p class="text-muted">Minta siswa untuk memindai QR Code ini saat akan masuk kembali ke area sekolah.</p>
                    <button id="print-ticket-btn" class="btn btn-primary">
                        <i class='bx bx-printer me-1'></i> Cetak Tiket
                    </button>
                </div>
                @endif
            </div>
            <a href="{{ route('admin.absensi.izin-siswa.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Izin</a>
        </div>
    </div>
</div>


{{-- [2] STRUKTUR HTML BARU UNTUK PRINT AREA --}}
@if($izinSiswa->tipe_izin == 'KELUAR_SEMENTARA' && $izinSiswa->status == 'DISETUJUI')
<div class="print-area" style="display: none;">
    <div class="print-area-content">
        <h5>IZIN KELUAR SEMENTARA</h5>
        <p><strong>{{ $izinSiswa->siswa->nama }}</strong></p>
        <p>{{ \Carbon\Carbon::parse($izinSiswa->tanggal_izin)->isoFormat('dddd, D MMM Y') }}</p>
        <p>Pukul {{ \Carbon\Carbon::parse($izinSiswa->jam_izin_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($izinSiswa->jam_izin_selesai)->format('H:i') }} WIB</p>
        
        <div class="qr-code">
            {!! QrCode::size(200)->generate($izinSiswa->token_sementara) !!}
        </div>
    
        <p class="footer-note">
            Scan QR ini saat kembali ke sekolah.
            <br>
            Dicatat oleh: {{ $izinSiswa->pencatat->name }}
        </p>
    </div>
</div>
@endif

@endsection


{{-- JavaScript tidak perlu diubah --}}
@push('scripts')
<script>
    const printButton = document.getElementById('print-ticket-btn');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }
</script>
@endpush