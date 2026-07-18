<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::latest('created_at')->paginate(10);
        return view('admin.achievements.index', compact('achievements'));
    }

    public function create()
    {
        return view('admin.achievements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tingkat'   => 'nullable|string|max:100',
            'tanggal'   => 'required|date',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/achievements'), $fileName);
            $validated['foto'] = 'uploads/achievements/' . $fileName;
        }

        Achievement::create($validated);

        return redirect()->route('admin.achievements.index')->with('success', 'Prestasi berhasil ditambahkan!');
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.achievements.edit', compact('achievement'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tingkat'   => 'nullable|string|max:100',
            'tanggal'   => 'required|date',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
            if ($achievement->foto && file_exists(public_path($achievement->foto))) {
                unlink(public_path($achievement->foto));
            }
            $file = $request->file('foto');
            $fileName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/achievements'), $fileName);
            $validated['foto'] = 'uploads/achievements/' . $fileName;
        } else {
            unset($validated['foto']);
        }

        $achievement->update($validated);

        return redirect()->route('admin.achievements.index')->with('success', 'Prestasi berhasil diperbarui!');
    }

    public function destroy(Achievement $achievement)
    {
        if ($achievement->foto && file_exists(public_path($achievement->foto))) {
            unlink(public_path($achievement->foto));
        }
        $achievement->delete();

        return redirect()->back()->with('success', 'Prestasi berhasil dihapus!');
    }
}
