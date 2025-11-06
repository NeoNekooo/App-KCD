@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Kepegawaian /</span> Detail GTK yang Dipilih
</h4>

<div class="d-flex justify-content-end mb-3 gap-2">
    @if($gtks->isNotEmpty())
        {{-- Tombol Cetak Semua --}}
        <a href="{{ route('admin.kepegawaian.gtk.cetak_pdf_multiple', ['ids' => $gtks->pluck('id')->implode(',')]) }}" 
           class="btn btn-primary" 
           target="_blank">
            <i class="bx bxs-file-pdf me-1"></i> Cetak Semua
        </a>

        {{-- Tombol Cetak 1 Tampil --}}
        <a href="{{ route('admin.kepegawaian.gtk.cetak_pdf', ['id' => $gtks->first()->id]) }}" 
           class="btn btn-outline-primary"
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

{{-- --- BLOK UNTUK MENAMPILKAN PESAN SUKSES --- --}}
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

{{-- --- BLOK UNTUK MENAMPILKAN PESAN ERROR --- --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <h5 class="alert-heading mb-1">Validasi Gagal!</h5>
        <p class="mb-2">Pastikan file yang diunggah sesuai dengan ketentuan.</p>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


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
                {{-- Setiap GTK punya card dan form-nya sendiri --}}
                <div class="card gtk-detail-content" id="gtk-detail-{{ $gtk->id }}" data-id="{{ $gtk->id }}" style="{{ !$loop->first ? 'display: none;' : '' }}">
                    <div class="card-body">
                        
                        {{-- PERBAIKAN NAMA ROUTE DAN @CSRF DI SINI --}}
                        <form action="{{ route('admin.kepegawaian.gtk.upload_media', $gtk->id) }}" method="POST" enctype="multipart/form-data">
@csrf
                            
                            <div class="row">
                                {{-- KOLOM KIRI BARU: UNTUK FOTO & TTD --}}
                                <div class="col-lg-4">
                                    <h5 class="card-title text-primary mb-3">Media Pegawai</h5>
                                    
                                    {{-- Upload Foto --}}
<div class="mb-3">
    <label class="form-label">Foto Profil</label>
    {{-- Kontainer baru dengan class untuk centering dan max-width --}}
    <div style="text-align: center; max-width: 200px; margin: 0 auto;"> 
        @if($gtk->foto)
            {{-- Langsung set style pada img tag --}}
            <img src="{{ asset('storage/' . $gtk->foto) }}" alt="Foto Profil" class="img-fluid rounded mb-2" style="max-width: 150px; height: auto;">
        @else
            <div class="mb-2 text-muted small" style="width: 150px; height: 150px; border: 2px dashed #ddd; border-radius: .5rem; display: flex; align-items: center; justify-content: center;">
                <i class="bx bx-user bx-lg"></i>
                <div>Belum ada foto</div>
            </div>
        @endif
    </div>
    <input class="form-control @error('foto') is-invalid @enderror" type="file" name="foto" id="foto-input-{{ $gtk->id }}" accept="image/*" 
        data-preview-target="#foto-preview-{{ $gtk->id }}" 
        data-placeholder-target="#foto-placeholder-{{ $gtk->id }}">

    @error('foto')
        <div class="form-text text-danger">{{ $message }}</div>
    @else
        <div class="form-text">Unggah foto (JPG/PNG). Maks 2MB.</div>
    @enderror
</div>
                                    
                                    {{-- Upload Tanda Tangan --}}
<div class="mb-3">
    <label class="form-label">Tanda Tangan</label>
    {{-- Kontainer baru dengan class untuk centering dan max-width --}}
    <div style="text-align: center; max-width: 250px; margin: 0 auto;"> 
        @if($gtk->tandatangan)
            {{-- Langsung set style pada img tag --}}
            <img src="{{ asset('storage/' . $gtk->tandatangan) }}" alt="Tanda Tangan" class="img-fluid rounded mb-2" style="max-width: 200px; height: auto; object-fit: contain;">
        @else
            <div class="mb-2 text-muted small" style="width: 200px; height: 100px; border: 2px dashed #ddd; border-radius: .5rem; display: flex; align-items: center; justify-content: center;">
                <i class="bx bx-pen bx-lg"></i>
                <div>Belum ada TTD</div>
            </div>
        @endif
    </div>
    <input class="form-control @error('tandatangan') is-invalid @enderror" type="file" name="tandatangan" id="ttd-input-{{ $gtk->id }}" accept="image/png"
        data-preview-target="#ttd-preview-{{ $gtk->id }}"
        data-placeholder-target="#ttd-placeholder-{{ $gtk->id }}">

    @error('tandatangan')
        <div class="form-text text-danger">{{ $message }}</div>
    @else
        <div class="form-text">Unggah TTD (PNG). Maks 1MB.</div>
    @enderror
</div>
                                    
                                    {{-- Tombol Simpan --}}
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-save me-1"></i> Simpan Media
                                    </button>
                                </div>

                                {{-- KOLOM KANAN: DATA DETAIL (KONTEN LAMA) --}}
                                <div class="col-lg-8">
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
                        </form>

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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. SCRIPT UNTUK NAVIGASI (KODE LAMA ANDA, SUDAH BENAR) ---
        const navItems = document.querySelectorAll('.gtk-nav-item');
        if (navItems.length > 0) {
            const detailContents = document.querySelectorAll('.gtk-detail-content');
            const cetakBtn = document.getElementById('cetak-pdf-btn');

            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                    const targetId = this.getAttribute('data-target');

                    detailContents.forEach(content => {
                        content.style.display = 'none';
                    });

                    const targetContent = document.getElementById(targetId);
                    if (targetContent) {
                        targetContent.style.display = 'block';

                        if(cetakBtn) {
                            const gtkId = targetContent.getAttribute('data-id');
                            let baseUrl = "{{ route('admin.kepegawaian.gtk.cetak_pdf', ['id' => ':id']) }}";
                            let newUrl = baseUrl.replace(':id', gtkId);
                            cetakBtn.setAttribute('href', newUrl);
                        }
                    }
                });
            });
        }

        document.querySelectorAll('input[type="file"][name="foto"], input[type="file"][name="tandatangan"]').forEach(input => {
    input.addEventListener('change', function(event) {
        // Kita tidak lagi perlu previewTarget dan placeholderTarget jika ingin langsung mengganti div-nya
        // Namun, jika ingin tetap menggunakan img tag dan placeholder icon, kita perlu logika sedikit berbeda.

        const file = event.target.files[0];
        const parentDiv = this.closest('.mb-3'); // Mencari div terdekat yang membungkus label, input, dan preview

        // Menentukan apakah ini untuk foto atau tanda tangan
        const isFoto = this.name === 'foto';
        const defaultMaxWidth = isFoto ? '150px' : '200px'; // Sesuaikan ukuran default
        const defaultHeight = isFoto ? '150px' : '100px'; // Sesuaikan ukuran default

        // Cek elemen gambar yang ada
        let currentImage = parentDiv.querySelector('img.img-fluid'); // Menggunakan class img-fluid dari Bootstrap
        let currentPlaceholder = parentDiv.querySelector('.mb-2.text-muted.small'); // Placeholder yang kita buat

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (currentImage) {
                    currentImage.src = e.target.result;
                    currentImage.style.display = 'block';
                    currentImage.style.maxWidth = defaultMaxWidth;
                    currentImage.style.height = 'auto'; // Pastikan tinggi otomatis
                } else {
                    // Jika belum ada img tag, buat yang baru
                    const newImage = document.createElement('img');
                    newImage.src = e.target.result;
                    newImage.alt = isFoto ? "Foto Profil" : "Tanda Tangan";
                    newImage.className = "img-fluid rounded mb-2";
                    newImage.style.maxWidth = defaultMaxWidth;
                    newImage.style.height = 'auto';
                    if (!isFoto) { // Untuk tanda tangan, set object-fit: contain
                        newImage.style.objectFit = 'contain';
                    }
                    // Ganti placeholder dengan gambar baru
                    if (currentPlaceholder) {
                        currentPlaceholder.parentNode.replaceChild(newImage, currentPlaceholder);
                    } else {
                        // Jika tidak ada placeholder (mungkin sudah ada gambar sebelumnya),
                        // cari div wrapper dan masukkan gambar baru
                        let wrapper = parentDiv.querySelector('div[style*="max-width"]');
                        if (wrapper) {
                            wrapper.innerHTML = ''; // Kosongkan dulu
                            wrapper.appendChild(newImage);
                        }
                    }
                    currentImage = newImage; // Update currentImage
                }
                if (currentPlaceholder) {
                    currentPlaceholder.style.display = 'none';
                }
            }
            reader.readAsDataURL(file);
        } else {
            // Jika file dihapus atau tidak ada, kembalikan ke placeholder
            if (currentImage) {
                currentImage.style.display = 'none'; // Sembunyikan gambar
            }
            if (currentPlaceholder) {
                currentPlaceholder.style.display = 'flex'; // Tampilkan placeholder
            } else {
                // Jika placeholder tidak ada, buat kembali
                const newPlaceholder = document.createElement('div');
                newPlaceholder.className = "mb-2 text-muted small";
                newPlaceholder.style.cssText = `width: ${defaultMaxWidth}; height: ${defaultHeight}; border: 2px dashed #ddd; border-radius: .5rem; display: flex; align-items: center; justify-content: center;`;
                newPlaceholder.innerHTML = `<i class="bx ${isFoto ? 'bx-user' : 'bx-pen'} bx-lg"></i><div>Belum ada ${isFoto ? 'foto' : 'TTD'}</div>`;

                let wrapper = parentDiv.querySelector('div[style*="max-width"]');
                if (wrapper) {
                    wrapper.innerHTML = '';
                    wrapper.appendChild(newPlaceholder);
                }
            }
        }
    });
});
    });
</script>
@endpush