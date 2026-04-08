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

        return view('frontend.welcome', compact('sliders', 'welcome'));
    }
}
