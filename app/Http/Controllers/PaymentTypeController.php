<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $paymentTypes = PaymentType::all();
        return view('skatepark.payment_types.index', compact('paymentTypes'));
    }

    public function create()
    {
        return view('skatepark.payment_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'is_alert' => 'nullable|boolean',
        ]);

        $isDefault = $request->has('is_default');
        $isAlert = $request->has('is_alert');

        if ($isDefault) {
            PaymentType::query()->update(['is_default' => false]);
        }

        PaymentType::create([
            'name' => $request->name,
            'is_default' => $isDefault,
            'is_alert' => $isAlert,
        ]);

        return redirect()->route('payment_types.index')->with('success', 'Payment Type created successfully.');
    }

    public function edit(PaymentType $paymentType)
    {
        return view('skatepark.payment_types.edit', compact('paymentType'));
    }

    public function update(Request $request, PaymentType $paymentType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'is_alert' => 'nullable|boolean',
        ]);

        $isDefault = $request->has('is_default');
        $isAlert = $request->has('is_alert');

        if ($isDefault) {
            PaymentType::query()->update(['is_default' => false]);
        }

        $paymentType->update([
            'name' => $request->name,
            'is_default' => $isDefault,
            'is_alert' => $isAlert,
        ]);

        return redirect()->route('payment_types.index')->with('success', 'Payment Type updated successfully.');
    }

    public function destroy(PaymentType $paymentType)
    {
        if ($paymentType->is_default) {
            return back()->with('error', 'Cannot delete default payment type. Make another payment type default first.');
        }
        if ($paymentType->playRecords()->exists()) {
            return back()->with('error', 'Cannot delete Payment Type because it is used in play records.');
        }
        $paymentType->delete();
        return redirect()->route('payment_types.index')->with('success', 'Payment Type deleted successfully.');
    }
}
