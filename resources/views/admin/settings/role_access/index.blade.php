@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Hak Akses Role</h4>

    <div class="row">
        {{-- KOLOM KIRI: PILIH ROLE (TABS) --}}
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 text-white"><i class='bx bx-user-check'></i> Pilih Role</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="roleTabs" role="tablist">
                        @foreach($roles as $index => $role)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 {{ $index === 0 ? 'active' : '' }}" 
                               id="list-{{ Str::slug($role) }}-list" 
                               data-bs-toggle="list" 
                               href="#list-{{ Str::slug($role) }}" 
                               role="tab">
                                <span class="fw-bold">{{ $role }}</span>
                                <i class='bx bx-chevron-right'></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: CHECKLIST MENU --}}
        <div class="col-md-9">
            <div class="tab-content p-0" id="nav-tabContent">
                @foreach($roles as $index => $role)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="list-{{ Str::slug($role) }}" role="tabpanel">
                        
                        <div class="card shadow-sm">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-white py-3">
                                <h5 class="mb-0 text-primary">Akses Menu: <strong>{{ $role }}</strong></h5>
                                
                                {{-- Tombol Select All --}}
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="toggleAll('{{ Str::slug($role) }}', true)">
                                        <i class='bx bx-check-double'></i> Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="toggleAll('{{ Str::slug($role) }}', false)">
                                        <i class='bx bx-x'></i> Hapus Semua
                                    </button>
                                </div>
                            </div>

                            <form action="{{ route('admin.settings.role-access.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role_name" value="{{ $role }}">

                                <div class="card-body p-0" style="max-height: 650px; overflow-y: auto;">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th style="width: 60px;" class="text-center">Pilih</th>
                                                    <th>Nama Menu</th>
                                                    <th>Tipe</th>
                                                    <th>Slug / Route</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    // Ambil daftar ID menu yang sudah dimiliki role ini
                                                    $myMenus = $currentAccess[$role] ?? [];
                                                @endphp

                                                @foreach($menus as $menu)
                                                    {{-- Logic pewarnaan baris --}}
                                                    @php
                                                        $bgClass = '';
                                                        if($menu->is_header) $bgClass = 'table-secondary'; // Header gelap dikit
                                                        elseif(!$menu->parent_id) $bgClass = 'table-light fw-bold'; // Induk tebal
                                                    @endphp

                                                    <tr class="{{ $bgClass }}">
                                                        <td class="text-center align-middle">
                                                            <div class="form-check d-flex justify-content-center">
                                                                <input class="form-check-input check-{{ Str::slug($role) }}" 
                                                                       type="checkbox" 
                                                                       name="menu_ids[]" 
                                                                       value="{{ $menu->id }}"
                                                                       {{ in_array($menu->id, $myMenus) ? 'checked' : '' }}
                                                                       id="chk_{{ Str::slug($role) }}_{{ $menu->id }}"
                                                                       style="cursor: pointer; width: 1.2em; height: 1.2em;">
                                                            </div>
                                                        </td>
                                                        <td class="align-middle">
                                                            <label class="form-check-label w-100 cursor-pointer py-1" for="chk_{{ Str::slug($role) }}_{{ $menu->id }}">
                                                                @if($menu->is_header)
                                                                    <span class="badge bg-dark">HEADER</span> <strong>{{ $menu->title }}</strong>
                                                                @elseif($menu->parent_id)
                                                                    <span class="ms-4 text-muted"><i class='bx bx-subdirectory-right'></i> {{ $menu->title }}</span>
                                                                @else
                                                                    <i class="{{ $menu->icon }} text-primary me-2"></i> {{ $menu->title }}
                                                                @endif
                                                            </label>
                                                        </td>
                                                        <td class="align-middle">
                                                            @if($menu->is_header)
                                                                <span class="badge bg-label-secondary">Pemisah</span>
                                                            @elseif($menu->parent_id)
                                                                <span class="badge bg-label-info">Submenu</span>
                                                            @else
                                                                <span class="badge bg-label-primary">Menu Utama</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-muted small font-monospace align-middle">
                                                            <div>{{ $menu->slug }}</div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-light text-end py-3">
                                    <button type="submit" class="btn btn-primary fw-bold px-4">
                                        <i class='bx bx-save'></i> Simpan Akses {{ $role }}
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
    /* Custom style biar list role di kiri kelihatan nyambung & modern */
    .list-group-item.active {
        background-color: #696cff;
        border-color: #696cff;
        border-radius: 0; 
        font-weight: bold;
    }
    .list-group-item {
        border-radius: 0;
        border-left: 0;
        border-right: 0;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    /* Sticky header tabel biar enak pas scroll */
    .table-responsive {
        position: relative;
    }
    thead.sticky-top th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f5f5f9; /* Warna header tabel */
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection