<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;

use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * INDEX: Mengambil semua produk dari database
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * STORE: Memasukkan produk ke database
     */
    public function store(Request $request)
    {
        // 1. Buat Aturan Validasi (Manual)
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'weight'      => 'required|integer|min:1',
            'stock'       => 'integer|min:0',
            'image_path'  => 'nullable|string',
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
        $product = Product::create($validator->validated()); // Pakai data yang sudah divalidasi

        // 4. Return Sukses
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $product
        ], 201);
    }
}
