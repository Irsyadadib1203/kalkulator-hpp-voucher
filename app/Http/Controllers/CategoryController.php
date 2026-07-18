<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $name = strtoupper(trim($validated['name']));

        if ($name === '') {
            return response()->json(['message' => 'Nama kategori tidak boleh kosong.'], 422);
        }

        $exists = Auth::user()->categories()
            ->whereRaw('UPPER(name) = ?', [$name])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => "Gagal: Kategori \"{$name}\" sudah terdaftar.",
            ], 422);
        }

        $category = Auth::user()->categories()->create(['name' => $name]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
        ], 201);
    }

    public function destroy(Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        // Transaksi terkait ikut terhapus otomatis (cascadeOnDelete di migration)
        $category->delete();

        return response()->json(['message' => 'deleted']);
    }
}
