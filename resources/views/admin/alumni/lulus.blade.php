@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akademik /</span> Penetapan Kelulusan Siswa</h4>

@if($tapelAktif->semester !== 'Genap')
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bx bx-error-circle me-2 fs-4"></i>
        <div>
            <strong>Belum Semester Genap.</strong><br>
            Proses penetapan kelulusan hanya dapat dilakukan pada semester genap.
        </div>
    </div>
@endif

@if($tapelAktif->semester === 'Genap')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Luluskan Siswa Kelas XII</h5>

        <form method="GET" action="{{ route('admin.alumni.pelulusan') }}" class="d-flex align-items-center">
            <select name="kelas" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih Kelas XII --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->nama_rombel }}" {{ request('kelas') == $k->nama_rombel ? 'selected' : '' }}>
                        {{ $k->nama_rombel }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.alumni.process') }}">
        @csrf

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NISN</th>
                            <th>NAMA SISWA</th>
                            <th class="text-center">
                                @if($siswa->where('status', '!=', 'Lulus')->count())
                                    <input type="checkbox" id="checkAll">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $i => $s)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $s->nisn }}</td>
                            <td>{{ $s->nama }}</td>
                            <td class="text-center">
                                @if($s->status === 'Lulus')
                                    <span class="badge bg-success">Sudah Lulus</span>
                                @else
                                    <input
                                        type="checkbox"
                                        class="check-item"
                                        name="siswa_id[]"
                                        value="{{ $s->id }}">
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada siswa kelas ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tombol muncul kalau ada centang --}}
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-success d-none" id="btnLuluskan">
                Luluskan Siswa
            </button>
        </div>
    </form>
</div>
@endif

@if($tapelAktif->semester === 'Genap')
<script>
    const checkAll = document.getElementById('checkAll');
    const items = document.querySelectorAll('.check-item');
    const btn = document.getElementById('btnLuluskan');

    const updateButton = () => {
        const anyChecked = [...items].some(i => i.checked);
        btn.classList.toggle('d-none', !anyChecked);
    };

    checkAll.addEventListener('change', () => {
        items.forEach(i => i.checked = checkAll.checked);
        updateButton();
    });

    items.forEach(i => i.addEventListener('change', updateButton));
</script>
@endif

@endsection
