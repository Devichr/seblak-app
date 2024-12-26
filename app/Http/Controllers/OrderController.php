<?php
namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('payment')->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'table_number' => $order->table_number,
                'portions' => is_string($order->portions) ? json_decode($order->portions) : $order->portions, // Pastikan decode hanya untuk string
                'total' => $this->calculateTotal($order->portions),
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'payment' => [
                    'customer_name' => $order->payment->customer_name ?? null,
                    'customer_email' => $order->payment->customer_email ?? null,
                    'customer_phone' => $order->payment->customer_phone ?? null,
                    'payment_method' => $order->payment->payment_method ?? null,
                ],
            ];
        });
    
        return response()->json($orders);
    }

// Fungsi untuk menghitung total dari portions JSON
private function calculateTotal($portionsJson)
{
    $portions = json_decode($portionsJson, true);
    $total = 0;

    if (is_array($portions)) {
        foreach ($portions as $portion) {
            $total += $portion['price'] ?? 0;

            if (!empty($portion['toppings'])) {
                foreach ($portion['toppings'] as $topping) {
                    $total += $topping['price'] ?? 0;
                }
            }
        }
    }

    return $total;
}

    // Controller untuk membuat pesanan baru
    public function create(Request $request)
    {
        $validated = $request->validate([
            'orderType' => 'required|string',
            'tableNumber' => 'nullable|integer',
            'portions' => 'required|array',
            'portions.*.toppings' => 'nullable|array',
            'portions.*.toppings.*.name' => 'required|string',
            'portions.*.toppings.*.price' => 'required|numeric',
        ]);

        // Simpan data order ke database (contoh sederhana)
        $order = Order::create([
            'order_type' => $validated['orderType'],
            'table_number' => $validated['tableNumber'],
            'portions' => json_encode($validated['portions']),
        ]);

        return response()->json(['message' => 'Pesanan berhasil dibuat!', 'id' => $order->id,], 201);
    }

    // Controller untuk mendapatkan daftar pesanan
    public function show($id)
    {
        // Cari order berdasarkan ID
    $order = Order::findOrFail($id);

    // Decode JSON portions
    $order->portions = json_decode($order->portions, true);

    // Kirim data ke frontend
    return Inertia::render('Detail', ['order' => $order]);
    }

    public function confirm($id){
        // Cari order berdasarkan ID
        $order = Order::findOrFail($id);

        // Update status order
        $order->update(['status' => 'completed']);

        return response()->json(['message' => 'Pesanan berhasil dikonfirmasi!']);
    }

    public function cancel($id){
        // Cari order berdasarkan ID
        $order = Order::findOrFail($id);

        // Update status order
        $order->update(['status' => 'canceled']);

        return response()->json(['message' => 'Pesanan berhasil dibatalkan!']);
    }

}
