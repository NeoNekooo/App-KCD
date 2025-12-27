<div class="col-6 col-md-4 col-lg-3">
    <div class="card h-100 shadow-sm position-relative group-hover border-0">
        {{-- Wrapper Gambar/Video --}}
        <div class="ratio ratio-4x3 bg-light overflow-hidden rounded">
            @if($item->jenis == 'foto')
                {{-- Tampilkan Foto --}}
                <a href="{{ asset('storage/galeris/items/'.$item->file) }}" target="_blank">
                    <img src="{{ asset('storage/galeris/items/'.$item->file) }}" 
                         class="object-fit-cover w-100 h-100 transition-transform" 
                         style="transition: transform 0.3s;"
                         onmouseover="this.style.transform='scale(1.1)'"
                         onmouseout="this.style.transform='scale(1)'"
                         alt="Foto">
                </a>
            @else
                {{-- Tampilkan Video --}}
                <video src="{{ asset('storage/galeris/items/'.$item->file) }}" 
                       class="object-fit-cover w-100 h-100" controls></video>
                <div class="position-absolute top-0 start-0 m-2 badge bg-black bg-opacity-75">
                    <i class='bx bx-play-circle fs-6 align-middle'></i> Video
                </div>
            @endif
        </div>
        
        {{-- Tombol Hapus Item (Pojok Kanan Atas) --}}
        <div class="position-absolute top-0 end-0 m-2">
            <form action="{{ route('admin.landing.galeri.item.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
                @csrf 
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger btn-icon shadow-sm rounded-circle" title="Hapus">
                    <i class='bx bx-x fs-5'></i>
                </button>
            </form>
        </div>
    </div>
</div>