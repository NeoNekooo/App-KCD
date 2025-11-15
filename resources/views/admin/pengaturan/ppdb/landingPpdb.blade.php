@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Pengaturan Landing /</span> PPDB
</h4>

<div class="card">
  <form action="{{ route('admin.ppdb.landing.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1" role="tab">
            Beranda
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2" role="tab">
            Keunggulan
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab3" role="tab">
            Kompetensi
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab4" role="tab">
            Kontak
          </button>
        </li>
      </ul>
    </div>

    <div class="card-body tab-content pt-4">

      {{-- TAB 1 : BERANDA --}}
      <div class="tab-pane fade show active" id="tab1" role="tabpanel">
        <div class="mb-4">
          <label class="form-label fw-semibold">Slogan Utama</label>
          <input type="text" name="slogan_utama" class="form-control" placeholder="Masukkan slogan utama"
                 value="{{ old('slogan_utama', $beranda->slogan_utama ?? '') }}">
          <small class="text-muted">jika bagian teks -nya mau berwarna kuning di awali "||"</small>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold">Deskripsi Singkat</label>
          <textarea name="deskripsi_singkat" class="form-control" rows="3">{{ old('deskripsi_singkat', $beranda->deskripsi_singkat ?? '') }}</textarea>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold">Point Keunggulan Pertama</label>
          <textarea name="point_keunggulan_1" class="form-control" rows="2">{{ old('point_keunggulan_1', $beranda->point_keunggulan_1 ?? '') }}</textarea>

          <small class="text-muted">jika point lebih dari satu, di setiap kalimat menggunakan ","</small>
        </div>
      </div>

      {{-- TAB 2 : KEUNGGULAN --}}
      <div class="tab-pane fade" id="tab2" role="tabpanel">
        <div class="mb-4">
          <label class="form-label fw-semibold">Judul Utama</label>
          <input type="text" name="judul_keunggulan" class="form-control"
                 value="{{ old('judul_keunggulan', $keunggulanList->first()->judul_keunggulan ?? '') }}">
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold">Deskripsi</label>
          <textarea name="deskripsi_keunggulan" class="form-control" rows="3">{{ old('deskripsi_keunggulan', $keunggulanList->first()->deskripsi_keunggulan ?? '') }}</textarea>
        </div>

        <hr class="my-4">
        <h5 class="fw-bold mb-3">Daftar Keunggulan</h5>

        <div id="keunggulan-wrapper">
          @foreach($keunggulanList as $item)
          <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">
              <input type="hidden" name="id_keunggulan[]" value="{{ $item->id }}">
              <input type="hidden" name="delete_keunggulan[]" value="0" class="delete-keunggulan-flag">
              <div class="row align-items-start">
                <div class="col-md-3 mb-3">
                  <label class="form-label fw-semibold">Icon</label>
                  <input type="file" name="icon_keunggulan[]" class="form-control">
                  @if($item->icon)
                    <img src="{{ asset('storage/'.$item->icon) }}" alt="Icon" class="mt-2" width="50">
                  @endif
                  <small class="text-muted">Upload icon (PNG/SVG)</small>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label fw-semibold">Judul Keunggulan</label>
                  <input type="text" name="judul_item[]" class="form-control" value="{{ $item->judul_item }}">
                </div>
                <div class="col-md-5 mb-3">
                  <label class="form-label fw-semibold">Deskripsi Keunggulan</label>
                  <textarea name="deskripsi_item[]" class="form-control" rows="3">{{ $item->deskripsi_item }}</textarea>
                </div>
              </div>
              <div class="text-end">
                <button type="button" class="btn btn-sm btn-danger remove-keunggulan">
                  <i class="bx bx-trash"></i> Hapus
                </button>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <div class="d-flex justify-content-between mt-3">
          <button type="button" id="add-keunggulan" class="btn btn-outline-primary">
            <i class="bx bx-plus"></i> Tambah Keunggulan
          </button>
        </div>
      </div>

      {{-- TAB 3 : KOMPETENSI --}}
      <div class="tab-pane fade" id="tab3" role="tabpanel">
        <div class="mb-4">
          <label class="form-label fw-semibold">Judul Utama</label>
          <input type="text" name="judul_kompetensi" class="form-control"
                 value="{{ old('judul_kompetensi', $kompetensiList->first()->judul_kompetensi ?? '') }}">
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold">Deskripsi</label>
          <textarea name="deskripsi_kompetensi" class="form-control" rows="3">{{ old('deskripsi_kompetensi', $kompetensiList->first()->deskripsi_kompetensi ?? '') }}</textarea>
        </div>

        <h5 class="fw-bold mb-3">Daftar Kompetensi</h5>

        <div id="kompetensi-wrapper">
          @foreach($kompetensiList as $item)
          <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">
              <input type="hidden" name="id_kompetensi[]" value="{{ $item->id }}">
              <input type="hidden" name="delete_kompetensi[]" value="0" class="delete-kompetensi-flag">
              <div class="row align-items-start">
                  <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Icon</label>
                    <input type="file" name="icon_kompetensi[]" class="form-control">
                    @if($item->icon)
                      <img src="{{ asset('storage/'.$item->icon) }}" alt="Icon" class="img-thumbnail" width="50" height="50">
                    @endif
                    <small class="text-muted">Upload icon (PNG/SVG)</small>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Kode Kompetensi</label>
                    <input type="text" name="kode_kompetensi[]" class="form-control" value="{{ $item->kode_kompetensi }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Kompetensi Keahlian</label>
                    <input type="text" name="nama_kompetensi[]" class="form-control" value="{{ $item->nama_kompetensi }}">
                  </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi Jurusan</label>
                <textarea name="deskripsi_jurusan[]" class="form-control" rows="3">{{ $item->deskripsi_jurusan }}</textarea>
              </div>
              <div class="text-end">
                <button type="button" class="btn btn-sm btn-danger remove-kompetensi">
                  <i class="bx bx-trash"></i> Hapus
                </button>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <div class="d-flex justify-content-between mt-3">
          <button type="button" id="add-kompetensi" class="btn btn-outline-primary">
            <i class="bx bx-plus"></i> Tambah Kompetensi
          </button>
        </div>
      </div>

      {{-- TAB 4 : KONTAK --}}
      <div class="tab-pane fade" id="tab4" role="tabpanel">
        <!-- Row 1: Nomer PPDB & Jam Pelayanan -->
        <div class="row mb-4">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Nama Singkatan</label>
            <input type="text" name="singkatan" class="form-control" placeholder="Masukkan Nama Singkatan Sekolah"
                   value="{{ old('singkatan', $kontak->singkatan ?? '') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Nomer PPDB</label>
            <input type="text" name="nomer_ppdb" class="form-control" placeholder="Masukkan Nomer PPDB"
                   value="{{ old('nomer_ppdb', $kontak->nomer_ppdb ?? '') }}">
          </div>
          <div class="col-md-5">
            <label class="form-label fw-semibold">Jam Pelayanan PPDB</label>
            <input type="text" name="jam_pelayanan_ppdb" class="form-control" placeholder="Masukkan Jam Pelayanan"
                   value="{{ old('jam_pelayanan_ppdb', $kontak->jam_pelayanan ?? '') }}">
          </div>
        </div>
      
        <!-- Row 2: Email and Alamat -->
        <div class="row mb-4">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan Email"
                   value="{{ old('email', $kontak->email ?? '') }}">
          </div>
          <div class="col-md-8">
          <label class="form-label fw-semibold">Alamat</label>
          <textarea id="alamatInput" name="alamat" class="form-control" rows="2" placeholder="Masukkan Alamat">{{ old('alamat', $kontak->alamat ?? '') }}</textarea>
          </div>
        </div>
      
        <!-- Row 4: Live Preview Google Maps -->
        <div class="mb-4">
          <label class="form-label fw-semibold">Live Preview Alamat</label>
          <div style="width: 100%; height: 400px; overflow: hidden; border-radius: 8px; border:1px solid #ddd;">
            <iframe id="alamatMap"
                    src="https://maps.google.com/maps?q={{ urlencode($kontak->alamat ?? '') }}&t=&z=15&ie=UTF8&iwloc=&output=embed"
                    width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen></iframe>
          </div>
        </div>

        <!-- Row 4: Media Sosial -->
        <hr class="my-4">
        <h5 class="fw-bold mb-3">Media Sosial</h5>
        <div id="medsos-wrapper" class="row">
            @foreach($medsos ?? [] as $item)
            <div class="col-md-3 mb-3 medsos-card">
                <div class="card p-2">
                  <input type="hidden" name="id_medsos[]" value="{{ $item->id ?? '' }}">
                  <input type="hidden" name="delete_medsos[]" value="0" class="delete-medsos-flag">
                  <select name="icon_class_medsos[]" class="form-control icon-select mb-2" data-value="{{ $item->icon_class }}"></select>
                  <input type="text" name="link_medsos[]" class="form-control mb-2" placeholder="Link / URL" value="{{ $item->link }}">
                  <div class="icon-preview text-center mb-2">
                    <i class="{{ $item->icon_class ?? '' }} fa-2x"></i>
                  </div>
                  <button type="button" class="btn btn-sm btn-danger w-100 remove-medsos">Hapus</button>
                </div>
            </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" id="add-medsos" class="btn btn-outline-primary">
                <i class="bx bx-plus"></i> Tambah Media Sosial
            </button>
        </div>

      </div>

      {{-- Tombol Simpan Semua --}}
      <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary">Simpan Semua</button>
      </div>

  </form>
