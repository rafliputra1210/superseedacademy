<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest('created_at')->paginate(10);
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal'   => 'required|date',
            'foto'      => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // max 5 MB
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            if (!in_array($ext, $allowedExtensions)) {
                $ext = 'jpg';
            }
            $fileName = time() . '_' . Str::random(20) . '.' . $ext;
            if (!file_exists(public_path('uploads/galleries'))) {
                mkdir(public_path('uploads/galleries'), 0755, true);
            }
            $file->move(public_path('uploads/galleries'), $fileName);
            $validated['foto'] = 'uploads/galleries/' . $fileName;
        }

        Gallery::create($validated);

        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil ditambahkan!');
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal'   => 'required|date',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // max 5 MB
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
            if ($gallery->foto && file_exists(public_path($gallery->foto))) {
                @unlink(public_path($gallery->foto));
            }
            $file = $request->file('foto');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            if (!in_array($ext, $allowedExtensions)) {
                $ext = 'jpg';
            }
            $fileName = time() . '_' . Str::random(20) . '.' . $ext;
            if (!file_exists(public_path('uploads/galleries'))) {
                mkdir(public_path('uploads/galleries'), 0755, true);
            }
            $file->move(public_path('uploads/galleries'), $fileName);
            $validated['foto'] = 'uploads/galleries/' . $fileName;
        } else {
            unset($validated['foto']);
        }

        $gallery->update($validated);

        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil diperbarui!');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->foto && file_exists(public_path($gallery->foto))) {
            unlink(public_path($gallery->foto));
        }
        $gallery->delete();

        return redirect()->back()->with('success', 'Galeri berhasil dihapus!');
    }
}
