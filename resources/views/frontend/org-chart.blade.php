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

    /* ---- CONNECTOR LINES (Improved Logic) ---- */
    .premium-org-tree ul {
        padding-top: 20px;
        position: relative;
        display: flex;
        justify-content: center;
        margin: 0;
        padding-inline-start: 0;
    }
    
    /* Garis vertikal yang turun dari parent */
    .premium-org-tree ul ul::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        border-left: 2px solid #94a3b8;
        width: 0;
        height: 20px;
        transform: translateX(-50%);
    }

    .premium-org-tree li {
        text-align: center;
        list-style-type: none;
        position: relative;
        padding: 20px 15px 0 15px;
    }

    /* Garis horizontal penghubung */
    .premium-org-tree li::before,
    .premium-org-tree li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid #94a3b8;
        width: 50%;
        height: 20px;
    }
    .premium-org-tree li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #94a3b8;
    }

    /* Menghilangkan garis untuk anak tunggal */
    .premium-org-tree li:only-child::after,
    .premium-org-tree li:only-child::before { display: none; }
    .premium-org-tree li:only-child { padding-top: 0; }

    /* Perbaikan garis untuk elemen pertama (menghilangkan garis ke kiri) */
    .premium-org-tree li:first-child::before {
        border: 0 none;
    }
    .premium-org-tree li:first-child::after {
        border-radius: 10px 0 0 0;
    }

    /* Perbaikan garis untuk elemen terakhir (menghilangkan garis ke kanan) */
    .premium-org-tree li:last-child::before {
        border-right: 2px solid #94a3b8;
        border-radius: 0 10px 0 0;
    }
    .premium-org-tree li:last-child::after {
        border: 0 none;
    }

    /* ---- NODE WRAPPER ---- */
    .org-node-wrapper {
        display: inline-flex;
        align-items: center;
        position: relative;
        justify-content: center;
    }

    /* ---- MAIN CARD ---- */
    .org-card {
        width: 220px;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: visible;
        position: relative;
        transition: all 0.3s ease;
        border: 1px solid rgba(226,232,240,0.8);
        z-index: 10;
    }
    .org-card:hover {
        box-shadow: 0 15px 35px rgba(59,130,246,0.12);
        transform: translateY(-4px);
    }
    .org-card-accent {
        height: 5px;
        border-radius: 20px 20px 0 0;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    }
    .org-card-root .org-card-accent {
        height: 7px;
        background: linear-gradient(90deg, #1e40af, #3b82f6);
    }
    .org-card-root { width: 240px; }
    .org-card-body {
        padding: 35px 16px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* ---- AVATAR RING ---- */
    .org-avatar-ring {
        position: absolute;
        top: -28px;
        left: 50%;
        transform: translateX(-50%);
        width: 56px;
        height: 56px;
        border-radius: 50%;
        padding: 3px;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 20;
    }
    .org-avatar-ring img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #3b82f6;
    }

    /* ---- TYPOGRAPHY ---- */
    .org-title {
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
        margin: 5px 0 2px;
        line-height: 1.2;
        text-align: center;
    }
    .org-name {
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
        margin: 0;
        text-align: center;
    }

    /* ---- ASSISTANT BRANCH (Staf Khusus) ---- */
    .assistant-branch {
        display: flex;
        align-items: center;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 5;
    }
    .assistant-left { right: 100%; }
    .assistant-right { left: 100%; }
    .assistant-line {
        width: 25px;
        border-top: 2px dashed #94a3b8;
    }
    .assistant-cards {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .assistant-card {
        width: 180px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px;
        position: relative;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .assistant-badge {
        position: absolute;
        top: -8px;
        right: 8px;
        background: #f59e0b;
        color: white;
        font-size: 8px;
        font-weight: 800;
        padding: 1px 8px;
        border-radius: 10px;
        text-transform: uppercase;
    }
    .assistant-inner {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .assistant-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        background: #eee;
    }
    .assistant-info h4 {
        font-size: 10px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }
    .assistant-info p {
        font-size: 9px;
        color: #64748b;
        margin: 0;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="relative bg-slate-900 py-20 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/20 to-transparent"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-200 text-[10px] font-black uppercase tracking-[0.3em] px-4 py-1.5 rounded-full mb-6 border border-blue-400/20">
            Hierarki Organisasi
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-white mb-4 tracking-tight uppercase italic">Struktur Organisasi</h1>
        <p class="text-lg text-blue-100/70 max-w-2xl mx-auto font-light leading-relaxed">Mengenal lebih dekat susunan kepemimpinan dan hierarki fungsional Kantor Cabang Dinas Pendidikan.</p>
    </div>
</div>

<!-- Main Content Area -->
<div class="py-24 bg-slate-50 relative min-h-screen">
    <div class="max-w-[100vw] overflow-x-auto relative z-10 px-4">
        @if($struktur->count() > 0)
            @php
                $roots = $struktur->where('parent_id', null)->where('jenis_hubungan', 'struktural');
                if($roots->isEmpty()) {
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
                <i class='bx bx-sitemap fs-1 text-slate-300 mb-4'></i>
                <h3 class="text-xl font-bold text-slate-800">Struktur Belum Tersedia</h3>
                <p class="text-slate-500 mt-2">Data struktur organisasi akan muncul setelah diatur di panel Admin.</p>
            </div>
        @endif
    </div>
</div>
@endsection