</div>

<script>
  // ======= Hapus Keunggulan =======
  document.querySelectorAll('.remove-keunggulan').forEach(btn => {
      btn.addEventListener('click', function() {
          const card = this.closest('.card');
          const flag = card.querySelector('.delete-keunggulan-flag');
          if(flag) {
            flag.value = 1;
            card.style.display = 'none';
          } else {
            card.remove();
          }
      });
  });

  // ======= Tambah Keunggulan =======
  document.getElementById('add-keunggulan').addEventListener('click', function() {
    const wrapper = document.getElementById('keunggulan-wrapper');
    const card = document.createElement('div');
    card.classList.add('card', 'mb-3', 'shadow-sm', 'border-0');
    card.innerHTML = `
      <div class="card-body">
        <input type="hidden" name="id_keunggulan[]" value="">
        <input type="hidden" name="delete_keunggulan[]" value="0" class="delete-keunggulan-flag">
        <div class="row align-items-start">
          <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold">Icon</label>
            <input type="file" name="icon_keunggulan[]" class="form-control">
            <small class="text-muted">Upload icon (PNG/SVG)</small>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">Judul Keunggulan</label>
            <input type="text" name="judul_item[]" class="form-control" placeholder="Contoh: Fasilitas Lengkap">
          </div>
          <div class="col-md-5 mb-3">
            <label class="form-label fw-semibold">Deskripsi Keunggulan</label>
            <textarea name="deskripsi_item[]" class="form-control" rows="3" placeholder="Tuliskan deskripsi singkat"></textarea>
          </div>
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-sm btn-danger remove-keunggulan">
            <i class="bx bx-trash"></i> Hapus
          </button>
        </div>
      </div>
    `;
    wrapper.appendChild(card);
    card.querySelector('.remove-keunggulan').addEventListener('click', function() {
      card.remove();
    });
  });

  // ======= Hapus Kompetensi =======
  document.querySelectorAll('.remove-kompetensi').forEach(btn => {
      btn.addEventListener('click', function() {
          const card = this.closest('.card');
          const flag = card.querySelector('.delete-kompetensi-flag');
          if(flag) {
            flag.value = 1;
            card.style.display = 'none';
          } else {
            card.remove();
          }
      });
  });

  // ======= Tambah Kompetensi =======
  document.getElementById('add-kompetensi').addEventListener('click', function() {
    const wrapper = document.getElementById('kompetensi-wrapper');
    const card = document.createElement('div');
    card.classList.add('card', 'mb-3', 'shadow-sm', 'border-0');
    card.innerHTML = `
      <div class="card-body">
        <input type="hidden" name="id_kompetensi[]" value="">
        <input type="hidden" name="delete_kompetensi[]" value="0" class="delete-kompetensi-flag">
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold">Icon</label>
            <input type="file" name="icon_kompetensi[]" class="form-control">
            <small class="text-muted">Upload icon (PNG/SVG)</small>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold">Kode Kompetensi</label>
            <input type="text" name="kode_kompetensi[]" class="form-control" placeholder="Contoh: TKJ, DKV, AKL...">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Kompetensi Keahlian</label>
            <input type="text" name="nama_kompetensi[]" class="form-control" placeholder="Masukkan nama kompetensi keahlian">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Deskripsi Jurusan</label>
          <textarea name="deskripsi_jurusan[]" class="form-control" rows="3" placeholder="Deskripsi jurusan"></textarea>
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-sm btn-danger remove-kompetensi">
            <i class="bx bx-trash"></i> Hapus
          </button>
        </div>
      </div>
    `;
    wrapper.appendChild(card);
    card.querySelector('.remove-kompetensi').addEventListener('click', function() {
      card.remove();
    });
  });

