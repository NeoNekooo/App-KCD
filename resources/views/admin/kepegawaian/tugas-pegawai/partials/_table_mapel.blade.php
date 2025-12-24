<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Mata Pelajaran (Ringkasan)</th>
                <th>Total Jam</th>
                <th>Nomor SK</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tugasPokok as $t)
            <tr>
                <td>{{ $loop->iteration + ($tugasPokok->currentPage() - 1) * $tugasPokok->perPage() }}</td>
                <td><strong>{{ Str::title(strtolower($t->gtk->nama)) }}</strong></td>
                <td class="text-wrap" style="max-width: 250px;">
                    {{ $t->details->where('jenis', 'pembelajaran')->pluck('tugas_pokok')->unique()->implode(', ') }}
                </td>
<td>
    <span class="badge bg-label-info">
        {{ $t->details->where('jenis', 'pembelajaran')->sum('jumlah_jam') }} Jam
    </span>
</td>                <td>
                    <div class="edit-sk-container" data-id="{{ $t->id }}">
                        <span class="sk-text cursor-pointer text-primary" onclick="toggleEditSk('{{ $t->id }}')">
                            {{ $t->nomor_sk ?? 'Klik isi SK...' }} <i class="bx bx-pencil small"></i>
                        </span>
                        <div class="sk-input-group d-none" id="input-group-{{ $t->id }}">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="input-{{ $t->id }}" value="{{ $t->nomor_sk }}" onkeyup="if(event.key === 'Enter') saveSk('{{ $t->id }}')">
                                <button class="btn btn-primary" onclick="saveSk('{{ $t->id }}')"><i class="bx bx-check"></i></button>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-icon btn-label-info" onclick="showDetail('{{ $t->id }}', '{{ $t->gtk->nama }}')" title="Lihat Rincian Kelas">
                        <i class="bx bx-show"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-label-primary" onclick="openCetakModal('{{ $t->id }}', '{{ $t->gtk->nama }}')" title="Cetak SK">
                        <i class="bx bx-printer"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3 ajax-pagination" data-target="mapel">
    {{ $tugasPokok->appends(['search_mapel' => request('search_mapel')])->links() }}
</div>
