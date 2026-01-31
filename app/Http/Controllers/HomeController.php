<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['specs', 'brand']); // brand = Category relation

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        // Category (navbar uses ?category=id)
        if ($request->filled('category')) {
            $query->where('brand_id', (int) $request->category);
        }

        // Sorting
        $sort = $request->get('sort', 'featured');

        switch ($sort) {
            case 'newest':
                $query->latest(); // created_at desc
                break;

            case 'price_asc':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;

            case 'price_desc':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;

            case 'featured':
            default:
                // if you later add is_featured column, use:
                // $query->orderByDesc('is_featured')->latest();
                $query->inRandomOrder();
                break;
        }

        // Pagination + keep query string
        $products = $query->paginate(12)->withQueryString();

        return view('welcome', compact('products', 'sort'));
    }
}
