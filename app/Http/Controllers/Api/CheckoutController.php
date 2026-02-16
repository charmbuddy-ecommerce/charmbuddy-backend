<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address' => ['required', 'string'],
            'courier_service' => ['required', 'string', 'max:100'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $user = $request->user();

        $result = DB::transaction(function () use ($user, $validated) {
            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->with('items.product')
                ->first();

            if (! $cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart kosong, checkout tidak dapat diproses.',
                ], 422);
            }

            $totalProductPrice = 0;
            $orderItemsPayload = [];

            foreach ($cart->items as $cartItem) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($cartItem->product_id);

                if ($product->stock < $cartItem->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok produk "'.$product->name.'" tidak mencukupi.',
                    ], 422);
                }

                $subtotal = (float) $product->price * $cartItem->quantity;
                $totalProductPrice += $subtotal;

                $orderItemsPayload[] = [
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price_at_checkout' => $product->price,
                    'subtotal' => $subtotal,
                ];

                $product->decrement('stock', $cartItem->quantity);
            }

            $shippingCost = (float) $validated['shipping_cost'];
            $totalPrice = $totalProductPrice + $shippingCost;

            $order = Order::create([
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'total_price' => $totalPrice,
                'shipping_cost' => $shippingCost,
                'shipping_address' => $validated['shipping_address'],
                'courier_service' => $validated['courier_service'],
                'status' => 'Pending',
            ]);

            foreach ($orderItemsPayload as $itemPayload) {
                $itemPayload['order_id'] = $order->id;
                OrderItem::create($itemPayload);
            }

            Payment::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $totalPrice,
                'status' => 'Pending',
            ]);

            $cart->update(['status' => 'checked_out']);

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil diproses.',
                'data' => $order->load(['items.product', 'payment']),
            ], 201);
        });

        return $result;
    }
}
