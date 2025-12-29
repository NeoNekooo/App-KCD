@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Alumni /</span> Input Data Manual</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Formulir Data Alumni</h5>
                    <small class="text-muted">Input atas nama Alumni</small>
                </div>

                <div class="card-body">

                    {{-- Alert Error Global --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.alumni.store') }}" method="POST">
                        @csrf

                        {{-- 1. DATA SISWA --}}
                        <div class="mb-4">
                            <label class="form-label">Nama Alumni (Siswa)</label>

                            <input type="text" class="form-control"
                                value="{{ $siswas->nama }}" readonly>

                            <input type="hidden" name="peserta_didik_id"
                                value="{{ $siswas->peserta_didik_id }}">

                            <div class="form-text">
                                Data siswa diambil otomatis dari session dan tidak bisa diubah.
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- 2. TABS INPUT --}}
                        <div class="nav-align-top mb-4">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#navs-testimoni">
                                        <i class="bx bx-chat me-1"></i> Input Testimoni
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#navs-tracer">
                                        <i class="bx bx-briefcase-alt-2 me-1"></i> Input Tracer Study
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">

                                {{-- TAB TESTIMONI --}}
                                <div class="tab-pane fade show active" id="navs-testimoni">
                                    <div class="mb-3">
                                        <label class="form-label">Isi Testimoni</label>
                                        <textarea name="testimoni" class="form-control" rows="5"
                                            placeholder="Ketik testimoni alumni di sini...">{{ old('testimoni', $alumni->testimoni ?? '') }}</textarea>
                                    </div>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox"
                                            name="tampilkan_testimoni" value="1"
                                            {{ old('tampilkan_testimoni', $alumni->tampilkan_testimoni ?? 1) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Langsung Tayangkan (Publish) di Website?
                                        </label>
                                    </div>
                                </div>

                                {{-- TAB TRACER STUDY --}}
                                <div class="tab-pane fade" id="navs-tracer">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status Saat Ini</label>
                                            <select class="form-select" name="status_kegiatan"
                                                id="statusSelect" onchange="toggleForm()">
                                                <option value="">-- Pilih Status --</option>
                                                @foreach (['Bekerja','Kuliah','Wirausaha','Mencari Kerja'] as $s)
                                                    <option value="{{ $s }}"
                                                        {{ old('status_kegiatan', $alumni->status_kegiatan ?? '') == $s ? 'selected' : '' }}>
                                                        {{ $s }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tahun Mulai / Masuk</label>
                                            <input type="number" name="tahun_mulai" class="form-control"
                                                placeholder="YYYY"
                                                value="{{ old('tahun_mulai', $alumni->tahun_mulai ?? '') }}">
                                        </div>
                                    </div>

                                    {{-- FORM DINAMIS --}}
                                    <div id="dynamicForm" style="display:none;"
                                        class="p-3 bg-label-secondary rounded">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" id="labelInstansi">Nama Tempat</label>
                                                <input type="text" name="nama_instansi"
                                                    class="form-control"
                                                    value="{{ old('nama_instansi', $alumni->nama_instansi ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" id="labelJabatan">Jabatan / Jurusan</label>
                                                <input type="text" name="bidang_jabatan"
                                                    class="form-control"
                                                    value="{{ old('bidang_jabatan', $alumni->bidang_jabatan ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3" id="divPendapatan">
                                                <label class="form-label">Range Pendapatan</label>
                                                <select class="form-select" name="pendapatan">
                                                    <option value="">-- Pilih Range --</option>
                                                    @foreach (['< UMR','UMR','> UMR'] as $p)
                                                        <option value="{{ $p }}"
                                                            {{ old('pendapatan', $alumni->pendapatan ?? '') == $p ? 'selected' : '' }}>
                                                            {{ $p }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Linieritas Jurusan?</label>
                                                <select class="form-select" name="linieritas">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="Ya"
                                                        {{ old('linieritas', $alumni->linieritas ?? '') == 'Ya' ? 'selected' : '' }}>
                                                        Ya, Sesuai
                                                    </option>
                                                    <option value="Tidak"
                                                        {{ old('linieritas', $alumni->linieritas ?? '') == 'Tidak' ? 'selected' : '' }}>
                                                        Tidak Sesuai
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bx bx-save me-1"></i> Simpan Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', toggleForm);

    function toggleForm() {
        const status = document.getElementById('statusSelect')?.value;
        const form = document.getElementById('dynamicForm');
        const lblInstansi = document.getElementById('labelInstansi');
        const lblJabatan = document.getElementById('labelJabatan');
        const divPendapatan = document.getElementById('divPendapatan');

        if (!status || status === 'Mencari Kerja') {
            form.style.display = 'none';
            return;
        }

        form.style.display = 'block';

        if (status === 'Kuliah') {
            lblInstansi.innerText = 'Nama Universitas';
            lblJabatan.innerText = 'Jurusan / Prodi';
            divPendapatan.style.display = 'none';
        } else {
            lblInstansi.innerText = status === 'Wirausaha' ? 'Nama Usaha' : 'Nama Perusahaan';
            lblJabatan.innerText = status === 'Wirausaha' ? 'Bidang Usaha' : 'Jabatan';
            divPendapatan.style.display = 'block';
        }
    }
</script>
@endpush

@endsection
