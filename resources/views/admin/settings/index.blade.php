@extends('layouts.admin')

@section('title', 'Pengaturan Umum Website')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Website /</span> Konfigurasi Utama
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Identitas & Branding Aplikasi</h5>
                    <span class="badge bg-label-primary">Konfigurasi Global</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.website.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-7 mb-4">
                                <div class="mb-3">
                                    <label class="form-label" for="site_name">Nama Aplikasi (Browser Tab & App Title)</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'MANDALA') }}" required />
                                    <div class="form-text">Nama ini akan tampil di Judul Tab Browser dan Judul Utama Dashboard.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="site_slogan">Slogan Aplikasi (Sub-teks)</label>
                                    <input type="text" class="form-control" id="site_slogan" name="site_slogan" value="{{ old('site_slogan', $settings['site_slogan'] ?? 'Sistem Monitoring Wilayah Jabar') }}" required />
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                        <i class="bx bx-save me-1"></i> Simpan Konfigurasi Pusat
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-5 mb-4">
                                <div class="row">
                                    <div class="col-6 text-center border-end">
                                        <h6 class="text-start mb-3"><i class="bx bx-image me-1"></i>Logo App</h6>
                                        <div class="mb-3 d-flex justify-content-center">
                                            <div style="border-radius: 8px; border: 1px dashed #d9dee3; padding: 10px; text-align: center; width: 120px; height: 120px; background-color: #f5f5f9; display: flex; align-items: center; justify-content: center;">
                                                @if(isset($settings['site_logo']) && $settings['site_logo'])
                                                    <img id="preview-logo" src="{{ asset('storage/' . $settings['site_logo']) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Logo" />
                                                @else
                                                    <div id="placeholder-logo" style="color: #a1acb8;">
                                                        <i class="bx bx-image-add bx-md"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <input class="form-control form-control-sm" type="file" name="site_logo" accept="image/*" onchange="previewImage(event, 'preview-logo', 'placeholder-logo')" />
                                        </div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <h6 class="text-start mb-3"><i class="bx bx-window-alt me-1"></i>Favicon</h6>
                                        <div class="mb-3 d-flex justify-content-center">
                                            <div style="border-radius: 8px; border: 1px dashed #d9dee3; padding: 10px; text-align: center; width: 120px; height: 120px; background-color: #f5f5f9; display: flex; align-items: center; justify-content: center;">
                                                @if(isset($settings['site_favicon']) && $settings['site_favicon'])
                                                    <img id="preview-favicon" src="{{ asset('storage/' . $settings['site_favicon']) }}" style="width: 48px; height: 48px; object-fit: contain;" alt="Favicon" />
                                                @else
                                                    <div id="placeholder-favicon" style="color: #a1acb8;">
                                                        <i class="bx bx-cube-alt bx-md"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <input class="form-control form-control-sm" type="file" name="site_favicon" accept="image/x-icon,image/png,image/jpg" onchange="previewImage(event, 'preview-favicon', 'placeholder-favicon')" />
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-4 py-2" style="font-size: 0.75rem;">
                                    <i class="bx bx-info-circle me-1"></i> Gunakan PNG/ICO untuk Favicon (Rekomendasi: 32x32px).
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var output = document.getElementById('preview-img');
                var placeholder = document.getElementById('preview-placeholder');
                
                output.src = dataURL;
                output.style.display = 'block';
                
                if(placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            if (input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
