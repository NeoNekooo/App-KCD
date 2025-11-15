@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">PPDB /</span> Pemberian NIS </h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="">Pemberian NIS</h5>

                <a href="{{ route('admin.kesiswaan.ppdb.pemberian-nis.generate') }}" class="btn btn-primary d-flex align-items-center">
                    Generate NIS
                </a>
            </div>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                  @if($calons->isEmpty())
                    <tr>
                      <td colspan="7">
                        <div class="alert alert-warning alert-dismissible" role="alert">
                          Tidak ada calon siswa dengan status <strong>Registered (2)</strong>.
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      </td>
                    </tr>
                  @else
                    <thead>
                      <tr>
                        <th>Nomor RESI</th>
                        <th>Tanggal Daftar</th>
                        <th>Nama Lengkap</th>
                        <th>Tempat Tanggal Lahir</th>
                        <th>L/P</th>
                        <th>jurusan</th>
                        <th>Asal Sekolah</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                            @foreach($calons as $calon)
                                <tr>
                                    <td><strong>{{ $calon->nomor_resi }}</strong></td>
                                    <td>{{ $calon->created_at->translatedFormat('d F Y') }}</td>
                                    <td>{{ $calon->nama_lengkap }}</td>
                                    <td>{{ $calon->tempat_lahir }}, {{ \Carbon\Carbon::parse($calon->tgl_lahir)->translatedFormat('d F Y') }}</td>
                                    <td>{{ $calon->jenis_kelamin }}</td>
                                    <td>{{ $calon->jurusan ?? '-' }}</td>
                                    <td>{{ $calon->asal_sekolah ?? '-' }}</td>
                                </tr>
                            @endforeach
                    </tbody>
                   @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection