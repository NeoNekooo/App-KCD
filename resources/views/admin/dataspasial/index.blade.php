@extends('layouts.admin')

@section('content')
    {{-- ================= LIBS & ASSETS ================= --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://ppete2.github.io/Leaflet.PolylineMeasure/Leaflet.PolylineMeasure.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        /* --- MAP CONTAINER PREMIUM --- */
        #map-container {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(105, 108, 255, 0.15);
            border: 6px solid #fff;
            height: calc(100vh - 160px);
            min-height: 600px;
            width: 100%;
            background: #f4f5f9;
        }

        #map {
            height: 100%;
            width: 100%;
            z-index: 1;
            font-family: 'Public Sans', sans-serif;
        }

        /* Fullscreen Fix */
        #map-container:fullscreen,
        #map:fullscreen {
            width: 100vw !important;
            height: 100vh !important;
            border-radius: 0 !important;
            border: none !important;
            z-index: 99999 !important;
        }

        #map-container:-webkit-full-screen {
            width: 100%;
            height: 100%;
        }

        /* --- GLASS EFFECT UI --- */
        .glass-ui {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }

        /* --- HUD STATS FLOATING --- */
        .map-stats {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            padding: 10px 24px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 15px;
            pointer-events: auto;
            transition: 0.3s;
        }

        .map-stats:hover {
            transform: translateX(-50%) translateY(-5px);
            box-shadow: 0 15px 30px rgba(105, 108, 255, 0.2);
        }

        .stats-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #696cff, #4e51e0);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        /* --- SMART FAB MENU --- */
        .fab-container {
            position: absolute;
            top: 25px;
            right: 25px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            align-items: end;
            gap: 12px;
            pointer-events: auto;
        }

        .btn-fab {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #566a7f;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .btn-fab:hover {
            transform: scale(1.1) translateY(-2px);
            color: #696cff;
        }

        .main-fab {
            background: linear-gradient(135deg, #696cff, #4e51e0);
            color: #fff;
            font-size: 1.5rem;
            z-index: 2001;
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.4);
        }

        .main-fab:hover {
            color: #fff;
        }

        .main-fab.open i {
            transform: rotate(135deg);
        }

        .main-fab i {
            transition: transform 0.4s ease;
        }

        .btn-fab.active-measure {
            background-color: #ff3e1d !important;
            color: white !important;
            animation: pulse-red 1.5s infinite;
        }

        /* Tooltip FAB */
        .btn-fab[data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            right: 60px;
            top: 50%;
            transform: translateY(-50%);
            background: #2b2c40;
            color: #fff;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            z-index: 3000;
            animation: fadeInRight 0.2s;
        }

        .fab-options {
            display: flex;
            flex-direction: column;
            align-items: end;
            gap: 10px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-15px) scale(0.95);
            transition: all 0.3s ease;
        }

        .fab-options.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .search-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-input {
            width: 0;
            opacity: 0;
            padding: 0;
            height: 48px;
            border: none;
            border-radius: 14px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            outline: none;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .search-input.active {
            width: 250px;
            opacity: 1;
            padding: 0 20px;
            margin-right: 5px;
        }

        /* --- SCHOOL DETAIL CARD (REDESIGNED) --- */
        .school-detail-card {
            position: absolute;
            top: 20px;
            left: 20px;
            bottom: 20px;
            width: 380px;
            z-index: 3000;
            border-radius: 20px;
            box-shadow: 15px 0 40px rgba(0, 0, 0, 0.12);
            transform: translateX(-120%);
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .school-detail-card.active {
            transform: translateX(0);
        }

        .card-header-img {
            height: 160px;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 20px;
            background: linear-gradient(135deg, #696cff 0%, #3f4191 100%);
            /* Default */
        }

        .bg-header-sma {
            background: linear-gradient(135deg, #2A81CB 0%, #1a5282 100%);
        }

        .bg-header-smk {
            background: linear-gradient(135deg, #CB2B3E 0%, #8a1d2a 100%);
        }

        .bg-header-slb {
            background: linear-gradient(135deg, #9C2BC3 0%, #6b1d86 100%);
        }

        .bg-header-other {
            background: linear-gradient(135deg, #2AAD27 0%, #1d771b 100%);
        }

        .btn-close-card {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(4px);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .btn-close-card:hover {
            background: rgba(255, 62, 29, 0.9);
            transform: rotate(90deg);
        }

        .school-badge {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(5px);
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 800;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .card-content {
            padding: 25px;
            overflow-y: auto;
            flex-grow: 1;
        }

        .school-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #32475c;
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 14px;
            margin-bottom: 12px;
            transition: 0.3s;
            border: 1px solid transparent;
        }

        .info-box:hover {
            background: #fff;
            border-color: #696cff;
            box-shadow: 0 4px 15px rgba(105, 108, 255, 0.08);
        }

        .info-box i {
            font-size: 1.4rem;
            color: #696cff;
            margin-top: 2px;
        }

        .info-box .label {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #a1acb8;
            display: block;
            margin-bottom: 2px;
        }

        .info-box .value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #435971;
        }

        /* Nearest List */
        .nearest-list {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px dashed #ebeef0;
        }

        .nearest-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 15px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e9ecef;
            margin-bottom: 10px;
            cursor: pointer;
            transition: 0.3s;
        }

        .nearest-item:hover {
            border-color: #696cff;
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.1);
            transform: translateY(-2px);
        }

        .nearest-info h6 {
            margin: 0 0 3px 0;
            font-size: 0.85rem;
            font-weight: 700;
            color: #444;
        }

        .nearest-info span {
            font-size: 0.7rem;
            color: #888;
            background: #f0f2f4;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .nearest-dist {
            font-size: 0.75rem;
            font-weight: 800;
            color: #696cff;
            background: #e7e7ff;
            padding: 5px 10px;
            border-radius: 8px;
        }

        /* --- MODAL CUSTOM --- */
        .map-overlay-modal {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(43, 44, 64, 0.5);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .map-modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: zoomIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* --- LEGEND --- */
        .legend-card {
            padding: 15px 20px;
            border-radius: 16px;
            width: 220px;
            display: none;
            margin-right: 15px;
        }

        .legend-card.show {
            display: block;
            animation: fadeInRight 0.3s;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #566a7f;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            margin-right: 12px;
        }

        .bg-legend-sma {
            background: #2A81CB;
        }

        .bg-legend-smk {
            background: #CB2B3E;
        }

        .bg-legend-slb {
            background: #9C2BC3;
        }

        .bg-legend-other {
            background: #2AAD27;
        }

        /* Animations */
        @keyframes pulse-red {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.7);
            }

            70% {
                box-shadow: 0 0 0 12px rgba(255, 62, 29, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translate(15px, 0);
            }

            to {
                opacity: 1;
                transform: translate(0, 0);
            }
        }

        /* Hide Defaults */
        .leaflet-control-polyline-measure,
        .leaflet-control-fullscreen-button,
        .leaflet-control-layers {
            display: none !important;
        }

        .leaflet-control-zoom {
            margin-top: 25px !important;
            margin-left: 25px !important;
            border: none !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            border-radius: 12px !important;
            overflow: hidden;
        }

        .leaflet-control-zoom a {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(5px);
            color: #566a7f !important;
            transition: 0.2s;
        }

        .leaflet-control-zoom a:hover {
            background: #696cff !important;
            color: white !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 animate__animated animate__fadeInDown">
            <span class="text-muted fw-light">Monitoring /</span> Peta Digital Sekolah
        </h4>

        <div id="map-container" class="animate__animated animate__zoomIn">
            <div id="map"></div>

            {{-- SCHOOL DETAIL CARD (MODERN) --}}
            <div class="school-detail-card glass-ui" id="schoolDetailCard">
                <div class="card-header-img" id="cardHeader">
                    <button class="btn-close-card" id="btnCloseCard"><i class='bx bx-x fs-5'></i></button>
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <span class="school-badge" id="cardJenjang">SMA</span>
                        <span class="badge bg-white text-dark rounded-pill px-3 py-2 shadow-sm"
                            id="cardStatus">Negeri</span>
                    </div>
                </div>

                <div class="card-content">
                    <div id="cardName" class="school-title">Pilih Sekolah</div>

                    <div class="info-box">
                        <i class='bx bx-fingerprint'></i>
                        <div><span class="label">Nomor Pokok (NPSN)</span><span class="value font-monospace"
                                id="cardNpsn">-</span></div>
                    </div>
                    <div class="info-box">
                        <i class='bx bx-map-pin'></i>
                        <div><span class="label">Alamat Lengkap</span><span class="value" id="cardAddress">-</span></div>
                    </div>
                    <div class="info-box">
                        <i class='bx bx-buildings'></i>
                        <div><span class="label">Wilayah Kecamatan</span><span class="value" id="cardKecamatan">-</span>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <a href="#" id="btnRouteGoogle" target="_blank"
                            class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">
                            <i class='bx bxs-navigation me-2 fs-5' style="vertical-align: middle;"></i> Navigasi Rute
                        </a>
                    </div>

                    <div class="nearest-list">
                        <h6 class="small fw-bold text-uppercase text-muted mb-3"><i
                                class='bx bx-radar me-1 text-primary'></i> Sekolah Terdekat</h6>
                        <div id="nearestSchoolsContainer"></div>
                    </div>
                </div>
            </div>

            {{-- HUD STATS --}}
            <div class="map-stats glass-ui">
                <div class="stats-icon"><i class='bx bx-buildings'></i></div>
                <div class="d-flex flex-column" style="line-height: 1.2;">
                    <small class="text-muted fw-bold text-uppercase"
                        style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Terdata</small>
                    <span id="statsText" style="color: #32475c; font-weight: 800; font-size: 1.1rem;">Memuat...</span>
                </div>
            </div>

            {{-- FAB MENU --}}
            <div class="fab-container" id="customFab">
                <button class="btn-fab main-fab" id="btnToggleMenu" data-tooltip="Menu Utama">
                    <i class='bx bx-plus'></i>
                </button>

                <div class="fab-options" id="fabOptions">
                    <div class="search-wrapper">
                        <input type="text" id="searchBox" class="form-control search-input glass-ui"
                            placeholder="Cari nama sekolah / NPSN...">
                        <button class="btn-fab glass-ui" id="btnShowSearch" data-tooltip="Pencarian"><i
                                class='bx bx-search'></i></button>
                    </div>

                    <button class="btn-fab glass-ui" id="btnFullscreenCustom" data-tooltip="Layar Penuh"><i
                            class='bx bx-fullscreen'></i></button>
                    <button class="btn-fab glass-ui text-info" id="btnChangeLayer" data-tooltip="Ubah Tampilan"><i
                            class='bx bx-layer'></i></button>
                    <button class="btn-fab glass-ui text-primary" id="btnShowFilter" data-tooltip="Filter Wilayah"><i
                            class='bx bx-filter-alt'></i></button>
                    <button class="btn-fab glass-ui text-warning" id="btnShowCalculator" data-tooltip="Ukur Jarak"><i
                            class='bx bx-calculator'></i></button>
                    <button class="btn-fab glass-ui" id="btnMeasureToggle" data-tooltip="Penggaris Area"><i
                            class='bx bx-ruler'></i></button>

                    <div class="d-flex align-items-center">
                        <div class="legend-card glass-ui" id="legendBox">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted border-bottom pb-2">Identitas Marker
                            </h6>
                            <div class="legend-item">
                                <div class="legend-color bg-legend-sma shadow-sm"></div> SMA (Sekolah Menengah Atas)
                            </div>
                            <div class="legend-item">
                                <div class="legend-color bg-legend-smk shadow-sm"></div> SMK (Sekolah Menengah Kejuruan)
                            </div>
                            <div class="legend-item">
                                <div class="legend-color bg-legend-slb shadow-sm"></div> SLB (Sekolah Luar Biasa)
                            </div>
                            <div class="legend-item">
                                <div class="legend-color bg-legend-other shadow-sm"></div> Lainnya
                            </div>
                        </div>
                        <button class="btn-fab glass-ui" id="btnToggleLegend" data-tooltip="Info Legenda"><i
                                class='bx bx-palette'></i></button>
                    </div>
                    <button class="btn-fab glass-ui text-danger mt-2" id="btnResetAll"
                        data-tooltip="Reset Kondisi Awal"><i class='bx bx-refresh'></i></button>
                </div>
            </div>

            {{-- MODAL FILTER --}}
            <div id="customFilterModal" class="map-overlay-modal">
                <div class="map-modal-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="m-0 fw-bolder text-primary"><i class='bx bx-filter-alt me-2'></i>Filter Data Peta</h5>
                        <button type="button" class="btn-close" id="btnCloseFilter"></button>
                    </div>
                    <form id="filterForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kabupaten/Kota</label>
                                <select class="form-select border-0 bg-light" id="f_kabupaten">
                                    <option value="">-- Tampilkan Semua --</option>
                                    @foreach ($filter_kabupaten as $kab)
                                        <option value="{{ $kab }}">{{ $kab }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kecamatan</label>
                                <select class="form-select border-0 bg-light" id="f_kecamatan">
                                    <option value="">-- Tampilkan Semua --</option>
                                    @foreach ($filter_kecamatan as $kec)
                                        <option value="{{ $kec }}">{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Jenjang</label>
                                <select class="form-select border-0 bg-light" id="f_jenjang">
                                    <option value="">Semua</option>
                                    @foreach ($filter_jenjang as $jjg)
                                        <option value="{{ $jjg }}">{{ $jjg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                                <select class="form-select border-0 bg-light" id="f_status">
                                    <option value="">Semua</option>
                                    @foreach ($filter_status as $st)
                                        <option value="{{ $st }}">{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-light fw-bold" id="btnModalReset">Reset</button>
                            <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm"
                                id="btnApplyFilter">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL CALCULATOR --}}
            <div id="customDistanceModal" class="map-overlay-modal">
                <div class="map-modal-content">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="m-0 fw-bolder text-warning"><i class='bx bx-calculator me-2'></i>Kalkulator Jarak</h5>
                        <button type="button" class="btn-close" id="btnCloseCalculator"></button>
                    </div>
                    <div class="alert alert-warning bg-label-warning py-2 px-3 mb-4 border-0 rounded-3"
                        style="font-size: 0.8rem;">
                        <i class='bx bx-info-circle me-1'></i> Pilih dua entitas sekolah untuk menghitung jarak udara
                        (garis lurus).
                    </div>
                    <form id="distanceForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">📍 Titik A (Asal)</label>
                            <input class="form-control border-0 bg-light" list="schoolOptions" id="inputSchoolA"
                                placeholder="Ketik nama sekolah...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">🏁 Titik B (Tujuan)</label>
                            <input class="form-control border-0 bg-light" list="schoolOptions" id="inputSchoolB"
                                placeholder="Ketik nama sekolah...">
                        </div>
                        <datalist id="schoolOptions"></datalist>

                        <div id="distanceResult"
                            style="display:none; background: #fff8e1; border-radius: 16px; padding: 20px; margin-top: 20px; border: 2px dashed #ffab00; text-align: center;">
                            <div class="text-warning small text-uppercase fw-bold mb-1">Hasil Perhitungan</div>
                            <div id="distVal"
                                style="font-size: 2rem; font-weight: 900; color: #ff3e1d; line-height: 1;">0 km</div>
                            <div class="badge bg-warning text-dark mt-2 rounded-pill px-3" id="distTime">Estimasi: -
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-warning shadow-sm fw-bold py-2"
                                id="btnCalculateDistance">
                                <i class='bx bx-map-pin me-1'></i> Kalkulasi Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <script src="https://ppete2.github.io/Leaflet.PolylineMeasure/Leaflet.PolylineMeasure.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var allSchools = @json($sekolahs);
            var baseIcon = {
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            };
            var icons = {
                'SMA': new L.Icon({
                    ...baseIcon,
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png'
                }),
                'SMK': new L.Icon({
                    ...baseIcon,
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png'
                }),
                'SLB': new L.Icon({
                    ...baseIcon,
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png'
                }),
                'OTHER': new L.Icon({
                    ...baseIcon,
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png'
                })
            };

            var dataList = document.getElementById('schoolOptions');
            allSchools.forEach(s => {
                var opt = document.createElement('option');
                opt.value = s.nama;
                dataList.appendChild(opt);
            });

            var streetLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© CARTO',
                subdomains: 'abcd',
                maxZoom: 20
            });
            var satelliteLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '© Esri',
                    maxZoom: 18
                });

            var boundsIndonesia = [
                [-11.00, 94.00],
                [6.00, 141.00]
            ];
            var map = L.map('map', {
                center: [-6.85, 107.35],
                zoom: 10,
                minZoom: 5,
                maxBounds: boundsIndonesia,
                maxBoundsViscosity: 1.0,
                layers: [streetLayer],
                zoomControl: false
            });

            L.control.zoom({
                position: 'topleft'
            }).addTo(map);
            var fullscreenControl = new L.Control.Fullscreen({
                position: 'topleft'
            });
            map.addControl(fullscreenControl);
            var polylineMeasure = L.control.polylineMeasure({
                position: 'topleft',
                unit: 'kilometres',
                showClearControl: true
            }).addTo(map);

            var mapContainer = document.getElementById('map-container');
            ['customFab', 'customFilterModal', 'customDistanceModal', '.map-stats', 'schoolDetailCard'].forEach(
                sel => {
                    var el = sel.startsWith('.') ? document.querySelector(sel) : document.getElementById(sel);
                    if (el) {
                        mapContainer.appendChild(el);
                        L.DomEvent.disableClickPropagation(el);
                        L.DomEvent.disableScrollPropagation(el);
                    }
                });

            var markers = L.markerClusterGroup();
            var markerList = [];
            var distanceLine = null;

            function renderMarkers(data) {
                markers.clearLayers();
                markerList = [];
                data.forEach(function(s) {
                    var lat = parseFloat(s.lintang),
                        lng = parseFloat(s.bujur);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        var jenjang = s.bentuk_pendidikan_id_str;
                        var icon = (jenjang === 'SMA') ? icons['SMA'] : (jenjang === 'SMK') ? icons['SMK'] :
                            (jenjang === 'SLB') ? icons['SLB'] : icons['OTHER'];
                        var marker = L.marker([lat, lng], {
                            icon: icon
                        });
                        marker.sekolahData = s;
                        marker.on('click', () => showSchoolDetail(s, lat, lng));
                        markers.addLayer(marker);
                        markerList.push(marker);
                    }
                });
                map.addLayer(markers);
                document.getElementById('statsText').innerHTML = `<b>${data.length}</b> Unit Sekolah`;
            }
            renderMarkers(allSchools);

            var detailCard = document.getElementById('schoolDetailCard');

            function showSchoolDetail(school, lat, lng) {
                document.getElementById('cardJenjang').innerText = school.bentuk_pendidikan_id_str;
                document.getElementById('cardStatus').innerText = school.status_sekolah_str;
                document.getElementById('cardName').innerText = school.nama;
                document.getElementById('cardNpsn').innerText = school.npsn;
                document.getElementById('cardAddress').innerText = school.alamat_jalan;
                document.getElementById('cardKecamatan').innerText = school.kecamatan;
                document.getElementById('btnRouteGoogle').href =
                    `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;

                var header = document.getElementById('cardHeader');
                var j = school.bentuk_pendidikan_id_str;
                header.className = 'card-header-img ' + (j === 'SMA' ? 'bg-header-sma' : (j === 'SMK' ?
                    'bg-header-smk' : (j === 'SLB' ? 'bg-header-slb' : 'bg-header-other')));

                var targetLatLng = L.latLng(lat, lng);
                var others = allSchools.filter(s => s.id !== school.id && s.lintang && s.bujur).map(s => {
                    return {
                        ...s,
                        distance: targetLatLng.distanceTo(L.latLng(s.lintang, s.bujur))
                    };
                }).sort((a, b) => a.distance - b.distance).slice(0, 3);

                var listHtml = '';
                others.forEach(n => {
                    var distKm = (n.distance / 1000).toFixed(2);
                    listHtml += `
                        <div class="nearest-item" onclick="flyToSchool(${n.lintang}, ${n.bujur})">
                            <div class="nearest-info">
                                <h6>${n.nama}</h6>
                                <span>${n.bentuk_pendidikan_id_str} | ${n.kecamatan}</span>
                            </div>
                            <div class="nearest-dist">${distKm} km</div>
                        </div>`;
                });
                document.getElementById('nearestSchoolsContainer').innerHTML = listHtml;
                detailCard.classList.add('active');
                map.flyTo([lat, lng], 15, {
                    animate: true,
                    duration: 1
                });
            }

            window.flyToSchool = function(lat, lng) {
                var found = allSchools.find(s => parseFloat(s.lintang) === lat && parseFloat(s.bujur) === lng);
                if (found) showSchoolDetail(found, lat, lng);
            };

            document.getElementById('btnCloseCard').addEventListener('click', () => detailCard.classList.remove(
                'active'));

            const btnToggle = document.getElementById('btnToggleMenu');
            const options = document.getElementById('fabOptions');
            btnToggle.addEventListener('click', () => {
                options.classList.toggle('show');
                btnToggle.classList.toggle('open');
            });

            document.getElementById('btnChangeLayer').addEventListener('click', () => {
                if (map.hasLayer(streetLayer)) {
                    map.removeLayer(streetLayer);
                    map.addLayer(satelliteLayer);
                } else {
                    map.removeLayer(satelliteLayer);
                    map.addLayer(streetLayer);
                }
            });

            document.getElementById('btnFullscreenCustom').addEventListener('click', () => {
                var elem = document.getElementById('map-container');
                if (!document.fullscreenElement) {
                    elem.requestFullscreen().catch(err => {
                        alert(`Error: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen();
                }
            });

            const modalFilter = document.getElementById('customFilterModal');
            const modalCalc = document.getElementById('customDistanceModal');
            document.getElementById('btnShowFilter').addEventListener('click', () => modalFilter.style.display =
                'flex');
            document.getElementById('btnCloseFilter').addEventListener('click', () => modalFilter.style.display =
                'none');
            document.getElementById('btnShowCalculator').addEventListener('click', () => modalCalc.style.display =
                'flex');
            document.getElementById('btnCloseCalculator').addEventListener('click', () => modalCalc.style.display =
                'none');
            document.getElementById('btnToggleLegend').addEventListener('click', () => document.getElementById(
                'legendBox').classList.toggle('show'));

            const searchInput = document.getElementById('searchBox');
            document.getElementById('btnShowSearch').addEventListener('click', () => {
                searchInput.classList.toggle('active');
                if (searchInput.classList.contains('active')) searchInput.focus();
            });
            searchInput.addEventListener("keyup", e => {
                if (e.keyCode === 13) {
                    var keyword = searchInput.value.toLowerCase();
                    var found = markerList.find(m => m.sekolahData.nama.toLowerCase().includes(keyword) || (
                        m.sekolahData.npsn && m.sekolahData.npsn.includes(keyword)));
                    if (found) found.fire('click');
                    else alert('Sekolah tidak ditemukan.');
                }
            });

            document.getElementById('btnApplyFilter').addEventListener('click', () => {
                var f_kab = document.getElementById('f_kabupaten').value;
                var f_kec = document.getElementById('f_kecamatan').value;
                var f_jenjang = document.getElementById('f_jenjang').value;
                var f_status = document.getElementById('f_status').value;
                var filteredData = allSchools.filter(s => {
                    return (f_kab === "" || s.kabupaten_kota === f_kab) && (f_kec === "" || s
                            .kecamatan === f_kec) &&
                        (f_jenjang === "" || s.bentuk_pendidikan_id_str === f_jenjang) && (
                            f_status === "" || s.status_sekolah_str === f_status);
                });
                renderMarkers(filteredData);
                if (filteredData.length > 0) map.fitBounds(new L.featureGroup(markerList).getBounds(), {
                    padding: [50, 50]
                });
                else alert("Data tidak ditemukan.");
                modalFilter.style.display = 'none';
            });

            function resetAll() {
                document.getElementById("filterForm").reset();
                document.getElementById("distanceForm").reset();
                if (distanceLine) map.removeLayer(distanceLine);
                document.getElementById("distanceResult").style.display = 'none';
                searchInput.value = '';
                searchInput.classList.remove('active');
                renderMarkers(allSchools);
                map.setView([-6.85, 107.35], 10);
                detailCard.classList.remove('active');
                modalFilter.style.display = 'none';
                modalCalc.style.display = 'none';
                const btnMeasure = document.getElementById('btnMeasureToggle');
                if (btnMeasure.classList.contains('active-measure')) btnMeasure.click();
            }
            document.getElementById('btnResetAll').addEventListener('click', resetAll);
            document.getElementById('btnModalReset').addEventListener('click', resetAll);

            document.getElementById('btnCalculateDistance').addEventListener('click', () => {
                var nameA = document.getElementById('inputSchoolA').value;
                var nameB = document.getElementById('inputSchoolB').value;
                var schoolA = allSchools.find(s => s.nama.toLowerCase() === nameA.toLowerCase());
                var schoolB = allSchools.find(s => s.nama.toLowerCase() === nameB.toLowerCase());
                if (!schoolA || !schoolB) {
                    alert("Pilih sekolah dari daftar yang tersedia.");
                    return;
                }

                var latLngA = L.latLng(schoolA.lintang, schoolA.bujur);
                var latLngB = L.latLng(schoolB.lintang, schoolB.bujur);
                var distKm = (latLngA.distanceTo(latLngB) / 1000).toFixed(2);

                document.getElementById('distVal').innerText = distKm + " km";
                document.getElementById('distTime').innerText = "~ " + (distKm * 10).toFixed(0) +
                    " menit (Kendaraan)";
                document.getElementById('distanceResult').style.display = 'block';

                if (distanceLine) map.removeLayer(distanceLine);
                distanceLine = L.polyline([latLngA, latLngB], {
                    color: '#ff3e1d',
                    weight: 4,
                    opacity: 0.9,
                    dashArray: '10, 12'
                }).addTo(map);
                map.fitBounds(distanceLine.getBounds(), {
                    padding: [80, 80]
                });
            });

            const btnMeasure = document.getElementById('btnMeasureToggle');
            btnMeasure.addEventListener('click', () => {
                btnMeasure.classList.toggle('active-measure');
                polylineMeasure._toggleMeasure();
            });
            map.on('polylinemeasure:toggle', e => {
                if (!e.statu) btnMeasure.classList.remove('active-measure');
            });
        });
    </script>
@endsection
