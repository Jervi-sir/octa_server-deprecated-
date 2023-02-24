<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search() {
        $items = Item::all();
        return $items;
    }

    public function suggest() {
        $items = Item::all();
        return $items;
    }
}
