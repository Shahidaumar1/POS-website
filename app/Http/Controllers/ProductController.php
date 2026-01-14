<?php
// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Display all products
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }
    

    // Show the form to create a new product
    public function create()
    {
        $categories = Category::all(); // Get all categories
        return view('products.create', compact('categories'));
    }

    // Store a newly created product in the database
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'price' => 'required|numeric',
    //         'stock_quantity' => 'required|integer',
    //         'category_id' => 'required|exists:categories,id',
    //     ]);

    //     Product::create([
    //         'name' => $request->name,
    //         'price' => $request->price,
    //         'stock_quantity' => $request->stock_quantity,
    //         'category_id' => $request->category_id,
    //     ]);

    //     return redirect('/products')->with('success', 'Product created successfully.');
    // }
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock_quantity' => 'required|integer',
        'category_id' => 'required|exists:categories,id',
    ]);

    Product::create([
        'name' => $request->name,
        'price' => $request->price,
        'stock_quantity' => $request->stock_quantity,
        'category_id' => $request->category_id,
    ]);

    return redirect()->route('products.index')->with('success', 'Product created successfully.');
}


    // Show the form to edit a product
    public function edit(Product $product)
    {
        $categories = Category::all(); // Get all categories
        return view('products.edit', compact('product', 'categories'));
    }

    // Update the product in the database
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'category_id' => $request->category_id,
        ]);

        return redirect('/products')->with('success', 'Product updated successfully.');
    }

    // Delete the product from the database
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted successfully.');
    }
}
