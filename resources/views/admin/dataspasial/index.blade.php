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
        /* --- MAP WRAPPER --- */
        #map-container {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 4px solid #fff;
            height: calc(100vh - 170px);
            min-height: 500px;
            width: 100%;
        }

        #map {
            height: 100%;
            width: 100%;
            background-color: #e9ecef;
            z-index: 1;
            font-family: 'Public Sans', sans-serif;
        }

        /* --- UI ELEMENTS (GLASS EFFECT) --- */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* --- HUD STATS (KIRI BAWAH) --- */
        .map-stats {
            position: absolute;
            bottom: 25px;
            left: 20px;
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
            box-shadow: 0 4px 8px rgba(105, 108, 255, 0.4);
        }

        /* --- FAB MENU (KANAN ATAS) --- */
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

        .main-fab i {
            transition: transform 0.3s ease;
        }

        .main-fab.open i {
            transform: rotate(135deg);
        }

        .btn-fab.active-measure {
            background-color: #ff3e1d !important;
            color: white !important;
            animation: pulse-red 1.5s infinite;
        }

        /* Tooltip */
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

        /* Search & Options */
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

        /* Modals */
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
            padding: 15px;
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

        /* Legend */
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

        /* Colors & Styles */
        .bg-legend-sma,
        .bg-header-sma {
            background: #2A81CB;
        }

        .bg-legend-smk,
        .bg-header-smk {
            background: #CB2B3E;
        }

        .bg-legend-slb,
        .bg-header-slb {
            background: #9C2BC3;
        }

        .bg-legend-other,
        .bg-header-other {
            background: #2AAD27;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            padding: 0;
            border: none;
            overflow: hidden;
        }

        .leaflet-popup-content {
            margin: 0;
            width: 260px !important;
        }

        .popup-header {
            padding: 10px 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .popup-body {
            padding: 15px;
        }

        .info-row {
            display: flex;
            align-items: start;
            gap: 8px;
            margin-bottom: 6px;
            font-size: 0.8rem;
            color: #555;
        }

        .distance-result-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            border: 1px dashed #696cff;
            text-align: center;
            display: none;
        }

        .distance-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #696cff;
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

        /* CUSTOMIZE LEAFLET CONTROLS */
        .leaflet-control-polyline-measure {
            display: none !important;
        }

        .leaflet-control-fullscreen-button {
            display: none !important;
        }

        /* Hapus control layer default */
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

        <div id="map-container">
            <div id="map">

                {{-- 1. HUD STATS (Kiri Bawah) --}}
                <div class="map-stats glass">
                    <div class="stats-icon"><i class='bx bx-map-alt'></i></div>
                    <div class="d-flex flex-column" style="line-height: 1.2;">
                        <small class="text-muted" style="font-size: 0.7rem;">Total Sekolah</small>
                        <span id="statsText" style="color: #333; font-weight: bold;">Loading...</span>
                    </div>
                </div>

                {{-- 2. FAB MENU (Kanan Atas) --}}
                <div class="fab-container" id="customFab">
                    <button class="btn-fab main-fab shadow-lg" id="btnToggleMenu" data-tooltip="Menu">
                        <i class='bx bx-grid-alt'></i>
                    </button>

                    <div class="fab-options" id="fabOptions">
                        {{-- Search --}}
                        <div class="search-wrapper">
                            <input type="text" id="searchBox" class="form-control search-input glass"
                                placeholder="Ketik nama sekolah...">
                            <button class="btn-fab glass" id="btnShowSearch" data-tooltip="Cari"><i
                                    class='bx bx-search'></i></button>
                        </div>

                        {{-- GPS --}}
                        <button class="btn-fab glass text-primary" id="btnMyLocation" data-tooltip="Lokasi Saya"><i
                                class='bx bx-crosshair'></i></button>

                        {{-- Fullscreen Custom --}}
                        <button class="btn-fab glass" id="btnFullscreenCustom" data-tooltip="Layar Penuh"><i
                                class='bx bx-fullscreen'></i></button>

                        {{-- 🔥 TOMBOL GANTI LAYER (BARU) 🔥 --}}
                        <button class="btn-fab glass text-info" id="btnChangeLayer" data-tooltip="Ganti Mode Peta"><i
                                class='bx bx-layer'></i></button>

                        {{-- Filter --}}
                        <button class="btn-fab glass" id="btnShowFilter" data-tooltip="Filter Data"><i
                                class='bx bx-filter-alt'></i></button>

                        {{-- Tools --}}
                        <button class="btn-fab glass text-warning" id="btnShowCalculator" data-tooltip="Kalkulator Jarak"><i
                                class='bx bx-calculator'></i></button>
                        <button class="btn-fab glass" id="btnMeasureToggle" data-tooltip="Penggaris Manual"><i
                                class='bx bx-ruler'></i></button>

                        {{-- Legend --}}
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

                        {{-- Reset --}}
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
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnModalReset">Reset
                                    Filter</button>
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
                                {{-- Diisi otomatis oleh Javascript --}}
                            </datalist>
                            <div id="distanceResult" class="distance-result-box">
                                <div class="text-muted small text-uppercase">Jarak Lurus (Euclidean)</div>
                                <div class="distance-value" id="distVal">0 km</div>
                                <small class="text-muted" id="distTime">Estimasi: -</small>
                            </div>
                            <div class="d-grid gap-2 mt-4 pt-2 border-top">
                                <button type="button" class="btn btn-warning shadow-sm" id="btnCalculateDistance">
                                    <i class='bx bx-calculator me-1'></i> Hitung & Visualisasikan
                                </button>
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

            // --- CONFIG ---
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

            // --- MAP INIT ---
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

            var southWest = L.latLng(-11.0, 94.0),
                northEast = L.latLng(6.0, 142.0);
            var bounds = L.latLngBounds(southWest, northEast);

            var map = L.map('map', {
                center: [-6.85, 107.35],
                zoom: 10,
                layers: [streetLayer],
                maxBounds: bounds,
                maxBoundsViscosity: 1.0,
                minZoom: 5,
                zoomControl: false
            });

            // --- CONTROLS ---
            L.control.zoom({
                position: 'topleft'
            }).addTo(map);
            // KITA TIDAK PAKAI L.control.layers LAGI
            map.addControl(new L.Control.Fullscreen({
                position: 'topleft'
            }));
            var polylineMeasure = L.control.polylineMeasure({
                position: 'topleft',
                unit: 'kilometres',
                showClearControl: true
            }).addTo(map);

            // --- DOM INJECTION ---
            var mapContainer = document.getElementById('map');
            ['customFab', 'customFilterModal', 'customDistanceModal', '.map-stats'].forEach(selector => {
                var el = selector.startsWith('.') ? document.querySelector(selector) : document
                    .getElementById(selector);
                if (el) {
                    mapContainer.appendChild(el);
                    L.DomEvent.disableClickPropagation(el);
                    L.DomEvent.disableScrollPropagation(el);
                }
            });

            var markers = L.markerClusterGroup();
            var markerList = [];
            var statsText = document.getElementById('statsText');
            var distanceLine = null;

            // --- RENDER ---
            function renderMarkers(data) {
                markers.clearLayers();
                markerList = [];
                data.forEach(function(s) {
                    var lat = parseFloat(s.lintang),
                        lng = parseFloat(s.bujur);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        var jenjang = s.bentuk_pendidikan_id_str;
                        var icon = icons['OTHER'];
                        var headerClass = 'bg-header-other';
                        if (jenjang === 'SMA') {
                            icon = icons['SMA'];
                            headerClass = 'bg-header-sma';
                        } else if (jenjang === 'SMK') {
                            icon = icons['SMK'];
                            headerClass = 'bg-header-smk';
                        } else if (jenjang === 'SLB') {
                            icon = icons['SLB'];
                            headerClass = 'bg-header-slb';
                        }

                        var popupContent =
                            `<div class="popup-header ${headerClass}"><span class="fw-bold">${jenjang}</span><span class="badge bg-white text-dark" style="font-size:0.65rem">${s.status_sekolah_str}</span></div><div class="popup-body"><h6 class="fw-bold text-dark mb-2" style="font-size:0.9rem">${s.nama}</h6><div class="info-row"><i class='bx bx-id-card'></i> <span>NPSN: <b>${s.npsn}</b></span></div><div class="info-row"><i class='bx bx-map'></i> <span style="line-height:1.2">${s.alamat_jalan}, ${s.kecamatan}</span></div><div class="d-grid mt-3"><a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" class="btn btn-sm btn-primary rounded-pill" style="font-size:0.75rem"><i class='bx bx-navigation me-1'></i> Rute Lokasi</a></div></div>`;
                        var marker = L.marker([lat, lng], {
                            icon: icon
                        });
                        marker.bindPopup(popupContent);
                        marker.sekolahData = s;
                        markers.addLayer(marker);
                        markerList.push(marker);
                    }
                });
                map.addLayer(markers);
                statsText.innerHTML = `<b>${data.length}</b> Sekolah`;
            }
            renderMarkers(allSchools);

            // --- UI INTERACTIONS ---
            const btnToggle = document.getElementById('btnToggleMenu');
            const options = document.getElementById('fabOptions');
            btnToggle.addEventListener('click', () => {
                options.classList.toggle('show');
                btnToggle.classList.toggle('open');
            });

            // 🔥 TOGGLE MAP LAYER (BUTTON) 🔥
            document.getElementById('btnChangeLayer').addEventListener('click', () => {
                if (map.hasLayer(streetLayer)) {
                    map.removeLayer(streetLayer);
                    map.addLayer(satelliteLayer);
                } else {
                    map.removeLayer(satelliteLayer);
                    map.addLayer(streetLayer);
                }
            });

            const btnSearch = document.getElementById('btnShowSearch');
            const searchInput = document.getElementById('searchBox');
            btnSearch.addEventListener('click', () => {
                searchInput.classList.toggle('active');
                if (searchInput.classList.contains('active')) searchInput.focus();
            });

            document.getElementById('btnFullscreenCustom').addEventListener('click', () => {
                map.toggleFullscreen();
            });

            const btnMeasure = document.getElementById('btnMeasureToggle');
            btnMeasure.addEventListener('click', () => {
                btnMeasure.classList.toggle('active-measure');
                polylineMeasure._toggleMeasure();
            });
            map.on('polylinemeasure:toggle', e => {
                if (!e.statu) btnMeasure.classList.remove('active-measure');
            });

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

            // Modals
            const customModal = document.getElementById('customFilterModal');
            const modalCalc = document.getElementById('customDistanceModal');
            document.getElementById('btnToggleLegend').addEventListener('click', () => document.getElementById(
                'legendBox').classList.toggle('show'));
            document.getElementById('btnShowFilter').addEventListener('click', () => customModal.style.display =
                'flex');
            document.getElementById('btnCloseFilter').addEventListener('click', () => customModal.style.display =
                'none');
            document.getElementById('btnShowCalculator').addEventListener('click', () => modalCalc.style.display =
                'flex');
            document.getElementById('btnCloseCalculator').addEventListener('click', () => modalCalc.style.display =
                'none');

            // Distance Calc
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
                var midLat = (latLngA.lat + latLngB.lat) / 2;
                var midLng = (latLngA.lng + latLngB.lng) / 2;
                distanceLine.bindPopup(`<b>Jarak: ${distKm} km</b>`).openPopup();
                map.fitBounds(distanceLine.getBounds(), {
                    padding: [50, 50]
                });
            });

            // Filter Apply
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
                customModal.style.display = 'none';
            });

            // Reset
            function resetAll() {
                document.getElementById("filterForm").reset();
                document.getElementById("distanceForm").reset();
                document.getElementById("distanceResult").style.display = 'none';
                if (distanceLine) map.removeLayer(distanceLine);
                searchInput.value = '';
                searchInput.classList.remove('active');
                renderMarkers(allSchools);
                map.setView([-6.85, 107.35], 10);
                map.closePopup();
                customModal.style.display = 'none';
                modalCalc.style.display = 'none';
                document.getElementById('legendBox').classList.remove('show');
                if (btnMeasure.classList.contains('active-measure')) btnMeasure.click();
            }
            document.getElementById('btnResetAll').addEventListener('click', resetAll);
            document.getElementById('btnModalReset').addEventListener('click', resetAll);

            // Search Keyup
            searchInput.addEventListener("keyup", e => {
                if (e.keyCode === 13) {
                    var keyword = searchInput.value.toLowerCase();
                    var found = markerList.find(m => m.sekolahData.nama.toLowerCase().includes(keyword));
                    if (found) markers.zoomToShowLayer(found, () => found.openPopup());
                    else alert('Sekolah tidak ditemukan.');
                }
            });
        });
    </script>
@endsection
