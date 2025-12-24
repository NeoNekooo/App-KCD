@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Landing /</span> Agenda Sekolah</h4>
            <small class="text-muted">Kelola jadwal, kalender pendidikan, dan jadwal PPDB</small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bx bx-calendar-plus me-1"></i> Tambah Agenda
        </button>
    </div>

    {{-- Pesan Sukses / Error (Opsional jika pakai Layout bawaan sudah ada) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-list" aria-controls="navs-list" aria-selected="true">
                    <i class="bx bx-list-ul me-1"></i> List Data
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-calendar" aria-controls="navs-calendar" aria-selected="false" id="tabCalendarTrigger">
                    <i class="bx bx-calendar me-1"></i> Preview Kalender
                </button>
            </li>
        </ul>
        
        <div class="tab-content">
            
            {{-- TAB 1: LIST DATA --}}
            <div class="tab-pane fade show active" id="navs-list" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kegiatan</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($agendas as $item)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $item->tanggal_mulai->format('d M Y') }}</span>
                                        @if($item->tanggal_selesai && $item->tanggal_selesai != $item->tanggal_mulai)
                                            <small class="text-muted">s/d {{ $item->tanggal_selesai->format('d M Y') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $item->judul }}</strong>
                                    @if($item->deskripsi)
                                        <p class="text-muted small mb-0 text-truncate" style="max-width: 250px;">{{ $item->deskripsi }}</p>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($item->kategori) {
                                            'PPDB'     => 'bg-label-info', // Biru terang untuk PPDB
                                            'Akademik' => 'bg-label-primary',
                                            'Libur'    => 'bg-label-danger',
                                            'Kegiatan' => 'bg-label-success',
                                            'Rapat'    => 'bg-label-warning',
                                            default    => 'bg-label-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $item->kategori }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" 
                                                class="btn btn-sm btn-icon btn-warning btn-edit-action"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit"
                                                data-id="{{ $item->id }}"
                                                data-judul="{{ $item->judul }}"
                                                data-mulai="{{ $item->tanggal_mulai->format('Y-m-d') }}"
                                                data-selesai="{{ $item->tanggal_selesai ? $item->tanggal_selesai->format('Y-m-d') : '' }}"
                                                data-kategori="{{ $item->kategori }}"
                                                data-deskripsi="{{ $item->deskripsi }}">
                                            <i class="bx bx-pencil"></i>
                                        </button>

                                        <form action="{{ route('admin.landing.agenda.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bx bx-calendar-x fs-1 text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada agenda kegiatan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $agendas->links() }}
                </div>
            </div>

            {{-- TAB 2: CALENDAR PREVIEW --}}
            <div class="tab-pane fade" id="navs-calendar" role="tabpanel">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bx bx-info-circle me-2"></i>
                    <div>
                        Klik pada tanggal agenda untuk melihat detailnya. Agenda <strong>PPDB</strong> akan otomatis membuka pendaftaran di website.
                    </div>
                </div>
                <div id='calendar'></div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL CREATE --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Agenda Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.landing.agenda.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan / Agenda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul" placeholder="Contoh: Gelombang 1 PPDB" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_mulai" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tanggal_selesai">
                            <div class="form-text">Isi jika lebih dari 1 hari.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" name="kategori" required>
                            <option value="" disabled selected>- Pilih Kategori -</option>
                            <option value="PPDB" class="fw-bold text-primary">★ JADWAL PPDB (Penting)</option>
                            <option value="Akademik">Akademik (Ujian/Rapor)</option>
                            <option value="Kegiatan">Kegiatan Sekolah</option>
                            <option value="Libur">Libur Nasional/Cuti</option>
                            <option value="Rapat">Rapat Guru/Ortu</option>
                        </select>
                        <div class="form-text text-primary small">
                            <i class='bx bx-info-circle'></i> Pilih <strong>PPDB</strong> untuk membuka pendaftaran online sesuai tanggal ini.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea class="form-control" name="deskripsi" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Agenda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan / Agenda</label>
                        <input type="text" class="form-control" id="editJudul" name="judul" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="editMulai" name="tanggal_mulai" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="editSelesai" name="tanggal_selesai">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" id="editKategori" name="kategori" required>
                            <option value="PPDB" class="fw-bold text-primary">★ JADWAL PPDB</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Kegiatan">Kegiatan</option>
                            <option value="Libur">Libur</option>
                            <option value="Rapat">Rapat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
<style>
    #calendar { max-width: 100%; margin: 0 auto; font-family: 'Public Sans', sans-serif; }
    .fc-event { cursor: pointer; border: none; padding: 2px 4px; }
    .fc-daygrid-day-number, .fc-col-header-cell-cushion { color: #566a7f; text-decoration: none; }
    .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 600; color: #566a7f; }
    .fc-button-primary { background-color: #696cff !important; border-color: #696cff !important; }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Script Modal Edit
        const editButtons = document.querySelectorAll('.btn-edit-action');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById('editJudul').value = this.dataset.judul;
                document.getElementById('editMulai').value = this.dataset.mulai;
                document.getElementById('editSelesai').value = this.dataset.selesai;
                document.getElementById('editKategori').value = this.dataset.kategori;
                document.getElementById('editDeskripsi').value = this.dataset.deskripsi;

                let updateUrl = "{{ route('admin.landing.agenda.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                document.getElementById('formEdit').action = updateUrl;
            });
        });

        // 2. Script Render Kalender
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            buttonText: { today: 'Hari Ini', month: 'Bulan', list: 'List' },
            events: [
                @foreach($agendas as $agenda)
                {
                    title: '{{ $agenda->judul }}',
                    start: '{{ $agenda->tanggal_mulai->format("Y-m-d") }}',
                    // FullCalendar end date is exclusive, so we add 1 day for multi-day events
                    end: '{{ $agenda->tanggal_selesai ? \Carbon\Carbon::parse($agenda->tanggal_selesai)->addDay()->format("Y-m-d") : $agenda->tanggal_mulai->format("Y-m-d") }}',
                    
                    // Warna event berdasarkan kategori
                    backgroundColor: 
                        @if($agenda->kategori == 'PPDB') '#03c3ec' // Cyan/Info
                        @elseif($agenda->kategori == 'Libur') '#ff3e1d' 
                        @elseif($agenda->kategori == 'Akademik') '#696cff' 
                        @elseif($agenda->kategori == 'Kegiatan') '#71dd37' 
                        @else '#ffab00' @endif,
                    
                    extendedProps: { deskripsi: '{{ $agenda->deskripsi }}' }
                },
                @endforeach
            ],
            eventClick: function(info) {
                alert('Kegiatan: ' + info.event.title + '\nKeterangan: ' + info.event.extendedProps.deskripsi);
            }
        });

        // Re-render calendar when tab is shown
        var tabCalendarTrigger = document.getElementById('tabCalendarTrigger')
        tabCalendarTrigger.addEventListener('shown.bs.tab', function (event) {
            calendar.render();
        })
    });
</script>
@endpush

@endsection