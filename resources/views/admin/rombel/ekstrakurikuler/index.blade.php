@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
<span class="text-muted fw-light">Rombongan Belajar /</span> Ekstrakurikuler
</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Data Ekstrakurikuler</h5>
            <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                <a href="{{ route('admin.rombel.ekstrakurikuler.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Tambah</a>
                <button type="button" class="btn btn-secondary"><i class="bx bx-pencil me-1"></i> Ubah</button>
                <button type="button" class="btn btn-success"><i class="bx bx-save me-1"></i> Simpan</button>
                <button type="button" class="btn btn-danger"><i class="bx bx-trash me-1"></i> Hapus</button>
            </div>
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
                @forelse ($ekskul as $item)
                    <tr>
                        <td>
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $item->id }}">
                        </td>
                        <td><strong>{{ $item->nama_ekskul }}</strong></td>
                        {{-- Mengambil nama pembina dari relasi 'pembina' --}}
                        <td>{{ $item->pembina->nama ?? 'Belum ada pembina' }}</td>
                        <td>{{ $item->prasarana }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data ekstrakurikuler.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="card-footer">
        {{ $ekskul->links() }}
    </div>
</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
const selectAllCheckbox = document.getElementById('selectAll');
const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAllCheckbox.addEventListener(&#39;change&#39;, function () {
        rowCheckboxes.forEach(checkbox =&gt; {
            checkbox.checked = this.checked;
        });
    });
});

</script>

@endpush