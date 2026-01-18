<div class="modal fade" id="modalCek{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            @php
                $actionRoute = match ($item->status) {
                    'Verifikasi Kasubag' => route('admin.verifikasi.kasubag_process', $item->id),
                    'Verifikasi Kepala' => route('admin.verifikasi.kepala_process', $item->id),
                    default => route('admin.verifikasi.process', $item->id),
                };
            @endphp
            <form action="{{ $actionRoute }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-bottom text-start">
                    <div>
                        <h5 class="modal-title fw-bold">Pemeriksaan Berkas</h5>
                        <small class="text-muted">Status Saat Ini: <span class="fw-bold">{{ $item->status }}</span></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light text-start">
                    {{-- Detail Pengajuan --}}
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-3">
                            <h6 class="mb-1 fw-bold">{{ $item->judul }}</h6>
                            <p class="mb-0 small text-muted">Pemohon: {{ $item->nama_guru }} ({{ $item->nama_sekolah }})</p>
                        </div>
                    </div>

                    {{-- Khusus Tahap Kepala: Pilih Template --}}
                    @if ($item->status == 'Verifikasi Kepala')
                        <div class="card p-3 border border-primary bg-white mb-4">
                            <label class="form-label fw-bold text-primary mb-2">Pilih Format SK yang Akan Diterbitkan:</label>
                            <select name="template_id" class="form-select border-primary" required>
                                <option value="" selected disabled>-- Pilih Template Surat --</option>
                                @foreach ($templates as $tpl)
                                    <option value="{{ $tpl->id }}">{{ $tpl->judul_surat }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Tabel Dokumen --}}
                    <div class="table-responsive bg-white rounded border">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Nama Dokumen</th>
                                    <th class="text-center" style="width: 80px;">File</th>
                                    <th class="text-end pe-3" style="width: 120px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item->dokumen_syarat ?? [] as $doc)
                                    @php $uniq = $item->id . '_' . $loop->index; @endphp
                                    <tr>
                                        <td class="ps-3 align-middle">
                                            <div class="fw-semibold small">{{ $doc['nama'] }}</div>
                                            @if ($item->status == 'Verifikasi Berkas')
                                                <div id="note{{ $uniq }}" class="mt-1">
                                                    <input type="text" name="catatan[{{ $doc['id'] }}]" class="form-control form-control-sm text-danger" placeholder="Alasan jika berkas ini ditolak">
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ asset('storage/' . $doc['file']) }}" target="_blank" class="btn btn-icon btn-sm btn-label-secondary">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                        <td class="text-end pe-3 align-middle">
                                            @if ($item->status == 'Verifikasi Berkas')
                                                <div class="form-check form-switch d-flex justify-content-end">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="chk{{ $uniq }}" onchange="toggleCatatan('{{ $uniq }}')">
                                                </div>
                                            @else
                                                <span class="badge bg-label-success"><i class='bx bx-check'></i> OK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Catatan Internal untuk Kasubag --}}
                    @if ($item->status == 'Verifikasi Kasubag')
                        <div class="mt-3">
                            <label class="form-label fw-bold small text-muted">CATATAN UNTUK KEPALA (OPSIONAL)</label>
                            <textarea name="catatan_internal" class="form-control" rows="2" placeholder="Tulis instruksi atau catatan tambahan untuk Kepala..."></textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top bg-white justify-content-between">
                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger">
                        <i class="bx bx-undo me-1"></i> Kembalikan / Tolak
                    </button>
                    <button type="submit" name="action" value="approve" class="btn btn-success">
                        <i class="bx bx-check-circle me-1"></i> Setujui & Lanjutkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>