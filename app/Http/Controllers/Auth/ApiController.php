<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ApiController extends Controller
{
    public function apiCategory(){
        $category = DB::table('categories')->get();

        return json_encode($category);
    }

    public function apiProduct(){
        $product = DB::table('products')->get();

        return json_encode($product);
    }
    public function apiDelivery(){
        $delivery = DB::table('deliveries')->get();

        return json_encode($delivery);
    }
}
