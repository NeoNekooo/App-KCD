@extends('layouts.frontend')

@section('title', 'Struktur Organisasi')

@push('styles')
<style>
    /* ============================================= */
    /*   FRONTEND PREMIUM ORG CHART - CSS TREE       */
    /* ============================================= */

    .premium-org-tree * { box-sizing: border-box; }
    .premium-org-tree {
        display: flex;
        justify-content: center;
        padding: 3rem 1rem 4rem;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* ---- CONNECTOR LINES (Gradient Style) ---- */
    .premium-org-tree ul {
        padding-top: 50px;
        position: relative;
        display: flex;
        justify-content: center;
        margin: 0;
        padding-inline-start: 0;
    }
    .premium-org-tree ul ul::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        border-left: 2px solid #94a3b8;
        width: 0;
        height: 20px; /* Dipendekkan agar tidak menusuk kartu di bawah */
        transform: translateX(-50%);
    }
    .premium-org-tree li {
        float: left;
        text-align: center;
        list-style-type: none;
        position: relative;
        padding: 40px 12px 0 12px; /* Dikurangi sedikit agar lebih rapat dan rapi */
    }
    .premium-org-tree li::before,
    .premium-org-tree li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid #94a3b8;
        width: 50%;
        height: 20px; /* Konsisten dengan tinggi garis atas */
    }
    .premium-org-tree li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #94a3b8;
        height: 20px; /* Berhenti tepat sebelum mengenai ring foto */
    }
    .premium-org-tree li:only-child::before { display: none; }
    .premium-org-tree li:only-child { padding-top: 0; }
    .premium-org-tree li:first-child::before,
    .premium-org-tree li:last-child::after { border: 0 none; }
    .premium-org-tree li:last-child::before {
        border-right: 2px solid #94a3b8;
        border-radius: 0 10px 0 0;
    }
    .premium-org-tree li:first-child::after {
        border-radius: 10px 0 0 0;
    }

    /* ---- NODE WRAPPER ---- */
    .org-node-wrapper {
        display: inline-flex;
        align-items: center;
        position: relative;
    }

    /* ---- MAIN CARD ---- */
    .org-card {
        width: 220px;
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
        overflow: visible;
        position: relative;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226,232,240,0.8);
    }
    .org-card:hover {
        box-shadow: 0 12px 40px rgba(59,130,246,0.15), 0 4px 12px rgba(0,0,0,0.06);
        transform: translateY(-4px);
    }
    .org-card-accent {
        height: 5px;
        border-radius: 20px 20px 0 0;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    }
    .org-card-root .org-card-accent {
        height: 7px;
        background: linear-gradient(90deg, #1e40af, #3b82f6, #8b5cf6);
    }
    .org-card-root {
        width: 240px;
    }
    .org-card-body {
        padding: 32px 16px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .org-card-root .org-card-body {
        padding: 36px 18px 22px;
    }

    /* ---- AVATAR RING ---- */
    .org-avatar-ring {
        position: absolute;
        top: -26px;
        left: 50%;
        transform: translateX(-50%);
        width: 56px;
        height: 56px;
        border-radius: 50%;
        padding: 3px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        box-shadow: 0 6px 16px rgba(59,130,246,0.3);
        transition: all 0.35s ease;
    }
    .org-card:hover .org-avatar-ring {
        box-shadow: 0 8px 24px rgba(59,130,246,0.4);
        transform: translateX(-50%) scale(1.05);
    }
    .org-card-root .org-avatar-ring {
        width: 64px;
        height: 64px;
        top: -30px;
        background: linear-gradient(135deg, #1e40af, #3b82f6, #a78bfa);
        box-shadow: 0 8px 20px rgba(30,64,175,0.35);
    }
    .org-avatar-ring img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ffffff;
    }

    /* ---- TYPOGRAPHY ---- */
    .org-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin: 6px 0 3px;
        line-height: 1.3;
        text-align: center;
        letter-spacing: -0.01em;
    }
    .org-card-root .org-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }
    .org-name {
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
        margin: 0;
        text-align: center;
        line-height: 1.4;
    }
    .org-card-root .org-name {
        font-size: 12px;
    }

    /* ---- ASSISTANT BRANCH ---- */
    .assistant-branch {
        display: flex;
        align-items: center;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 5;
    }
    .assistant-left {
        right: 100%;
    }
    .assistant-right {
        left: 100%;
    }
    .assistant-line {
        width: 32px;
        min-width: 32px;
        border-top: 2px dashed #fbbf24;
    }
    .assistant-cards {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .assistant-card {
        width: 190px;
        background: rgba(255, 251, 235, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid #fde68a;
        border-radius: 14px;
        padding: 12px 12px 12px 14px;
        position: relative;
        box-shadow: 0 2px 8px rgba(245,158,11,0.08);
        transition: all 0.3s ease;
    }
    .assistant-card:hover {
        box-shadow: 0 6px 20px rgba(245,158,11,0.18);
        transform: translateY(-2px);
    }
    .assistant-left .assistant-card {
        border-left: 4px solid #f59e0b;
    }
    .assistant-right .assistant-card {
        border-right: 4px solid #f59e0b;
    }
    .assistant-badge {
        position: absolute;
        top: -9px;
        right: 10px;
        background: linear-gradient(90deg, #f59e0b, #d97706);
        color: white;
        font-size: 9px;
        font-weight: 700;
        padding: 2px 10px;
        border-radius: 12px;
        letter-spacing: 0.03em;
        box-shadow: 0 2px 8px rgba(217,119,6,0.3);
    }
    .assistant-inner {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .assistant-avatar {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fde68a;
        background: #fff;
    }
    .assistant-info h4 {
        font-size: 11px;
        font-weight: 700;
        color: #92400e;
        margin: 0;
        line-height: 1.3;
    }
    .assistant-info p {
        font-size: 10px;
        color: #b45309;
        margin: 2px 0 0;
        line-height: 1.3;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="relative bg-slate-900 py-20 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-1/3 h-full bg-gradient-to-r from-purple-600/10 to-transparent"></div>
        <!-- Decorative dots -->
        <div class="absolute top-10 right-10 w-32 h-32 opacity-10">
            <svg viewBox="0 0 100 100" fill="currentColor" class="text-blue-400">
                <circle cx="10" cy="10" r="2"/><circle cx="30" cy="10" r="2"/><circle cx="50" cy="10" r="2"/><circle cx="70" cy="10" r="2"/><circle cx="90" cy="10" r="2"/>
                <circle cx="10" cy="30" r="2"/><circle cx="30" cy="30" r="2"/><circle cx="50" cy="30" r="2"/><circle cx="70" cy="30" r="2"/><circle cx="90" cy="30" r="2"/>
                <circle cx="10" cy="50" r="2"/><circle cx="30" cy="50" r="2"/><circle cx="50" cy="50" r="2"/><circle cx="70" cy="50" r="2"/><circle cx="90" cy="50" r="2"/>
            </svg>
        </div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <div class="inline-flex items-center gap-2 bg-blue-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-200 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Hierarki Organisasi
        </div>
        <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">Struktur Organisasi</h1>
        <p class="text-lg text-blue-200/80 max-w-2xl mx-auto leading-relaxed">Mengenal lebih dekat susunan kepemimpinan dan hierarki fungsional Kantor Cabang Dinas Pendidikan.</p>
    </div>
</div>

<!-- Main Content Area -->
<div class="py-16 relative min-h-screen" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 40%, #e2e8f0 100%);">
    <!-- Dekorasi background -->
    <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-slate-900/5 to-transparent z-0"></div>
    
    <div class="max-w-[100vw] overflow-x-auto relative z-10 px-4 py-8">
        @if($struktur->count() > 0)
            @php
                $roots = $struktur->where('parent_id', null)->where('jenis_hubungan', 'struktural');
                if($roots->isEmpty() && $struktur->count() > 0) {
                    $roots = $struktur->where('jenis_hubungan', 'struktural')->take(1); 
                }
            @endphp
            <div class="premium-org-tree min-w-max pb-32">
                <ul>
                    @foreach($roots as $root)
                        <x-org-tree-node :node="$root" :allNodes="$struktur" />
                    @endforeach
                </ul>
            </div>
        @else
            <div class="text-center py-20">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Belum ada struktur organisasi</h3>
                <p class="mt-2 text-gray-500">Struktur akan tampil di sini setelah ditambahkan oleh Admin.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <!-- Premium CSS Tree - No external JS needed -->
@endpush
@endsection
