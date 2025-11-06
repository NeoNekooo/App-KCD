@extends('layouts.admin') {{-- Anda bisa gunakan layout yang sama atau buat layout khusus guru --}}

@section('content')
<div class="card">
    <h5 class="card-header">Jadwal Mengajar Anda Hari Ini ({{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }})</h5>
    <div class="card-body">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($jadwalHariIni->isEmpty())
            <div class="alert alert-info">
                Tidak ada jadwal mengajar untuk Anda hari ini.
            </div>
        @else
            <div class="list-group">
                @foreach ($jadwalHariIni as $jadwal)
                    <a href="{{ route('guru.absensi.show', $jadwal->id) }}" 
                       class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $jadwal->mata_pelajaran }}</h5>
                            <small>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</small>
                        </div>
                        <p class="mb-1">
                            Kelas: <strong>{{ $jadwal->rombel->nama }}</strong>
                        </p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
