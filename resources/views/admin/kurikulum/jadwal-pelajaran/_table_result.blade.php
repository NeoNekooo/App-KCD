@if($rombels->isEmpty())
    <div class="d-flex flex-column align-items-center justify-content-center mt-5 p-5 text-center text-muted">
        <div class="bg-light rounded-circle p-4 mb-3">
            <i class='bx bx-search-alt-2' style="font-size: 3rem; color: #cbd5e1;"></i>
        </div>
        <h5>Belum ada kelas yang dipilih</h5>
        <p class="small">Silakan pilih kelas pada filter di atas.</p>
    </div>
@else
    <div class="nav-align-top mb-4">
        {{-- TABS DENGAN STYLE 'FLOATING' --}}
       <ul class="nav nav-pills nav-tabs-floating bg-light p-2 rounded" role="tablist">
        @foreach($rombels as $index => $rombel)
            <li class="nav-item">
                <button type="button" class="nav-link {{ $index == 0 ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#tab-{{ $rombel->id }}">
                    <i class="bx bx-chalkboard"></i> <span>{{ $rombel->nama }}</span>
                </button>
            </li>
        @endforeach
       </ul>

        <div class="tab-content shadow-sm p-0 bg-white" style="border-radius: 12px; border: 1px solid #dfe3e7;">
            @foreach($rombels as $index => $rombel)
                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="tab-{{ $rombel->id }}" role="tabpanel">

                    {{-- INFO KELAS HEADER --}}
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-50">
                        <div>
                            <h4 class="text-primary fw-bold mb-0 d-flex align-items-center">
                                <span class="badge bg-primary me-2 rounded-2">{{ $rombel->nama }}</span>
                            </h4>
                        </div>
                        <div class="d-flex align-items-center bg-white px-3 py-1 rounded shadow-sm border text-secondary">
                            <i class="bx bx-user-circle fs-4 me-2 text-primary"></i>
                            <div>
                                <small class="d-block text-muted" style="font-size: 10px;">WALI KELAS</small>
                                <span class="fw-bold text-dark">{{ $rombel->waliKelas->nama ?? 'Belum ditentukan' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL JADWAL --}}
                    <div class="table-container-scroll">
                        <table class="table table-bordered table-jadwal text-center mb-0 align-middle table-hover">
                            <thead>
                                <tr>
                                    <th class="col-sticky-1 py-3 font-monospace small">JAM</th>
                                    <th class="col-sticky-2 py-3 font-monospace small">WAKTU</th>
                                    @foreach($days as $hari)
                                        <th class="py-3 text-uppercase text-dark">{{ strtoupper($hari) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($masterJam as $jam)
                                <tr>
                                    <td class="col-sticky-1 fw-bold text-secondary bg-light">{{ $jam->urutan }}</td>
                                    <td class="col-sticky-2 text-muted small bg-light">
                                        <div class="d-flex flex-column">
                                            <span>{{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }}</span>
                                            <span class="text-light-gray" style="font-size: 8px; border-top: 1px solid #ddd;">s/d</span>
                                            <span>{{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    @foreach($days as $hari)
                                        @php $data = $jadwalGrouped[$rombel->id][$hari][$jam->urutan] ?? null; @endphp

                                        {{-- LOGIC WARNA BACKGROUND CELL KOSONG --}}
                                        <td class="p-1 {{ !$data && $jam->tipe == 'kbm' ? 'td-empty' : '' }}">
                                            @if($data)
                                                @if($data->jamPelajaran->tipe != 'kbm')
                                                    {{-- [PERBAIKAN] Logic Badge Warna-Warni --}}
                                                    @php
                                                        $badgeClass = match($data->jamPelajaran->tipe) {
                                                            'upacara' => 'bg-primary text-white',
                                                            'keagamaan' => 'bg-success text-white',
                                                            'literasi' => 'bg-info text-white',
                                                            'wali_kelas' => 'bg-info text-dark',
                                                            'istirahat' => 'bg-warning text-dark',
                                                            default => 'bg-secondary text-white'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} w-100 py-2">{{ strtoupper($data->jamPelajaran->nama) }}</span>

                                                @elseif($data->pembelajaran)
                                                    @php
                                                        $namaMapel = $data->pembelajaran->nama_mata_pelajaran;
                                                        $hash = crc32($namaMapel);
                                                        $bgClass = 'bg-soft-' . (abs($hash) % 10);
                                                        $kodeGuru = $data->pembelajaran->guru->kode ?? $data->pembelajaran->guru->id ?? '?';
                                                        $namaGuru = $data->pembelajaran->guru->nama ?? 'Belum ada guru';
                                                    @endphp

                                                    <div class="mapel-card {{ $bgClass }}"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-html="true"
                                                         title="<div class='text-center fw-bold'>{{ $namaMapel }}</div><small>{{ $namaGuru }}</small>">
                                                        <div class="fw-bold text-dark text-truncate" style="max-width: 140px; font-size: 0.85rem;">
                                                            {{ $controller->helperSingkatan($namaMapel) }}<span class="text-secondary ms-1 small">({{ $kodeGuru }})</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                {{-- SLOT KOSONG TAPI BUKAN KBM --}}
                                                @if($jam->tipe != 'kbm')
                                                    @php
                                                        $badgeClass = match($jam->tipe) {
                                                            'upacara' => 'bg-primary text-white',
                                                            'keagamaan' => 'bg-success text-white',
                                                            'literasi' => 'bg-info text-white',
                                                            'wali_kelas' => 'bg-info text-dark',
                                                            'istirahat' => 'bg-warning text-dark',
                                                            default => 'bg-secondary text-white'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} w-100 py-2">{{ strtoupper($jam->nama) }}</span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- LEGEND GURU --}}
    <div class="card mt-4 border-start border-warning border-4 shadow-sm">
        <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center cursor-pointer"
             data-bs-toggle="collapse" data-bs-target="#collapseLegend">
            <span class="fw-bold text-uppercase text-dark small">
                <i class="bx bx-id-card me-2"></i> Daftar Kode & Nama Guru
            </span>
            <i class="bx bx-chevron-down"></i>
        </div>
        <div class="collapse show" id="collapseLegend">
            <div class="card-body p-3 bg-white">
                <div class="row g-2">
                    @foreach($listGuru as $g)
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <span class="badge bg-warning text-dark me-2 shadow-sm" style="min-width: 30px; text-align: center;">
                                    {{ $g->kode ?? $g->id }}
                                </span>
                                <span class="text-truncate small fw-bold" title="{{ $g->nama }}">
                                    {{ $g->nama }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
