{{-- 
 File ini di-include oleh index.blade.php.
 Pastikan Controller (method daftarIndex) mengirimkan variabel:
 1. $rombelList
 2. $kategoriPelanggaranSiswaList
--}}

<div class="modal fade" id="modalInputPelanggaran" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Input Pelanggaran Siswa (Manual)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
 
        <form action="{{ route('admin.indisipliner.siswa.daftar.store') }}" method="POST" id="formInputPelanggaran">
          @csrf
          <div class="modal-body">
 
            {{-- 1. Rombongan Belajar (KUNCI UTAMA) --}}
            <div class="mb-3">
                <label class="form-label">Rombongan Belajar <span class="text-danger">*</span></label>
                <select name="rombongan_belajar_id" id="modal_rombel_id" class="form-select select2-modal" required>
                    <option value="">- Pilih Rombel Dahulu -</option>
                    @foreach ($rombelList as $rombel)
                        <option value="{{ $rombel->id }}"
                            {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                            {{ $rombel->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
 
            <div class="row g-2 mb-3">
                {{-- 2. Semester (Otomatis terisi via JS) --}}
                <div class="col-md-6">
                    <label class="form-label">Tahun Pelajaran - Semester</label>
                    
                    {{-- Input Text untuk tampilan user --}}
                    <input type="text" id="modal_semester_text" class="form-control" 
                           placeholder="Otomatis terisi..." readonly>
                           
                    {{-- Input Hidden untuk dikirim ke controller --}}
                    <input type="hidden" name="semester_id" id="modal_semester_id" value="">
                </div>
 
                {{-- 3. Siswa (Akan terisi otomatis oleh AJAX setelah pilih rombel) --}}
                <div class="col-md-6">
                    <label class="form-label">Siswa <span class="text-danger">*</span></label>
                    <select name="nipd" id="modal_nipd" class="form-select select2-modal" required disabled>
                        <option value="">- Pilih Rombel Terlebih Dahulu -</option>
                    </select>
                </div>
            </div>
 
            {{-- 4. Jenis Pelanggaran (Data dari Pengaturan) --}}
            <div class="mb-3">
                <label class="form-label">Jenis Pelanggaran <span class="text-danger">*</span></label>
                <select name="IDpelanggaran_poin" id="modal_pelanggaran_id" class="form-select select2-modal" required>
                    <option value="">- Pilih Jenis Pelanggaran -</option>
                    @foreach ($kategoriPelanggaranSiswaList as $kategori)
                        <optgroup label="{{ $kategori->nama }}">
                            @foreach ($kategori->pelanggaranPoin as $poin)
                                <option value="{{ $poin->ID }}" data-poin="{{ $poin->poin }}">
                                    {{ $poin->nama }} ({{ $poin->poin }} Poin)
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
 
            <div class="row g-2 mb-3">
                {{-- 5. Tanggal --}}
                <div class="col-md-4">
                    <label class="form-label">Tanggal Kejadian <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                {{-- 6. Jam --}}
                <div class="col-md-4">
                    <label class="form-label">Jam Kejadian <span class="text-danger">*</span></label>
                    <input type="time" name="jam" class="form-control" value="{{ date('H:i') }}" required>
                </div>
                {{-- 7. Poin (otomatis) --}}
                <div class="col-md-4">
                    <label class="form-label">Poin <span class="text-danger">*</span></label>
                    <input type="number" name="poin" id="modal_poin" class="form-control" readonly required>
                </div>
            </div>
 
            {{-- 8. Pembelajaran/Mapel (BISA DIPILIH) --}}
            <div class="mb-3">
                <label class="form-label">Keterangan Pembelajaran</label>
                {{-- Kembalikan ke <select> agar bisa dipilih jika mapel tersedia dari AJAX --}}
                {{-- Default value kosong untuk 'Di Luar Jam Pelajaran / Bolos' --}}
                <select name="pembelajaran" id="modal_mapel_id" class="form-select select2-modal">
                    <option value="">Di Luar Jam Pelajaran / Bolos</option>
                    {{-- Opsi mapel lain akan diisi oleh AJAX saat Rombel dipilih --}}
                </select>
                <small class="text-muted">Biarkan default jika bolos atau di luar jam pelajaran.</small>
            </div>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Data</button>
          </div>
        </form>
      </div>
    </div>
  </div>