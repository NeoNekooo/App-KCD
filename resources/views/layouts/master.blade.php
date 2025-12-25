<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Website Sekolah')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        body { font-family: sans-serif; padding-top: 76px; background-color: #f8f9fa; }
        .section-padding { padding: 80px 0; }
        .img-cover { object-fit: cover; width: 100%; height: 100%; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    @include('partials.web.navbar')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('partials.web.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>