<style>
    /* Styling for Tree View Connections */
    .tree-connector {
        position: relative;
    }

    .tree-connector::before {
        content: '';
        position: absolute;
        top: -1.25rem;
        left: -1rem;
        bottom: 50%;
        width: 1px;
        background-color: #d9dee3;
        /* Bootstrap light border color */
    }

    .tree-connector::after {
        content: '';
        position: absolute;
        top: 50%;
        left: -1rem;
        width: 1rem;
        height: 1px;
        background-color: #d9dee3;
    }

    .tree-connector.last-child::before {
        height: calc(50% + 1.25rem);
        bottom: auto;
    }
</style>

@php
    $level = $level ?? 0;
    $paddingLeft = $level == 0 ? 1 : 1 + $level * 1.5;
@endphp

<tr class="{{ $menu->is_header ? 'table-secondary border-transparent' : '' }}">
    <td style="padding-left: {{ $paddingLeft }}rem; position: relative;">
        @if ($menu->is_header)
            <div class="d-flex align-items-center mt-2">
                <span class="badge bg-dark rounded-pill px-3 py-2 fw-bold shadow-sm"
                    style="font-size: 0.7rem; letter-spacing: 0.5px;">HEADER</span>
                <span class="fw-bold ms-3 text-uppercase text-dark"
                    style="letter-spacing: 1px; font-size: 0.85rem;">{{ $menu->title }}</span>
            </div>
        @else
            <div
                class="d-flex align-items-center position-relative {{ $level > 0 ? 'tree-connector' : '' }} {{ isset($isLastChild) && $isLastChild ? 'last-child' : '' }}">
                <div class="avatar avatar-sm me-3 flex-shrink-0">
                    <span
                        class="avatar-initial rounded bg-label-{{ $level == 0 ? 'primary' : 'secondary' }} text-{{ $level == 0 ? 'primary' : 'secondary' }}">
                        <i class="{{ $menu->icon }} fs-5"></i>
                    </span>
                </div>
                <div>
                    <span class="fw-semibold text-dark" style="font-size: 0.95rem;">{{ $menu->title }}</span>
                    @if ($menu->urutan)
                        <div class="small fw-semibold mt-1" style="color: #a1acb8; font-size: 0.75rem;">
                            <i class='bx bx-sort-asc'></i> Urutan: {{ $menu->urutan }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </td>
    <td>
        @if (!$menu->is_header)
            <div class="small text-muted mb-1 d-flex align-items-center">
                <i class='bx bx-link me-1'></i> {{ $menu->slug }}
            </div>
            @if ($menu->route)
                <div class="small mb-1 d-flex align-items-center">
                    <span class="badge bg-label-info rounded-pill font-monospace" style="font-size: 0.7rem;">
                        <i class='bx bx-navigation me-1'></i>{{ $menu->route }}
                    </span>
                </div>
            @endif
        @else
            <small class="text-muted fst-italic">-</small>
        @endif
    </td>
    <td style="white-space: normal; max-width: 250px;">
        @php $myRoles = $accesses[$menu->id] ?? []; @endphp
        <div class="d-flex flex-wrap gap-1">
            @foreach ($myRoles as $r)
                <span class="badge bg-label-primary shadow-sm" style="font-size: 0.7rem;"><i
                        class='bx bx-user-circle me-1'></i>{{ $r }}</span>
            @endforeach
        </div>
    </td>
    <td class="text-end">
        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-sm btn-icon btn-label-primary rounded-circle shadow-sm transition-all"
                onclick='editMenu(@json($menu), @json($myRoles))' data-bs-toggle="tooltip"
                title="Edit Menu">
                <i class="bx bx-edit-alt"></i>
            </button>
            <form action="{{ route('admin.settings.menus.destroy', $menu->id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini? Semua submenu (jika ada) juga mungkin akan terpengaruh.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-icon btn-label-danger rounded-circle shadow-sm transition-all"
                    data-bs-toggle="tooltip" title="Hapus Menu"><i class="bx bx-trash"></i></button>
            </form>
        </div>
    </td>
</tr>

@if ($menu->childrenRecursive->count() > 0)
    @foreach ($menu->childrenRecursive as $child)
        @include('admin.settings.menu._menu_item_row', [
            'menu' => $child,
            'accesses' => $accesses,
            'level' => $level + 1,
            'isLastChild' => $loop->last,
        ])
    @endforeach
@endif
