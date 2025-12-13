<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets/') }}" 
  data-template="vertical-menu-template-free"
  data-layout="wide"

>
  <head>
    {{-- ... (Isi <head> tidak ada perubahan) ... --}}
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Dashboard Admin Sekolah</title>

    {{-- Tailwind + Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
  </head>
  <body>

    <!-- Toast -->
        
     @include('layouts.partials.toast')

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container ">
            @include('layouts.partials.sidebar')
            <div class="layout-page ">
                @include('layouts.partials.topbar')
                <div class="content-wrapper">
                    <div class="container flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    @include('layouts.partials.footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                const successToast = document.getElementById('successToast');
                const successToastBody = document.getElementById('successToastBody');
                successToastBody.innerHTML = "{{ session('success') }}";
                const toast = new bootstrap.Toast(successToast);
                toast.show();
            @endif

            @if(session('error'))
                const errorToast = document.getElementById('errorToast');
                const errorToastBody = document.getElementById('errorToastBody');
                errorToastBody.innerHTML = "{{ session('error') }}";
                const toast = new bootstrap.Toast(errorToast);
                toast.show();
            @endif

            {{-- Logika untuk Toast Info - BARU --}}
            @if(session('info'))
                const infoToast = document.getElementById('infoToast');
                const infoToastBody = document.getElementById('infoToastBody');
                infoToastBody.innerHTML = "{{ session('info') }}";
                const toast = new bootstrap.Toast(infoToast);
                toast.show();
            @endif
          });
          </script>
          
    @stack('scripts')
    @vite(['resources/js/app.js'])
  </body>
</html>

