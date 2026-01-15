<?php

// CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Show categories with products dashboard
    public function dashboard()
    {
        $categories = Category::with(['products' => function($query) {
            $query->withCount('saleItems');
        }])->get()->map(function($category) {
            $category->products->map(function($product) {
                $product->total_sold = SaleItem::where('product_id', $product->id)->sum('quantity');
                return $product;
            });
            return $category;
        });

        return view('categories.dashboard', compact('categories'));
    }

    // Show all categories
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    // Create a new category
    public function create()
    {
        return view('categories.create');
    }

    // Store new category
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create($request->all());
        return redirect()->route('categories.index');
    }

    // Edit an existing category
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Update an existing category
    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category->update($request->all());
        return redirect()->route('categories.index');
    }

    // Delete category
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index');
    }
}
