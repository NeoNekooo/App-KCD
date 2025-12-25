<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Data {{ $type }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden transform transition-all hover:scale-[1.01]">
        <div class="bg-blue-600 px-6 py-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3 text-white text-2xl border-2 border-white/30">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2 class="text-white text-xl font-bold tracking-wide">Portal Verifikasi Data</h2>
                <p class="text-blue-100 text-sm mt-1">Sistem Informasi Akademik</p>
            </div>
        </div>

        <div class="p-8">
            <div class="text-center mb-6">
                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full uppercase tracking-wider mb-2">
                    {{ $type }}
                </span>
                <h3 class="text-slate-800 text-lg font-semibold">{{ $name }}</h3>
                <p class="text-slate-500 text-sm">Silakan masukkan kata sandi akun Anda untuk melihat data lengkap.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('scan.verify') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="type" value="{{ $type }}">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400"></i>
                        </div>
                        <input type="password" name="password" required
                            class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm"
                            placeholder="Masukkan password akun...">
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Buka Data Lengkap <i class="fas fa-arrow-right ml-2 mt-0.5"></i>
                </button>
            </form>
        </div>

        <div class="bg-slate-50 px-6 py-4 text-center border-t border-slate-100">
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} Sistem Informasi Sekolah. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
