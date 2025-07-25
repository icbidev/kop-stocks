<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\WeightUnits;
use App\Models\Category;
use Inertia\Inertia;
use App\Models\Supplier;

class LowStocksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $stockStatus = $request->query('stock-status'); // 'low', 'out', etc.
        $supplier = $request->query('supplier');
    
        $products = Product::query();
    
        if ($stockStatus === 'low') {
            $products->whereColumn('quantity', '<', 'minimum_quantity');
        } elseif ($stockStatus === 'out') {
            $products->where('quantity', '=', 0);
        }
    
        if ($supplier) {
            $products->whereHas('suppliers', function ($query) use ($supplier) {
                $query->where('name', $supplier); // adjust if you're filtering by name or ID
            });
        }

        $products = Product::with(['movements', 'category', 'suppliers', 'weight_unit'])
            ->get()
            ->filter(function ($product) {
                return $product->quantity < $product->minimum_quantity;
            })
            ->map(function ($product) {
                $product->supplier_ids = $product->suppliers->pluck('id');
                return $product;
            })
            ->values(); // reindex the collection after filtering
    
        return Inertia::render('Low Stocks/Index', [
            'products' => $products,
            'stockStatus' => $stockStatus,
            'selectedSupplier' => $supplier,
            'category' => Category::with(['products'])->get(),
            'supplier' => Supplier::pluck('name'), // or Supplier::all()
        ]);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
