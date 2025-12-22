<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Jabatan</th>
                <th>Total Jam</th>
                <th>Nomor SK</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jabatanStruktural as $j)
            <tr>
                <td>{{ $loop->iteration + ($jabatanStruktural->currentPage() - 1) * $jabatanStruktural->perPage() }}</td>
                <td><strong>{{ Str::title(strtolower($j->gtk->nama)) }}</strong></td>
                <td class="text-wrap" style="max-width: 300px;">{{ $j->tugas_pokok }}</td>
                <td><span class="badge bg-label-secondary">{{ $j->jumlah_jam }} Jam</span></td>
                <td>
                    <div class="edit-sk-container" data-id="{{ $j->id }}">
                        <span class="sk-text cursor-pointer text-primary small" onclick="toggleEditSk('{{ $j->id }}')">
                            {{ $j->nomor_sk ?? 'Isi SK...' }} <i class="bx bx-pencil small"></i>
                        </span>
                        <div class="sk-input-group d-none" id="input-group-{{ $j->id }}">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="input-{{ $j->id }}"
                                       value="{{ $j->nomor_sk }}"
                                       onkeyup="if(event.keyCode === 13) saveSk('{{ $j->id }}')">
                                <button class="btn btn-primary" onclick="saveSk('{{ $j->id }}')"><i class="bx bx-check"></i></button>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex">
                        <button class="btn btn-sm btn-icon btn-label-warning me-1" onclick="editStruktural('{{ $j->id }}', '{{ $j->gtk->nama }}', '{{ $j->tugas_pokok }}', '{{ $j->jumlah_jam }}', '{{ $j->nomor_sk }}')"><i class="bx bx-edit"></i></button>
                        <button class="btn btn-sm btn-icon btn-label-primary me-1" onclick="openCetakModal('{{ $j->id }}', '{{ $j->gtk->nama }}')"><i class="bx bx-printer"></i></button>
                        <form action="{{ route('admin.kepegawaian.tugas-pegawai.destroy', $j->id) }}" method="POST" onsubmit="confirmDelete(event, this)">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-sm btn-icon btn-label-danger">
        <i class="bx bx-trash"></i>
    </button>
</form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3 ajax-pagination" data-target="struktural">
    {{ $jabatanStruktural->appends(['search_struktural' => request('search_struktural')])->links() }}
</div>
