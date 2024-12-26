<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::all();
        return response()->json($methods);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'account_number' => 'nullable|string|max:255',
            'account_holder' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('payment_methods', 'public');
        }

        $method = PaymentMethod::create([
            'name' => $validated['name'],
            'image' => $imagePath,
            'account_number' => $validated['account_number'],
            'account_holder' => $validated['account_holder'],
            'bank_name' => $validated['bank_name'],
        ]);

        return response()->json($method, 201);
    }

    public function update(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'account_number' => 'nullable|string|max:255',
            'account_holder' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            if ($method->image) {
                \Storage::delete('public/' . $method->image);
            }
            $method->image = $request->file('image')->store('payment_methods', 'public');
        }

        $method->update($validated);

        return response()->json($method);
    }

    public function destroy($id)
    {
        $method = PaymentMethod::findOrFail($id);

        if ($method->image) {
            \Storage::delete('public/' . $method->image);
        }

        $method->delete();

        return response()->json(['message' => 'Metode pembayaran berhasil dihapus.']);
    }
}
