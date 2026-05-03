@extends('layouts.admin')

@section('title', 'Daftar Pengawas Pembina')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PKKS /</span> Daftar Pengawas Pembina</h4>

    <div class="card shadow-none border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Pengawas & Sekolah Binaan</h5>
            <a href="{{ route('admin.pkks.mapping-pengawas.index') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i> Kelola Mapping
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Pengawas</th>
                        <th>Jabatan</th>
                        <th>Sekolah Binaan</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($pembina as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($p->name, 0, 1) }}</span>
                                </div>
                                <span class="fw-bold">{{ $p->name }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-label-info">{{ $p->role }}</span></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($p->pengawasPembinas as $mp)
                                    <span class="badge bg-label-secondary" style="font-size: 11px;">
                                        {{ $mp->sekolah->nama ?? 'Sekolah tidak ditemukan' }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.pkks.mapping-pengawas.index') }}" class="btn btn-icon btn-sm btn-outline-primary">
                                <i class="bx bx-edit-alt"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Belum ada pengawas yang dipetakan ke sekolah.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
