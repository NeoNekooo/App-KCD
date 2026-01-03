@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Monitoring /</span> Data Satuan Pendidikan</h4>

{{-- RINGKASAN JUMLAH SEKOLAH --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ number_format($totalSekolah) }}</h4>
                        <small class="text-muted text-uppercase fw-semibold">Total Sekolah</small>
                    </div>
                    <div class="avatar bg-label-primary rounded p-2"><i class="bx bx-buildings fs-3"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0 fw-bold text-success">{{ number_format($totalNegeri) }}</h4>
                        <small class="text-muted text-uppercase fw-semibold">Sekolah Negeri</small>
                    </div>
                    <div class="avatar bg-label-success rounded p-2"><i class="bx bx-check-shield fs-3"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">{{ number_format($totalSwasta) }}</h4>
                        <small class="text-muted text-uppercase fw-semibold">Sekolah Swasta</small>
                    </div>
                    <div class="avatar bg-label-warning rounded p-2"><i class="bx bx-home-heart fs-3"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    {{-- HEADER & FILTER --}}
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Daftar Sekolah Binaan</h5>
    </div>
    <div class="card-body mt-3">
        <form action="{{ route('admin.sekolah.index') }}" method="GET">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small text-muted">Kabupaten</label>
                    <select name="kabupaten_kota" class="form-select" onchange="this.form.submit()">
                        <option value="">- Semua -</option>
                        @foreach($listKabupaten as $kab)
                            <option value="{{ $kab }}" {{ request('kabupaten_kota') == $kab ? 'selected' : '' }}>{{ str_replace('Kab. ', '', $kab) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Kecamatan</label>
                    <select name="kecamatan" class="form-select" onchange="this.form.submit()">
                        <option value="">- Semua -</option>
                        @foreach($listKecamatan as $kec)
                            <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ str_replace('Kec. ', '', $kec) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Jenjang</label>
                    <select name="jenjang" class="form-select" onchange="this.form.submit()">
                        <option value="">- Semua -</option>
                        @foreach($listJenjang as $j)
                            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status_sekolah" class="form-select" onchange="this.form.submit()">
                        <option value="">- Semua -</option>
                        @foreach($listStatus as $s)
                            <option value="{{ $s }}" {{ request('status_sekolah') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Cari Data</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nama Sekolah / NPSN..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover table-striped align-middle">
            <thead class="bg-light">
                <tr>
                    <th width="1%">No</th>
                    <th>Satuan Pendidikan</th>
                    <th>NPSN</th>
                    <th>Jenjang</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                    <th class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($sekolahs as $index => $sekolah)
                <tr>
                    <td>{{ $sekolahs->firstItem() + $index }}</td>
                    <td style="min-width: 250px;">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                @if(!empty($sekolah->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->logo))
                                    <img src="{{ asset('storage/' . $sekolah->logo) }}" alt="Logo" class="rounded-circle" style="object-fit: cover;">
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($sekolah->nama, 0, 2) }}</span>
                                @endif
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-body text-truncate" style="max-width: 250px;">{{ $sekolah->nama }}</span>
                                <small class="text-muted">{{ $sekolah->email ?? '-' }}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-label-dark">{{ $sekolah->npsn ?? '-' }}</span></td>
                    <td><span class="fw-medium">{{ $sekolah->bentuk_pendidikan_id_str ?? '-' }}</span></td>
                    <td>
                        <span class="badge bg-label-{{ ($sekolah->status_sekolah_str == 'Negeri') ? 'success' : 'warning' }}">
                            {{ $sekolah->status_sekolah_str ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-dark small fw-semibold">{{ $sekolah->kecamatan ?? '-' }}</span>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $sekolah->kabupaten_kota ?? '-' }}</small>
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.sekolah.show', $sekolah->id) }}" class="btn btn-sm btn-icon btn-label-info"><i class="bx bx-show-alt"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center"><i class="bx bx-buildings bx-lg text-muted mb-3"></i><h6 class="text-muted">Data tidak ditemukan.</h6></div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- PAGINATION --}}
    <div class="card-footer border-top">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="text-muted me-2 small">Per halaman:</span>
                <form action="{{ route('admin.sekolah.index') }}" method="GET" class="d-inline-block">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="per_page" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </form>
                <span class="text-muted ms-2 small">Total: <strong>{{ $sekolahs->total() }}</strong></span>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                {{ $sekolahs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection