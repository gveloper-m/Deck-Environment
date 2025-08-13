<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalService;

class PayPalController extends Controller
{
    protected $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'amount'   => 'required|numeric|min:0.5',
            'currency' => 'sometimes|string|size:3',
        ]);

        $order = $this->paypal->createOrder(
            $data['amount'],
            $data['currency'] ?? 'USD',
            route('paypal.success'),
            route('paypal.cancel')
        );

        // Find the approval URL
        $approvalUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;

        return response()->json([
            'order_id' => $order['id'],
            'approval_url' => $approvalUrl
        ]);
    }

    public function success(Request $request)
    {
        $orderId = $request->query('token');

        $result = $this->paypal->captureOrder($orderId);

        // TODO: mark order as paid in your DB
        return response()->json([
            'status' => 'success',
            'details' => $result
        ]);
    }

    public function cancel()
    {
        return response()->json(['status' => 'cancelled']);
    }
}
