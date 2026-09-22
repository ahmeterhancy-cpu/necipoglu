<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.shop.index', [
            'categories' => Category::query()->active()->whereNull('parent_id')->ordered()->withCount('products')->get(),
            'products' => $this->query($request)->paginate(24)->withQueryString(),
            'brands' => Brand::query()->active()->ordered()->get(),
            'category' => null,
        ]);
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        return view('pages.shop.index', [
            'categories' => Category::query()->active()->whereNull('parent_id')->ordered()->withCount('products')->get(),
            'products' => $this->query($request)
                ->whereIn('category_id', $category->descendantIds())
                ->paginate(24)
                ->withQueryString(),
            'brands' => Brand::query()->active()->ordered()->get(),
            'category' => $category,
        ]);
    }

    public function product(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'brand']);

        return view('pages.shop.product', [
            'product' => $product,
            'related' => Product::query()
                ->active()
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->getKey())
                ->ordered()
                ->take(4)
                ->get(),
        ]);
    }

    /** Listeleme sorgusu — arama, marka filtresi ve sıralama. */
    protected function query(Request $request)
    {
        $products = Product::query()->active()->with(['category', 'brand']);

        if ($term = trim((string) $request->query('q'))) {
            $products->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        if ($brand = $request->query('marka') ?: $request->query('brand')) {
            $products->whereHas('brand', fn ($query) => $query->where('slug', $brand));
        }

        return match ($request->query('sirala') ?: $request->query('sort')) {
            'fiyat-artan', 'price-asc' => $products->orderBy('price'),
            'fiyat-azalan', 'price-desc' => $products->orderByDesc('price'),
            'yeni', 'new' => $products->orderByDesc('created_at'),
            default => $products->ordered(),
        };
    }
}
