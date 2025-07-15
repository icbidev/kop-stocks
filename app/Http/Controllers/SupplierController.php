<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use Inertia\Inertia;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Supplier/Index', [
            'suppliers' => Supplier::get(),
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $supplier = Supplier::create($validated);
    
        return back()->with('supplier', $supplier);
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

        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $unit = Supplier::findOrFail($id);
        $unit->update([
            'name' => $request->name,
        ]);
    
        return redirect()->back()->with('newSupplier', $unit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $unit = Supplier::findOrFail($id);
        $unit->delete();
    
        return back();
    }
}
