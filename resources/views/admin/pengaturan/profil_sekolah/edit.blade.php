@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Profil Sekolah</h4>



<form action="{{ route('admin.pengaturan.profil_sekolah.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Kolom Kiri: Branding (Logo & Icon) -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    {{-- Bagian Logo yang Disempurnakan --}}
                    <div class="mb-3">
                        <h5 class="card-title mb-3">Logo Sekolah</h5>
                        <!-- Wrapper to center the image -->
                        <div class="d-flex justify-content-center">
                            <img src="{{ $profil->logo ? asset('storage/' . $profil->logo) : 'https://placehold.co/150x150?text=Logo' }}"
                                 alt="Logo Sekolah"
                                 class="img-thumbnail rounded-circle"
                                 id="logo-preview"
                                 style="width: 75px; height: 75px; object-fit: cover; aspect-ratio: 1/1;">
                        </div>
                    </div>
                    <div>
                        <input class="d-none" type="file" id="logo" name="logo" onchange="previewImage('logo', 'logo-preview', 'logo-filename');">
                        <label for="logo" class="btn btn-primary btn-sm">
                            <i class="bx bx-upload me-1"></i> Pilih File
                        </label>
                        <p class="text-muted mb-0 mt-2" id="logo-filename">No file chosen</p>
                    </div>

                    <hr class="my-4">

                    {{-- Bagian Icon yang Disempurnakan --}}
                    <div class="mb-3">
                        <h5 class="card-title mb-3">Icon (Favicon)</h5>
                        <!-- Wrapper to center the image -->
                        <div class="d-flex justify-content-center">
                            <img src="{{ $profil->icon ? asset('storage/' . $profil->icon) : 'https://placehold.co/64x64?text=Icon' }}"
                                 alt="Icon Sekolah"
                                 class="img-thumbnail"
                                 id="icon-preview"
                                 style="width: 64px; height: 64px; object-fit: cover; aspect-ratio: 1/1;">
                        </div>
                    </div>
                    <div>
                        <input class="d-none" type="file" id="icon" name="icon" onchange="previewImage('icon', 'icon-preview', 'icon-filename');">
                         <label for="icon" class="btn btn-primary btn-sm">
                            <i class="bx bx-upload me-1"></i> Pilih File
                        </label>
                        <p class="text-muted mb-0 mt-2" id="icon-filename">No file chosen</p>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Informasi dengan Tabs -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                     <div class="nav-align-top">
                        <ul class="nav nav-pills mb-3" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-profil" aria-controls="tab-profil" aria-selected="true">
                                    <i class="tf-icons bx bx-user me-1"></i> Profil
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-alamat" aria-controls="tab-alamat" aria-selected="false">
                                    <i class="tf-icons bx bx-map-alt me-1"></i> Alamat & Kontak
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-peta" aria-controls="tab-peta" aria-selected="false">
                                    <i class="tf-icons bx bx-map me-1"></i> Peta Lokasi
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content p-0">
                        <!-- Tab Profil -->
                        <div class="tab-pane fade show active" id="tab-profil" role="tabpanel">
                            <div class="row">
                                <div class="mb-3 col-md-8">
                                    <label for="nama_instansi" class="form-label">Nama Instansi</label>
                                    <input class="form-control" type="text" id="nama_instansi" name="nama_instansi" value="{{ old('nama_instansi', $profil->nama_instansi) }}">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="singkatan" class="form-label">Singkatan</label>
                                    <input class="form-control" type="text" name="singkatan" id="singkatan" value="{{ old('singkatan', $profil->singkatan) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <input class="form-control" type="text" id="status" name="status" value="{{ old('status', $profil->status) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="tahun_berdiri" class="form-label">Tahun Berdiri</label>
                                    <input class="form-control" type="text" name="tahun_berdiri" id="tahun_berdiri" value="{{ old('tahun_berdiri', $profil->tahun_berdiri) }}">
                                </div>
                                 <div class="mb-3 col-md-6">
                                    <label for="npsn" class="form-label">NPSN</label>
                                    <input class="form-control" type="text" id="npsn" name="npsn" value="{{ old('npsn', $profil->npsn) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="nss" class="form-label">NSS</label>
                                    <input class="form-control" type="text" id="nss" name="nss" value="{{ old('nss', $profil->nss) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="nama_kepala_sekolah" class="form-label">Nama Kepala Sekolah</label>
                                    <input class="form-control" type="text" id="nama_kepala_sekolah" name="nama_kepala_sekolah" value="{{ old('nama_kepala_sekolah', $profil->nama_kepala_sekolah) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="nip_kepala_sekolah" class="form-label">NIP Kepala Sekolah</label>
                                    <input class="form-control" type="text" id="nip_kepala_sekolah" name="nip_kepala_sekolah" value="{{ old('nip_kepala_sekolah', $profil->nip_kepala_sekolah) }}">
                                </div>
                            </div>
                        </div>
                        <!-- Tab Alamat & Kontak -->
                        <div class="tab-pane fade" id="tab-alamat" role="tabpanel">
                            <div class="row">
                                <div class="mb-3 col-12">
                                    <label for="alamat" class="form-label">Alamat Jalan</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $profil->alamat) }}</textarea>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="desa" class="form-label">Desa/Kelurahan</label>
                                    <input class="form-control" type="text" id="desa" name="desa" value="{{ old('desa', $profil->desa) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="kecamatan" class="form-label">Kecamatan</label>
                                    <input class="form-control" type="text" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $profil->kecamatan) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="kabupaten" class="form-label">Kabupaten</label>
                                    <input class="form-control" type="text" id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $profil->kabupaten) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="provinsi" class="form-label">Provinsi</label>
                                    <input class="form-control" type="text" id="provinsi" name="provinsi" value="{{ old('provinsi', $profil->provinsi) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="telepon" class="form-label">Telepon</label>
                                    <input class="form-control" type="text" id="telepon" name="telepon" value="{{ old('telepon', $profil->telepon) }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $profil->email) }}">
                                </div>
                                 <div class="mb-3 col-md-6">
                                    <label for="website" class="form-label">Website</label>
                                    <input class="form-control" type="text" id="website" name="website" value="{{ old('website', $profil->website) }}">
                                </div>
                            </div>
                        </div>
                        <!-- Tab Peta -->
                        <div class="tab-pane fade" id="tab-peta" role="tabpanel">
                            <div class="mb-3">
                                <label for="embed_peta" class="form-label">Embed Peta Google Maps</label>
                                <textarea class="form-control" id="embed_peta" name="embed_peta" rows="5">{{ old('embed_peta', $profil->embed_peta) }}</textarea>
                                <div class="form-text">Tempelkan kode embed dari Google Maps di sini (biasanya diawali dengan &lt;iframe&gt;).</div>
                            </div>
                            <div class="mt-3">
                                <h5>Pratinjau Peta:</h5>
                                <div class="w-100 border rounded p-2" style="min-height: 300px;">
                                    @if($profil->embed_peta)
                                        {!! $profil->embed_peta !!}
                                    @else
                                        <p class="text-muted text-center pt-5">Belum ada peta yang disematkan.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function previewImage(inputId, previewId, filenameId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const filenameText = document.getElementById(filenameId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
            filenameText.textContent = input.files[0].name;
        }
    }
</script>
@endpush
