{{-- resources/views/admin/keuangan/master_kas/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Master Kas/Bank')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Keuangan / Master /</span> Kas/Bank
    </h4>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <span class="alert-icon text-success me-2"><i class="ti ti-check ti-xs"></i></span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <span class="alert-icon text-danger me-2"><i class="ti ti-ban ti-xs"></i></span>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Kas dan Bank</h5>
            <a href="{{ route('bendahara.keuangan.kas-master.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Tambah Kas/Bank
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kas/Bank</th>
                            <th>Saldo Awal</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($masterKas as $kas)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $kas->nama_kas }}</td>
                                <td>{{ 'Rp ' . number_format($kas->saldo_awal, 0, ',', '.') }}</td>
                                <td>
                                    @if ($kas->is_active)
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge bg-label-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Tombol Edit (Pastikan Anda sudah membuat route & method edit/update) --}}
                                    <a href="{{ route('bendahara.keuangan.kas-master.edit', $kas->id) }}" class="btn btn-sm btn-icon btn-warning" title="Edit">
  <i class="menu-icon tf-icons bx bx-pencil" aria-hidden="true"></i>

                                    </a>

                                    {{-- Tombol Hapus (Pastikan Anda sudah membuat method destroy) --}}
                                    <form action="{{ route('bendahara.keuangan.kas-master.destroy', $kas->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Kas/Bank ini? Semua mutasi terkait akan terpengaruh.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                                                                    <i class="menu-icon tf-icons bx bx-trash" aria-hidden="true"></i>

                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data Kas/Bank yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
