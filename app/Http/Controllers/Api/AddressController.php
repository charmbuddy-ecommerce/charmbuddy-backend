<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AddressController extends Controller
{
    /**
     * GET: Ambil daftar alamat milik user yang sedang login
     */
    public function index()
    {
        // where('user_id', Auth::id()) <-- KUNCI KEAMANANNYA
        $addresses = Address::where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'data'    => $addresses
        ]);
    }

    public function primary_address()
    {
        // Langsung cari di tabel Address pakai ID User yg sedang login
        $address = Address::where('user_id', Auth::id())
                          ->where('is_primary', true)
                          ->first();

        // Jaga-jaga kalau belum set alamat utama
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada alamat utama',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $address
        ]);
    }

    /**
     * POST: Tambah alamat baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'full_address' => 'required|string',
            'province_id'  => 'required|integer',
            'city_id'      => 'required|integer',
            'postal_code'  => 'required|string|max:10',
            'is_primary'   => 'boolean' // Opsional, user bisa kirim true/false
        ]);

        // Cek Error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input alamat tidak valid',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Ambil data yang sudah bersih
        $data = $validator->validated();
        $data['user_id'] = Auth::id(); // Pasang ID User

        // LOGIKA CEK ALAMAT PERTAMA
        // Cek apakah user ini sudah punya alamat sebelumnya?
        $count = Address::where('user_id', Auth::id())->count();

        if ($count === 0) {
            // Kalau ini alamat PERTAMA, paksa jadi UTAMA
            $data['is_primary'] = true;
        } else {
            // Kalau bukan alamat pertama, tapi user maksa mau jadikan utama
            if (isset($data['is_primary']) && $data['is_primary'] === true) {
                // Turunkan tahta alamat lama (Set semua jadi false)
                Address::where('user_id', Auth::id())->update(['is_primary' => false]);
            } else {
                // Defaultnya false kalau tidak diisi
                $data['is_primary'] = false;
            }
        }

        // 3. SIMPAN KE DATABASE
        $address = Address::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil disimpan',
            'data'    => $address
        ], 201);
    }
}
