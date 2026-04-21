<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    private const PER_PAGE_DEFAULT = 12;

    private const PER_PAGE_MAX = 50;

    /**
     * GET /api/products
     *
     * List products with pagination and optional ?category_id filter.
     * Eager-loads the related category to avoid N+1.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(
            (int) $request->integer('per_page', self::PER_PAGE_DEFAULT),
            self::PER_PAGE_MAX,
        );

        $categoryId = $request->integer('category_id');

        $products = Product::query()
            ->with('category')
            ->when($categoryId, fn ($query, $id) => $query->where('category_id', $id))
            ->latest('id')
            ->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * GET /api/products/{product}
     */
    public function show(Product $product): ProductResource
    {
        $product->load('category');

        return new ProductResource($product);
    }
}
