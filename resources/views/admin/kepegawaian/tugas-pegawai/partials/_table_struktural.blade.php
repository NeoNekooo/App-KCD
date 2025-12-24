<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Jabatan</th>
                <th>Jam</th>
                <th>Nomor SK</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jabatanStruktural as $j)
            @php
                // Escape data agar aman untuk JavaScript
                $safeNama = addslashes(Str::title(strtolower($j->parent->gtk->nama)));
                $safeTugas = addslashes($j->tugas_pokok);
                $safeSk = addslashes($j->parent->nomor_sk ?? '');
            @endphp
            <tr>
                <td>{{ $loop->iteration + ($jabatanStruktural->currentPage() - 1) * $jabatanStruktural->perPage() }}</td>
                <td><strong>{{ Str::title(strtolower($j->parent->gtk->nama)) }}</strong></td>
                <td class="text-wrap" style="max-width: 300px;">{{ $j->tugas_pokok }}</td>
                <td><span class="badge bg-label-secondary">{{ $j->jumlah_jam }} Jam</span></td>
                <td>
                    <div class="edit-sk-container" data-id="{{ $j->tugas_pegawai_id }}">
                        <span class="sk-text cursor-pointer text-primary small" onclick="toggleEditSk('{{ $j->tugas_pegawai_id }}')">
                            {{ $j->parent->nomor_sk ?? 'Isi SK...' }} <i class="bx bx-pencil small"></i>
                        </span>
                        <div class="sk-input-group d-none" id="input-group-{{ $j->tugas_pegawai_id }}">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="input-{{ $j->tugas_pegawai_id }}"
                                       value="{{ $j->parent->nomor_sk }}"
                                       onkeyup="if(event.key === 'Enter') saveSk('{{ $j->tugas_pegawai_id }}')">
                                <button class="btn btn-primary" onclick="saveSk('{{ $j->tugas_pegawai_id }}')"><i class="bx bx-check"></i></button>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex">
                        {{-- Tombol Edit menggunakan data yang sudah di-escape --}}
                        <button class="btn btn-sm btn-icon btn-label-warning me-1"
                                onclick="editStruktural('{{ $j->id }}', '{{ $safeNama }}', '{{ $safeTugas }}', '{{ $j->jumlah_jam }}', '{{ $safeSk }}')">
                            <i class="bx bx-edit"></i>
                        </button>

                        {{-- Cetak menggunakan ID Header ($j->tugas_pegawai_id) --}}
                        <button class="btn btn-sm btn-icon btn-label-primary me-1"
                                onclick="openCetakModal('{{ $j->tugas_pegawai_id }}', '{{ $safeNama }}')">
                            <i class="bx bx-printer"></i>
                        </button>

                        <button type="button" class="btn btn-sm btn-icon btn-label-danger" onclick="confirmDeleteDetail('{{ $j->id }}')">
    <i class="bx bx-trash"></i>
</button>

{{-- Form disembunyikan, akan di-submit via JS --}}
<form id="form-delete-{{ $j->id }}" action="{{ route('admin.kepegawaian.tugas-pegawai.destroy-detail', $j->id) }}" method="POST" style="display:none;">
    @csrf @method('DELETE')
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
