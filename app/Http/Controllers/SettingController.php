<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $gdrive_folder_id = Setting::get('gdrive_folder_id');
        $gdrive_credentials_path = Setting::get('gdrive_credentials_path');
        
        return view('settings.index', compact('gdrive_folder_id', 'gdrive_credentials_path'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gdrive_folder_id' => 'nullable|string',
            'gdrive_credentials' => 'nullable|file|mimes:json',
        ]);

        if ($request->has('gdrive_folder_id')) {
            Setting::set('gdrive_folder_id', $request->gdrive_folder_id);
        }

        if ($request->hasFile('gdrive_credentials')) {
            $file = $request->file('gdrive_credentials');
            // Store locally in storage/app/private
            $path = $file->storeAs('', 'google-drive-credentials.json', ['disk' => 'local']);
            
            Setting::set('gdrive_credentials_path', 'google-drive-credentials.json');
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
