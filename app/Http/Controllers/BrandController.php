<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        return view('pages.brands', [
            'brands' => Brand::query()->active()->ordered()->withCount('products')->get(),
        ]);
    }

    public function show(Brand $brand): View
    {
        abort_unless($brand->is_active, 404);

        return view('pages.brand', [
            'brand' => $brand,
            'gallery' => $brand->galleryImages(),
            'branches' => \App\Models\Branch::query()->active()->ordered()->get(),
            'products' => $brand->products()->active()->ordered()->with('category')->take(8)->get(),
            'productCount' => $brand->products()->active()->count(),
            'others' => Brand::query()->active()->ordered()
                ->whereKeyNot($brand->getKey())
                ->take(12)->get(),
        ]);
    }
}
