<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;


class PaymentController extends Controller
{
    public function processPayment(Request $request, Order $order)
{
    // Validasi input
    $validated = $request->validate([
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:20',
        'customer_email' => 'required|email',
        'payment_method' => 'required|string',
    ]);

    // Simpan data pembayaran
    $payment = $order->payment()->create([
        'customer_name' => $validated['customer_name'],
        'customer_phone' => $validated['customer_phone'],
        'customer_email' => $validated['customer_email'],
        'payment_method' => $validated['payment_method'],
    ]);


    if ($validated['payment_method'] === 'cash') {
        // Redirect untuk pembayaran tunai
        return redirect()->route('orders.payment.detail', [
            'paymentMethod' => 'cash',
        ]);
    }

    // Ambil informasi metode pembayaran
    $paymentMethod = PaymentMethod::where('name', $validated['payment_method'])->firstOrFail();

    // Render halaman pembayaran non-tunai
    return Inertia::render('Payment', [
        'paymentMethod' => 'non-cash',
        'paymentDetails' => [
            'bank_name' => $paymentMethod->bank_name,
            'account_number' => $paymentMethod->account_number,
            'account_holder' => $paymentMethod->account_holder,
            'qr_image' => $paymentMethod->image,
        ],
        'timeLimit' => 15, // Waktu pembayaran
    ]);
}


}
