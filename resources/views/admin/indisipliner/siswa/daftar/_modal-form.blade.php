<div class="modal fade" id="modalInputPelanggaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Formulir Pengisian Pelanggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.indisipliner.siswa.daftar.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="IDtapel" class="form-label">Tahun Pelajaran</label>
                                <select name="IDtapel" id="IDtapel" class="form-select">
                                    @foreach ($tapels as $tapel)
                                        <option value="{{ $tapel->id }}" {{ $tapel->is_active ? 'selected' : '' }}>{{ $tapel->tahun_pelajaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="IDsemester" class="form-label">Semester</label>
                                <select name="IDsemester" id="IDsemester" class="form-select">
                                     @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}">{{ $semester->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                             <div class="mb-3">
                                <label for="jam" class="form-label">Waktu</label>
                                <input type="time" name="jam" id="jam" class="form-control" value="{{ date('H:i') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="IDkelas" class="form-label">Kelas</label>
                                <select name="IDkelas" id="IDkelas" class="form-select" required>
                                    <option value="">- Pilih Kelas -</option>
                                    @foreach ($rombels as $rombel)
                                        <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="NIS" class="form-label">Siswa</label>
                                <select name="NIS" id="NIS" class="form-select" required disabled>
                                    <option value="">- Pilih Kelas Terlebih Dahulu -</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="IDpelanggaran_poin" class="form-label">Poin - Jenis Pelanggaran</label>
                                <select name="IDpelanggaran_poin" id="IDpelanggaran_poin" class="form-select" required>
                                    <option value="">- Pilih Jenis Pelanggaran -</option>
                                    @foreach ($kategoriList as $kategori)
                                        <optgroup label="{{ strtoupper($kategori->nama) }}">
                                            @foreach ($kategori->pelanggaranPoin as $poin)
                                                <option value="{{ $poin->ID }}">{{ $poin->nama }} ({{$poin->poin}} Poin)</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="IDmapel" class="form-label">Mata Pelajaran (Opsional)</label>
                                <select name="IDmapel" id="IDmapel" class="form-select" disabled>
                                    <option value="">- Belum Tersedia -</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>