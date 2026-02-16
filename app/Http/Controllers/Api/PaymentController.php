<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * POST: Upload Bukti Pembayaran
     * URL: /api/orders/{id}/pay
     */
    public function store(Request $request, $orderId)
    {
        // 1. Validasi File
        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Cari Order milik user
        $order = Order::where('id', $orderId)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();

        // 3. Cari Data Payment yang dibuat otomatis oleh CheckoutController Argy
        // Kita pakai firstOrNew buat jaga-jaga kalau ternyata belum ke-create
        $payment = Payment::firstOrNew([
            'order_id' => $order->id
        ]);
        
        // Kalo baru (new), set user_id & amount
        if (!$payment->exists) {
            $payment->user_id = Auth::id();
            $payment->amount = $order->total_price;
        }

        // 4. Proses Upload
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $payment->payment_proof_path = $path;
        }

        $payment->status = 'Pending'; // Ubah status biar dicek admin
        $payment->save();

        // Update status Order juga biar sinkron
        $order->update(['status' => 'Pending']);

        return response()->json([
            'success' => true,
            'message' => 'Bukti berhasil diupload',
            'data'    => $payment
        ]);
    }
}
