<?php

namespace App\Http\Controllers\Api\Shop;

use App\Models\Item;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ShopListItemsController extends Controller
{
    public function listMyProducts($category_name) {
        $shop = auth()->user();
        $category_id = ProductType::where('name', 'like', $category_name)->first()->id; // You'll need to map this to an actual ID
        $products = $shop->items()->where('product_type_id', $category_id)->orderBy('id', 'desc')->paginate(7);
        //$products = Item::where('product_type_id', $category_id)->orderBy('id', 'desc')->paginate(7);
        
        $data['products'] = [];

        foreach ($products as $index => $product) {
            $selected_image = json_decode($product->images)[0];
           // $image = strpos($selected_image, "https") !== false ? $selected_image : 'http://192.168.1.105:8000' . Storage::url($selected_image);
            
            $data['products'][$index] = [
                'id' => $product->id,
                'name' => $product->name,
                'thumbnail' => json_decode($product->images),
                'price' => $product->price,
                'product_type_id' => $product->product_type_id,
                'is_expired' => checkIfItemIsExpired($product->last_reposted),
                'last_reposted' => $product->last_reposted,
                'created_at' => $product->created_at
            ];
        }
        return response()->json([
            'products' => $data['products'],
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    public function listMyProductsWithOffset($category_name, $start_id = null) {
        $shop = auth()->user();
        $category_id = ProductType::where('name', 'like', $category_name)->first()->id;
        
        $products = $shop->items()->where('product_type_id', $category_id)->where('id', '<=', $start_id)->orderBy('id', 'desc')->paginate(7);
        //$products = Item::where('product_type_id', $category_id)->where('id', '<=', $start_id)->orderBy('id', 'desc')->paginate(7);
    
        $data['products'] = [];
    
        $currentPage = $products->currentPage(); // Capture the current page
    
        foreach ($products as $index => $product) {
            $selected_image = json_decode($product->images)[0];
    
            $data['products'][$index] = [
                'id' => $product->id,
                'name' => $product->name,
                'thumbnail' => json_decode($product->images),
                'price' => $product->price,
                'product_type_id' => $product->product_type_id,
                'current_page' => $currentPage,  // Include the current page for each product
                'is_expired' => checkIfItemIsExpired($product->last_reposted),
                'last_reposted' => $product->last_reposted,
                'created_at' => $product->created_at
            ];
        }
    
        return response()->json([
            'products' => $data['products'],
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }
}
