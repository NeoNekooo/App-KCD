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
          <h5 class="modal-title">Input Pelanggaran Siswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
 
        <form action="{{ route('admin.indisipliner.siswa.daftar.store') }}" method="POST" id="formInputPelanggaran">
          @csrf
          <div class="modal-body">
 
            {{-- =================================== --}}
            {{-- START BAGIAN SCANNER (BARU)       --}}
            {{-- =================================== --}}
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bx bx-qr-scan bx-lg me-2"></i>
                <div>
                    Input data lebih cepat! Klik tombol "Scan" dan arahkan 
                    kamera ke QR code pada kartu siswa.
                </div>
            </div>

            <div class="d-flex justify-content-center mb-3">
                <button type="button" id="btn-start-scan" class="btn btn-primary">
                    <i class="bx bx-camera me-1"></i> Buka Kamera & Scan
                </button>
            </div>

            <div id="qr-reader" class="mx-auto" style="width: 300px; display: none;"></div>
            
            <div class="d-flex justify-content-center mb-3" style="display: none;" id="stop-scan-container">
                <button type="button" id="btn-stop-scan" class="btn btn-danger btn-sm">
                    <i class="bx bx-stop-circle me-1"></i> Tutup Kamera
                </button>
            </div>
            
            <hr>
            <p class="text-center text-muted small">Atau, input manual di bawah ini:</p>
            {{-- =================================== --}}
            {{-- AKHIR BAGIAN SCANNER (BARU)      --}}
            {{-- =================================== --}}


            {{-- 1. Rombongan Belajar (KUNCI UTAMA) --}}
            <div class="mb-3">
                <label class="form-label">Rombongan Belajar <span class="text-danger">*</span></label>
                {{-- Ini menggunakan $rombelList (dari Controller) untuk fix error Anda --}}
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
                {{-- 2. Semester (Akan terisi otomatis oleh AJAX) --}}
                <div class="row g-2 mb-3">
                  {{-- 2. Semester (Otomatis terisi) --}}
                  <div class="col-md-6">
                      <label class="form-label">Tahun Pelajaran - Semester <span class="text-danger">*</span></label>
                      
                      {{-- GANTI <select> DENGAN INI --}}
                      
                      <input type_="text" id="modal_semester_text" class="form-control" 
                             placeholder="- Pilih Rombel Dahulu -" disabled>
                             
                      <input type="hidden" name="semester_id" id="modal_semester_id" value="">
                      
                      {{-- AKHIR PERUBAHAN --}}
                  </div>
 
                  {{-- 3. Siswa (Akan terisi otomatis oleh AJAX) --}}
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
                {{-- Ini menggunakan $kategoriPelanggaranSiswaList (dari Controller) --}}
                <select name="IDpelanggaran_poin" id="modal_pelanggaran_id[]" class="form-select select2-modal" required multiple>
                    <option value="">- Pilih Jenis Pelanggaran -</option>
                    @foreach ($kategoriPelanggaranSiswaList as $kategori)
                        <optgroup label="{{ $kategori->nama }}">
                            {{-- Asumsi relasi di Model Kategori: pelanggaranPoinSiswa --}}
                            @foreach ($kategori->pelanggaranPoin as $poin)
                                <option value="{{ $poin->ID }}" data-poin="{{ $poin->poin }}">
                                    {{ $poin->nama }}
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
 
            {{-- 8. Pembelajaran/Mapel (Akan terisi otomatis oleh AJAX) --}}
            <div class="mb-3">
                <label class="form-label">Terjadi Saat Mata Pelajaran (Opsional)</label>
                {{-- 'name="pembelajaran"' sesuai migrasi BARU (string/uuid) --}}
                <select name="pembelajaran" id="modal_mapel_id" class="form-select select2-modal" disabled>
                    <option value="">- Di Luar Jam Pelajaran -</option>
                </select>
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