@php
    use App\Models\Gtk;
    use App\Models\Siswa;
    use Illuminate\Support\Facades\Storage;

    $profile = null;
    $profileType = null; // 'gtk' | 'siswa'

    if (auth()->check()) {
        if (session('ptk_id')) {
            $profile = Gtk::where('ptk_id', session('ptk_id'))->first();
            $profileType = 'gtk';
        } elseif (session('peserta_didik_id')) {
            $profile = Siswa::where('peserta_didik_id', session('peserta_didik_id'))->first();
            $profileType = 'siswa';
        }
    }

    $foto = $profile?->foto;
    $nama = $profile?->nama ?? (Auth::user()->nama ?? 'User');
@endphp

<nav class="container layout-navbar navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Search..."
                    aria-label="Search..." />
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @if ($foto && Storage::disk('public')->exists($foto))
                            <img src="{{ asset('storage/' . $foto) }}" class="rounded-circle"
                                style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($nama) }}&background=random&color=ffffff&size=100"
                                class="rounded-circle" style="width:100%;height:100%;object-fit:cover;">
                        @endif
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        @if ($foto && Storage::disk('public')->exists($foto))
                                            <img src="{{ asset('storage/' . $foto) }}" class="rounded-circle"
                                                style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($nama) }}&background=random&color=ffffff&size=100"
                                                class="rounded-circle" style="width:100%;height:100%;object-fit:cover;">
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ $nama }}</span>
                                    <small class="text-muted">
                                        @if (!empty(session('sub_role')))
                                            {{ session('role') }} - {{ session('sub_role') }}
                                        @else
                                            {{ session('role') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="
                                 @if (session('ptk_id')) {{ route('admin.personal.gtk.profil') }}
                                 @elseif (session('peserta_didik_id'))
                                     {{ route('admin.personal.siswa.profil') }}
                                 @else
                                     # @endif
                            ">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bx bx-cog me-2"></i>
                            <span class="align-middle">Settings</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Log Out</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
