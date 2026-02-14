<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $cart = $this->activeCart($request);

        return response()->json([
            'success' => true,
            'data' => $cart,
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $cart = $this->activeCart($request);

        $item = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $newQuantity = ($item->exists ? $item->quantity : 0) + $validated['quantity'];

        if ($newQuantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi stok produk.',
            ], 422);
        }

        $item->quantity = $newQuantity;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke cart.',
            'data' => $this->activeCart($request),
        ]);
    }

    public function update(Request $request, int $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->activeCart($request);
        $item = $cart->items()->with('product')->findOrFail($itemId);

        if ($validated['quantity'] > $item->product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi stok produk.',
            ], 422);
        }

        $item->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'success' => true,
            'message' => 'Jumlah item cart berhasil diubah.',
            'data' => $this->activeCart($request),
        ]);
    }

    public function destroy(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->activeCart($request);
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item cart berhasil dihapus.',
            'data' => $this->activeCart($request),
        ]);
    }

    protected function activeCart(Request $request): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'active'],
            ['user_id' => $request->user()->id, 'status' => 'active']
        )->load('items.product');
    }
}

