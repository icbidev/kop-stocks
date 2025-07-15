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
    public function index()
    {
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
            'category' => Category::with(['products'])->get()
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
