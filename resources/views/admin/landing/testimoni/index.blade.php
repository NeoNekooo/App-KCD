@extends('layouts.admin') {{-- Perbaikan disini --}}

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Landing /</span> Manajemen Testimoni
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Testimoni Alumni</h5>
        </div>
        
        {{-- Alert Sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible mx-4 mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Alumni</th>
                        <th width="20%">Status & Instansi</th>
                        <th width="35%">Pesan Testimoni</th>
                        <th width="10%">Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($testimonis as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        
                        {{-- Kolom Alumni --}}
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ $item->siswa->nama ?? $item->nama }}</span>
                                <small class="text-muted">{{ $item->siswa->nisn ?? 'NISN Tidak Ada' }}</small>
                            </div>
                        </td>

                        {{-- Kolom Kegiatan --}}
                        <td>
                            <span class="badge {{ $item->status_kegiatan == 'Bekerja' ? 'bg-label-primary' : ($item->status_kegiatan == 'Kuliah' ? 'bg-label-info' : 'bg-label-secondary') }} me-1">
                                {{ $item->status_kegiatan }}
                            </span>
                            <div class="small mt-1 text-wrap" style="max-width: 200px;">
                                {{ $item->nama_instansi }}
                            </div>
                        </td>

                        {{-- Kolom Pesan --}}
                        <td>
                            <div class="text-wrap fst-italic text-secondary" style="min-width: 250px; max-width: 400px; line-height: 1.5;">
                                "{{ Str::limit($item->pesan, 150) }}"
                            </div>
                        </td>

                        {{-- Kolom Status --}}
                        <td>
                            @if($item->status == 'Approved')
                                <span class="badge bg-success">Tayang</span>
                            @elseif($item->status == 'Rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-warning">Menunggu</span>
                            @endif
                        </td>

                        {{-- Kolom Aksi --}}
                        <td>
                            <div class="d-flex gap-2">
                                
                                {{-- Tombol Approve --}}
                                @if($item->status != 'Approved')
                                <form action="{{ route('admin.landing.testimoni.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="Approved">
                                    <button type="submit" class="btn btn-sm btn-icon btn-success" title="Publish">
                                        <i class="bx bx-check"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Tombol Reject --}}
                                @if($item->status != 'Rejected')
                                <form action="{{ route('admin.landing.testimoni.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="Rejected">
                                    <button type="submit" class="btn btn-sm btn-icon btn-warning" title="Tolak">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.landing.testimoni.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class='bx bx-message-square-x text-muted' style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">Belum ada data testimoni masuk.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection