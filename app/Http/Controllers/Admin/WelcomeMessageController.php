<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WelcomeMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WelcomeMessageController extends Controller
{
    public function index()
    {
        $welcome = WelcomeMessage::first() ?? new WelcomeMessage();
        return view('admin.welcome.index', compact('welcome'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pimpinan_name' => 'nullable|string|max:255',
            'pimpinan_role' => 'nullable|string|max:255',
        ]);

        $welcome = WelcomeMessage::first() ?? new WelcomeMessage();
        
        $data = $request->only(['title', 'content', 'pimpinan_name', 'pimpinan_role']);

        if ($request->hasFile('image')) {
            if ($welcome->image) {
                Storage::disk('public')->delete($welcome->image);
            }
            $data['image'] = $request->file('image')->store('welcome', 'public');
        }

        if ($welcome->exists) {
            $welcome->update($data);
        } else {
            WelcomeMessage::create($data);
        }

        return redirect()->back()->with('success', 'Pesan sambutan berhasil diperbarui!');
    }
}
