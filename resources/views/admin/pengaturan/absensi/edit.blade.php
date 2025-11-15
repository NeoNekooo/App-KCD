@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Pengaturan Sistem /</span> Pengaturan Absensi
</h4>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.pengaturan.absensi.update') }}" method="POST">
            @csrf
            @method('PUT')

<ul class="nav nav-tabs" id="myTab" role="tablist">
    @foreach ($pengaturan as $item)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                    id="tab-{{ $item->hari }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#content-{{ $item->hari }}" 
                    type="button" 
                    role="tab" 
                    aria-controls="content-{{ $item->hari }}" 
                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                <i class='bx bxs-calendar-edit me-1'></i> Hari {{ $item->hari }}
            </button>
        </li>
    @endforeach
</ul>

<div class="tab-content card" id="myTabContent">
    @foreach ($pengaturan as $item)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
             id="content-{{ $item->hari }}" 
             role="tabpanel" 
             aria-labelledby="tab-{{ $item->hari }}">
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="jam_masuk_{{ $item->id }}" class="form-label">Jam Masuk Sekolah</label>
                        <input class="form-control" type="time" name="pengaturan[{{ $item->id }}][jam_masuk_sekolah]" value="{{ \Carbon\Carbon::parse($item->jam_masuk_sekolah)->format('H:i') }}" id="jam_masuk_{{ $item->id }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="jam_pulang_{{ $item->id }}" class="form-label">Jam Pulang Sekolah</label>
                        <input class="form-control" type="time" name="pengaturan[{{ $item->id }}][jam_pulang_sekolah]" value="{{ \Carbon\Carbon::parse($item->jam_pulang_sekolah)->format('H:i') }}" id="jam_pulang_{{ $item->id }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="toleransi_{{ $item->id }}" class="form-label">Batas Toleransi Terlambat (Menit)</label>
                        <input class="form-control" type="number" name="pengaturan[{{ $item->id }}][batas_toleransi_terlambat]" value="{{ $item->batas_toleransi_terlambat }}" id="toleransi_{{ $item->id }}">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch pb-1">
                            <input class="form-check-input" type="checkbox" name="pengaturan[{{ $item->id }}][is_active]" id="is_active_{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active_{{ $item->id }}">Absensi Aktif</label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endforeach
</div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection