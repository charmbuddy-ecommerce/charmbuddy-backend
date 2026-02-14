<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword')->toString();
                $query->where(function ($innerQuery) use ($keyword) {
                    $innerQuery->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('description', 'like', '%'.$keyword.'%');
                });
            })
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::query()
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}

