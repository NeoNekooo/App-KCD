@extends('layouts.admin')

@section('content')
    <style>
        /* Styling Visual Icon Picker */
        .icon-option {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .icon-option:hover {
            background-color: #e7e7ff;
            transform: translateY(-2px);
            border-color: #696cff;
        }

        /* Styling Role Chips */
        .role-label {
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
        }

        .btn-check:checked+.btn-outline-primary {
            background-color: #696cff;
            color: white;
            border-color: #696cff;
            box-shadow: 0 4px 6px rgba(105, 108, 255, 0.4);
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Manajemen Menu</h4>

        <div class="row">
            {{-- KOLOM KIRI: FORM INPUT --}}
            <div class="col-lg-4 col-md-5 mb-4">
                <div class="card sticky-top" style="top: 80px; z-index: 10;">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom py-3">
                        <h5 class="mb-0 text-primary fw-bold" id="formTitle"><i class='bx bx-plus-circle'></i> Tambah Menu
                        </h5>
                        <button type="button" class="btn btn-sm btn-label-secondary d-none" id="btnReset"
                            onclick="resetForm()">
                            <i class='bx bx-x'></i> Batal
                        </button>
                    </div>
                    <div class="card-body mt-3">
                        <form id="menuForm" action="{{ route('admin.settings.menus.store') }}" method="POST">
                            @csrf
                            <div id="methodField"></div>

                            {{-- 1. JUDUL & SLUG --}}
                            <div class="mb-3">
                                <label class="form-label">Judul Menu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control fw-bold" name="title" id="title" required
                                    placeholder="Contoh: Data Pegawai" onkeyup="generateSlug()">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Slug (ID Sistem) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" name="slug" id="slug" required
                                    readonly placeholder="terisi-otomatis">
                            </div>

                            {{-- 2. HEADER TOGGLE --}}
                            <div class="mb-3">
                                <div class="form-check form-switch p-3 border rounded bg-light">
                                    <input class="form-check-input" type="checkbox" name="is_header" id="is_header"
                                        onchange="toggleHeaderFields()">
                                    <label class="form-check-label fw-bold" for="is_header">Jadikan Judul Pembatas
                                        (Header)?</label>
                                    <div class="form-text mt-1" style="font-size: 0.75rem">
                                        Jika aktif, menu ini hanya jadi tulisan pemisah (seperti "KEPEGAWAIAN").
                                    </div>
                                </div>
                            </div>

                            {{-- 3. FIELD REGULAR (Icon, Route, Parent) --}}
                            <div id="regularFields">
                                <div class="mb-3">
                                    <label class="form-label">Icon Menu</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-primary" id="iconPreviewAddon"><i
                                                class='bx bx-cube'></i></span>
                                        <input type="text" class="form-control" name="icon" id="icon"
                                            placeholder="Pilih icon..." readonly onclick="openIconModal()"
                                            style="cursor: pointer;">
                                        <button class="btn btn-outline-primary" type="button"
                                            onclick="openIconModal()">Pilih</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Route Laravel</label>
                                    <input type="text" class="form-control font-monospace" name="route_name"
                                        id="route_name" placeholder="admin.dashboard">
                                    <div class="form-text">Kosongkan jika ini menu induk yang punya submenu.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Induk Menu (Parent)</label>
                                    <select class="form-select" name="parent_id" id="parent_id">
                                        <option value="">-- Menu Utama (Paling Luar) --</option>
                                        @foreach ($parents as $p)
                                            <option value="{{ $p->id }}" data-depth="{{ $p->depth }}">
                                                @for ($i = 0; $i < $p->depth; $i++)
                                                    &nbsp;&nbsp;&nbsp;
                                                @endfor
                                                {{ $p->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- 4. URUTAN --}}
                            <div class="mb-3">
                                <label class="form-label">Urutan Tampil</label>
                                <input type="number" class="form-control" name="urutan" id="urutan" value="1"
                                    min="1">
                            </div>

                            {{-- 5. HAK AKSES (CHIPS) --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-2">Siapa yang boleh akses?</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($roles as $role)
                                        <input type="checkbox" class="btn-check role-check" name="roles[]"
                                            id="role_{{ Str::slug($role) }}" value="{{ $role }}" autocomplete="off">
                                        <label class="btn btn-sm btn-outline-primary rounded-pill role-label px-3"
                                            for="role_{{ Str::slug($role) }}">
                                            {{ $role }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                                <i class='bx bx-save'></i> Simpan Menu
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: TABEL DATA --}}
            <div class="col-lg-8 col-md-7">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white"><i class='bx bx-list-ul'></i> Struktur Menu Aplikasi</h5>
                    </div>
                    {{-- FIX BUG 100%: Class text-nowrap dihapus di sini --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Menu</th>
                                    <th>Detail</th>
                                    <th>Akses Role</th>
                                    {{-- Kita pastikan khusus tombol aksi tidak turun ke bawah --}}
                                    <th class="text-end" style="white-space: nowrap;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menus as $menu)
                                    @include('admin.settings.menu._menu_item_row', [
                                        'menu' => $menu,
                                        'accesses' => $accesses,
                                        'level' => 0,
                                    ])
                                @endforeach
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
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Pilih Icon Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="row g-3" id="iconGrid"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- ICON LIST ---
        const icons = [
            'bx-home-circle', 'bx-user', 'bx-group', 'bx-grid-alt', 'bx-folder',
            'bx-file', 'bx-envelope', 'bx-cog', 'bx-shield', 'bx-lock',
            'bx-bar-chart-alt-2', 'bx-pie-chart-alt', 'bx-briefcase-alt-2', 'bx-id-card',
            'bx-buildings', 'bx-check-shield', 'bx-paper-plane', 'bx-wallet', 'bx-medal',
            'bx-bell', 'bx-calendar', 'bx-star', 'bx-archive', 'bx-trash',
            'bx-printer', 'bx-search', 'bx-filter', 'bx-list-ul', 'bx-menu'
        ];

        const iconGrid = document.getElementById('iconGrid');
        icons.forEach(icon => {
            const col = document.createElement('div');
            col.className = 'col-4 col-sm-3 col-md-2 text-center';
            col.innerHTML = `
            <div class="icon-option p-3 rounded border" onclick="selectIcon('bx ${icon}')">
                <i class='bx ${icon} fs-2 mb-2'></i>
                <div style="font-size: 10px;" class="text-truncate">${icon}</div>
            </div>
        `;
            iconGrid.appendChild(col);
        });

        function openIconModal() {
            new bootstrap.Modal(document.getElementById('iconModal')).show();
        }

        function selectIcon(iconClass) {
            document.getElementById('icon').value = iconClass;
            document.getElementById('iconPreviewAddon').innerHTML = `<i class='${iconClass}'></i>`;
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
            document.getElementById('formTitle').innerHTML = "<i class='bx bx-edit'></i> Edit Menu: " + menu.title;
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
                document.getElementById('iconPreviewAddon').innerHTML = `<i class='${menu.icon}'></i>`;
            } else {
                document.getElementById('iconPreviewAddon').innerHTML = `<i class='bx bx-cube'></i>`;
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
            document.getElementById('formTitle').innerHTML = "<i class='bx bx-plus-circle'></i> Tambah Menu";
            document.getElementById('btnReset').classList.add('d-none');
            document.getElementById('menuForm').action = "{{ route('admin.settings.menus.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('menuForm').reset();
            document.getElementById('iconPreviewAddon').innerHTML = "<i class='bx bx-cube'></i>";
            toggleHeaderFields();
        }

        function toggleIconField() {
            const parentId = document.getElementById('parent_id').value;
            const iconField = document.querySelector('#icon').closest('.mb-3');
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
