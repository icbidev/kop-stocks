<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\Route;
use App\Models\RoleRoute;
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return Inertia::render('Roles/Index', [
            'roles' => Role::get(),
            'role_routes' => RoleRoute::get(),
            'routes' => Route::get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Roles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);

        return Inertia::render('Roles/Show', [
            'role' => $role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        return Inertia::render('Roles/Edit', [
            'role' => $role
        ]);
    }

    public function editRoute(Role $role)
    {
        $allRoutes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'name' => $route->getName(),
                'path' => $route->uri(),
            ];
        })->filter(fn($route) => $route['name']); // skip unnamed routes
    
        $roleRoutes = RoleRoute::where('role_id', $role->id)->get(['route_id', 'role_id'])->toArray();


        return Inertia::render('Roles/EditRoute', [
            'role' => $role,
            'routes' => $allRoutes,
            'role_routes' => $roleRoutes,
        ]);
    }
    
    

    public function updateRoute(Request $request, $id)
    {
        $newPaths = $request->input('selectedRoutes'); // array of route_ids
        $oldPaths = RoleRoute::where('role_id', $id)->pluck('route_id')->toArray();
    
        $toAdd = array_diff($newPaths, $oldPaths);
        $toRemove = array_diff($oldPaths, $newPaths);
    
        // Insert new routes
        foreach ($toAdd as $routeId) {
            RoleRoute::create([
                'role_id' => $id,
                'route_id' => $routeId,
            ]);
        }
    
        // Delete removed routes
        if (!empty($toRemove)) {
            RoleRoute::where('role_id', $id)
                ->whereIn('route_id', $toRemove)
                ->delete();
        }
    
        return back()->with('success', 'Routes updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
