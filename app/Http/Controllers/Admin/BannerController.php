<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('urutan')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120' // max 5 MB
        ]);

        if (Banner::count() >= 5) {
            return back()->with('error', 'Maksimal hanya 5 banner yang diperbolehkan.');
        }

        $file = $request->file('image');
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower($file->guessExtension() ?: 'jpg');
        if (!in_array($ext, $allowedExtensions)) {
            $ext = 'jpg';
        }
        $fileName = time() . '_banner_' . Str::random(12) . '.' . $ext;
        if (!file_exists(public_path('uploads/banners'))) {
            mkdir(public_path('uploads/banners'), 0755, true);
        }
        $file->move(public_path('uploads/banners'), $fileName);
        $imagePath = 'uploads/banners/' . $fileName;

        Banner::create([
            'image_path' => $imagePath,
            'is_active' => true,
            'urutan' => Banner::max('urutan') + 1
        ]);

        return back()->with('success', 'Banner berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        if ($banner->image_path && file_exists(public_path($banner->image_path))) {
            @unlink(public_path($banner->image_path));
        }
        $banner->delete();

        return back()->with('success', 'Banner berhasil dihapus.');
    }

    public function toggleActive($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return back()->with('success', 'Status banner diperbarui.');
    }
}
