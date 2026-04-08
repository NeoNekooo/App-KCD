<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('order')->orderBy('created_at', 'desc')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'order' => 'required|integer|min:1|unique:sliders,order',
        ], [
            'order.unique' => 'Nomor urutan ini sudah digunakan oleh slider lain. Silakan pilih nomor lain.',
            'order.required' => 'Nomor urutan wajib diisi.',
        ]);

        $imagePath = $request->file('image')->store('sliders', 'public');

        Slider::create([
            'image' => $imagePath,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'order' => $request->order,
        ]);

        return redirect()->route('admin.website.sliders.index')->with('success', 'Slider baru berhasil ditambahkan!');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'order' => [
                'required', 
                'integer', 
                'min:1', 
                Rule::unique('sliders', 'order')->ignore($slider->id)
            ],
        ], [
            'order.unique' => 'Nomor urutan ini sudah digunakan. Silakan gunakan nomor lain.',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slider->image);
            $imagePath = $request->file('image')->store('sliders', 'public');
            $slider->image = $imagePath;
        }

        $slider->title = $request->title;
        $slider->subtitle = $request->subtitle;
        $slider->order = $request->order;
        $slider->is_active = $request->has('is_active');
        $slider->save();

        return redirect()->route('admin.website.sliders.index')->with('success', 'Perubahan slider berhasil disimpan!');
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('public')->delete($slider->image);
        $slider->delete();

        return redirect()->route('admin.website.sliders.index')->with('success', 'Slider telah berhasil dihapus!');
    }
}
