@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Formulir Absensi Manual Guru & GTK</h5>
            <small>Silakan pilih tanggal dan tandai status kehadiran.</small>
        </div>
    </div>
    
    <div class="card-body">
        
        {{-- Form Filter Tanggal --}}
        <form action="{{ route('admin.absensi.gtk.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="tanggal" class="form-label">Pilih Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" 
                           value="{{ $tanggal }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </div>
        </form>

        <hr>

        {{-- Form Input Absensi --}}
        <form action="{{ route('admin.absensi.gtk.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            
            <div class="d-flex justify-content-between mb-3">
                <strong>Daftar Guru & GTK ({{ $gtks->count() }} orang)</strong>
                <div>
                    <button type="button" class="btn btn-sm btn-success btn-set-all" data-status="Hadir">Set Semua Hadir</button>
                    <button type="button" class="btn btn-sm btn-danger btn-set-all" data-status="Alfa">Set Semua Alfa</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Guru / GTK</th>
                            <th class="text-center" style="width: 50%">Status Kehadiran</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gtks as $index => $gtk)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $gtk->nama }}</td>
                            <td>
                                @php
                                    $absensiRecord = $absensiRecords->get($gtk->id);
                                    $currentStatus = optional($absensiRecord)->status;
                                @endphp
                                
                                <div class="d-flex justify-content-around flex-wrap">
                                    @foreach ($statusOptions as $status)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" 
                                               name="absensi[{{ $gtk->id }}][status]" 
                                               id="status-{{ $gtk->id }}-{{ $status }}" 
                                               value="{{ $status }}" 
                                               @if ($currentStatus == $status) checked @endif>
                                        <label class="form-check-label" for="status-{{ $gtk->id }}-{{ $status }}">{{ $status }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                {{-- Tampilkan info scan jika ada --}}
                                @if ($absensiRecord && $absensiRecord->jam_masuk)
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        Scan Masuk: <strong>{{ \Carbon\Carbon::parse($absensiRecord->jam_masuk)->format('H:i') }}</strong>
                                        @if($absensiRecord->status_kehadiran == 'Terlambat')
                                            <span class="badge bg-label-warning ms-1">Terlambat</span>
                                        @endif
                                    </small>
                                </div>
                                @endif

                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" 
                                       name="absensi[{{ $gtk->id }}][keterangan]" 
                                       placeholder="Keterangan..."
                                       value="{{ optional($absensiRecord)->keterangan }}">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data GTK.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan Absensi GTK</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk Set Semua Status
    document.querySelectorAll('.btn-set-all').forEach(button => {
        button.addEventListener('click', function() {
            const statusToSet = this.getAttribute('data-status');
            document.querySelectorAll('.form-check-input[value="' + statusToSet + '"]').forEach(radio => {
                radio.checked = true;
            });
        });
    });
});
</script>
@endpush