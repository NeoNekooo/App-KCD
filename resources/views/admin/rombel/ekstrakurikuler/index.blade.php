@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Rombongan Belajar /</span> Ekstrakurikuler
</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Ekstrakurikuler</h5>
            
            {{-- Tombol-tombol aksi sudah dihapus --}}
            {{-- <small class="text-muted">Menampilkan data ekstrakurikuler dari server.</small> --}}
        </div>
    </div>
    
    <div class="table-responsive text-nowrap"> 
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 1%;"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                    <th>Nama Ekskul</th>
                    <th>Pembina</th>
                    <th>Prasarana</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                {{-- Variabel $ekskul dari compact('ekskul') di controller --}}
                @forelse ($ekskul as $item)
                    <tr>
                        <td>
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $item->id }}">
                        </td>
                        <td><strong>{{ $item->nama_ekskul }}</strong></td>
                        
                        {{-- Mengambil nama pembina dari relasi 'pembina' (hasil dari with('pembina')) --}}
                        {{-- ?? 'Belum ada pembina' digunakan jika relasi pembina null (database-nya NULL) --}}
                        <td>{{ $item->pembina->nama ?? 'Belum ada pembina' }}</td>
                        
                        <td>{{ $item->prasarana ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data ekstrakurikuler.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Paginasi --}}
    <div class="card-footer">
        {{ $ekskul->links() }}
    </div>
</div>

@endsection

@push('scripts')
{{-- Script untuk fungsionalitas checkbox "select all" --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAllCheckbox.addEventListener('change', function () {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});
</script>
@endpush
