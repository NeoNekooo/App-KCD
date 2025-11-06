@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Kepegawaian /</span> Detail GTK yang Dipilih
</h4>

<div class="d-flex justify-content-end mb-3 gap-2">
    @if($gtks->isNotEmpty())
        {{-- TOMBOL BARU: Cetak Semua --}}
        <a href="{{ route('admin.kepegawaian.gtk.cetak_pdf_multiple', ['ids' => $gtks->pluck('id')->implode(',')]) }}" 
           class="btn btn-primary" 
           target="_blank">
            <i class="bx bxs-file-pdf me-1"></i> Cetak Semua
        </a>

        {{-- TOMBOL LAMA: Ganti Teksnya --}}
        <a href="{{ route('admin.kepegawaian.gtk.cetak_pdf', ['id' => $gtks->first()->id]) }}" 
           class="btn btn-outline-primary" {{-- Saya ganti jadi outline agar beda --}}
           id="cetak-pdf-btn"
           target="_blank">
            <i class="bx bx-printer me-1"></i> Cetak (1 Tampil)
        </a>
    @endif

    {{-- Tombol Kembali --}}
    <a href="javascript:history.back()" class="btn btn-secondary">
        <i class="bx bx-arrow-back me-1"></i> Kembali
    </a>
</div>

@if($gtks->isNotEmpty())
    <div class="row">
        {{-- KOLOM NAVIGASI NAMA GTK --}}
        <div class="col-md-4 col-lg-3 mb-4 mb-md-0">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">GTK Terpilih</h5>
                </div>
                <div class="list-group list-group-flush" id="gtk-navigation">
                    @foreach($gtks as $gtk)
                        <a href="javascript:void(0);"
                           class="list-group-item list-group-item-action gtk-nav-item {{ $loop->first ? 'active' : '' }}"
                           data-target="gtk-detail-{{ $gtk->id }}">
                            {{ $gtk->nama }}
                            <small class="d-block text-muted">{{ $gtk->jabatan_ptk_id_str ?? 'Jabatan tidak tersedia' }}</small>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KOLOM DETAIL GTK --}}
        <div class="col-md-8 col-lg-9">
            @foreach ($gtks as $gtk)
                {{-- PENAMBAHAN data-id DI SINI --}}
                <div class="card gtk-detail-content" id="gtk-detail-{{ $gtk->id }}" data-id="{{ $gtk->id }}" style="{{ !$loop->first ? 'display: none;' : '' }}">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-4">Detail Lengkap: {{ $gtk->nama }}</h5>

                        {{-- INFORMASI PRIBADI --}}
                        <p class="text-muted small text-uppercase">Informasi Pribadi</p>
                        <table class="table table-borderless table-sm mb-4">
                            <tbody>
                                <tr>
                                    <td style="width: 35%;"><strong>Nama Lengkap</strong></td>
                                    <td>: {{ $gtk->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NIK</strong></td>
                                    <td>: {{ $gtk->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Kelamin</strong></td>
                                    <td>: {{ $gtk->jenis_kelamin ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tempat, Tanggal Lahir</strong></td>
                                    <td>: {{ $gtk->tempat_lahir ?? '-' }}, {{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->format('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Agama</strong></td>
                                    <td>: {{ $gtk->agama_id_str ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr class="my-3">

                        {{-- INFORMASI KEPEGAWAIAN --}}
                        <p class="text-muted small text-uppercase">Informasi Kepegawaian</p>
                        <table class="table table-borderless table-sm mb-4">
                           <tbody>
                                <tr>
                                    <td style="width: 35%;"><strong>Status Kepegawaian</strong></td>
                                    <td>: {{ $gtk->status_kepegawaian_id_str ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NIP</strong></td>
                                    <td>: {{ $gtk->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NUPTK</strong></td>
                                    <td>: {{ $gtk->nuptk ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis GTK</strong></td>
                                    <td>: {{ $gtk->jenis_ptk_id_str ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan</strong></td>
                                    <td>: {{ $gtk->jabatan_ptk_id_str ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Surat Tugas</strong></td>
                                    <td>: {{ $gtk->tanggal_surat_tugas ? \Carbon\Carbon::parse($gtk->tanggal_surat_tugas)->format('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status Induk</strong></td>
                                    <td>: {{ $gtk->ptk_induk == 1 ? 'Induk' : 'Non-Induk' }}</td>
                                </tr>
                           </tbody>
                        </table>
                        <hr class="my-3">

                        {{-- INFORMASI PENDIDIKAN & RIWAYAT --}}
                        <p class="text-muted small text-uppercase">Informasi Pendidikan & Riwayat</p>
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td style="width: 35%;"><strong>Pendidikan Terakhir</strong></td>
                                    <td>: {{ $gtk->pendidikan_terakhir ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bidang Studi Terakhir</strong></td>
                                    <td>: {{ $gtk->bidang_studi_terakhir ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pangkat/Golongan Terakhir</strong></td>
                                    <td>: {{ $gtk->pangkat_golongan_terakhir ?? '-' }}</td>
                                </tr>
                                
                                <tr>
                                    <td class="align-top"><strong>Riwayat Pendidikan</strong></td>
                                    <td>
                                        @php $pendidikan = json_decode($gtk->rwy_pend_formal); @endphp
                                        @if(!empty($pendidikan) && is_array($pendidikan))
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Jenjang</th>
                                                        <th>Institusi</th>
                                                        <th>Tahun Lulus</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($pendidikan as $riwayat)
                                                    <tr>
                                                        <td>{{ $riwayat->jenjang_pendidikan_id_str ?? '-' }}</td>
                                                        <td>{{ $riwayat->satuan_pendidikan_formal ?? '-' }}</td>
                                                        <td>{{ $riwayat->tahun_lulus ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            : -
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td class="align-top"><strong>Riwayat Kepangkatan</strong></td>
                                    <td>
                                        @php $kepangkatan = json_decode($gtk->rwy_kepangkatan); @endphp
                                        @if(!empty($kepangkatan) && is_array($kepangkatan))
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Pangkat/Gol</th>
                                                        <th>Nomor SK</th>
                                                        <th>TMT Pangkat</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($kepangkatan as $riwayat)
                                                    <tr>
                                                        <td>{{ $riwayat->pangkat_golongan_id_str ?? '-' }}</td>
                                                        <td>{{ $riwayat->nomor_sk ?? '-' }}</td>
                                                        <td>{{ $riwayat->tmt_pangkat ? \Carbon\Carbon::parse($riwayat->tmt_pangkat)->format('d-m-Y') : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            : -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    {{-- Tampilan jika tidak ada data yang dipilih --}}
    <div class="card">
        <div class="card-body">
            <div class="text-center py-4">
                <i class="bx bx-info-circle bx-lg text-muted d-block mx-auto mb-2"></i>
                <span class="text-muted">Tidak ada data GTK yang dipilih untuk ditampilkan.</span>
            </div>
        </div>
    </div>
@endif

@endsection

{{-- BAGIAN BARU ADA DI SINI --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah ada item navigasi sebelum menjalankan script
        const navItems = document.querySelectorAll('.gtk-nav-item');
        if (navItems.length === 0) return;

        const detailContents = document.querySelectorAll('.gtk-detail-content');
        const cetakBtn = document.getElementById('cetak-pdf-btn');

        navItems.forEach(item => {
            item.addEventListener('click', function() {
                // Hapus kelas 'active' dari semua item navigasi
                navItems.forEach(nav => nav.classList.remove('active'));
                
                // Tambahkan kelas 'active' ke item yang diklik
                this.classList.add('active');

                const targetId = this.getAttribute('data-target');

                // Sembunyikan semua konten detail
                detailContents.forEach(content => {
                    content.style.display = 'none';
                });

                // Tampilkan konten detail yang sesuai
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.style.display = 'block';

                    // Perbarui link tombol cetak jika tombolnya ada
                    if(cetakBtn) {
                        const gtkId = targetContent.getAttribute('data-id');
                        let baseUrl = "{{ route('admin.kepegawaian.gtk.cetak_pdf', ['id' => ':id']) }}";
                        let newUrl = baseUrl.replace(':id', gtkId);
                        cetakBtn.setAttribute('href', newUrl);
                    }
                }
            });
        });
    });
</script>
@endpush

