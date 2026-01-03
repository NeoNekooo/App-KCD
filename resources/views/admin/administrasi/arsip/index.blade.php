@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Arsip Surat Digital</h4>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-archive me-2"></i>Riwayat Surat Keluar</h5>
            
            {{-- Form Pencarian --}}
            <form action="{{ route('admin.administrasi.arsip-surat.index') }}" method="GET" class="d-flex mt-2 mt-md-0">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Cari Nomor / Nama..." value="{{ request('q') }}">
                    <button class="btn btn-primary" type="submit"><i class="bx bx-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal Dibuat</th>
                        <th>Nomor Surat</th>
                        <th>Kategori</th>
                        <th>Tujuan / Keterangan</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arsip as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($arsip->currentPage() - 1) * $arsip->perPage() }}</td>
                        <td>
                            <span class="d-block fw-bold text-dark">{{ date('d M Y', strtotime($item->tanggal_dibuat)) }}</span>
                            <small class="text-muted">{{ date('H:i', strtotime($item->tanggal_dibuat)) }} WIB</small>
                        </td>
                        <td>
                            <span class="badge bg-label-primary px-3">{{ $item->nomor_surat_final }}</span>
                        </td>
                        <td>
                            @if($item->kategori == 'siswa')
                                <span class="badge bg-info">Siswa</span>
                            @elseif($item->kategori == 'guru')
                                <span class="badge bg-warning">Guru</span>
                            @elseif($item->kategori == 'sk')
                                <span class="badge bg-success">SK Tugas</span>
                            @else
                                <span class="badge bg-secondary">{{ $item->kategori }}</span>
                            @endif
                        </td>
                        <td class="text-wrap" style="max-width: 300px;">
                            {{ $item->tujuan }}
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                {{-- TOMBOL CETAK ULANG --}}
                                <a href="{{ route('admin.administrasi.arsip-surat.cetak', $item->id) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-success" title="Cetak Ulang (Reprint)">
                                    <i class="bx bx-printer"></i>
                                </a>
                                
                                {{-- TOMBOL HAPUS LOG --}}
                                <form action="{{ route('admin.administrasi.arsip-surat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus arsip ini? Data nomor surat tidak akan kembali.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus Arsip"><i class="bx bx-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class='bx bx-file-blank display-4 mb-3'></i>
                            <p class="mb-0">Belum ada arsip surat yang tersimpan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-end">
                {{ $arsip->links() }}
            </div>
        </div>
    </div>
</div>
@endsection