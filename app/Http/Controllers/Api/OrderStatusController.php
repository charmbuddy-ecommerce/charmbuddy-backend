<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function status(Request $request, int $orderId): JsonResponse
    {
        $order = Order::query()
            ->where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->with('payment')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'status' => $order->status,
                'tracking_number' => $order->tracking_number,
                'payment_status' => optional($order->payment)->status,
            ],
        ]);
    }

    public function approve(int $orderId): JsonResponse
    {
        $order = Order::with('payment')->findOrFail($orderId);

        if (! $order->payment || ! $order->payment->payment_proof_path) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti pembayaran belum tersedia.',
            ], 422);
        }

        $order->payment->update(['status' => 'Approved']);
        $order->update(['status' => 'Paid']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil disetujui.',
            'data' => $order->fresh()->load('payment'),
        ]);
    }

    public function reject(int $orderId): JsonResponse
    {
        $order = Order::with('payment')->findOrFail($orderId);

        if ($order->payment) {
            $order->payment->update(['status' => 'Rejected']);
        }

        $order->update(['status' => 'Pending']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran ditolak.',
            'data' => $order->fresh()->load('payment'),
        ]);
    }

    public function ship(Request $request, int $orderId): JsonResponse
    {
        $validated = $request->validate([
            'tracking_number' => ['required', 'string', 'max:100'],
        ]);

        $order = Order::with('payment')->findOrFail($orderId);

        if ($order->status !== 'Paid' && $order->status !== 'Processed') {
            return response()->json([
                'success' => false,
                'message' => 'Order belum siap dikirim.',
            ], 422);
        }

        $order->update([
            'tracking_number' => $validated['tracking_number'],
            'status' => 'Shipped',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dikirim.',
            'data' => $order->fresh()->load('payment'),
        ]);
    }
}

