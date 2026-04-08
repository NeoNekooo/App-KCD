@extends('layouts.admin')

@section('title', 'Manajemen Slider')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Website /</span> Slider Beranda
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Slider</h5>
            <a href="{{ route('admin.website.sliders.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah Slider
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">Urutan</th>
                        <th>Preview</th>
                        <th>Informasi Konten</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($sliders as $slider)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-label-primary">{{ $slider->order }}</span>
                            </td>
                            <td>
                                <div style="width: 120px; height: 70px; border-radius: 8px; overflow: hidden; border: 1px solid #d9dee3;">
                                    <img src="{{ asset('storage/' . $slider->image) }}" alt="Slider Image" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </td>
                            <td>
                                <strong>{{ $slider->title }}</strong>
                                <div class="text-muted small mt-1">{{ $slider->subtitle ?? 'Tanpa subjudul' }}</div>
                            </td>
                            <td class="text-center">
                                @if($slider->is_active)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-danger">Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.website.sliders.edit', $slider) }}" class="btn btn-sm btn-icon btn-warning" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <form id="delete-form-{{ $slider->id }}" action="{{ route('admin.website.sliders.destroy', $slider) }}" method="POST" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDelete('{{ $slider->id }}')"
                                                class="btn btn-sm btn-icon btn-danger" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bx bx-images bx-lg mb-3"></i>
                                <p class="mb-0">Belum ada slider yang ditambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Slider?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#696cff',
                cancelButtonColor: '#ff3e1d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endpush
