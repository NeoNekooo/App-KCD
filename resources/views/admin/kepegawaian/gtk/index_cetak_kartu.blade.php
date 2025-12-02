@extends('layouts.admin') 

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepegawaian /</span> Cetak ID Card</h4>

    <div class="card mb-4 accordion-item">
        <h2 class="accordion-header" id="headingBackground">
            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionBackground" aria-expanded="false" aria-controls="accordionBackground">
                <i class="bx bx-image me-2"></i> Atur Desain Background Kartu
            </button>
        </h2>
        <div id="accordionBackground" class="accordion-collapse collapse" aria-labelledby="headingBackground" data-bs-parent="#accordionExample">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <label class="form-label d-block fw-bold mb-2">Preview Saat Ini:</label>
                        @if(isset($sekolah) && $sekolah->background_kartu)
                            <img src="{{ asset('storage/' . $sekolah->background_kartu) }}" alt="Background" class="img-fluid rounded border shadow-sm" style="height: 150px; object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center border rounded bg-light text-muted" style="height: 150px;">
                                <small>Belum ada<br>background custom</small>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <form action="{{ route('admin.kepegawaian.gtk.upload-background-kartu') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="formFile" class="form-label">Upload Desain Baru (JPG/PNG)</label>
                                <input class="form-control" type="file" id="formFile" name="background_kartu" accept="image/*" required>
                                <div class="form-text">
                                    Disarankan ukuran <strong>638 x 1011 pixel</strong> (Ratio Portrait). 
                                    Kosongkan area tengah desain agar foto & teks pegawai terlihat jelas.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-upload me-1"></i> Upload & Simpan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-3">Daftar Pegawai Siap Cetak</h5>
            
            <form action="{{ route('admin.kepegawaian.gtk.index-cetak-kartu') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama / NIP..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-4">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="status" id="statusAll" value="" {{ request('status') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="statusAll">Semua</label>

                            <input type="radio" class="btn-check" name="status" id="statusGuru" value="Guru" {{ request('status') == 'Guru' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="statusGuru">Guru</label>

                            <input type="radio" class="btn-check" name="status" id="statusTendik" value="Tenaga Kependidikan" {{ request('status') == 'Tenaga Kependidikan' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="statusTendik">Tendik</label>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i></button>
                    </div>
                    
                    <div class="col-md-3">
                        <a href="{{ route('admin.kepegawaian.gtk.print-all', request()->all()) }}" target="_blank" class="btn btn-dark w-100">
                            <i class="bx bx-printer me-1"></i> Cetak Semua Data
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>NIP / NUPTK</th>
                        <th>Status</th>
                        <th>Foto</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($gtks as $key => $gtk)
                    <tr>
                        <td>{{ $gtks->firstItem() + $key }}</td>
                        <td><strong>{{ $gtk->nama }}</strong></td>
                        <td>
                            @if($gtk->nip) {{ $gtk->nip }} <br><small class="text-muted">(NIP)</small>
                            @elseif($gtk->nuptk) {{ $gtk->nuptk }} <br><small class="text-muted">(NUPTK)</small>
                            @else - @endif
                        </td>
                        <td>
                            @if($gtk->jenis_ptk_id_str == 'Guru') <span class="badge bg-label-primary">Guru</span>
                            @else <span class="badge bg-label-info">Tendik</span> @endif
                        </td>
                        <td>
                            @if($gtk->foto)
                                <div class="avatar avatar-md">
                                    <img src="{{ asset('storage/' . $gtk->foto) }}" alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 40px; height: 40px;">
                                </div>
                            @else <span class="badge bg-warning">Belum Upload</span> @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.kepegawaian.gtk.print-kartu', $gtk->id) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-primary">
                                <i class="bx bx-printer"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $gtks->withQueryString()->links() }}</div>
    </div>
</div>
@endsection