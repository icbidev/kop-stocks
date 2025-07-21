<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route as RouteFacade;
use App\Models\Role;
use App\Models\Route;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Load all roles (admin, cashier, manager, etc.)
        $roles = Role::all()->keyBy('name'); // ['admin' => Role, 'cashier' => Role, 'manager' => Role]
        $routeIds = [];

        // Get all named routes
        $routes = collect(RouteFacade::getRoutes())->map(function ($route) {
            return [
                'name' => $route->getName(),
                'path' => $route->uri(),
            ];
        })->filter(fn ($route) => !is_null($route['name'])); // Skip unnamed routes

        foreach ($routes as $route) {
            // Save route to DB if not already saved
            $saved = Route::firstOrCreate([
                'name' => $route['name'],
                'path' => $route['path'],
            ]);

            // Detect prefix (e.g., "admin/dashboard" => "admin")
            $prefix = explode('/', $route['path'])[0];

            // Assign route to matching role by prefix
            if (isset($roles[$prefix])) {
                $routeIds[$prefix][] = $saved->id;
            }

            // Assign ALL routes to admin as fallback
            if (isset($roles['admin'])) {
                $routeIds['admin'][] = $saved->id;
            }
        }

        // Attach routes to each role
        foreach ($routeIds as $roleName => $ids) {
            if (isset($roles[$roleName])) {
                $roles[$roleName]->routes()->syncWithoutDetaching($ids);
            }
        }

        echo "Seeded {$routes->count()} named routes and linked to roles.\n";

    // You can comment out the rest while testing route list only
    /*
    foreach ($allRoutes as $route) {
        $saved = Route::firstOrCreate([
            'name' => $route['name'],
            'path' => $route['path'],
        ]);

        $prefix = explode('/', $route['path'])[0];

        if (isset($routeIds[$prefix])) {
            $routeIds[$prefix][] = $saved->id;
        }

        $routeIds['admin'][] = $saved->id;
    }

    foreach ($routeIds as $roleName => $ids) {
        if ($roles[$roleName]) {
            $roles[$roleName]->routes()->sync($ids);
        }
    }
    */
    }
}
