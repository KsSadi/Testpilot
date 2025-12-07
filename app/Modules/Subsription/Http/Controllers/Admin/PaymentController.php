<?php

namespace App\Modules\Subsription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Subsription\Models\SubscriptionPayment;
use App\Modules\Subsription\Models\UserSubscription;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionPayment::with(['user', 'subscription.plan']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest()->paginate(20);

        $pendingCount = SubscriptionPayment::pending()->count();

        return view('Subsription::admin.payments.index', compact('payments', 'pendingCount'));
    }

    public function show(SubscriptionPayment $payment)
    {
        $payment->load(['user', 'subscription.plan', 'approver']);

        return view('Subsription::admin.payments.show', compact('payment'));
    }

    public function approve(Request $request, SubscriptionPayment $payment)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->approve(auth()->user(), $request->notes);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment approved and subscription activated!');
    }

    public function reject(Request $request, SubscriptionPayment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payment->reject(auth()->user(), $request->reason);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment rejected!');
    }
}
