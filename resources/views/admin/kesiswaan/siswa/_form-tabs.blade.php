<div class="card">
    <div class="card-header">
        <div class="nav-align-top">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item"><button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-pribadi">Pribadi</button></li>
                <li class="nav-item"><button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-tempat-tinggal">Tempat Tinggal</button></li>
                <li class="nav-item"><button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-jasmani">Jasmani & Riwayat Penyakit</button></li>
                <li class="nav-item"><button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-pendidikan">Pendidikan</button></li>
                <li class="nav-item"><button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-ortu">Ortu/Wali</button></li>
                <li class="nav-item"><button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-intelegensi">Intelegensi</button></li>
                <li class="nav-item"><button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-prestasi">Prestasi</button></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-pribadi" role="tabpanel">
                @include('admin.kesiswaan.siswa.partials._form-pribadi')
            </div>
            <div class="tab-pane fade" id="tab-tempat-tinggal" role="tabpanel">
                @include('admin.kesiswaan.siswa.partials._form-tempat-tinggal')
            </div>
            <div class="tab-pane fade" id="tab-jasmani" role="tabpanel"><p>Isi form Jasmani & Riwayat Penyakit akan kita tambahkan di sini...</p></div>
            <div class="tab-pane fade" id="tab-pendidikan" role="tabpanel"><p>Isi form Pendidikan akan kita tambahkan di sini...</p></div>
            <div class="tab-pane fade" id="tab-ortu" role="tabpanel"><p>Isi form Ortu/Wali akan kita tambahkan di sini...</p></div>
            <div class="tab-pane fade" id="tab-intelegensi" role="tabpanel"><p>Isi form Intelegensi akan kita tambahkan di sini...</p></div>
            <div class="tab-pane fade" id="tab-prestasi" role="tabpanel"><p>Isi form Prestasi akan kita tambahkan di sini...</p></div>
        </div>
    </div>
</div>

