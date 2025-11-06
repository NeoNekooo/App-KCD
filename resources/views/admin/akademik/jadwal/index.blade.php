@extends('layouts.admin')

@section('content')
<div class="card">
    <h5 class="card-header">Manajemen Jadwal Pelajaran</h5>
    <div class="card-body">
        <p>Silakan pilih rombongan belajar untuk melihat atau mengatur jadwal pelajaran.</p>
        <div class="list-group">
            @foreach ($rombels as $rombel)
                <a href="{{ route('admin.akademik.jadwal.edit', $rombel->id) }}" class="list-group-item list-group-item-action">
                    {{ $rombel->nama }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
