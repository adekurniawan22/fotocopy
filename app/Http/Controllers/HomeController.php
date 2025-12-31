<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(8);

        if ($request->ajax()) {
            return view('home.partials.item_grid', compact('items'))->render();
        }

        return view('home.index', compact('items'));
    }
}
