@extends('layouts.admin')

@push('styles')
{{-- Library untuk Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
{{-- Library untuk Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

{{-- [PERBAIKAN CSS] Untuk mengatasi layout 'kacau' di grid --}}
<style>
    /* [BARU] Paksa tabel agar lebarnya 100% dan kolomnya dibagi rata */
    .table-bordered {
        table-layout: fixed;
        width: 100%;
    }

    .schedule-cell {
        padding: 8px;
        vertical-align: top;
        min-width: 140px; /* Kita set min-width, bukan width */

        /* [BARU] Ini adalah kunci untuk memotong teks panjang */
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .schedule-cell .schedule-item {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.5rem;
        font-size: 0.9rem;
        
        /* [BARU] Pastikan div-nya juga bisa menangani overflow */
        overflow: hidden; 
        height: 100%; /* [BARU] Penuhi tinggi sel */
    }

    .schedule-cell .schedule-item strong {
        display: block;
        font-size: 1rem;
        color: #000;
        
        /* [BARU] Izinkan nama mapel yang panjang untuk patah juga */
        word-break: break-word;
        white-space: normal; /* Pastikan tidak jadi satu baris */
    }

    /* Highlight untuk jadwal yang sedang diedit */
    .schedule-item.editing {
        background-color: #d1ecf1; /* table-info */
        border-color: #bee5eb;
    }
</style>
@endpush

@section('content')

@php
    // Logika untuk menentukan apakah form harus ditampilkan (terbuka)
    $isEditing = (bool)$jadwalToEdit;
    $hasErrors = $errors->any();
    $showForm = $isEditing || $hasErrors;
@endphp

<div class="row">
    
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    @if($isEditing)
                        <i class="bi bi-pencil-square me-2"></i> Edit Jadwal Pelajaran
                    @else
                        <i class="bi bi-plus-circle me-2"></i> Tambah Jadwal Baru
                    @endif
                </h5>
                <a class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" href="#formJadwalCollapse" role="button" aria-expanded="{{ $showForm ? 'true' : 'false' }}" aria-controls="formJadwalCollapse">
                    @if($showForm)
                        <i class="bi bi-chevron-up"></i> Tutup Form
                    @else
                        <i class="bi bi-chevron-down"></i> Buka Form
                    @endif
                </a>
            </div>
            
            <div class="collapse {{ $showForm ? 'show' : '' }}" id="formJadwalCollapse">
                <div class="card-body">
                    
                    @if ($errors->any()) 
                    <div class="alert alert-danger" role="alert"> 
                        <h4 class="alert-heading">Oops! Terjadi kesalahan validasi:</h4> 
                        <ul class="mb-0"> 
                            @foreach ($errors->all() as $error) 
                            <li>{{ $error }}</li> 
                            @endforeach 
                        </ul> 
                    </div> 
                    @endif

                    @php 
                        $formAction = $isEditing 
                            ? route('admin.akademik.jadwal-pelajaran.update', $jadwalToEdit->id) 
                            : route('admin.akademik.jadwal-pelajaran.store'); 
                    @endphp
                    
                    <form action="{{ $formAction }}" method="POST">
                        @csrf
                        @if($isEditing) @method('PUT') @endif
                        
                        {{-- [PERBAIKAN FORM] TAHUN AJARAN (DISABLE) --}}
                        <div class="mb-3">
                            <label for="tahun_ajaran_id_display" class="form-label">Tahun Ajaran</label>
                            
                            @if($tapelAktif)
                                {{-- Input ini hanya untuk ditampilkan, tidak dikirim --}}
                                <input type="text" class="form-control" id="tahun_ajaran_id_display" 
                                    value="{{ $tapelAktif->tahun_ajaran }} ({{ $tapelAktif->semester }})" disabled readonly>
                                    
                                {{-- Input ini tersembunyi dan akan dikirim bersama form --}}
                                <input type="hidden" name="tahun_ajaran_id" value="{{ $tapelAktif->id }}">
                            @else
                                {{-- Jika karena suatu alasan tidak ada Tapel Aktif --}}
                                <input type="text" class="form-control is-invalid" 
                                    value="Tidak ada Tahun Ajaran aktif!" disabled readonly>
                                <div class="invalid-feedback">
                                    Harap aktifkan satu Tahun Ajaran di pengaturan.
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="rombel_id" class="form-label">Rombongan Belajar</label>
                            <select id="rombel_id" class="form-select select2" name="rombel_id" required>
                                <option value="">Pilih Rombel</option>
                                @foreach($rombels as $rombel) 
                                <option value="{{ $rombel->id }}" @selected(old('rombel_id', optional($jadwalToEdit)->rombel_id) == $rombel->id)> 
                                    {{ $rombel->nama }} 
                                </option> 
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                            <select id="mapel_id" class="form-select select2" name="mapel_id" required>
                                <option value="">Pilih Mapel</option>
                                @foreach($mapels as $mapel) 
                                <option value="{{ $mapel['kode'] }}" @selected(old('mapel_id') == $mapel['kode'])> 
                                    {{ $mapel['nama_mapel'] }} 
                                </option> 
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ptk_id" class="form-label">Guru Pengajar</label>
                            <select class="form-select" id="ptk_id" name="ptk_id">
    <option>Pilih Guru</option>
    @foreach ($gtks as $guru)
        <option value="{{ $guru->id }}" 
            @if(isset($jadwalToEdit) && $jadwalToEdit->gtk?->id == $guru->id) selected @endif
        >
            {{ $guru->nama }}
        </option>
    @endforeach
</select>
                        </div>

                        <div class="mb-3">
                            <label for="hari" class="form-label">Hari</label>
                            <select id="hari" class="form-select" name="hari" required>
                                <option value="">Pilih Hari</option>
                                @foreach($daftarHari as $hari) 
                                <option value="{{ $hari }}" @selected(old('hari', optional($jadwalToEdit)->hari) == $hari)> 
                                    {{ $hari }} 
                                </option> 
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-6"> <label for="jam_mulai" class="form-label">Jam Mulai</label> <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', optional($jadwalToEdit)->jam_mulai) }}" required> </div>
                            <div class="mb-3 col-6"> <label for="jam_selesai" class="form-label">Jam Selesai</label> <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', optional($jadwalToEdit)->jam_selesai) }}" required> </div>
                        </div>

                        <div class="d-grid gap-2">
                            @if($isEditing)
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                                <a href="{{ route('admin.akademik.jadwal-pelajaran.index', ['rombel_id_filter' => $rombelFilterId]) }}" class="btn btn-outline-secondary">Batal Edit</a>
                            @else
                                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Simpan Jadwal Baru</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Tampilan Grid Jadwal Pelajaran</h5>
                
                {{-- Filter berdasarkan Rombel --}}
                <form action="{{ route('admin.akademik.jadwal-pelajaran.index') }}" method="GET" class="d-flex gap-2">
                    <select name="rombel_id_filter" class="form-select" style="width: 250px;" required> 
                        <option value="">-- Pilih Rombongan Belajar --</option> 
                        @foreach($allRombels as $rombel) 
                        <option value="{{ $rombel->id }}" @selected($rombelFilterId == $rombel->id)>
                            {{ $rombel->nama }}
                        </option> 
                        @endforeach 
                    </select> 
                    <button class="btn btn-primary d-flex align-items-center" type="submit">
                        <i class="bi bi-display me-1"></i> Tampilkan
                    </button> 
                </form>
            </div>

            <div class="card-body">
                
                @if(session('success')) <div class="alert alert-success alert-dismissible" role="alert"> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div> @endif
                @if(session('error')) <div class="alert alert-danger alert-dismissible" role="alert"> {{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div> @endif

                {{-- Tabel Grid --}}
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-light"> 
                            <tr> 
                                <th style="width: 12%;">Jam</th> 
                                @foreach($daftarHari as $hari)
                                <th>{{ $hari }}</th> 
                                @endforeach
                            </tr> 
                        </thead>
                        <tbody>
                            @if (!$rombelFilterId)
                                <tr>
                                    <td colspan="{{ count($daftarHari) + 1 }}" class="text-center p-5">
                                        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                                        Silakan pilih Rombongan Belajar di atas untuk menampilkan jadwal.
                                    </td>
                                </tr>
                            @elseif (empty($uniqueTimeSlots))
                                <tr>
                                    <td colspan="{{ count($daftarHari) + 1 }}" class="text-center p-5">
                                        <i class="bi bi-x-circle fs-3 d-block mb-2"></i>
                                        Tidak ada data jadwal pelajaran untuk rombel yang dipilih.
                                    </td>
                                </tr>
                            @else
                                {{-- Loop berdasarkan BARIS (Jam) --}}
                                @foreach ($uniqueTimeSlots as $timeSlot)
                                <tr>
                                    {{-- Kolom Header Jam --}}
                                    <td class="align-middle"><strong>{{ $timeSlot }}</strong></td>
                                    
                                    {{-- Loop berdasarkan KOLOM (Hari) --}}
                                    @foreach ($daftarHari as $hari)
                                        
                                        @php
                                            // Cek apakah ada jadwal di [JAM][HARI] ini
                                            $jadwal = $jadwalGrid[$timeSlot][$hari] ?? null;
                                        @endphp
                                        
                                        {{-- Sel jadwal --}}
                                        <td class="schedule-cell">
                                            @if ($jadwal)
                                                @php
                                                    // Cek apakah ini jadwal yg sedang diedit
                                                    $isEditingThis = $isEditing && $jadwalToEdit->id == $jadwal->id;
                                                @endphp
                                                <div class="schedule-item {{ $isEditingThis ? 'editing' : '' }}">
                                                    {{-- Info Mapel & Guru --}}
                                                    <strong>{{ $jadwal->mata_pelajaran }}</strong>
                                                    <span>{{ $jadwal->ptk->nama ?? 'N/A' }}</span>
                                                    
                                                    <hr class="my-2">
                                                    
                                                    {{-- Tombol Aksi --}}
                                                    <div class="d-flex justify-content-center gap-1"> 
                                                        {{-- Link Edit + bawa filter rombel --}}
                                                        <a href="{{ route('admin.akademik.jadwal-pelajaran.edit', $jadwal->id) }}?rombel_id_filter={{ $rombelFilterId }}" class="btn btn-xs btn-info" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a> 
                                                        <form action="{{ route('admin.akademik.jadwal-pelajaran.destroy', $jadwal->id) }}?rombel_id_filter={{ $rombelFilterId }}" method="POST" onsubmit="return confirm('Yakin hapus jadwal ini?');"> 
                                                            @csrf 
                                                            @method('DELETE') 
                                                            <button typeS="submit" class="btn btn-xs btn-danger" data-bs-toggle="tooltip" title="Hapus">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button> 
                                                        </form> 
                                                    </div> 
                                                </div>
                                            @else
                                                {{-- Sel kosong --}}
                                                &nbsp;
                                            @endif
                                        </td>

                                    @endforeach
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
</div> 
@endsection

@push('scripts')
{{-- (Abaikan jika error 'select2 not found' sudah Anda perbaiki di layout utama) --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}} 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {

        // Skrip Select2
        if (typeof $().select2 === 'function') {
            $('.select2').select2({
                theme: "bootstrap-5",
                width: $( this ).data( 'width' ) ? $( this ).data( 'width') : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            });
        } else {
            console.error('Error: Fungsi jQuery $.select2() tidak ditemukan.');
        }


        // [PERBAIKAN LOGIKA MAPEL SAAT EDIT]
        @if($isEditing)
            // 1. Ambil nama mapel yang tersimpan di database
            var savedMapelName = @json($jadwalToEdit->mata_pelajaran);
            
            // 2. Ambil daftar mapel (dari dropdown)
            var mapelList = @json($mapels); 
            
            var selectedMapelId = null;

            // Debugging (bisa dihapus nanti)
            // console.log("Mode Edit: Mencari Mapel...");
            // console.log("Nama Tersimpan:", savedMapelName);

            // 3. Loop untuk mencari ID yang cocok
            if (savedMapelName && mapelList) {
                for (var i = 0; i < mapelList.length; i++) {
                    // Cocokkan nama mapel
                    if (mapelList[i].nama_mapel === savedMapelName) {
                        selectedMapelId = mapelList[i].kode; // 'kode' adalah value di <option>
                        
                        // Debugging (bisa dihapus nanti)
                        // console.log("COCOK!", "ID ditemukan:", selectedMapelId);
                        break; 
                    }
                }
            }

            // 4. Jika ID ditemukan, set value dropdown-nya
            if (selectedMapelId) {
                // Gunakan .val() untuk memilih dan .trigger('change') untuk update tampilan Select2
                $('#mapel_id').val(selectedMapelId).trigger('change');
            } else {
                console.warn("PERINGATAN: Tidak ditemukan ID mapel yang cocok untuk nama:", savedMapelName);
            }
        @endif


        // Inisialisasi Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Skrip Buka/Tupup Form
        var collapseElement = document.getElementById('formJadwalCollapse');
        var collapseToggleButton = document.querySelector('[href="#formJadwalCollapse"]');

        if (collapseElement && collapseToggleButton) {
            collapseElement.addEventListener('show.bs.collapse', function () {
                collapseToggleButton.innerHTML = '<i class="bi bi-chevron-up"></i> Tutup Form';
            });
            collapseElement.addEventListener('hide.bs.collapse', function () {
                collapseToggleButton.innerHTML = '<i class="bi bi-chevron-down"></i> Buka Form';
            });
        }
    });
</script>
@endpush