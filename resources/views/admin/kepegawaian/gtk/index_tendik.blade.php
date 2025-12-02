@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepegawaian /</span> Data Tenaga Kependidikan</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Daftar Tenaga Kependidikan</h5>
            <div class="d-flex gap-2">
                {{-- Tombol Lihat Data --}}
                <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm" style="display: none;">
                    <i class="bx bx-show-alt me-1"></i> Lihat Data
                </a>

                {{-- Tombol Opsi Export --}}
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-export me-1"></i> Opsi Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.kepegawaian.tendik.export.excel', request()->query()) }}">Export Semua</a></li>
                        <li><a class="dropdown-item disabled" href="javascript:void(0);" id="exportSelectedLink">Export yang Dipilih</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.kepegawaian.tendik.index') }}" method="GET">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama, NIP, atau NIK..." value="{{ request('search') }}">
            </div>
        </form>
    </div>
    
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="1%"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                    <th>Induk</th>
                    <th>Nama Tendik</th>
                    <th>NIK</th>
                    <th>L/P</th>
                    <th>Tgl Lahir</th>
                    <th>Status</th>
                    <th>Jenis GTK</th>
                    <th>Jabatan</th>
                    <th>NUPTK</th>
                    <th>Tgl Surat Tugas</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($tendiks as $gtk)
                <tr>
                    <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $gtk->id }}"></td>
                    <td><span class="badge bg-label-secondary">{{ $gtk->ptk_induk == 1 ? 'Induk' : 'Non-Induk' }}</span></td>
                    
                    {{-- KOLOM NAMA (LOGIKA DIPERBAIKI SAMAKAN DENGAN GURU) --}}
                    <td style="min-width: 250px;">
                        <div class="d-flex justify-content-start align-items-center">
                            {{-- Container Avatar --}}
                            <div class="avatar-wrapper me-3">
                                <div class="avatar avatar-sm">
                                    @if(!empty($gtk->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists($gtk->foto))
                                        {{-- Jika ada foto profil --}}
                                        <img src="{{ asset('storage/' . $gtk->foto) }}" 
                                             alt="Avatar" 
                                             class="rounded-circle" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        {{-- Jika tidak ada foto, pakai Inisial Nama (UI Avatars) --}}
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($gtk->nama) }}&background=random&color=ffffff&size=100" 
                                             alt="Avatar Default" 
                                             class="rounded-circle" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Container Teks --}}
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-truncate text-body">{{ $gtk->nama }}</span>
                                <small class="text-muted">
                                    {{-- LOGIKA SUBTITLE CERDAS (Ignore Strip) --}}
                                    @if(!empty($gtk->nip) && trim($gtk->nip) != '-')
                                        NIP: {{ $gtk->nip }}
                                    @elseif(!empty($gtk->nuptk) && trim($gtk->nuptk) != '-')
                                        NUPTK: {{ $gtk->nuptk }}
                                    @else
                                        -
                                    @endif
                                </small>
                            </div>
                        </div>
                    </td>
                    {{-- END KOLOM NAMA --}}

                    <td>{{ $gtk->nik ?? '-' }}</td>
                    
                    {{-- PERBAIKAN LOGIKA JENIS KELAMIN --}}
                    <td>{{ ($gtk->jenis_kelamin == 'L' || $gtk->jenis_kelamin == 'Laki-laki') ? 'L' : 'P' }}</td>

                    <td>{{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->format('d-m-Y') : '-' }}</td>
                    <td><span class="badge bg-label-primary">{{ $gtk->status_kepegawaian_id_str ?? '-' }}</span></td>
                    <td>{{ $gtk->jenis_ptk_id_str ?? '-' }}</td>
                    <td>{{ $gtk->jabatan_ptk_id_str ?? '-' }}</td>
                    <td>{{ $gtk->nuptk ?? '-' }}</td>
                    <td>{{ $gtk->tanggal_surat_tugas ? \Carbon\Carbon::parse($gtk->tanggal_surat_tugas)->format('d-m-Y') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="bx bx-user-x bx-lg text-muted mb-2"></i>
                            <h6 class="text-muted">Tidak ada data tendik ditemukan.</h6>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tendiks->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $tendiks->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const viewSelectedBtn = document.getElementById('viewSelectedBtn');
        const exportSelectedLink = document.getElementById('exportSelectedLink');

        function handleCheckboxChange() {
            const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            
            if (checkedCheckboxes.length > 0) {
                viewSelectedBtn.style.display = 'inline-block';
                exportSelectedLink.classList.remove('disabled');
            } else {
                viewSelectedBtn.style.display = 'none';
                exportSelectedLink.classList.add('disabled');
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                handleCheckboxChange();
            });
        }

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', handleCheckboxChange);
        });

        if (viewSelectedBtn) {
            viewSelectedBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
                
                if (checkedCheckboxes.length > 0) {
                    const selectedIds = Array.from(checkedCheckboxes)
                                             .map(cb => cb.value)
                                             .join(',');
                    
                    let url = `{{ route('admin.kepegawaian.gtk.show-multiple') }}?ids=${selectedIds}`;
                    window.location.href = url;
                }
            });
        }

        if (exportSelectedLink) {
            exportSelectedLink.addEventListener('click', function(e) {
                if (this.classList.contains('disabled')) {
                    e.preventDefault();
                    return;
                }
                e.preventDefault();
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                                          .map(cb => cb.value)
                                          .join(',');

                if (selectedIds) {
                    let url = `{{ route('admin.kepegawaian.tendik.export.excel') }}?ids=${selectedIds}`;
                    const otherParams = new URLSearchParams(window.location.search);
                    otherParams.delete('ids'); 
                    
                    if (otherParams.toString()) {
                        url += `&${otherParams.toString()}`;
                    }
                    
                    window.location.href = url;
                }
            });
        }

        handleCheckboxChange();
    });
</script>
@endpush