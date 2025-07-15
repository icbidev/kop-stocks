<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user') // <== Add this
        ->when($request->model_type, fn($q) => $q->where('model_type', 'like', "%{$request->model_type}%"))
        ->when($request->changedBy, function ($q) use ($request) {
            $q->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->changedBy . '%');
            });
        })
             
        ->when($request->event, fn($q) => $q->where('event', $request->event))
        ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
        ->latest()
        ->paginate(10)
        ->withQueryString();
    

        return Inertia::render('AuditLogs/Index', [
            'logs' => $logs,
            'filters' => [
                'model_type' => $request->model_type,
                'event' => $request->event,
                'date' => $request->date,
                'changedBy' => $request->changedBy,
            ],
            
        ]);
    }
}
