<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(6);

        if ($request->ajax()) {
            return view('items.partials.table_data', compact('items'))->render();
        }

        return view('items.list', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name'   => 'required|string|max:255',
            'buy_price'   => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'unit'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'foto.*'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
        ]);

        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $this->uploadAndCompress($file);
                $fotoPaths[] = $path;
            }
        }

        $validated['foto'] = $fotoPaths;
        Item::create($validated);
        return response()->json(['success' => 'Data barang berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'item_name'   => 'required|string|max:255',
            'buy_price'   => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'unit'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'foto.*'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            'saved_fotos' => 'nullable|array',
        ]);

        $keptPhotos = $request->input('saved_fotos', []);

        $oldPhotosFromDb = $item->foto ?? [];
        if (is_array($oldPhotosFromDb)) {
            foreach ($oldPhotosFromDb as $dbPhoto) {
                if (!in_array($dbPhoto, $keptPhotos)) {
                    if (Storage::disk('public')->exists($dbPhoto)) {
                        Storage::disk('public')->delete($dbPhoto);
                    }
                }
            }
        }

        $newPhotos = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $this->uploadAndCompress($file);
                $newPhotos[] = $path;
            }
        }

        $finalPhotos = array_merge($keptPhotos, $newPhotos);
        $validated['foto'] = $finalPhotos;

        unset($validated['saved_fotos']);

        $item->update($validated);

        return response()->json(['success' => 'Data barang berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        if ($item->foto && is_array($item->foto)) {
            foreach ($item->foto as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $item->delete();

        return response()->json(['success' => 'Data barang berhasil dihapus.']);
    }

    private function uploadAndCompress($file)
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'items/' . $filename;
        $image = Image::read($file);

        $image->scaleDown(width: 1200);

        $encoded = $image->toJpeg(quality: 80);

        Storage::disk('public')->put($path, $encoded);

        return $path;
    }
}
