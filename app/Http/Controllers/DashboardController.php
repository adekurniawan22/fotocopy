<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Item;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalItems = Item::count();

        $expensiveItems = Item::orderBy('sell_price', 'desc')->take(5)->get();

        $cheapestItems = Item::orderBy('sell_price', 'asc')->take(5)->get();

        return view('dashboard.index', compact(
            'totalUsers', 
            'totalItems', 
            'expensiveItems', 
            'cheapestItems'
        ));
    }
}