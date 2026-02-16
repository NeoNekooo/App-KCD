@php
    $level = $level ?? 0;
    $paddingLeft = 16 + ($level * 30);
@endphp

<tr class="{{ $menu->is_header ? 'table-secondary' : '' }}">
    <td style="padding-left: {{ $paddingLeft }}px;">
        @if($menu->is_header)
            <span class="badge bg-dark fw-bold">HEADER</span> 
            <span class="fw-bold ms-2 text-uppercase">{{ $menu->title }}</span>
        @else
            <div class="d-flex align-items-center">
                @if($level > 0)
                    <i class="bx bx-subdirectory-right me-2 text-muted"></i> 
                @endif
                <i class="{{ $menu->icon }} fs-4 me-2 text-primary"></i> 
                <span class="fw-bold text-dark">{{ $menu->title }}</span>
            </div>
        @endif
        <div class="small text-muted mt-1 ms-1">Urutan: {{ $menu->urutan }}</div>
    </td>
    <td>
        @if(!$menu->is_header)
            <small class="d-block text-muted">Slug: {{ $menu->slug }}</small>
            <small class="d-block text-muted">Route: <span class="text-primary">{{ $menu->route ?? '-' }}</span></small>
        @else
            <small class="text-muted">-</small>
        @endif
    </td>
    <td style="white-space: normal; max-width: 250px;">
        @php $myRoles = $accesses[$menu->id] ?? []; @endphp
        @foreach($myRoles as $r)
            <span class="badge bg-label-primary mb-1">{{ $r }}</span>
        @endforeach
    </td>
    <td class="text-end">
        <button class="btn btn-sm btn-icon btn-label-primary" onclick='editMenu(@json($menu), @json($myRoles))'>
            <i class="bx bx-edit-alt"></i>
        </button>
        <form action="{{ route('admin.settings.menus.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-icon btn-label-danger"><i class="bx bx-trash"></i></button>
        </form>
    </td>
</tr>

@if($menu->childrenRecursive->count() > 0)
    @foreach($menu->childrenRecursive as $child)
        @include('admin.settings.menu._menu_item_row', ['menu' => $child, 'accesses' => $accesses, 'level' => $level + 1])
    @endforeach
@endif