</script>


<script>
    // ===== Load Font Awesome Brands dari JSON =====
    let faBrands = [];
    fetch('{{ asset('assets/js/fa-brands.json') }}')
        .then(res => res.json())
        .then(data => {
            faBrands = data;
            console.log('faBrands', faBrands); // <--- pastikan array terisi
            initializeExistingMedsos();
        })
        .catch(err => console.error('Gagal load fa-brands.json', err));

    function initializeExistingMedsos() {
        document.querySelectorAll('.icon-select').forEach(select => {
            const selectedValue = select.dataset.value; // ambil dari Blade
            const ts = new TomSelect(select, {
                options: faBrands.map(c => ({value: c, text: c})),
                create: false,
                onChange: function(value) {
                    const preview = select.closest('.card').querySelector('.icon-preview');
                    preview.innerHTML = `<i class="${value} fa-2x"></i>`;
                }
            });
            if(selectedValue){
                ts.setValue(selectedValue); // set value awal
            }
        });
    }


    // ===== Tambah Media Sosial =====
    document.getElementById('add-medsos').addEventListener('click', function() {
        const wrapper = document.getElementById('medsos-wrapper');
        const card = document.createElement('div');
        card.classList.add('col-md-3', 'mb-3', 'medsos-card');
        card.innerHTML = `
            <div class="card p-2">
              <select name="icon_class_medsos[]" class="form-control icon-select mb-2" placeholder="Pilih Icon..."></select>
                <input type="text" name="link_medsos[]" class="form-control mb-2" placeholder="Link / URL">
                <div class="icon-preview text-center mb-2"></div>
                <button type="button" class="btn btn-sm btn-danger w-100 remove-medsos">Hapus</button>
            </div>
        `;
        wrapper.appendChild(card);

        const select = card.querySelector('.icon-select');
        new TomSelect(select, {
            options: faBrands.map(c => ({value: c, text: c})),
            create: false,
            onChange: function(value) {
                const preview = card.querySelector('.icon-preview');
                preview.innerHTML = `<i class="${value} fa-2x"></i>`;
            }
        });

        card.querySelector('.remove-medsos').addEventListener('click', function() {
            card.remove();
        });
    });

    // ===== Hapus Media Sosial =====
    document.querySelectorAll('.remove-medsos').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.medsos-card');
            const flag = card.querySelector('.delete-medsos-flag');
            if(flag){
                flag.value = 1;
                card.style.display = 'none';
            } else {
                card.remove();
            }
        });
    });

    // ===== Live Update Maps =====
    const alamatInput = document.getElementById('alamatInput');
    const alamatMap = document.getElementById('alamatMap');
    alamatInput.addEventListener('input', () => {
        const alamat = encodeURIComponent(alamatInput.value);
        alamatMap.src = `https://maps.google.com/maps?q=${alamat}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
    });
</script>

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


@endsection
