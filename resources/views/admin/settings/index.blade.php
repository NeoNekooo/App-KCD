@extends('layouts.admin')

@section('title', 'Pengaturan Umum Website')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Website /</span> Konfigurasi Utama
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Identitas Instansi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.website.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8 mb-4">
                                <div class="mb-3">
                                    <label class="form-label" for="site_name">Nama Instansi / Website</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Kantor Cabang Dinas') }}" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="site_slogan">Slogan / Wilayah (Sub-teks)</label>
                                    <input type="text" class="form-control" id="site_slogan" name="site_slogan" value="{{ old('site_slogan', $settings['site_slogan'] ?? 'Provinsi Jawa Barat') }}" required />
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4 text-center">
                                <h6 class="text-start">Logo Instansi</h6>
                                <div class="mb-3">
                                    <div style="border-radius: 8px; border: 1px dashed #d9dee3; padding: 20px; text-align: center; max-width: 250px; background-color: #f5f5f9;">
                                        @if(isset($settings['site_logo']) && $settings['site_logo'])
                                            <img id="preview-img" src="{{ asset('storage/' . $settings['site_logo']) }}" style="max-width: 100%; max-height: 150px; object-fit: contain;" alt="Logo" />
                                        @else
                                            <img id="preview-img" src="" style="max-width: 100%; max-height: 150px; object-fit: contain; display: none;" alt="Logo" />
                                            <div id="preview-placeholder" style="height: 150px; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #a1acb8;">
                                                <i class="bx bx-image bx-lg mb-2"></i>
                                                <small>Preview Logo</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-3 text-start">
                                    <input class="form-control" type="file" name="site_logo" accept="image/*" onchange="previewImage(event)" />
                                    <div class="form-text mt-1 text-muted">Gunakan file PNG transparan.</div>
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
