<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::all();
        return response()->json($menus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'type' => 'required|in:paket,satuan,dessert',
        'image' => 'nullable', // Allow the image field to be empty
    ]);

    $data = $request->only(['name', 'price', 'type']);

    // Check if the image is a file
    if ($request->hasFile('image')) {
        $request->validate([
            'image' => 'file|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        // Store the uploaded file
        $filePath = $request->file('image')->store('images', 'public');
        $data['image'] = $filePath; // Save the file path to the database
    } elseif ($request->image) {
        // Validate the URL if provided
        $request->validate([
            'image' => 'string|url|max:2048',
        ]);
        // Save the URL if provided
        $data['image'] = $request->image;
    }

    $menu = Menu::create($data);

    return response()->json($menu);
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'type' => 'required|in:paket,satuan,dessert',
        'image' => 'nullable', // Allow the image field to be empty
    ]);

    $menu = Menu::findOrFail($id);
    $data = $request->only(['name', 'price', 'type']);

    // Check if the image is a file
    if ($request->hasFile('image')) {
        $request->validate([
            'image' => 'file|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        // Store the uploaded file
        $filePath = $request->file('image')->store('images', 'public');
        $data['image'] = $filePath; // Save the file path to the database
    } elseif ($request->image) {
        // Validate the URL if provided
        $request->validate([
            'image' => 'string|url|max:2048',
        ]);
        // Save the URL if provided
        $data['image'] = $request->image;
    }

    $menu->update($data);

    return response()->json($menu);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return response()->json(['message' => 'Menu deleted successfully.']);
    }
}
