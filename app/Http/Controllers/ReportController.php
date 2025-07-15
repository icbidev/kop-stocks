<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default range: last 7 days (or adjust as needed)
        $from = $request->input('from_date', Carbon::today()->subDays(7)->toDateString());
        $to   = $request->input('to_date', Carbon::today()->toDateString());

        // Eager load movements in the given date range
        $products = Product::with(['movements' => function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }])->get();

        $reportData = $products->map(function ($product) {
            $qtyIn = $product->movements->where('type', 'in')->sum('quantity');
            $qtyOut = $product->movements->where('type', 'out')->sum('quantity');
            return [
                'id'       => $product->id,
                'name'     => $product->name,
                'qty_in'   => $qtyIn,
                'qty_out'  => $qtyOut,
                'total'    => $qtyIn - $qtyOut,
            ];
        });

        return Inertia::render('Reports/Index', [
            'report'    => $reportData,
            'from_date' => $from,
            'to_date'   => $to,
        ]);
    }
}
