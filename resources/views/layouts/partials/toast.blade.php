@php
    $toastTypes = [
        'success' => ['class' => 'bg-success', 'title' => 'Sukses', 'icon' => 'bx bx-check-circle text-success'],
        'danger'  => ['class' => 'bg-danger', 'title' => 'Error',  'icon' => 'bx bx-error-circle text-danger'],
        'dark'    => ['class' => 'bg-dark',   'title' => 'Info',   'icon' => 'bx bx-bell text-white'],
    ];
@endphp

@foreach ($toastTypes as $key => $config)
    @if (session($key))
        <div class="bs-toast toast align-items-center {{ $config['class'] }} position-fixed top-0 end-0 m-3"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
            data-bs-autohide="true"
            data-bs-delay="3000"> {{-- 3 detik --}}
            
            <div class="toast-header">
                <i class="icon-base {{ $config['icon'] }} me-2 text-white"></i>
                <div class="me-auto fw-medium">{{ $config['title'] }}</div>
                <small>Sekarang</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-white">
                {{ session($key) }}
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            toastElList.map(function (toastEl) {
                var toast = new bootstrap.Toast(toastEl)
                toast.show()
            })
        });
    </script>

@endforeach
