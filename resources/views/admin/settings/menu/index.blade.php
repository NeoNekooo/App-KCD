@extends('layouts.admin')

@section('content')
    <style>
        /* Modern Card Styling */
        .card {
            border-radius: 1rem;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }

        /* Styling Visual Icon Picker */
        .icon-option {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border: 2px solid transparent;
            border-radius: 1rem;
            background-color: #f8f9fa;
        }

        .icon-option:hover {
            background-color: #e7e7ff;
            transform: translateY(-4px) scale(1.02);
            border-color: #696cff;
            box-shadow: 0 8px 16px rgba(105, 108, 255, 0.15);
        }

        /* Styling Role Chips */
        .role-label {
            cursor: pointer;
            user-select: none;
            transition: all 0.25s ease;
            font-weight: 500;
            border-width: 1.5px;
            padding: 0.5rem 1rem !important;
        }

        .role-label:hover {
            background-color: rgba(105, 108, 255, 0.08);
            transform: translateY(-2px);
        }

        .btn-check:checked+.btn-outline-primary {
            background-image: linear-gradient(135deg, #696cff, #4e51cc);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 12px rgba(105, 108, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Custom Switch Box */
        .switch-box {
            background-color: #f8f9fa;
            border: 1px solid #eaeaec;
            border-left: 4px solid #696cff;
            transition: all 0.3s ease;
            border-radius: 0.75rem !important;
        }

        .switch-box:hover {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transform: translateX(4px);
        }

        /* Table Enhancements */
        .table> :not(caption)>*>* {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
        }

        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(105, 108, 255, 0.04);
        }

        /* Modern Input Styling */
        .input-group-text {
            background-color: #f8f9fa;
            border-color: #eaeaec;
            color: #696cff;
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }

        .form-control,
        .form-select {
            border-color: #eaeaec;
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.15);
            border-color: #696cff;
        }

        .input-group>.form-control {
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .btn-primary {
            background-image: linear-gradient(135deg, #696cff, #4e51cc);
            border: none;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 15px rgba(105, 108, 255, 0.4);
            transform: translateY(-2px);
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Pengaturan /</span> Manajemen Menu
            </h4>
        </div>

        <div class="row g-4">
            {{-- KOLOM KIRI: FORM INPUT --}}
            <div class="col-lg-4 col-md-5">
                <div class="card border-0 sticky-top" style="top: 80px; z-index: 10;">
                    <div class="card-header d-flex justify-content-between align-items-center pt-4 pb-3">
                        <h5 class="mb-0 text-primary fw-bold d-flex align-items-center" id="formTitle">
                            <i class='bx bx-plus-circle fs-4 me-2'></i> Tambah Menu Baru
                        </h5>
                        <button type="button"
                            class="btn btn-sm btn-light text-danger fw-bold d-none rounded-pill px-3 transition-all"
                            id="btnReset" onclick="resetForm()">
                            <i class='bx bx-x fs-5'></i> Batal
                        </button>
                    </div>

                    <div class="card-body pt-4">
                        <form id="menuForm" action="{{ route('admin.settings.menus.store') }}" method="POST">
                            @csrf
                            <div id="methodField"></div>

                            {{-- 1. JUDUL & SLUG --}}
                            <div class="mb-4">
                                <label class="form-label text-muted text-uppercase fw-bold"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Judul Menu <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-sm">
                                    <span class="input-group-text border-end-0"><i class="bx bx-text"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" name="title"
                                        id="title" required placeholder="Contoh: Data Pegawai" onkeyup="generateSlug()"
                                        style="font-size: 1.05rem; font-weight: 500;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted text-uppercase fw-bold"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Slug (ID Sistem) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-sm">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bx bx-link text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0 text-muted"
                                        name="slug" id="slug" required readonly placeholder="terisi-otomatis">
                                </div>
                            </div>

                            {{-- 2. HEADER TOGGLE --}}
                            <div class="mb-4">
                                <div class="form-check form-switch p-3 switch-box d-flex align-items-start gap-3">
                                    <input class="form-check-input mt-1 ms-0 shadow-sm" type="checkbox" name="is_header"
                                        id="is_header" onchange="toggleHeaderFields()"
                                        style="width: 2.8rem; height: 1.4rem; cursor: pointer;">
                                    <div class="ms-2">
                                        <label class="form-check-label fw-bold text-dark mb-1 d-block" for="is_header"
                                            style="cursor: pointer; font-size: 1.05rem;">Jadikan Header</label>
                                        <p class="text-muted small mb-0 lh-sm">Aktifkan untuk membuat menu ini sebagai
                                            tulisan pemisah grup.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. FIELD REGULAR (Icon, Route, Parent) --}}
                            <div id="regularFields">
                                <div class="mb-4">
                                    <label class="form-label text-muted text-uppercase fw-bold"
                                        style="font-size: 0.75rem; letter-spacing: 0.5px;">Route Laravel</label>
                                    <div class="input-group input-group-merge shadow-sm">
                                        <span class="input-group-text border-end-0"><i class="bx bx-navigation"></i></span>
                                        <input type="text"
                                            class="form-control font-monospace text-primary border-start-0 ps-0"
                                            name="route_name" id="route_name" placeholder="admin.dashboard">
                                    </div>
                                    <div class="form-text text-muted mt-2 small"><i
                                            class="bx bx-info-circle text-primary"></i> Kosongkan jika
                                        ini menu induk.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-muted text-uppercase fw-bold"
                                        style="font-size: 0.75rem; letter-spacing: 0.5px;">Induk Menu (Parent)</label>
                                    <select class="form-select shadow-sm" name="parent_id" id="parent_id">
                                        <option value="" class="fw-bold">-- 🌟 Menu Utama (Paling Luar) --</option>
                                        @foreach ($parents as $p)
                                            <option value="{{ $p->id }}" data-depth="{{ $p->depth }}">
                                                @for ($i = 0; $i < $p->depth; $i++)
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                @endfor
                                                ↳ {{ $p->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted text-uppercase fw-bold"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Icon Menu</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-primary border-end-0"
                                        id="iconPreviewAddon"><i class='bx bx-cube fs-4'></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" name="icon"
                                        id="icon" placeholder="Pilih icon..." readonly onclick="openIconModal()"
                                        style="cursor: pointer; background-color: #fff;">
                                    <button class="btn btn-outline-primary px-3 rounded-end" type="button"
                                        onclick="openIconModal()" style="border-radius: 0 0.5rem 0.5rem 0;">Pilih</button>
                                </div>
                            </div>

                            {{-- 4. WARNA MENU --}}
                            <div class="mb-4">
                                <label class="form-label text-muted text-uppercase fw-bold"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Warna Menu</label>
                                <select class="form-select shadow-sm" name="color" id="color">
                                    <option value="normal">Normal (Default)</option>
                                    <option value="danger" class="text-danger">Danger (Merah)</option>
                                </select>
                            </div>

                            {{-- 5. URUTAN --}}
                            <div class="mb-4">
                                <label class="form-label text-muted text-uppercase fw-bold"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">Urutan Tampil</label>
                                <input type="number" class="form-control shadow-sm" name="urutan" id="urutan"
                                    value="1" min="1">
                            </div>

                            {{-- 5. HAK AKSES (CHIPS) --}}
                            <div class="mb-4">
                                <label class="form-label text-muted text-uppercase fw-bold d-block mb-3"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;"><i
                                        class="bx bx-shield-quarter text-primary fs-6 me-1"></i> Hak Akses Role</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($roles as $role)
                                        <div>
                                            <input type="checkbox" class="btn-check role-check" name="roles[]"
                                                id="role_{{ Str::slug($role) }}" value="{{ $role }}"
                                                autocomplete="off">
                                            <label
                                                class="btn btn-outline-primary rounded-pill role-label px-3 py-1 shadow-sm"
                                                for="role_{{ Str::slug($role) }}">
                                                {{ $role }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="my-4 border-light">

                            <button type="submit"
                                class="btn btn-primary w-100 fw-bold py-3 shadow-lg d-flex align-items-center justify-content-center gap-2"
                                style="font-size: 1.05rem;">
                                <i class='bx bx-save fs-4'></i> Simpan Menu
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: TABEL DATA --}}
            <div class="col-lg-8 col-md-7">
                <div class="card border-0 h-100">
                    <div class="card-header pt-4 pb-3">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary"><i
                                        class='bx bx-list-ul fs-4'></i></span>
                            </div>
                            Struktur Menu Aplikasi
                        </h5>
                    </div>

                    <div class="table-responsive text-nowrap rounded-bottom">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-bold text-uppercase text-muted"
                                        style="letter-spacing: 1px; font-size: 0.7rem;">Menu</th>
                                    <th class="fw-bold text-uppercase text-muted"
                                        style="letter-spacing: 1px; font-size: 0.7rem;">Pengaturan</th>
                                    <th class="fw-bold text-uppercase text-muted"
                                        style="letter-spacing: 1px; font-size: 0.7rem;">Akses Role</th>
                                    <th class="text-end fw-bold text-uppercase text-muted"
                                        style="letter-spacing: 1px; font-size: 0.7rem; white-space: nowrap;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($menus as $menu)
                                    @include('admin.settings.menu._menu_item_row', [
                                        'menu' => $menu,
                                        'accesses' => $accesses,
                                        'level' => 0,
                                        'isLastChild' => $loop->last,
                                    ])
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                <i class="bx bx-menu-alt-left fs-1 text-light mb-3"
                                                    style="opacity: 0.5;"></i>
                                                <h6 class="fw-semibold mb-1">Struktur Menu Kosong</h6>
                                                <p class="small mb-0">Belum ada menu yang ditambahkan ke aplikasi.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ICON PICKER --}}
    <div class="modal fade" id="iconModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class="bx bx-category me-2"></i> Pilih Icon Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 500px; overflow-y: auto;">
                    <div class="row g-3" id="iconGrid"></div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- ICON LIST (Ditambah biar makin kaya pilihan) ---
        const icons = [
            'bx-home-circle', 'bx-home-alt', 'bx-user', 'bx-user-circle', 'bx-group',
            'bx-grid-alt', 'bx-category', 'bx-folder', 'bx-folder-open', 'bx-file',
            'bx-file-blank', 'bx-envelope', 'bx-cog', 'bx-wrench', 'bx-shield',
            'bx-shield-quarter', 'bx-lock', 'bx-key', 'bx-bar-chart-alt-2', 'bx-line-chart',
            'bx-pie-chart-alt', 'bx-briefcase-alt-2', 'bx-id-card', 'bx-buildings',
            'bx-store-alt', 'bx-check-shield', 'bx-paper-plane', 'bx-wallet', 'bx-money',
            'bx-medal', 'bx-bell', 'bx-calendar', 'bx-calendar-event', 'bx-star',
            'bx-archive', 'bx-trash', 'bx-printer', 'bx-search', 'bx-filter',
            'bx-list-ul', 'bx-menu', 'bx-dots-vertical-rounded'
        ];

        const iconGrid = document.getElementById('iconGrid');
        icons.forEach(icon => {
            const col = document.createElement('div');
            col.className = 'col-4 col-sm-3 col-md-2 text-center';
            col.innerHTML = `
            <div class="icon-option p-3 d-flex flex-column align-items-center justify-content-center h-100" onclick="selectIcon('bx ${icon}')">
                <i class='bx ${icon} text-secondary mb-2' style="font-size: 2rem;"></i>
                <div style="font-size: 0.7rem; color: #666;" class="text-wrap w-100">${icon.replace('bx-', '')}</div>
            </div>
            `;
            iconGrid.appendChild(col);
        });

        function openIconModal() {
            new bootstrap.Modal(document.getElementById('iconModal')).show();
        }

        function selectIcon(iconClass) {
            document.getElementById('icon').value = iconClass;
            document.getElementById('iconPreviewAddon').innerHTML = `<i class='${iconClass} fs-5'></i>`;
            bootstrap.Modal.getInstance(document.getElementById('iconModal')).hide();
        }

        function generateSlug() {
            const title = document.getElementById('title').value;
            const slug = title.toLowerCase().replace(/[^\w\s-]/g, '').replace(/\s+/g, '-');
            document.getElementById('slug').value = slug;
        }

        function toggleHeaderFields() {
            const isHeader = document.getElementById('is_header').checked;
            const regFields = document.getElementById('regularFields');
            regFields.style.display = isHeader ? 'none' : 'block';
            toggleIconField();
        }

        function editMenu(menu, roles) {
            document.getElementById('formTitle').innerHTML =
                "<i class='bx bx-edit fs-4 me-2'></i> Edit Menu: <span class='text-dark ms-1'>" + menu.title + "</span>";
            document.getElementById('btnReset').classList.remove('d-none');

            let url = "{{ route('admin.settings.menus.update', ':id') }}".replace(':id', menu.id);
            document.getElementById('menuForm').action = url;
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('title').value = menu.title;
            document.getElementById('slug').value = menu.slug;
            document.getElementById('icon').value = menu.icon || '';
            document.getElementById('route_name').value = menu.route || '';
            document.getElementById('urutan').value = menu.urutan;
            document.getElementById('parent_id').value = menu.parent_id || '';
            document.getElementById('color').value = menu.color || 'normal';

            if (menu.icon) {
                document.getElementById('iconPreviewAddon').innerHTML = `<i class='${menu.icon} fs-5'></i>`;
            } else {
                document.getElementById('iconPreviewAddon').innerHTML = `<i class='bx bx-cube fs-5'></i>`;
            }

            document.getElementById('is_header').checked = menu.is_header == 1;
            toggleHeaderFields();

            document.querySelectorAll('.role-check').forEach(el => el.checked = false);
            roles.forEach(roleName => {
                let chk = document.querySelector(`input[value="${roleName}"]`);
                if (chk) chk.checked = true;
            });

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function resetForm() {
            document.getElementById('formTitle').innerHTML = "<i class='bx bx-plus-circle fs-4 me-2'></i> Tambah Menu Baru";
            document.getElementById('btnReset').classList.add('d-none');
            document.getElementById('menuForm').action = "{{ route('admin.settings.menus.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('menuForm').reset();
            document.getElementById('iconPreviewAddon').innerHTML = "<i class='bx bx-cube fs-5'></i>";
            toggleHeaderFields();
        }

        function toggleIconField() {
            const parentId = document.getElementById('parent_id').value;
            const iconField = document.querySelector('#icon').closest('.mb-4');
            const isHeader = document.getElementById('is_header').checked;
            if (parentId || isHeader) {
                iconField.style.display = 'none';
                document.getElementById('icon').value = '';
            } else {
                iconField.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('parent_id').addEventListener('change', toggleIconField);
            toggleIconField();
        });
    </script>
@endsection
