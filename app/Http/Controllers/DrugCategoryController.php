<?php

namespace App\Http\Controllers;

use App\Models\DrugCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DrugCategoryController extends Controller
{
    public function index()
    {
        $categories = DrugCategory::orderBy('name')->get();
        $dipakai = $this->hitungObatPerKategori();

        return view('categories.index', compact('categories', 'dipakai'));
    }

    public function create()
    {
        return view('categories.form', ['category' => new DrugCategory]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:drug_categories,name',
        ]);

        DrugCategory::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(DrugCategory $category)
    {
        return view('categories.form', compact('category'));
    }

    public function update(Request $request, DrugCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:drug_categories,name,' . $category->id,
        ]);

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diubah.');
    }

    public function destroy(DrugCategory $category)
    {
        if (($this->hitungObatPerKategori()[$category->id] ?? 0) > 0) {
            return back()->with('error', 'Kategori masih dipakai oleh data obat, tidak bisa dihapus.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    // Jumlah obat per kategori. Aman dipanggil sebelum tabel drugs ada (PBI-05).
    private function hitungObatPerKategori(): array
    {
        if (! Schema::hasTable('drugs')) {
            return [];
        }

        return \DB::table('drugs')
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->all();
    }
}