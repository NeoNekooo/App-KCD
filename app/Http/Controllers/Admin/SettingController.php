<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_slogan' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        // Update Text Settings
        Setting::updateOrCreate(['key' => 'site_name'], ['value' => $request->site_name]);
        Setting::updateOrCreate(['key' => 'site_slogan'], ['value' => $request->site_slogan]);

        // Handle Logo Upload
        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $logoPath = $request->file('site_logo')->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'site_logo'], ['value' => $logoPath]);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
