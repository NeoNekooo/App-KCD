<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\WelcomeMessage;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('is_active', true)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        $welcome = WelcomeMessage::first();
        
        // Ambil 3 data terbaru
        $latestBerita = \App\Models\Berita::where('status', 'publish')->latest()->take(3)->get();
        $latestPengumuman = \App\Models\Pengumuman::where('status', 'publish')->latest()->take(3)->get();
        $latestGaleri = \App\Models\Galeri::latest()->take(3)->get();

        return view('frontend.welcome', compact(
            'sliders', 
            'welcome', 
            'latestBerita', 
            'latestPengumuman', 
            'latestGaleri'
        ));
    }
}
