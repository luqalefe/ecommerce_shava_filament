<?php

namespace App\Http\Controllers;

use App\Models\Product; // Importe o Model
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Vamos pegar os 8 produtos mais recentes que estão ativos
        $products = Product::where('is_active', true)
                           ->latest() // Ordena pelos mais recentes
                           ->take(8)  // Pega apenas 8
                           ->get();

        // Retorna a view 'home.blade.php' e passa a variável 'products' para ela
        return view('home', compact('products'));
    }
}