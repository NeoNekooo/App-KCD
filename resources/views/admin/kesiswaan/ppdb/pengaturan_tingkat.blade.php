@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">PPDB /</span> Pengaturan Tingkat PPDB
</h4>

<div class="row">
  <div class="col-md-12">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="">Pengaturan Tingkat</h5>
      </div>
      <div class="table-responsive text-nowrap">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>No</th>
        <th>Tingkat</th>
        <th>Keterangan</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @foreach ($tingkats as $tingkat)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $tingkat->tingkat }}</td>
        <td>{{ $tingkat->keterangan }}</td>
        <td>
          <div class="d-flex align-items-center">
            {{-- Toggle Active --}}
            <form action="{{ route('admin.kesiswaan.ppdb.tingkat-ppdb.toggleActive', $tingkat->id) }}" method="POST" class="me-1">
              @csrf
              <button type="submit"
                class="btn btn-icon btn-sm @if($tingkat->is_active) text-danger @else text-info @endif"
                data-bs-toggle="tooltip" data-bs-placement="top"
                title="@if($tingkat->is_active) Nonaktifkan @else Aktifkan @endif">
                <i class='bx @if($tingkat->is_active) bx-block @else bx-power-off @endif'></i>
              </button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>
@endsection