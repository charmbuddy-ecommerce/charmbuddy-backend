<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;

use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    /**
     * INDEX: Ambil Kategori (Struktur Pohon)
     * URL: /api/categories
     */
    public function index()
    {
        // MAGIC LINE: 
        // 1. whereNull('parent_id') -> Ambil cuma Induknya saja (misal: Perhiasan, Aksesoris)
        // 2. with('children') -> Tapi tolong bawain anak-anaknya sekalian (misal: Cincin, Kalung)
        $categories = Category::whereNull('parent_id')->with('children')->get();

        return response()->json([
            'success' => true,
            'message' => 'List Kategori berhasil diambil',
            'data'    => $categories
        ]);
    }

    /**
     * SHOW: Lihat detail 1 kategori beserta produk di dalamnya
     * URL: /api/categories/{id}
     */
    public function show($id)
    {
        // Cari kategori, sekalian bawa produk-produknya
        $category = Category::with('products')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Kategori ditemukan',
            'data'    => $category
        ]);
    }

    public function store(Request $request)
    {
        // 1. Buat Aturan Validasi (Manual)
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|exists:categories,id',
            'name'      => 'required|string|max:255',
        ]);

        // 2. Cek Apakah Gagal?
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid, silahkan cek lagi',
                'errors'  => $validator->errors() // Kasih tau bagian mana yang salah
            ], 422); // Kode 422 = Unprocessable Entity (Input Salah)
        }

        // 3. Kalau Lolos, Simpan Datanya
        $category = Category::create($validator->validated()); // Pakai data yang sudah divalidasi

        // 4. Return Sukses
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $category
        ], 201);
    }
}
