<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Admin: show all payments with optional filter
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all, payed, not_payed

        $query = Payment::query();

        if ($filter === 'payed') {
            $query->where('payed', true);
        } elseif ($filter === 'not_payed') {
            $query->where('payed', false);
        }

        return response()->json($query->get());
    }

    // User: show own payments with filter
    public function myPayments(Request $request)
    {
        $filter = $request->query('filter', 'all');

        $query = Payment::where('user_id', Auth::id());

        if ($filter === 'payed') {
            $query->where('payed', true);
        } elseif ($filter === 'not_payed') {
            $query->where('payed', false);
        }

        return response()->json($query->get());
    }

    // Create payment
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer',
            'amount'     => 'required|numeric',
            'payed'      => 'required|boolean',
            'when'       => 'nullable|date',
            'name'       => 'nullable|string|max:255',
        ]);

        $payment = Payment::create([
            'user_id'    => Auth::id(),
            'booking_id' => $request->booking_id,
            'amount'     => $request->amount,
            'payed'      => $request->payed,
            'when'       => $request->when ?? now(),
            'name'       => $request->name,
        ]);

        return response()->json($payment, 201);
    }
}
