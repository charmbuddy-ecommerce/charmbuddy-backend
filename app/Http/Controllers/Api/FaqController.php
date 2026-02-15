<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Faq::query()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        // 1. Buat Aturan Validasi (Manual)
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:1000',
            'answer'   => 'required|string|max:1000',
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
        $faq = Faq::create($validator->validated()); // Pakai data yang sudah divalidasi

        // 4. Return Sukses
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $faq
        ], 201);
    }
}

