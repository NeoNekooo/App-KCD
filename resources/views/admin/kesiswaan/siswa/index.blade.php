@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Monitoring /</span> Data Siswa Wilayah</h4>

<div class="card">
    {{-- HEADER --}}
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Data Sebaran Siswa</h5>
            <div>
                {{-- Tombol ini hanya muncul jika ada baris yang diceklis --}}
                <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm" style="display: none;">
                    <i class="bx bx-show-alt me-1"></i> Lihat Detail
                </a>
            </div>
        </div>
    </div>

    {{-- FILTER WILAYAH --}}
    <div class="card-body">
        <form action="{{ route('admin.kesiswaan.siswa.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">

            <div class="row g-2">
                {{-- Filter Kabupaten --}}
                <div class="col-md-3">
                    <label class="form-label text-muted small">Kabupaten/Kota</label>
                    <select name="kabupaten_kota" class="form-select" onchange="this.form.submit()">
                        <option value="">- Semua Wilayah -</option>
                        @foreach($listKabupaten as $kab)
                            <option value="{{ $kab }}" {{ request('kabupaten_kota') == $kab ? 'selected' : '' }}>
                                {{ $kab }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kecamatan --}}
                <div class="col-md-3">
                    <label class="form-label text-muted small">Kecamatan</label>
                    <select name="kecamatan" class="form-select" onchange="this.form.submit()">
                        <option value="">- Semua Kecamatan -</option>
                        @foreach($listKecamatan as $kec)
                            <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>
                                {{ $kec }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian --}}
                <div class="col-md-6">
                    <label class="form-label text-muted small">Cari Siswa</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nama, NISN, atau NIK..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover table-striped">
            <thead class="bg-light">
                <tr>
                    <th width="1%"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                    <th>Identitas Siswa</th>
                    <th>L/P</th>
                    <th>Nomor Induk</th> {{-- Digabung NISN & NIK --}}
                    <th>Domisili (Wilayah)</th> {{-- PENTING BUAT KCD --}}
                    <th>TTL</th>
                    <th>Kelas</th>
                    <th width="5%" class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($siswas as $siswa)
                <tr>
                    {{-- Checkbox --}}
                    <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $siswa->id }}"></td>

                    {{-- NAMA & FOTO --}}
                    <td style="min-width: 250px;">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="avatar-wrapper me-3">
                                <a href="{{ route('admin.kesiswaan.siswa.show', $siswa->id) }}">
                                    <div class="avatar avatar-sm">
                                        @if(!empty($siswa->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto))
                                            <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Avatar" class="rounded-circle" style="object-fit: cover;">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama) }}&background=random&color=ffffff&size=100" class="rounded-circle">
                                        @endif
                                    </div>
                                </a>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.kesiswaan.siswa.show', $siswa->id) }}" class="text-body text-truncate fw-semibold">
                                    {{ $siswa->nama }}
                                </a>
                                <small class="text-muted">NIS: {{ $siswa->nis ?? '-' }}</small>
                            </div>
                        </div>
                    </td>

                    {{-- JENIS KELAMIN --}}
                    <td>{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'L' : 'P' }}</td>

                    {{-- NISN & NIK (Digabung biar hemat tempat) --}}
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold small" title="NISN">{{ $siswa->nisn ?? '-' }}</span>
                            <small class="text-muted" title="NIK">{{ $siswa->nik ?? '-' }}</small>
                        </div>
                    </td>

                    {{-- DOMISILI (Kecamatan & Kab/Kota) --}}
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-semibold" style="font-size: 0.9em;">
                                Kec. {{ $siswa->kecamatan ?? '-' }}
                            </span>
                            <small class="text-muted">
                                {{ $siswa->kabupaten_kota ?? '-' }}
                            </small>
                        </div>
                    </td>

                    {{-- TTL --}}
                    <td>
                        <div class="d-flex flex-column">
                            <span>{{ $siswa->tempat_lahir ?? '' }}</span>
                            <small class="text-muted">{{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') : '-' }}</small>
                        </div>
                    </td>

                    {{-- KELAS --}}
                    <td>
                        @php
                            $namaKelas = $siswa->rombel->nama ?? null;
                            if (!$namaKelas && isset($siswaRombelMap) && isset($siswaRombelMap[$siswa->peserta_didik_id])) {
                                $namaKelas = $siswaRombelMap[$siswa->peserta_didik_id];
                            }
                        @endphp

                        @if($namaKelas)
                            <span class="badge bg-label-primary">{{ $namaKelas }}</span>
                        @else
                            <span class="badge bg-label-secondary">Belum Masuk Kelas</span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="text-center">
                        <a href="{{ route('admin.kesiswaan.siswa.show', $siswa->id) }}" class="btn btn-sm btn-icon btn-label-info" title="Lihat Detail">
                            <i class="bx bx-show-alt"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="bx bx-map-alt bx-lg text-muted mb-2"></i>
                            <h6 class="text-muted">Data siswa tidak ditemukan untuk wilayah/filter ini.</h6>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER PAGINATION --}}
    <div class="card-footer border-top">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center mb-2 mb-md-0">
                <span class="text-muted me-2 small">Show:</span>
                <form action="{{ route('admin.kesiswaan.siswa.index') }}" method="GET" class="d-inline-block">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="kabupaten_kota" value="{{ request('kabupaten_kota') }}">
                    <input type="hidden" name="kecamatan" value="{{ request('kecamatan') }}">

                    <select name="per_page" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                        <option value="35" {{ request('per_page') == '35' ? 'selected' : '' }}>35</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </form>
                <span class="text-muted ms-2 small">Total: <strong>{{ $siswas->total() }}</strong> Siswa</span>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end justify-content-center">
                @if(request('per_page') != 'all')
                    {{ $siswas->appends(request()->query())->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const viewSelectedBtn = document.getElementById('viewSelectedBtn');
        
        // Logic Tampilan Tombol "Lihat Detail"
        function handleCheckboxChange() {
            const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkedCheckboxes.length > 0) {
                viewSelectedBtn.style.display = 'inline-block';
                if(checkedCheckboxes.length === 1) {
                    viewSelectedBtn.innerHTML = '<i class="bx bx-show-alt me-1"></i> Lihat Detail';
                } else {
                    viewSelectedBtn.innerHTML = '<i class="bx bx-show-alt me-1"></i> Lihat Detail (' + checkedCheckboxes.length + ')';
                }
            } else {
                viewSelectedBtn.style.display = 'none';
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => { checkbox.checked = this.checked; });
                handleCheckboxChange();
            });
        }

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', handleCheckboxChange);
        });

        // Logic Klik Tombol "Lihat Detail"
        if (viewSelectedBtn) {
            viewSelectedBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
                if (checkedCheckboxes.length === 1) {
                    window.location.href = `/admin/kesiswaan/siswa/${checkedCheckboxes[0].value}`;
                } else if (checkedCheckboxes.length > 1) {
                    const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value).join(',');
                    window.location.href = `{{ route('admin.kesiswaan.siswa.show-multiple') }}?ids=${selectedIds}`;
                }
            });
        }
        handleCheckboxChange();
    });
</script>
@endpush