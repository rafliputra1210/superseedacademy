<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest('created_at')->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'konten'    => 'required|string',
            'tanggal'   => 'required|date',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // max 5 MB
        ]);

        // Auto-generate slug dari judul agar URL ramah SEO
        $validated['slug'] = Str::slug($request->judul) . '-' . Str::random(5);
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            if (!in_array($ext, $allowedExtensions)) {
                $ext = 'jpg';
            }
            $fileName = time() . '_' . Str::slug($validated['judul'], '_') . '_' . Str::random(8) . '.' . $ext;
            if (!file_exists(public_path('uploads/news'))) {
                mkdir(public_path('uploads/news'), 0755, true);
            }
            $file->move(public_path('uploads/news'), $fileName);
            $validated['foto'] = 'uploads/news/' . $fileName;
        }

        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diterbitkan!');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'konten'    => 'required|string',
            'tanggal'   => 'required|date',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // max 5 MB
        ]);

        // Update slug jika judul berubah
        if ($news->judul !== $request->judul) {
            $validated['slug'] = Str::slug($request->judul) . '-' . Str::random(5);
        }

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
            if ($news->foto && file_exists(public_path($news->foto))) {
                @unlink(public_path($news->foto));
            }
            $file = $request->file('foto');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            if (!in_array($ext, $allowedExtensions)) {
                $ext = 'jpg';
            }
            $fileName = time() . '_' . Str::slug($validated['judul'], '_') . '_' . Str::random(8) . '.' . $ext;
            if (!file_exists(public_path('uploads/news'))) {
                mkdir(public_path('uploads/news'), 0755, true);
            }
            $file->move(public_path('uploads/news'), $fileName);
            $validated['foto'] = 'uploads/news/' . $fileName;
        } else {
            unset($validated['foto']);
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy(News $news)
    {
        if ($news->foto && file_exists(public_path($news->foto))) {
            unlink(public_path($news->foto));
        }
        News::destroy($news->id);

        return redirect()->back()->with('success', 'Berita berhasil dihapus!');
    }
}