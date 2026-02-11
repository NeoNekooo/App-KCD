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
        /* --- MAP WRAPPER (DEFAULT) --- */
        #map-container {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 4px solid #fff;
            height: calc(100vh - 170px);
            /* Tinggi normal */
            min-height: 500px;
            width: 100%;
            background: #e9ecef;
        }

        #map {
            height: 100%;
            width: 100%;
            background-color: #e9ecef;
            z-index: 1;
            font-family: 'Public Sans', sans-serif;
        }

        /* 🔥 FIX FULLSCREEN MODE 🔥 */
        /* Saat elemen masuk fullscreen, paksa tinggi & lebar 100% */
        #map-container:fullscreen,
        #map:fullscreen {
            width: 100vw !important;
            height: 100vh !important;
            border-radius: 0 !important;
            border: none !important;
            z-index: 99999 !important;
        }

        /* Dukungan browser lain */
        #map-container:-webkit-full-screen {
            width: 100%;
            height: 100%;
        }

        #map-container:-moz-full-screen {
            width: 100%;
            height: 100%;
        }

        #map-container:-ms-fullscreen {
            width: 100%;
            height: 100%;
        }

        /* --- GLASS UI --- */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }

        /* --- HUD STATS --- */
        .map-stats {
            position: absolute;
            bottom: 25px;
            right: 20px;
            z-index: 1000;
            padding: 8px 16px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            pointer-events: auto;
        }

        .stats-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #696cff, #4e51e0);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- FAB MENU --- */
        .fab-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            align-items: end;
            gap: 10px;
            pointer-events: auto;
        }

        .btn-fab {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #566a7f;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .btn-fab:hover {
            transform: translateY(-2px);
            color: #696cff;
            background: white;
        }

        .main-fab {
            background: #fff;
            color: #696cff;
            font-size: 1.5rem;
            z-index: 2001;
        }

        .main-fab.open i {
            transform: rotate(135deg);
        }

        .main-fab i {
            transition: transform 0.3s ease;
        }

        .btn-fab.active-measure {
            background-color: #ff3e1d !important;
            color: white !important;
            animation: pulse-red 1.5s infinite;
        }

        .btn-fab[data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            right: 55px;
            top: 50%;
            transform: translateY(-50%);
            background: #2b2c40;
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
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
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .fab-options.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
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
            height: 45px;
            border: none;
            border-radius: 12px;
            transition: all 0.4s ease;
            outline: none;
            font-size: 0.9rem;
        }

        .search-input.active {
            width: 220px;
            opacity: 1;
            padding: 0 15px;
        }

        /* --- SCHOOL DETAIL CARD --- */
        .school-detail-card {
            position: absolute;
            top: 20px;
            left: 20px;
            bottom: 20px;
            width: 350px;
            background: rgba(255, 255, 255, 0.98);
            z-index: 3000;
            border-radius: 16px;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
            transform: translateX(-120%);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .school-detail-card.active {
            transform: translateX(0);
        }

        .card-header-img {
            height: 140px;
            background: #696cff;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 15px;
        }

        .btn-close-card {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.3);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .btn-close-card:hover {
            background: rgba(255, 0, 0, 0.7);
        }

        .school-badge {
            background: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: bold;
            color: #444;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card-content {
            padding: 20px;
            overflow-y: auto;
            flex-grow: 1;
        }

        .school-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .school-meta {
            font-size: 0.8rem;
            color: #777;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
        }

        /* Nearest List */
        .nearest-list {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
        }

        .nearest-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 8px;
            cursor: pointer;
            transition: 0.2s;
            border: 1px solid transparent;
        }

        .nearest-item:hover {
            border-color: #696cff;
            background: #f0f1ff;
        }

        .nearest-info h6 {
            margin: 0;
            font-size: 0.8rem;
            font-weight: 700;
            color: #444;
        }

        .nearest-info span {
            font-size: 0.7rem;
            color: #888;
        }

        .nearest-dist {
            font-size: 0.8rem;
            font-weight: bold;
            color: #696cff;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .school-detail-card {
                width: 100%;
                top: auto;
                bottom: 0;
                left: 0;
                border-radius: 20px 20px 0 0;
                height: 60vh;
                transform: translateY(120%);
            }

            .school-detail-card.active {
                transform: translateY(0);
            }
        }

        /* --- MODAL & LEGEND --- */
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
            backdrop-filter: blur(3px);
            padding: 20px;
        }

        .map-modal-content {
            background: #fff;
            padding: 25px;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            animation: zoomIn 0.25s;
        }

        .legend-card {
            padding: 12px 15px;
            border-radius: 12px;
            width: 200px;
            display: none;
            margin-right: 12px;
        }

        .legend-card.show {
            display: block;
            animation: zoomIn 0.2s;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            margin-right: 10px;
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

        @keyframes pulse-red {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 62, 29, 0);
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
                transform: translate(10px, -50%);
            }

            to {
                opacity: 1;
                transform: translate(0, -50%);
            }
        }

        /* Hide Defaults */
        .leaflet-control-polyline-measure {
            display: none !important;
        }

        .leaflet-control-fullscreen-button {
            display: none !important;
        }

        .leaflet-control-layers {
            display: none !important;
        }

        .leaflet-control-zoom {
            margin-top: 80px !important;
            border: none !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Monitoring /</span> Peta Digital Sekolah</h4>

        {{-- Gunakan ID map-container untuk dibungkus, tapi Fullscreen API akan menargetkan ID ini --}}
        <div id="map-container">
            <div id="map">

                {{-- SCHOOL DETAIL CARD --}}
                <div class="school-detail-card" id="schoolDetailCard">
                    <div class="card-header-img" id="cardHeader">
                        <button class="btn-close-card" id="btnCloseCard"><i class='bx bx-x'></i></button>
                        <div class="w-100 d-flex justify-content-between align-items-end">
                            <span class="school-badge" id="cardJenjang">SMA</span>
                            <span class="badge bg-dark" id="cardStatus">Negeri</span>
                        </div>
                    </div>

                    <div class="card-content">
                        <div id="cardName" class="school-title">Nama Sekolah</div>
                        <div class="school-meta"><i class='bx bx-id-card'></i> <span id="cardNpsn">-</span></div>
                        <div class="school-meta"><i class='bx bx-map'></i> <span id="cardAddress">-</span></div>
                        <div class="school-meta"><i class='bx bx-buildings'></i> <span id="cardKecamatan">-</span></div>

                        <div class="d-grid gap-2 mt-3">
                            <a href="#" id="btnRouteGoogle" target="_blank"
                                class="btn btn-primary btn-sm rounded-pill">
                                <i class='bx bx-navigation me-1'></i> Rute Google Maps
                            </a>
                        </div>

                        <div class="nearest-list">
                            <h6 class="small fw-bold text-uppercase text-muted mb-3"><i class='bx bx-radar me-1'></i> 3
                                Sekolah Terdekat</h6>
                            <div id="nearestSchoolsContainer"></div>
                        </div>
                    </div>
                </div>

                {{-- HUD STATS --}}
                <div class="map-stats glass">
                    <div class="stats-icon"><i class='bx bx-map-alt'></i></div>
                    <div class="d-flex flex-column" style="line-height: 1.2;">
                        <small class="text-muted" style="font-size: 0.7rem;">Total Sekolah</small>
                        <span id="statsText" style="color: #333; font-weight: bold;">Loading...</span>
                    </div>
                </div>

                {{-- FAB MENU --}}
                <div class="fab-container" id="customFab">
                    <button class="btn-fab main-fab shadow-lg" id="btnToggleMenu" data-tooltip="Menu">
                        <i class='bx bx-grid-alt'></i>
                    </button>

                    <div class="fab-options" id="fabOptions">
                        <div class="search-wrapper">
                            <input type="text" id="searchBox" class="form-control search-input glass"
                                placeholder="Cari nama sekolah...">
                            <button class="btn-fab glass" id="btnShowSearch" data-tooltip="Cari"><i
                                    class='bx bx-search'></i></button>
                        </div>

                        {{-- Tombol Fullscreen Custom --}}
                        <button class="btn-fab glass" id="btnFullscreenCustom" data-tooltip="Layar Penuh"><i
                                class='bx bx-fullscreen'></i></button>

                        <button class="btn-fab glass text-info" id="btnChangeLayer" data-tooltip="Ganti Mode Peta"><i
                                class='bx bx-layer'></i></button>
                        <button class="btn-fab glass" id="btnShowFilter" data-tooltip="Filter Data"><i
                                class='bx bx-filter-alt'></i></button>
                        <button class="btn-fab glass text-warning" id="btnShowCalculator" data-tooltip="Kalkulator Jarak"><i
                                class='bx bx-calculator'></i></button>
                        <button class="btn-fab glass" id="btnMeasureToggle" data-tooltip="Penggaris Manual"><i
                                class='bx bx-ruler'></i></button>

                        <div class="d-flex align-items-center">
                            <div class="legend-card glass" id="legendBox">
                                <h6 class="fw-bold mb-2 small text-uppercase text-muted border-bottom pb-1">Legenda</h6>
                                <div class="legend-item">
                                    <div class="legend-color bg-legend-sma"></div> SMA
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color bg-legend-smk"></div> SMK
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color bg-legend-slb"></div> SLB
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color bg-legend-other"></div> Lainnya
                                </div>
                            </div>
                            <button class="btn-fab glass" id="btnToggleLegend" data-tooltip="Legenda"><i
                                    class='bx bx-palette'></i></button>
                        </div>
                        <button class="btn-fab glass text-danger" id="btnResetAll" data-tooltip="Reset Normal"><i
                                class='bx bx-refresh'></i></button>
                    </div>
                </div>

                {{-- MODAL FILTER --}}
                <div id="customFilterModal" class="map-overlay-modal">
                    <div class="map-modal-content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="m-0 fw-bold text-primary"><i class='bx bx-filter-alt me-2'></i>Filter Data</h5>
                            <button type="button" class="btn-close" id="btnCloseFilter"></button>
                        </div>
                        <form id="filterForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Wilayah</label>
                                    <select class="form-select" id="f_kabupaten">
                                        <option value="">Semua Kabupaten/Kota</option>
                                        @foreach ($filter_kabupaten as $kab)
                                            <option value="{{ $kab }}">{{ $kab }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select class="form-select" id="f_kecamatan">
                                        <option value="">Semua Kecamatan</option>
                                        @foreach ($filter_kecamatan as $kec)
                                            <option value="{{ $kec }}">{{ $kec }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Jenjang</label>
                                    <select class="form-select" id="f_jenjang">
                                        <option value="">Semua</option>
                                        @foreach ($filter_jenjang as $jjg)
                                            <option value="{{ $jjg }}">{{ $jjg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Status</label>
                                    <select class="form-select" id="f_status">
                                        <option value="">Semua</option>
                                        @foreach ($filter_status as $st)
                                            <option value="{{ $st }}">{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-2 border-top">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    id="btnModalReset">Reset</button>
                                <button type="button" class="btn btn-sm btn-primary px-4"
                                    id="btnApplyFilter">Terapkan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MODAL CALCULATOR --}}
                <div id="customDistanceModal" class="map-overlay-modal">
                    <div class="map-modal-content">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="m-0 fw-bold text-warning"><i class='bx bx-calculator me-2'></i>Analisis Jarak</h5>
                            <button type="button" class="btn-close" id="btnCloseCalculator"></button>
                        </div>
                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.8rem;">
                            <i class='bx bx-info-circle me-1'></i> Pilih dua sekolah untuk dihitung jaraknya.
                        </div>
                        <form id="distanceForm">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">📍 Sekolah Asal (Titik A)</label>
                                <input class="form-control" list="schoolOptions" id="inputSchoolA"
                                    placeholder="Ketik nama sekolah asal...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">🏁 Sekolah Tujuan (Titik B)</label>
                                <input class="form-control" list="schoolOptions" id="inputSchoolB"
                                    placeholder="Ketik nama sekolah tujuan...">
                            </div>
                            <datalist id="schoolOptions">
                                {{-- Diisi otomatis --}}
                            </datalist>
                            <div id="distanceResult" class="distance-result-box"
                                style="display:none; background: #f8f9fa; border-radius: 10px; padding: 15px; margin-top: 15px; border: 1px dashed #696cff; text-align: center;">
                                <div class="text-muted small text-uppercase">Jarak Lurus (Euclidean)</div>
                                <div class="distance-value" id="distVal"
                                    style="font-size: 1.4rem; font-weight: 800; color: #696cff;">0 km</div>
                                <small class="text-muted" id="distTime">Estimasi: -</small>
                            </div>
                            <div class="d-grid gap-2 mt-4 pt-2 border-top">
                                <button type="button" class="btn btn-warning shadow-sm" id="btnCalculateDistance"><i
                                        class='bx bx-calculator me-1'></i> Hitung</button>
                            </div>
                        </form>
                    </div>
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
            // Data
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

            // Datalist
            var dataList = document.getElementById('schoolOptions');
            allSchools.forEach(s => {
                var option = document.createElement('option');
                option.value = s.nama;
                dataList.appendChild(option);
            });

            // Init Map
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
            var map = L.map('map', {
                center: [-6.85, 107.35],
                zoom: 10,
                layers: [streetLayer],
                zoomControl: false
            });

            L.control.zoom({
                position: 'topleft'
            }).addTo(map);

            // Fullscreen Plugin
            var fullscreenControl = new L.Control.Fullscreen({
                position: 'topleft'
            });
            map.addControl(fullscreenControl);

            var polylineMeasure = L.control.polylineMeasure({
                position: 'topleft',
                unit: 'kilometres',
                showClearControl: true
            }).addTo(map);

            // UI Elements Injection
            var mapContainer = document.getElementById(
            'map-container'); // Inject ke map-container biar ikut fullscreen
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

            // Render
            function renderMarkers(data) {
                markers.clearLayers();
                markerList = [];
                data.forEach(function(s) {
                    var lat = parseFloat(s.lintang),
                        lng = parseFloat(s.bujur);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        var jenjang = s.bentuk_pendidikan_id_str;
                        var icon = icons['OTHER'];
                        if (jenjang === 'SMA') icon = icons['SMA'];
                        else if (jenjang === 'SMK') icon = icons['SMK'];
                        else if (jenjang === 'SLB') icon = icons['SLB'];

                        var marker = L.marker([lat, lng], {
                            icon: icon
                        });
                        marker.sekolahData = s;
                        marker.on('click', function(e) {
                            showSchoolDetail(s, lat, lng);
                        });
                        markers.addLayer(marker);
                        markerList.push(marker);
                    }
                });
                map.addLayer(markers);
                document.getElementById('statsText').innerHTML = `<b>${data.length}</b> Sekolah`;
            }
            renderMarkers(allSchools);

            // Side Panel Logic
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
                    var dist = targetLatLng.distanceTo(L.latLng(s.lintang, s.bujur));
                    return {
                        ...s,
                        distance: dist
                    };
                });
                others.sort((a, b) => a.distance - b.distance);
                var nearest3 = others.slice(0, 3);

                var listHtml = '';
                nearest3.forEach(n => {
                    var distKm = (n.distance / 1000).toFixed(2);
                    listHtml +=
                        `<div class="nearest-item" onclick="flyToSchool(${n.lintang}, ${n.bujur})"><div class="nearest-info"><h6>${n.nama}</h6><span>${n.bentuk_pendidikan_id_str} | ${n.kecamatan}</span></div><div class="nearest-dist">${distKm} km</div></div>`;
                });
                document.getElementById('nearestSchoolsContainer').innerHTML = listHtml;
                detailCard.classList.add('active');
                map.setView([lat, lng], 16);
            }

            window.flyToSchool = function(lat, lng) {
                var found = allSchools.find(s => parseFloat(s.lintang) === lat && parseFloat(s.bujur) === lng);
                if (found) showSchoolDetail(found, lat, lng);
            };

            document.getElementById('btnCloseCard').addEventListener('click', () => {
                detailCard.classList.remove('active');
            });

            // Handlers
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

            // Fullscreen Custom Trigger
            document.getElementById('btnFullscreenCustom').addEventListener('click', () => {
                // Gunakan ID map-container agar semua UI ikut membesar
                var elem = document.getElementById('map-container');
                if (!document.fullscreenElement) {
                    elem.requestFullscreen().catch(err => {
                        alert(
                            `Error attempting to enable fullscreen mode: ${err.message} (${err.name})`);
                    });
                } else {
                    document.exitFullscreen();
                }
            });

            // Modals
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

            // Search
            const searchInput = document.getElementById('searchBox');
            document.getElementById('btnShowSearch').addEventListener('click', () => {
                searchInput.classList.toggle('active');
                if (searchInput.classList.contains('active')) searchInput.focus();
            });
            searchInput.addEventListener("keyup", e => {
                if (e.keyCode === 13) {
                    var keyword = searchInput.value.toLowerCase();
                    var found = markerList.find(m => m.sekolahData.nama.toLowerCase().includes(keyword));
                    if (found) found.fire('click');
                    else alert('Sekolah tidak ditemukan.');
                }
            });

            // Filter
            document.getElementById('btnApplyFilter').addEventListener('click', () => {
                var f_kab = document.getElementById('f_kabupaten').value;
                var f_kec = document.getElementById('f_kecamatan').value;
                var f_jenjang = document.getElementById('f_jenjang').value;
                var f_status = document.getElementById('f_status').value;
                var filteredData = allSchools.filter(s => {
                    return (f_kab === "" || s.kabupaten_kota === f_kab) && (f_kec === "" || s
                        .kecamatan === f_kec) && (f_jenjang === "" || s
                        .bentuk_pendidikan_id_str === f_jenjang) && (f_status === "" || s
                        .status_sekolah_str === f_status);
                });
                renderMarkers(filteredData);
                if (filteredData.length > 0) map.fitBounds(new L.featureGroup(markerList).getBounds());
                else alert("Data tidak ditemukan.");
                modalFilter.style.display = 'none';
            });

            // Reset
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

            // Calc
            document.getElementById('btnCalculateDistance').addEventListener('click', () => {
                var nameA = document.getElementById('inputSchoolA').value;
                var nameB = document.getElementById('inputSchoolB').value;
                var schoolA = allSchools.find(s => s.nama.toLowerCase() === nameA.toLowerCase());
                var schoolB = allSchools.find(s => s.nama.toLowerCase() === nameB.toLowerCase());
                if (!schoolA || !schoolB) {
                    alert("Data sekolah tidak valid.");
                    return;
                }
                var latLngA = L.latLng(schoolA.lintang, schoolA.bujur);
                var latLngB = L.latLng(schoolB.lintang, schoolB.bujur);
                var distKm = (latLngA.distanceTo(latLngB) / 1000).toFixed(2);
                document.getElementById('distVal').innerText = distKm + " km";
                document.getElementById('distTime').innerText = "Estimasi Lurus: " + (distKm * 10).toFixed(
                    0) + " menit";
                document.getElementById('distanceResult').style.display = 'block';
                if (distanceLine) map.removeLayer(distanceLine);
                distanceLine = L.polyline([latLngA, latLngB], {
                    color: '#ff3e1d',
                    weight: 5,
                    opacity: 0.8,
                    dashArray: '10, 10'
                }).addTo(map);
                map.fitBounds(distanceLine.getBounds(), {
                    padding: [50, 50]
                });
            });

            // GPS & Measure
            document.getElementById('btnMyLocation').addEventListener('click', () => {
                map.locate({
                    setView: true,
                    maxZoom: 15
                });
            });
            map.on('locationfound', e => {
                L.popup().setLatLng(e.latlng).setContent("📍 Lokasi Anda").openOn(map);
                L.circle(e.latlng, {
                    radius: e.accuracy / 2,
                    color: '#696cff'
                }).addTo(map);
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
