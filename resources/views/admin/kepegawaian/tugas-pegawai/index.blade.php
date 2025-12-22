@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center py-3 mb-4">
    <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Kepegawaian /</span> Tugas Pegawai</h4>
    <div class="d-flex align-items-center">
        <form action="{{ route('admin.kepegawaian.tugas-pegawai.sync') }}" method="POST" class="me-3">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-sync me-1"></i> Sinkron dari Rombel</button>
        </form>
        <div class="badge bg-label-primary fs-6">{{ $tahunAktifTampil }} - Semester {{ $semesterAktifTampil }}</div>
    </div>
</div>

<div class="nav-align-top mb-4">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-mapel"><i class="bx bx-book me-1"></i> Mata Pelajaran</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-struktural"><i class="bx bx-briefcase me-1"></i> Jabatan Struktural</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-wali"><i class="bx bx-user me-1"></i> Wali Kelas</button></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-mapel">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Mata Pelajaran</th>
                            <th>Jam</th>
                            <th>Nomor SK</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tugasPokok as $t)
                        <tr>
                            <td>{{ $loop->iteration + ($tugasPokok->currentPage() - 1) * $tugasPokok->perPage() }}</td>
                            <td>
                                <strong>{{ Str::title(strtolower($t->gtk->nama)) }}</strong>
                                <div class="small text-muted">{{ $t->gtk->nip ?? $t->gtk->nik }}</div>
                            </td>
                            <td>
                                <span class="text-capitalize-custom">{{ $t->tugas_pokok }}</span>
                            </td>
                            <td><span class="badge bg-label-info">{{ $t->jumlah_jam }} Jam</span></td>
                            <td>
                                <div class="edit-sk-container" data-id="{{ $t->id }}">
                                    <span class="sk-text cursor-pointer text-primary" onclick="toggleEditSk('{{ $t->id }}')">
                                        {{ $t->nomor_sk ?? 'Klik untuk isi SK...' }} <i class="bx bx-pencil ms-1 small"></i>
                                    </span>
                                    <div class="sk-input-group d-none" id="input-group-{{ $t->id }}">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="input-{{ $t->id }}" value="{{ $t->nomor_sk }}">
                                            <button class="btn btn-primary" onclick="saveSk('{{ $t->id }}')"><i class="bx bx-check"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-label-primary" onclick="openCetakModal('{{ $t->id }}', '{{ Str::title(strtolower($t->gtk->nama)) }}')"><i class="bx bx-printer"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $tugasPokok->appends(request()->except('page_mapel'))->links() }}</div>
        </div>

        <div class="tab-pane fade" id="tab-struktural">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Jabatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jabatanStruktural as $j)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ Str::title(strtolower($j->nama)) }}</strong></td>
                            <td>{{ Str::title(strtolower($j->jabatan_ptk_id_str)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $jabatanStruktural->appends(request()->except('page_struktural'))->links() }}</div>
        </div>

        <div class="tab-pane fade" id="tab-wali">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kelas</th>
                            <th>Wali Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($waliKelas as $w)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $w->nama }}</td>
                            <td>{{ Str::title(strtolower($w->waliKelas->nama ?? '-')) }}</td>
                        </tr>
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
            {{-- <div class="mt-3">{{ $waliKelas->appends(request()->except('page_wali'))->links() }}</div> --}}
        </div>
    </div>
</div>

{{-- MODAL CETAK --}}
<div class="modal fade" id="modalCetakSk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="formCetakSk" target="_blank">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Template SK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Mencetak SK untuk: <b id="targetNama"></b></p>
                <div class="mb-3">
                    <label class="form-label">Daftar Template SK</label>
                    <select name="template_id" class="form-select" required>
                        @foreach(\App\Models\TipeSurat::where('kategori', 'sk')->get() as $tmpl)
                            <option value="{{ $tmpl->id }}">{{ $tmpl->judul_surat }} ({{ $tmpl->ukuran_kertas }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Preview PDF</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleEditSk(id) {
        $(`.edit-sk-container[data-id="${id}"] .sk-text`).toggleClass('d-none');
        $(`#input-group-${id}`).toggleClass('d-none');
    }

    function saveSk(id) {
        const val = $(`#input-${id}`).val();
        $.ajax({
            url: "{{ route('admin.kepegawaian.tugas-pegawai.update-sk') }}",
            method: "POST",
            data: { _token: "{{ csrf_token() }}", id: id, nomor_sk: val },
            success: function() {
                $(`.edit-sk-container[data-id="${id}"] .sk-text`).html(val ? val + ' <i class="bx bx-pencil ms-1 small"></i>' : 'Klik untuk isi...');
                toggleEditSk(id);
            }
        });
    }

    function openCetakModal(id, nama) {
        $('#targetNama').text(nama);
        let url = "{{ route('admin.kepegawaian.tugas-pegawai.cetak', ':id') }}";
        url = url.replace(':id', id);
        $('#formCetakSk').attr('action', url);
        $('#modalCetakSk').modal('show');
    }
</script>
@endpush

<style>
    .cursor-pointer { cursor: pointer; }
    .sk-text:hover { color: #696cff !important; text-decoration: underline; }
    /* Memastikan huruf awal kapital lewat CSS (opsional sebagai cadangan) */
    .text-capitalize-custom {
        text-transform: lowercase;
        display: inline-block;
    }
    .text-capitalize-custom::first-line {
        text-transform: capitalize;
    }
</style>
@endsection
