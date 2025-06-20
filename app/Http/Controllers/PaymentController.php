<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Payment;
use App\Models\PurchasedProduct;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $totalAmount = 0;

        // Calculate total amount
        foreach ($request->products as $item) {
            $product = Product::findOrFail($item['product_id']);
            $totalAmount += $product->price * $item['quantity'];
        }

        // Create ONE payment record with total amount
$payment = Payment::create([
    'user_id' => $user->id,
    'price' => $totalAmount,   // use price, as in DB
    'status' => 'pending',
]);


        // Create PurchasedProduct entries for each product in the payment
        foreach ($request->products as $item) {
            PurchasedProduct::create([
                'payment_id' => $payment->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        // Generate QR Code (your existing Python script call)
        $merchantId = 'cheahun_seng@wing';
        $merchantName = 'CHEAHUN SENG';
        $merchantCity = 'Phnom Penh';
        $pythonPath = '"C:/Program Files/Python312/python.exe"';
        $scriptPath = base_path('qr/generate_khqr.py');

        $command = $pythonPath . " "
                 . escapeshellarg($scriptPath) . " "
                 . escapeshellarg($merchantId) . " "
                 . escapeshellarg($totalAmount) . " "
                 . escapeshellarg($merchantName) . " "
                 . escapeshellarg($merchantCity);

        exec($command, $outputLines, $returnVar);
        $output = implode('', $outputLines);

        if ($returnVar !== 0 || empty($output)) {
            return response()->json(['error' => 'Failed to generate QR code'], 500);
        }

        $qrData = json_decode($output, true);

        return response()->json([
            'message' => 'KHQR generated',
            'qr_string' => $qrData['qr'] ?? null,
            'md5' => $qrData['md5'] ?? null,
            'status' => 'your not pay yet',
            'name' => $merchantName,
            'amount' => $totalAmount,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'purchased_products' => $request->products,
        ]);
    }

 public function all(Request $request)
    {
        // Get all payments with eager loaded user and purchasedProducts relations
        $payments = Payment::with(['user', 'purchasedProducts'])->get();

        // Format the response if needed (e.g., rename price to amount)
        $result = $payments->map(function ($payment) {
            return [
                'user' => $payment->user,
                'purchased_products' => $payment->purchasedProducts,
                'amount' => $payment->price, // assuming price is total amount
            ];
        });

        return response()->json($result);
    }

}
