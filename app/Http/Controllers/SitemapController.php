<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->get();

        $content = view('sitemap.index', compact('products', 'categories'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
