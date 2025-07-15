<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lowStockProducts = Product::where('quantity', '<=', 10)->get();
    
        if ($lowStockProducts->isNotEmpty()) {
            // You can adjust how you determine the admin user
            $admin = User::where('email', 'chrisangeles30@gmail.com')->first(); // or use a role, etc.
    
            if ($admin) {
                $admin->notify(new LowStockNotification($lowStockProducts));
                $this->info('Low stock notification sent to admin.');
            } else {
                $this->error('Admin user not found.');
            }
        } else {
            $this->info('No low stock products today.');
        }
    }
}
