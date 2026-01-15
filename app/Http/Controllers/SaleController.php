<?php
// SaleController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
 use Barryvdh\DomPDF\Facade as PDF;

class SaleController extends Controller
{
    // Show POS page (Select Products, Quantity)
    public function create()
    {
        $categories = Category::all();  // Get all categories
        $products = Product::all();     // Get all products
        return view('pos.create', compact('categories', 'products'));
    }

    // Store the completed sale
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'payment_method' => 'required|in:cash,card,paypal,stripe',
        ]);

        $totalAmount = 0;
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            $totalAmount += $product->price * $productData['quantity'];
        }

        // Create Sale
        $sale = Sale::create([
            'user_id' => auth()->user()->id,
            'total_amount' => $totalAmount,
            'payment_method' => $request->payment_method,
        ]);

        // Create Sale Items
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'price' => $product->price,
            ]);
            $product->stock_quantity -= $productData['quantity']; // Update stock
            $product->save();
        }

        return redirect('/sales')->with('success', 'Sale completed successfully!');
    }

    // Show all sales
    public function index()
    {
        $sales = Sale::with('user')->get();
        return view('sales.index', compact('sales'));
    }

    // Show a single sale with its items
    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    // Delete a sale
    public function destroy(Sale $sale)
    {
        // Delete associated sale items first
        $sale->items()->delete();
        
        // Restore product stock
        foreach ($sale->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock_quantity += $item->quantity;
                $product->save();
            }
        }
        
        // Delete the sale
        $sale->delete();
        
        return redirect('/sales')->with('success', 'Sale deleted successfully!');
    }
   
    public function printReceipt($saleId)
    {
        $sale = Sale::findOrFail($saleId);
        $pdf = PDF::loadView('sales.receipt', compact('sale'));
        return $pdf->download('receipt.pdf');
    }

}
