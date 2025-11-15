<div class="modal fade" id="modalInputPelanggaran" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white">
        <h5 class="modal-title">
          <i class="bx bx-error-circle me-2"></i> Formulir Pelanggaran Guru
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('admin.indisipliner.guru.daftar.store') }}" method="POST" id="formPelanggaranGuru">
        @csrf
        <div class="modal-body">
          <div class="row">
            
            {{-- =========================
                KOLOM KIRI
            ========================== --}}
            <div class="col-md-6">
              
              {{-- Tahun Pelajaran & Semester (otomatis dari semester aktif rombel) --}}
              <div class="mb-3">
                <label class="form-label fw-semibold">Tahun Pelajaran - Semester</label>
                @php 
                  $tahun = substr($semesterAktif, 0, 4);
                  $sem = substr($semesterAktif, 4, 1);
                  $tapel = $tahun . '/' . ($tahun + 1);
                  $semesterTeks = $sem == '1' ? 'Ganjil' : 'Genap';
                @endphp
                <input type="text" 
                       class="form-control bg-light fw-semibold" 
                       value="{{ $tapel }} - {{ $semesterTeks }}"
                       readonly>
                {{-- Hidden value agar tersimpan di database --}}
                <input type="hidden" name="semester_id" value="{{ $semesterAktif }}">
              </div>

              {{-- Tanggal --}}
              <div class="mb-3">
                <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control"
                       value="{{ date('Y-m-d') }}" required>
              </div>

              {{-- Jam --}}
              <div class="mb-3">
                <label for="jam" class="form-label fw-semibold">Waktu</label>
                <input type="time" name="jam" id="jam" class="form-control"
                       value="{{ date('H:i') }}" required>
              </div>
            </div>

            {{-- =========================
                KOLOM KANAN
            ========================== --}}
            <div class="col-md-6">

              {{-- Nama Guru --}}
              <div class="mb-3">
                <label for="nama_guru" class="form-label fw-semibold">Nama Guru</label>
                <select name="nama_guru" id="nama_guru" class="form-select select2" required>
                  <option value="">- Pilih Guru -</option>
                  @foreach ($gurus as $guru)
                    <option value="{{ $guru->nama }}">{{ $guru->nama }}</option>
                  @endforeach
                </select>
              </div>

              {{-- Jenis Pelanggaran --}}
              <div class="mb-3">
                <label for="IDpelanggaran_poin" class="form-label fw-semibold">Poin - Jenis Pelanggaran</label>
                <select name="IDpelanggaran_poin" id="IDpelanggaran_poin" class="form-select select2" required>
                  <option value="">- Pilih Jenis Pelanggaran -</option>
                  @foreach ($kategoriList as $kategori)
                    <optgroup label="{{ strtoupper($kategori->nama) }}">
                      @foreach ($kategori->pelanggaranPoinGtk as $poin)
                        <option value="{{ $poin->ID }}">
                          {{ $poin->nama }} ({{ $poin->poin }} Poin)
                        </option>
                      @endforeach
                    </optgroup>
                  @endforeach
                </select>
              </div>

            </div>
          </div>
        </div>

        {{-- =========================
            FOOTER
        ========================== --}}
        <div class="modal-footer bg-light d-flex justify-content-end">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bx bx-x me-1"></i> Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
  // Aktifkan Select2 dalam modal
  $('#modalInputPelanggaran .select2').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#modalInputPelanggaran'),
    placeholder: '- Pilih -',
    width: '100%'
  });

  // Prefill guru jika sedang difilter di halaman
  const selectedGuru = "{{ request('nama_guru') }}";
  if (selectedGuru) {
    $('#modalInputPelanggaran').on('shown.bs.modal', function() {
      $('#nama_guru').val(selectedGuru).trigger('change');
    });
  }
});
</script>
@endpush
