<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori penilaian
     */
    public function index()
    {
        $categories = AssessmentCategory::orderBy('urutan', 'asc')->orderBy('id', 'asc')->get();
        return view('admin.assessment_categories.index', compact('categories'));
    }

    /**
     * Simpan kategori penilaian baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:assessment_categories,nama',
            'deskripsi' => 'nullable|string|max:500',
            'urutan'    => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique'   => 'Kategori dengan nama ini sudah ada.',
        ]);

        $validated['urutan'] = $validated['urutan'] ?? (AssessmentCategory::max('urutan') + 1);
        $validated['is_active'] = $request->has('is_active') ? true : false;

        AssessmentCategory::create($validated);

        return redirect()->route('admin.assessment_categories.index')
                         ->with('success', 'Kategori penilaian baru berhasil ditambahkan!');
    }

    /**
     * Update kategori penilaian
     */
    public function update(Request $request, AssessmentCategory $category)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:assessment_categories,nama,' . $category->id,
            'deskripsi' => 'nullable|string|max:500',
            'urutan'    => 'nullable|integer|min:0',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique'   => 'Kategori dengan nama ini sudah digunakan.',
        ]);

        $validated['urutan'] = $validated['urutan'] ?? $category->urutan;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $category->update($validated);

        return redirect()->route('admin.assessment_categories.index')
                         ->with('success', "Kategori '{$category->nama}' berhasil diperbarui!");
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleActive(AssessmentCategory $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.assessment_categories.index')
                         ->with('success', "Kategori '{$category->nama}' berhasil {$status}!");
    }

    /**
     * Hapus kategori penilaian
     */
    public function destroy(AssessmentCategory $category)
    {
        $nama = $category->nama;
        $category->delete();

        return redirect()->route('admin.assessment_categories.index')
                         ->with('success', "Kategori '{$nama}' berhasil dihapus!");
    }
}
