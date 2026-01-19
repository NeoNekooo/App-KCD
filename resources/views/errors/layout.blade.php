<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - Sistem KCD</title>

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

    <style>
        body {
            /* Background Kantor Modern + Overlay */
            background: linear-gradient(rgba(255, 255, 255, 0.85), rgba(240, 242, 245, 0.9)),
                        url('https://images.unsplash.com/photo-1497215728101-856f4ea42174?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Public Sans', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0;
        }

        .container-xxl {
            max-width: 500px;
            width: 100%;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        /* CARD GLASSMORPHISM */
        .misc-wrapper {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(67, 89, 113, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transform: translateY(30px);
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0;
        }

        /* ANIMASI SVG X (CROSS) */
        .forbidden-icon {
            width: 100px; height: 100px; margin: 0 auto 1.5rem; display: block;
            filter: drop-shadow(0 4px 6px rgba(255, 62, 29, 0.3));
        }
        .circle {
            stroke: #ff3e1d; stroke-width: 4; fill: none;
            stroke-dasharray: 314; stroke-dashoffset: 314;
            animation: drawCircle 1s ease-out forwards;
        }
        .cross {
            stroke: #ff3e1d; stroke-width: 4; stroke-linecap: round;
            stroke-dasharray: 70; stroke-dashoffset: 70;
        }
        .cross-1 { animation: drawLine 0.4s ease-out 0.8s forwards; }
        .cross-2 { animation: drawLine 0.4s ease-out 1.1s forwards; }

        @keyframes drawCircle { to { stroke-dashoffset: 0; } }
        @keyframes drawLine { to { stroke-dashoffset: 0; } }
        @keyframes slideUp { to { transform: translateY(0); opacity: 1; } }

        /* TYPOGRAPHY */
        .error-code {
            font-size: 4.5rem; font-weight: 800;
            background: linear-gradient(45deg, #696cff, #8a8dff);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem; letter-spacing: -2px; line-height: 1;
        }
        .error-title {
            color: #566a7f; font-weight: 700; font-size: 1.4rem;
            margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 1px;
        }
        .error-desc {
            color: #697a8d; font-size: 0.95rem; margin-bottom: 1.5rem;
        }

        /* BOX PESAN ERROR */
        .error-message-box {
            background: rgba(255, 62, 29, 0.1);
            border-left: 4px solid #ff3e1d;
            color: #ff3e1d;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: left;
            display: flex; align-items: center; gap: 12px;
        }

        /* BUTTONS */
        .btn {
            border-radius: 50px; padding: 10px 24px; font-weight: 600;
            font-size: 0.9rem; transition: all 0.3s ease;
        }
        .btn-primary {
            background: #696cff; border: none;
            box-shadow: 0 4px 15px rgba(105, 108, 255, 0.4);
        }
        .btn-primary:hover {
            background: #5f61e6; transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.5);
        }
        .btn-outline-secondary {
            border: 2px solid #b4bdc6; color: #697a8d; background: transparent;
        }
        .btn-outline-secondary:hover {
            border-color: #697a8d; background: #697a8d; color: white;
            transform: translateY(-3px);
        }
        
        /* BACKGROUND DECORATION */
        .shape {
            position: absolute; border-radius: 50%;
            background: linear-gradient(45deg, #696cff, #9b9dff);
            opacity: 0.4; z-index: 1; filter: blur(60px);
        }
        .shape-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .shape-2 { width: 250px; height: 250px; bottom: -50px; right: -50px; background: linear-gradient(45deg, #ff3e1d, #ffab91); }
    </style>
</head>
<body>

    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="container-xxl">
        <div class="misc-wrapper">
            
            <svg class="forbidden-icon" viewBox="0 0 100 100">
                <circle class="circle" cx="50" cy="50" r="45"></circle>
                <line class="cross cross-1" x1="32" y1="32" x2="68" y2="68"></line>
                <line class="cross cross-2" x1="68" y1="32" x2="32" y2="68"></line>
            </svg>

            <h1 class="error-code">@yield('code')</h1>
            <h2 class="error-title">@yield('title')</h2>
            <p class="error-desc">@yield('description')</p>

            <div class="error-message-box">
                <i class='bx bxs-error-alt fs-4'></i>
                <div>
                    @yield('message', 'Terjadi kesalahan pada sistem.')
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <button onclick="history.back()" class="btn btn-outline-secondary">
                    <i class='bx bx-left-arrow-alt me-1'></i> Kembali
                </button>
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class='bx bxs-dashboard me-1'></i> Dashboard
                </a>
            </div>

        </div>
    </div>

</body>
</html>