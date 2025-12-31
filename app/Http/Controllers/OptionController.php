<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function index()
    {
        $options = Option::all();
        return response()->json($options);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'option_name' => 'required|string|unique:options|max:255',
            'text_value'  => 'nullable|string',
        ]);

        $option = Option::create($validated);

        return response()->json([
            'message' => 'Option created successfully',
            'data'    => $option
        ], 201);
    }

    public function show($id)
    {
        $option = Option::find($id);

        if (!$option) {
            return response()->json(['message' => 'Option not found'], 404);
        }

        return response()->json($option);
    }

    public function getByName($name)
    {
        $option = Option::where('option_name', $name)->first();

        if (!$option) {
            return response()->json(['message' => 'Option key not found'], 404);
        }

        return response()->json($option);
    }

    public function update(Request $request, $id)
    {
        $option = Option::find($id);

        if (!$option) {
            return response()->json(['message' => 'Option not found'], 404);
        }

        $validated = $request->validate([
            'option_name' => 'sometimes|required|string|unique:options,option_name,' . $id . ',option_id',
            'text_value'  => 'nullable|string',
        ]);

        $option->update($validated);

        return response()->json([
            'message' => 'Option updated successfully',
            'data'    => $option
        ]);
    }

    public function destroy($id)
    {
        $option = Option::find($id);

        if (!$option) {
            return response()->json(['message' => 'Option not found'], 404);
        }

        $option->delete();

        return response()->json(['message' => 'Option deleted successfully']);
    }
}
