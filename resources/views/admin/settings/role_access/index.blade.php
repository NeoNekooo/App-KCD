@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Hak Akses Role</h4>

        <div class="row g-4">
            {{-- KOLOM KIRI: PILIH ROLE (TABS) --}}
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm sticky-top" style="top: 80px; z-index: 10;">
                    <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                        <h5 class="mb-0 text-primary fw-bold d-flex align-items-center">
                            <i class='bx bx-user-check fs-4 me-2'></i> Pilih Role
                        </h5>
                        <small class="text-muted d-block mt-1">Pilih role untuk mengatur akses.</small>
                    </div>
                    <div class="card-body p-3">
                        <div class="list-group list-group-flush role-list-wrapper" id="roleTabs" role="tablist">
                            @foreach ($roles as $index => $role)
                                <a class="list-group-item list-group-item-action role-item d-flex justify-content-between align-items-center {{ $index === 0 ? 'active' : '' }}"
                                    id="list-{{ Str::slug($role) }}-list" data-bs-toggle="list"
                                    href="#list-{{ Str::slug($role) }}" role="tab">
                                    <span class="fw-semibold d-flex align-items-center">
                                        <i class='bx bx-shield-quarter me-2 text-primary opacity-75'></i>
                                        {{ $role }}
                                    </span>
                                    <i class='bx bx-chevron-right fs-5 text-muted'></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: CHECKLIST MENU --}}
            <div class="col-md-8 col-lg-9">
                <div class="tab-content p-0" id="nav-tabContent">
                    @foreach ($roles as $index => $role)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="list-{{ Str::slug($role) }}"
                            role="tabpanel">

                            <div class="card shadow-sm h-100">
                                <div
                                    class="card-header border-bottom d-flex justify-content-between align-items-center bg-transparent pt-4 pb-3">
                                    <div>
                                        <h5 class="mb-0 text-dark fw-bold d-flex align-items-center">
                                            Pengaturan Akses: <span class="text-primary ms-2">{{ $role }}</span>
                                        </h5>
                                        <small class="text-muted mt-1 d-block">Centang menu yang boleh diakses oleh role
                                            ini.</small>
                                    </div>

                                    {{-- Tombol Select All --}}
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3"
                                            onclick="toggleAll('{{ Str::slug($role) }}', true)">
                                            <i class='bx bx-check-double me-1'></i> Pilih Semua
                                        </button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger shadow-sm rounded-pill px-3"
                                            onclick="toggleAll('{{ Str::slug($role) }}', false)">
                                            <i class='bx bx-x me-1'></i> Hapus Semua
                                        </button>
                                    </div>
                                </div>

                                <form action="{{ route('admin.settings.role-access.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="role_name" value="{{ $role }}">

                                    <div class="card-body p-0">
                                        <div class="table-responsive rounded-bottom"
                                            style="max-height: 70vh; overflow-y: auto;">
                                            <table class="table table-hover mb-0">
                                                <thead class="sticky-top">
                                                    <tr>
                                                        <th style="width: 80px;"
                                                            class="text-center text-uppercase text-muted"
                                                            style="letter-spacing: 1px; font-size: 0.75rem;">Akses</th>
                                                        <th class="text-uppercase text-muted"
                                                            style="letter-spacing: 1px; font-size: 0.75rem;">Struktur Menu
                                                        </th>
                                                        <th class="text-uppercase text-muted"
                                                            style="letter-spacing: 1px; font-size: 0.75rem;">Tipe</th>
                                                        <th class="text-uppercase text-muted"
                                                            style="letter-spacing: 1px; font-size: 0.75rem;">Target
                                                            Route/Slug</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    @php
                                                        // Ambil daftar ID menu yang sudah dimiliki role ini
                                                        $myMenus = $currentAccess[$role] ?? [];
                                                    @endphp

                                                    @foreach ($menus as $menu)
                                                        {{-- Logic styling baris --}}
                                                        @php
                                                            $rowClass = 'row-child';
                                                            if ($menu->is_header) {
                                                                $rowClass = 'row-header';
                                                            } elseif (!$menu->parent_id) {
                                                                $rowClass = 'row-parent';
                                                            }
                                                        @endphp

                                                        <tr class="{{ $rowClass }}">
                                                            <td class="text-center align-middle">
                                                                @if (!$menu->is_header)
                                                                    <div
                                                                        class="d-flex justify-content-center align-items-center">
                                                                        <input
                                                                            class="form-check-input check-{{ Str::slug($role) }} d-none"
                                                                            type="checkbox" name="menu_ids[]"
                                                                            value="{{ $menu->id }}"
                                                                            {{ in_array($menu->id, $myMenus) ? 'checked' : '' }}
                                                                            id="chk_{{ Str::slug($role) }}_{{ $menu->id }}">
                                                                        <label
                                                                            for="chk_{{ Str::slug($role) }}_{{ $menu->id }}"
                                                                            class="custom-checkbox-wrapper mb-0"></label>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td
                                                                class="align-middle {{ $menu->parent_id ? 'child-connector' : '' }}">
                                                                <label class="cursor-pointer mb-0 d-flex align-items-center"
                                                                    for="chk_{{ Str::slug($role) }}_{{ $menu->id }}">
                                                                    @if ($menu->is_header)
                                                                        <span
                                                                            class="badge bg-dark rounded-pill px-3 py-1 fw-bold shadow-sm me-2"
                                                                            style="font-size: 0.65rem;">HEADER</span>
                                                                        <span class="fw-bold text-uppercase text-dark"
                                                                            style="letter-spacing: 1px; font-size: 0.85rem;">{{ $menu->title }}</span>
                                                                    @elseif($menu->parent_id)
                                                                        <span class="fw-semibold text-dark"
                                                                            style="font-size: 0.95rem;">{{ $menu->title }}</span>
                                                                    @else
                                                                        <div class="avatar avatar-xs me-2 flex-shrink-0">
                                                                            <span
                                                                                class="avatar-initial rounded-circle bg-label-primary text-primary">
                                                                                <i class="{{ $menu->icon }} fs-6"></i>
                                                                            </span>
                                                                        </div>
                                                                        <span
                                                                            class="fw-bold text-dark">{{ $menu->title }}</span>
                                                                    @endif
                                                                </label>
                                                            </td>
                                                            <td class="align-middle">
                                                                @if ($menu->is_header)
                                                                    <span
                                                                        class="badge bg-label-secondary shadow-sm rounded-pill"><i
                                                                            class='bx bx-minus me-1'></i>Pemisah</span>
                                                                @elseif($menu->parent_id)
                                                                    <span
                                                                        class="badge bg-label-info shadow-sm rounded-pill"><i
                                                                            class='bx bx-subdirectory-right me-1'></i>Submenu</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-label-primary shadow-sm rounded-pill"><i
                                                                            class='bx bx-folder me-1'></i>Menu Utama</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-muted small align-middle">
                                                                @if ($menu->is_header)
                                                                    <span class="fst-italic">-</span>
                                                                @else
                                                                    <div
                                                                        class="d-flex align-items-center text-secondary mb-1">
                                                                        <i class='bx bx-link me-1'></i> {{ $menu->slug }}
                                                                    </div>
                                                                    @if ($menu->route)
                                                                        <div class="d-flex align-items-center text-primary font-monospace"
                                                                            style="font-size: 0.75rem;">
                                                                            <i class='bx bx-navigation me-1'></i>
                                                                            {{ $menu->route }}
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-top text-end py-3">
                                        <button type="submit"
                                            class="btn btn-primary fw-bold px-4 py-2 shadow-sm d-inline-flex align-items-center transition-all"
                                            style="background-image: linear-gradient(135deg, #696cff, #4e51cc);">
                                            <i class='bx bx-save fs-5 me-2'></i> Simpan Konfigurasi Akses
                                            {{ $role }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAll(roleSlug, status) {
            const checkboxes = document.querySelectorAll(`.check-${roleSlug}`);
            checkboxes.forEach(chk => chk.checked = status);
        }
    </script>

    <style>
        /* Styling List Role di Kiri */
        .role-list-wrapper {
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid #eaeaec;
            background-color: #fff;
        }

        .role-item {
            border: none;
            border-bottom: 1px solid #f0f0f2;
            padding: 1rem 1.25rem;
            color: #566a7f;
            transition: all 0.25s ease;
            position: relative;
            background-color: transparent;
        }

        .role-item:last-child {
            border-bottom: none;
        }

        .role-item:hover {
            background-color: #f8f9fa;
            color: #696cff;
            padding-left: 1.5rem;
        }

        .role-item.active {
            background-image: linear-gradient(135deg, #696cff, #4e51cc) !important;
            color: white !important;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.3);
            border-radius: 0.5rem;
            margin: 0.5rem;
            border-bottom: none;
            transform: scale(1.02);
        }

        .role-item.active i {
            color: white !important;
        }

        /* Styling Custom Checkbox */
        .custom-checkbox-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 0.4rem;
            border: 2px solid #d9dee3;
            transition: all 0.2s ease;
            background-color: #fff;
            cursor: pointer;
        }

        .form-check-input:checked+.custom-checkbox-wrapper {
            background-color: #696cff;
            border-color: #696cff;
            box-shadow: 0 2px 6px rgba(105, 108, 255, 0.3);
        }

        .form-check-input:checked+.custom-checkbox-wrapper::after {
            content: '\eb84';
            /* bx-check icon dari Boxicons */
            font-family: 'boxicons';
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* Tabel Enhancements */
        .table-responsive {
            position: relative;
        }

        thead.sticky-top th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
            border-bottom: 2px solid #eaeaec;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        .cursor-pointer {
            cursor: pointer;
            user-select: none;
            display: block;
            width: 100%;
        }

        /* Styling Header & Submenu di Tabel */
        .row-header {
            background-color: #f8f9fa !important;
            border-left: 4px solid #435971;
        }

        .row-parent {
            background-color: #ffffff;
            border-left: 4px solid #696cff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .row-child {
            background-color: #ffffff;
            transition: all 0.2s;
        }

        .row-child:hover {
            background-color: rgba(105, 108, 255, 0.03);
        }

        /* Child Line Connector */
        .child-connector {
            position: relative;
            padding-left: 4.5rem !important;
            /* Menjorok lebih ke dalam */
        }

        .child-connector::before {
            content: '';
            position: absolute;
            left: 2.75rem;
            /* Menyesuaikan posisi garis */
            top: -1rem;
            bottom: 50%;
            width: 1px;
            background-color: #d9dee3;
        }

        .child-connector::after {
            content: '';
            position: absolute;
            left: 2.75rem;
            /* Menyesuaikan posisi garis */
            top: 50%;
            width: 1.25rem;
            /* Memanjangkan garis horizontal sedikit */
            height: 1px;
            background-color: #d9dee3;
        }
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: none;
        }
    </style>
@endsection
