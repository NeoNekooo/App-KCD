@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">PPDB /</span> Calon Peserta Didik
</h4>

<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <h5 class="card-header">Daftar Calon Peserta Didik</h5>
      <div class="card-body">
        <form method="GET" class="row g-3 align-items-center mb-3">

          <!-- Search -->
          <div class="col-md-4">
            <div class="input-group input-group-merge">
              <span class="input-group-text">
                <i class="bx bx-search"></i>
              </span>
              <input type="text" name="search" class="form-control"
                     placeholder="Cari nama / resi / sekolah..."
                     value="{{ request('search') }}">
            </div>
          </div>
        
          <!-- Per Page -->
          <div class="col-md-1">
            <select name="per_page" class="form-select" onchange="this.form.submit()">
              <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
              <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
              <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
              <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
          </div>
        
          <div class="col-md-2">
            <button class="btn btn-primary w-100">Cari</button>
          </div>
        
        </form>

        <div class="table-responsive text-nowrap">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Nomor Resi</th>
                <th>Informasi Pendaftar</th>
                <th>Tanggal Daftar</th>
                <th>Syarat-syarat</th>
                <th>Keterangan</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($formulirs as $i => $calon)
                <tr>
                  <td>{{ $i+1 }}</td>
                  <td>
                    <strong class="text-primary">{{ $calon->nomor_resi }}</strong><br>
                    <small class="text-muted">{{ $calon->jurusan ?? '-' }}</small>
                  </td>
                  <td>
                    <strong>{{ $calon->nama_lengkap }} ({{ $calon->jenis_kelamin }})</strong><br>
                    <small class="text-muted">
                      Ttl: {{ $calon->tempat_lahir }}, {{ \Carbon\Carbon::parse($calon->tgl_lahir)->translatedFormat('d F Y') }}
                    </small><br>
                    <small class="text-muted">Telp: {{ $calon->kontak }}</small><br>
                    <small class="text-muted">Asal: {{ $calon->asal_sekolah }}</small>
                  </td>
                  <td>{{ $calon->created_at->translatedFormat('d F Y') }}</td>
                  <td>
                      @php
                          // ambil semua syarat aktif dari jalur pendaftaran
                          $semuaSyarat = $calon->jalurPendaftaran
                              ? $calon->jalurPendaftaran->syaratPendaftaran()->where('is_active', 1)->get()
                              : collect();
                                  
                          // ambil ID syarat yang sudah dipenuhi
                          $syaratTerpenuhiIds = $calon->syarat
                              ->where('pivot.is_checked', true)
                              ->pluck('id')
                              ->toArray();
                  
                          // ambil syarat yang belum terpenuhi
                          $syaratBelum = $semuaSyarat->whereNotIn('id', $syaratTerpenuhiIds);
                      @endphp
  
                      @if($syaratBelum->count() > 0)
                          <ul class="mb-0 ps-3 list-unstyled">
                              @foreach($syaratBelum as $syarat)
                                  <li class="d-flex align-items-center text-danger">
                                      <i class="bx bx-x me-2"></i>
                                      {{ $syarat->syarat }}
                                  </li>
                              @endforeach
                          </ul>
                      @else
                          <span class="badge bg-success border-0 px-2 py-2">syarat sudah lengkap</span>
                      @endif
                  </td>
                
                
                  <td class="text-center">
                      @if($calon->status == 0)
                          {{-- Status 0: syarat belum lengkap --}}
                          <a href="{{ route('admin.ppdb.formulir-ppdb.edit', $calon->id) }}"
                             class="badge bg-warning text-dark text-decoration-none  px-2 py-2" title="klik untuk melengkapi persyaratan">
                              Syarat Belum Lengkap
                          </a>
                        
                      @elseif($calon->status == 1)
                          {{-- Status 1: unregistered --}}
                          <form action="{{ route('admin.ppdb.updateStatus', $calon->id) }}" method="POST" class="d-inline">
                              @csrf
                              @method('PATCH')
                              <input type="hidden" name="status" value="2">
                              <button type="submit" class="badge bg-danger border-0 px-2 py-2" title="klik untuk daftar ulang">Unregistered</button>
                          </form>
                        
                      @elseif($calon->status == 2)
                          {{-- Status 2: registered --}}
                          <form action="{{ route('admin.ppdb.updateStatus', $calon->id) }}" method="POST" class="d-inline">
                              @csrf
                              @method('PATCH')
                              <input type="hidden" name="status" value="1">
                              <button type="submit" class="badge bg-success border-0 px-2 py-2" title="klik untuk membatalkan daftar ulang">Registered</button>
                          </form>
                        
                      @elseif ($calon->status == 3)
                          {{-- Status 3: Sudah Pemberian NIS --}}
                          <span class="badge bg-primary border-0 px-2 py-2" >Sudah diberi NIS</span>
                        
                      @endif
                        
                  </td>
                
                  <td class="text-center">
                      @if($calon->status != 3)
                          <div class="d-flex justify-content-center gap-2">
                              <a href="{{ route('admin.ppdb.formulir-ppdb.edit', $calon->id) }}" 
                                 class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                <i class="bx bx-edit"></i>
                              </a>
                            
                              {{-- Tombol Cetak Resi --}}
                              <a href="{{ route('admin.ppdb.daftar_calon.resi', $calon->id) }}" 
                                 target="_blank" 
                                 class="btn btn-sm btn-icon btn-outline-secondary" title="Cetak Resi">
                                <i class="bx bx-printer"></i>
                              </a>
                            
                              <form action="{{ route('admin.ppdb.daftar-calon-peserta-didik.destroy', $calon->id) }} " method="POST" onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                  <i class="bx bx-trash"></i>
                                </button>
                              </form>
                          </div>
                      @endif
                  </td>
                
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">Belum ada data pendaftar</td>
                </tr>
              @endforelse
            </tbody>
          </table>
          <div class="mt-3">
            {{ $formulirs->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
