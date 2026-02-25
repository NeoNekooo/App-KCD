@extends('layouts.admin')

@section('content')
    <style>
        /* Modern Card Styling */
        .card {
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        /* Styling Visual Icon Picker */
        .icon-option {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border: 2px solid transparent;
            border-radius: 12px;
            background-color: #f8f9fa;
        }

        .icon-option:hover {
            background-color: #e7e7ff;
            transform: translateY(-3px);
            border-color: #696cff;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.15);
        }

        /* Styling Role Chips */
        .role-label {
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
            font-weight: 500;
            border-width: 1.5px;
        }

        .role-label:hover {
            background-color: rgba(105, 108, 255, 0.1);
        }

        .btn-check:checked+.btn-outline-primary {
            background-color: #696cff;
            color: white;
            border-color: #696cff;
            box-shadow: 0 4px 8px rgba(105, 108, 255, 0.3);
            transform: translateY(-1px);
        }

        /* Custom Switch Box */
        .switch-box {
            background-color: #f8f9fa;
            border: 1px solid #eaeaec;
            border-left: 4px solid #696cff;
            transition: all 0.3s ease;
        }

        .switch-box:hover {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Table Enhancements */
        .table> :not(caption)>*>* {
            padding: 1rem 1.25rem;
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
                <div class="card shadow-sm border-0 sticky-top" style="top: 80px; z-index: 10;">
                    <div
                        class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom pt-4 pb-3">
                        <h5 class="mb-0 text-primary fw-bold d-flex align-items-center" id="formTitle">
                            <i class='bx bx-plus-circle fs-4 me-2'></i> Tambah Menu Baru
                        </h5>
                        <button type="button" class="btn btn-sm btn-light text-danger fw-bold d-none rounded-pill px-3"
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
                                <label class="form-label fw-semibold text-dark">Judul Menu <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-text"></i></span>
                                    <input type="text" class="form-control" name="title" id="title" required
                                        placeholder="Contoh: Data Pegawai" onkeyup="generateSlug()">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Slug (ID Sistem) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light"><i class="bx bx-link"></i></span>
                                    <input type="text" class="form-control bg-light" name="slug" id="slug"
                                        required readonly placeholder="terisi-otomatis">
                                </div>
                            </div>

                            {{-- 2. HEADER TOGGLE --}}
                            <div class="mb-4">
                                <div class="form-check form-switch p-3 rounded switch-box d-flex align-items-start gap-3">
                                    <input class="form-check-input mt-1 ms-0" type="checkbox" name="is_header"
                                        id="is_header" onchange="toggleHeaderFields()"
                                        style="width: 2.5rem; height: 1.25rem; cursor: pointer;">
                                    <div class="ms-2">
                                        <label class="form-check-label fw-bold text-dark mb-1" for="is_header"
                                            style="cursor: pointer;">Jadikan Judul Pembatas (Header)</label>
                                        <p class="text-muted small mb-0">Jika aktif, menu ini hanya berfungsi sebagai
                                            tulisan pemisah (Misal: "DATA MASTER").</p>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. FIELD REGULAR (Icon, Route, Parent) --}}
                            <div id="regularFields">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">Route Laravel</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-navigation"></i></span>
                                        <input type="text" class="form-control font-monospace text-primary"
                                            name="route_name" id="route_name" placeholder="admin.dashboard">
                                    </div>
                                    <div class="form-text text-muted mt-2"><i class="bx bx-info-circle"></i> Kosongkan jika
                                        ini menu induk yang punya submenu.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">Induk Menu (Parent)</label>
                                    <select class="form-select" name="parent_id" id="parent_id">
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
                                <label class="form-label fw-semibold text-dark">Icon Menu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-primary" id="iconPreviewAddon"><i
                                            class='bx bx-cube fs-5'></i></span>
                                    <input type="text" class="form-control" name="icon" id="icon"
                                        placeholder="Pilih icon..." readonly onclick="openIconModal()"
                                        style="cursor: pointer; background-color: #fff;">
                                    <button class="btn btn-primary px-4" type="button"
                                        onclick="openIconModal()">Pilih</button>
                                </div>
                            </div>

                            {{-- 4. URUTAN --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Urutan Tampil</label>
                                <input type="number" class="form-control" name="urutan" id="urutan" value="1"
                                    min="1">
                            </div>

                            {{-- 5. HAK AKSES (CHIPS) --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark d-block mb-2"><i
                                        class="bx bx-shield-quarter text-primary"></i> Siapa yang boleh akses?</label>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach ($roles as $role)
                                        <div>
                                            <input type="checkbox" class="btn-check role-check" name="roles[]"
                                                id="role_{{ Str::slug($role) }}" value="{{ $role }}"
                                                autocomplete="off">
                                            <label class="btn btn-outline-primary rounded-pill role-label px-3 py-1"
                                                for="role_{{ Str::slug($role) }}">
                                                {{ $role }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="my-4">

                            <button type="submit"
                                class="btn btn-primary w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class='bx bx-save fs-5'></i> Simpan Konfigurasi Menu
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: TABEL DATA --}}
            <div class="col-lg-8 col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class='bx bx-list-ul text-primary fs-4 me-2'></i> Struktur Menu Aplikasi Saat Ini
                        </h5>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold text-dark">Menu</th>
                                    <th class="fw-semibold text-dark">Detail</th>
                                    <th class="fw-semibold text-dark">Akses Role</th>
                                    <th class="text-end fw-semibold text-dark" style="white-space: nowrap;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($menus as $menu)
                                    @include('admin.settings.menu._menu_item_row', [
                                        'menu' => $menu,
                                        'accesses' => $accesses,
                                        'level' => 0,
                                    ])
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bx bx-menu-alt-left fs-1 text-light mb-3"></i>
                                            <br>Belum ada menu yang dikonfigurasi.
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
